<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('orders.view'), 403);

        $search  = $request->search;
        $status  = $request->status;
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        [$from, $to, $activePeriod] = $this->resolvePeriodRange($request);

        $orders = Order::with(['customer', 'user', 'payments'])
            ->withCount('orderDetails')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('invoice_num', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn($q3) => $q3->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($from && $to, fn($q) => $q->whereBetween('order_date', [$from, $to]))
            ->orderBy('order_date', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('orders.index', compact(
            'orders',
            'search',
            'status',
            'perPage',
            'activePeriod',
            'from',
            'to'
        ));
    }

    public function exportPdf(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('orders.view'), 403);

        [$from, $to, $activePeriod] = $this->resolvePeriodRange($request);

        $search = $request->search;
        $status = $request->status;

        $orders = Order::with(['customer', 'user', 'payments'])
            ->withCount('orderDetails')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('invoice_num', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn($q3) => $q3->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($from && $to, fn($q) => $q->whereBetween('order_date', [$from, $to]))
            ->orderBy('order_date', 'desc')
            ->get();

        $periodLabel = $this->periodLabel($activePeriod, $from, $to);
        $fileName = 'orders-' . $activePeriod . '-' . now()->format('YmdHis') . '.pdf';

        return Pdf::loadView('orders.pdf.index', compact(
            'orders',
            'search',
            'status',
            'periodLabel',
            'from',
            'to'
        ))
            ->setPaper('a4', 'landscape')
            ->download($fileName);
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('orders.create'), 403);

        return view('orders.create');
    }

    // STEP 1: customer + auto date
    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('orders.create'), 403);
        $validated = $request->validate([
            'customer_id'      => 'nullable|exists:customers,id',
            'customer_nik'     => 'required|string|max:255',
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'nullable|email|max:255',
            'customer_no_tlp'  => 'nullable|string|max:255',
            'customer_address' => 'nullable|string',
        ]);

        $order = DB::transaction(function () use ($validated) {
            if (!empty($validated['customer_id'])) {
                $customer = Customer::findOrFail($validated['customer_id']);
            } else {
                $customer = Customer::firstOrCreate(
                    ['nik' => $validated['customer_nik']],
                    [
                        'name'    => $validated['customer_name'],
                        'email'   => $validated['customer_email'] ?? null,
                        'no_tlp'  => $validated['customer_no_tlp'] ?? null,
                        'address' => $validated['customer_address'] ?? null,
                    ]
                );
            }

            return Order::create([
                'user_id'      => auth()->id(),
                'customer_id'  => $customer->id,
                'invoice_num'  => 'INV-' . now()->format('YmdHis') . '-' . rand(100, 999),
                'order_date'   => now(),
                'tax_type'     => 'percent',
                'tax_value'    => 0,
                'tax_amount'   => 0,
                'total_amount' => 0,
                'grand_total'  => 0,
                'status'       => 'pending',
            ]);
        });

        return redirect()
            ->route('orders.items', $order)
            ->with('success', 'Order dibuat. Silakan tambahkan produk.');
    }

    // STEP 2: show add items form
    public function showItems(Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.create'), 403);
        abort_if(
            in_array($order->status, ['pending_approval', 'approved', 'paid', 'cancelled']),
            403,
            'Item tidak dapat diedit pada status saat ini.'
        );
        $order->load(['customer', 'orderDetails.product.category']);
        $products = Product::with('category')->orderBy('name')->get();

        // Menambahkan info stok pada nama produk yang akan dipilih
        $products->each(function ($p) {
            $p->name = $p->name . ' (Stok: ' . $p->stock . ')';
        });

        return view('orders.items', compact('order', 'products'));
    }

    // STEP 2: save items, then submit for HO approval (or suspend to index)
    public function storeItems(Request $request, Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.create'), 403);
        abort_if(
            in_array($order->status, ['pending_approval', 'approved', 'paid', 'cancelled']),
            403,
            'Item tidak dapat diedit pada status saat ini.'
        );
        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
        ], [
            'items.required'              => 'Minimal harus ada 1 item produk yang ditambahkan.',
            'items.*.product_id.required' => 'Produk ke-:position wajib dipilih.',
            'items.*.product_id.exists'   => 'Produk ke-:position tidak valid.',
            'items.*.qty.required'        => 'Kuantitas (Qty) ke-:position wajib diisi.',
            'items.*.qty.integer'         => 'Kuantitas (Qty) ke-:position harus berupa angka bulat.',
            'items.*.qty.min'             => 'Kuantitas (Qty) ke-:position minimal :min.',
        ]);

        // Cek duplikat produk
        $productIds = [];
        $pos = 1;
        foreach ($request->input('items', []) as $item) {
            $pid = $item['product_id'];
            if (isset($productIds[$pid])) {
                $pos1 = $productIds[$pid];
                $pos2 = $pos;
                return back()->withErrors(['items' => "Produk ke $pos1 dan ke $pos2 yang dipilih sama."])->withInput();
            }
            $productIds[$pid] = $pos;
            $pos++;
        }

        // Cek stok produk yang diinput
        if ($request->input('action') !== 'suspend') {
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                if ($product && $product->stock < $item['qty']) {
                    return back()->withErrors(['stock_error' => "Maaf, stok produk {$product->name} saat ini sisa {$product->stock}, tidak mencukupi untuk pesanan sebanyak {$item['qty']}."])->withInput();
                }
            }
        }

        DB::transaction(function () use ($validated, $order) {
            $order->orderDetails()->delete();

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $product  = Product::findOrFail($item['product_id']);
                $qty      = $item['qty'];
                $subtotal = $product->price * $qty;

                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'price'      => $product->price,
                    'qty'        => $qty,
                    'subtotal'   => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $order->update([
                'total_amount' => $totalAmount,
                'tax_amount'   => 0,
                'grand_total'  => $totalAmount,
            ]);
        });

        if ($request->input('action') === 'suspend') {
            return redirect()
                ->route('orders.index')
                ->with('success', 'Order tersimpan sebagai draft.');
        }

        // action = submit → kirim ke Head Office untuk approval
        $order->update([
            'status'         => 'pending_approval',
            'approved_by'    => null,
            'approved_at'    => null,
            'rejection_note' => null,
        ]);

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order berhasil disubmit. Menunggu approval Head Office.');
    }

    // STEP 3: Head Office review & approve/reject
    public function showApproval(Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.approve'), 403);
        abort_unless($order->status === 'pending_approval', 403);

        $order->load(['customer', 'orderDetails.product.category', 'user']);

        return view('orders.approve', compact('order'));
    }

    public function processApproval(Request $request, Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.approve'), 403);
        abort_unless($order->status === 'pending_approval', 403);

        $validated = $request->validate([
            'action'         => 'required|in:approve,reject',
            'rejection_note' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        if ($validated['action'] === 'approve') {
            // Cek ketersediaan stok sebelum disetujui (antisipasi jika stok dihabiskan transaksi lain saat pending)
            foreach ($order->orderDetails as $detail) {
                $product = $detail->product;
                if ($product && $product->stock < $detail->qty) {
                    return back()->withErrors(['stock_error' => "Maaf, order tidak dapat disetujui karena stok produk {$product->name} hanya tersisa {$product->stock}, sedangkan pesanan membutuhkan {$detail->qty}."]);
                }
            }

            $order->update([
                'status'         => 'approved',
                'approved_by'    => auth()->id(),
                'approved_at'    => now(),
                'rejection_note' => null,
            ]);

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order berhasil disetujui.');
        }

        $order->update([
            'status'         => 'rejected',
            'approved_by'    => auth()->id(),
            'approved_at'    => now(),
            'rejection_note' => $validated['rejection_note'],
        ]);

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order telah ditolak.');
    }

    // STEP 4: show payment form (hanya untuk order yang sudah approved)
    public function showPayment(Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.create'), 403);
        abort_unless($order->status === 'approved', 403);
        $order->load(['customer', 'orderDetails.product.category', 'payments']);

        return view('orders.payment', compact('order'));
    }

    // STEP 4: process payment or suspend with saved tax
    public function processPayment(Request $request, Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.create'), 403);
        abort_unless($order->status === 'approved', 403);
        $action = $request->input('action', 'pay');

        $validated = $request->validate([
            'tax_type'       => 'required|in:percent,amount',
            'tax_value'      => 'nullable|numeric|min:0',
            'payment_method' => $action === 'pay' ? 'required|in:cash,transfer,qris' : 'nullable|in:cash,transfer,qris',
            'action'         => 'required|in:pay,suspend',
        ]);

        $taxValue  = (float) ($validated['tax_value'] ?? 0);
        $taxAmount = $this->calculateTaxAmount($order->total_amount, $validated['tax_type'], $taxValue);
        $grandTotal = $order->total_amount + $taxAmount;

        if ($action === 'suspend') {
            $order->update([
                'tax_type'    => $validated['tax_type'],
                'tax_value'   => $taxValue,
                'tax_amount'  => $taxAmount,
                'grand_total' => $grandTotal,
            ]);

            return redirect()
                ->route('orders.index')
                ->with('success', 'Pembayaran ditunda. Order tersimpan.');
        }

        // action = pay
        DB::transaction(function () use ($validated, $order, $taxValue, $taxAmount, $grandTotal) {
            $order->load('orderDetails');

            foreach ($order->orderDetails as $detail) {
                $product = Product::lockForUpdate()->findOrFail($detail->product_id);
                if ($product->stock < $detail->qty) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'stock_error' => "Maaf, pembayaran gagal diproses. Stok produk {$product->name} hanya tersisa {$product->stock}, sedangkan pesanan ini membutuhkan {$detail->qty}."
                    ]);
                }
            }

            $this->decreaseStockFromDetails($order->orderDetails);

            $order->update([
                'tax_type'     => $validated['tax_type'],
                'tax_value'    => $taxValue,
                'tax_amount'   => $taxAmount,
                'grand_total'  => $grandTotal,
                'status'       => 'paid',
            ]);

            $payment = $order->payments()->first();
            $paymentData = [
                'payment_method' => $validated['payment_method'],
                'amount'         => $grandTotal,
                'paid_at'        => now(),
            ];

            if ($payment) {
                $payment->update($paymentData);
            } else {
                Payment::create(array_merge(['order_id' => $order->id], $paymentData));
            }
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Pembayaran berhasil diproses.');
    }

    public function show(Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.view'), 403);

        $order->load([
            'customer',
            'user',
            'orderDetails.product.category',
            'payments',
        ]);

        return view('orders.show', compact('order'));
    }

    public function exportOrderPdf(Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.view'), 403);

        $order->load([
            'customer',
            'user',
            'approvedBy',
            'orderDetails.product.category',
            'payments',
        ]);

        $invoice = str_replace(['/', '\\'], '-', $order->invoice_num);

        return Pdf::loadView('orders.pdf.show', compact('order'))
            ->setPaper('a4')
            ->download('order-' . $invoice . '.pdf');
    }

    public function edit(Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.edit'), 403);

        $order->load(['customer', 'orderDetails.product.category', 'payments']);

        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.edit'), 403);

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        // cegah status tertentu kalau perlu
        if (in_array($oldStatus, ['pending', 'cancelled']) && $newStatus === 'paid') {
            return back()->withErrors([
                'status' => 'Status paid tidak bisa dipilih dari status pending atau cancelled.'
            ])->withInput();
        }

        DB::transaction(function () use ($order, $oldStatus, $newStatus) {
            $order->load('orderDetails', 'payments');

            // kalau sebelumnya paid lalu diubah ke pending/cancelled
            // stok dikembalikan
            if ($oldStatus === 'paid' && in_array($newStatus, ['pending', 'cancelled'])) {
                $this->restoreStockFromDetails($order->orderDetails);
            }

            // kalau sebelumnya bukan paid lalu diubah ke paid
            // stok dikurangi
            if ($oldStatus !== 'paid' && $newStatus === 'paid') {
                foreach ($order->orderDetails as $detail) {
                    $product = Product::lockForUpdate()->findOrFail($detail->product_id);

                    if ($product->stock < $detail->qty) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'stock_error' => "Maaf, status tidak dapat diubah ke Paid. Stok produk {$product->name} hanya tersisa {$product->stock}, tidak mencukupi untuk pesanan sebanyak {$detail->qty}."
                        ]);
                    }
                }

                $this->decreaseStockFromDetails($order->orderDetails);
            }

            $order->update([
                'status' => $newStatus,
            ]);

            $payment = $order->payments()->first();

            if ($newStatus === 'paid') {
                $paymentData = [
                    'payment_method' => $payment->payment_method ?? 'cash',
                    'amount'         => $order->grand_total,
                    'paid_at'        => $payment->paid_at ?? now(),
                ];

                if ($payment) {
                    $payment->update($paymentData);
                } else {
                    Payment::create(array_merge([
                        'order_id' => $order->id,
                    ], $paymentData));
                }
            } else {
                if ($payment) {
                    $payment->update([
                        'amount'  => 0,
                        'paid_at' => null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('orders.index')
            ->with('success', 'Status order berhasil diupdate.');
    }

    public function destroy(Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.delete'), 403);

        DB::transaction(function () use ($order) {
            $order->load('orderDetails');

            if ($order->status === 'paid') {
                $this->restoreStockFromDetails($order->orderDetails);
            }

            $order->delete();
        });

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order berhasil dihapus.');
    }

    private function calculateTaxAmount(float $totalAmount, string $taxType, float $taxValue): float
    {
        if ($taxType === 'percent') {
            return ($totalAmount * $taxValue) / 100;
        }

        return $taxValue;
    }

    private function resolvePeriodRange(Request $request): array
    {
        $activePeriod = $request->input('period', '1d');
        $from = null;
        $to = null;

        if ($request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $activePeriod = 'custom';
        } elseif ($activePeriod === '1d') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($activePeriod === '7d') {
            $from = now()->subDays(6)->startOfDay();
            $to = now()->endOfDay();
        } elseif ($activePeriod === '30d') {
            $from = now()->subDays(29)->startOfDay();
            $to = now()->endOfDay();
        } elseif ($activePeriod === 'this_month') {
            $from = now()->startOfMonth();
            $to = now()->endOfMonth();
        } elseif ($activePeriod === 'this_year') {
            $from = now()->startOfYear();
            $to = now()->endOfYear();
        } else {
            $activePeriod = '1d';
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        }

        return [$from, $to, $activePeriod];
    }

    private function periodLabel(string $period, ?Carbon $from, ?Carbon $to): string
    {
        return match ($period) {
            '1d' => 'Hari Ini',
            '7d' => '7 Hari Terakhir',
            '30d' => '30 Hari Terakhir',
            'this_month' => 'Bulan Ini',
            'this_year' => 'Tahun Ini',
            'custom' => $from && $to ? $from->format('d M Y') . ' - ' . $to->format('d M Y') : 'Custom',
            default => 'Hari Ini',
        };
    }

    private function decreaseStockFromDetails($details): void
    {
        foreach ($details as $detail) {
            $product = Product::lockForUpdate()->find($detail->product_id);
            if ($product) {
                $product->decrement('stock', $detail->qty);
            }
        }
    }

    private function restoreStockFromDetails($details): void
    {
        foreach ($details as $detail) {
            $product = Product::lockForUpdate()->find($detail->product_id);
            if ($product) {
                $product->increment('stock', $detail->qty);
            }
        }
    }
}
