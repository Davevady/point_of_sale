<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('orders.view'), 403);

        $search = $request->search;
        $status = $request->status;

        $orders = Order::with(['customer', 'user', 'payments'])
            ->withCount('orderDetails')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_num', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();

        return view('orders.index', compact('orders', 'search', 'status'));
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
        $order->load(['customer', 'orderDetails.product.category']);
        $products = Product::with('category')->orderBy('name')->get();

        return view('orders.items', compact('order', 'products'));
    }

    // STEP 2: save items, redirect to payment (or suspend to index)
    public function storeItems(Request $request, Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.create'), 403);
        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

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

        return redirect()
            ->route('orders.payment', $order)
            ->with('success', 'Item produk berhasil disimpan.');
    }

    // STEP 3: show payment form
    public function showPayment(Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.create'), 403);
        $order->load(['customer', 'orderDetails.product.category', 'payments']);

        return view('orders.payment', compact('order'));
    }

    // STEP 3: process payment or suspend with saved tax
    public function processPayment(Request $request, Order $order)
    {
        abort_unless(auth()->user()->hasPermission('orders.create'), 403);
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
                    abort(422, "Stock produk {$product->name} tidak mencukupi.");
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
                        abort(422, "Stock produk {$product->name} tidak mencukupi.");
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
