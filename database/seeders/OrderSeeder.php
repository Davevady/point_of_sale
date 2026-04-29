<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users     = User::all();
        $customers = Customer::all();
        $products  = Product::all();

        if ($users->isEmpty() || $customers->isEmpty() || $products->isEmpty()) {
            $this->command?->warn('Seeder Order dilewati karena users/customers/products belum tersedia.');
            return;
        }

        // ── Fixed seed orders ──────────────────────────────────────────
        $orderSeeds = [
            [
                'user_email'     => 'admin@example.com',
                'customer_nik'   => '3173010101010001',
                'status'         => 'paid',
                'payment_method' => 'cash',
                'items'          => [
                    ['product_name' => 'Laptop Asus Vivobook', 'qty' => 1],
                    ['product_name' => 'Mouse Wireless Logitech', 'qty' => 2],
                ],
                'tax_amount'     => 50000,
            ],
            [
                'user_email'     => 'kasir@example.com',
                'customer_nik'   => '3173010101010002',
                'status'         => 'pending',
                'payment_method' => 'transfer',
                'items'          => [
                    ['product_name' => 'Meja Kantor Minimalis', 'qty' => 1],
                ],
                'tax_amount'     => 25000,
            ],
            [
                'user_email'     => 'owner@example.com',
                'customer_nik'   => '3173010101010003',
                'status'         => 'cancelled',
                'payment_method' => 'qris',
                'items'          => [
                    ['product_name' => 'Tas Laptop', 'qty' => 3],
                ],
                'tax_amount'     => 15000,
            ],
        ];

        foreach ($orderSeeds as $index => $seed) {
            $user     = User::where('email', $seed['user_email'])->first();
            $customer = Customer::where('nik', $seed['customer_nik'])->first();

            if (!$user || !$customer) {
                continue;
            }

            $invoiceNum = 'INV-' . now()->format('Ymd') . '-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'user_id'      => $user->id,
                'customer_id'  => $customer->id,
                'invoice_num'  => $invoiceNum,
                'order_date'   => now()->subDays($index),
                'tax_amount'   => $seed['tax_amount'],
                'total_amount' => 0,
                'grand_total'  => 0,
                'status'       => $seed['status'],
            ]);

            $totalAmount = 0;

            foreach ($seed['items'] as $item) {
                $product = Product::where('name', $item['product_name'])->first();
                if (!$product) continue;

                $price    = $product->price;
                $qty      = $item['qty'];
                $subtotal = $price * $qty;

                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'price'      => $price,
                    'qty'        => $qty,
                    'subtotal'   => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $grandTotal = $totalAmount + $seed['tax_amount'];
            $order->update(['total_amount' => $totalAmount, 'grand_total' => $grandTotal]);

            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => $seed['payment_method'],
                'amount'         => $seed['status'] === 'paid' ? $grandTotal : 0,
                'paid_at'        => $seed['status'] === 'paid' ? now()->subDays($index) : null,
            ]);
        }

        // ── Random orders for the last 30 days ────────────────────────
        $this->command?->info('Membuat random orders selama 30 hari...');

        $statusPool  = ['paid', 'paid', 'paid', 'paid', 'pending', 'pending', 'cancelled'];
        $methods     = ['cash', 'transfer', 'qris'];
        $counter     = 100;

        for ($day = 29; $day >= 0; $day--) {
            $baseDate    = Carbon::now()->subDays($day);
            $ordersToday = rand(3, 8);

            for ($i = 0; $i < $ordersToday; $i++) {
                $user     = $users->random();
                $customer = $customers->random();
                $status   = $statusPool[array_rand($statusPool)];
                $method   = $methods[array_rand($methods)];

                $orderDate = $baseDate->copy()
                    ->setHour(rand(8, 21))
                    ->setMinute(rand(0, 59))
                    ->setSecond(rand(0, 59));

                $numProducts    = rand(1, min(4, $products->count()));
                $pickedProducts = $products->random($numProducts);

                $totalAmount = 0;
                $lineItems   = [];

                foreach ($pickedProducts as $product) {
                    $qty      = rand(1, 5);
                    $price    = $product->price;
                    $subtotal = $price * $qty;
                    $totalAmount += $subtotal;
                    $lineItems[] = compact('product', 'qty', 'price', 'subtotal');
                }

                $taxPercent = collect([0, 0, 5, 10, 11])->random();
                $taxAmount  = (int) round($totalAmount * $taxPercent / 100);
                $grandTotal = $totalAmount + $taxAmount;

                $invoiceNum = 'INV-' . $orderDate->format('YmdHis') . '-' . $counter++;

                $order = Order::create([
                    'user_id'      => $user->id,
                    'customer_id'  => $customer->id,
                    'invoice_num'  => $invoiceNum,
                    'order_date'   => $orderDate,
                    'tax_type'     => 'percent',
                    'tax_value'    => $taxPercent,
                    'tax_amount'   => $taxAmount,
                    'total_amount' => $totalAmount,
                    'grand_total'  => $grandTotal,
                    'status'       => $status,
                ]);

                foreach ($lineItems as $line) {
                    OrderDetail::create([
                        'order_id'   => $order->id,
                        'product_id' => $line['product']->id,
                        'price'      => $line['price'],
                        'qty'        => $line['qty'],
                        'subtotal'   => $line['subtotal'],
                    ]);
                }

                Payment::create([
                    'order_id'       => $order->id,
                    'payment_method' => $method,
                    'amount'         => $status === 'paid' ? $grandTotal : 0,
                    'paid_at'        => $status === 'paid' ? $orderDate : null,
                ]);
            }
        }

        $this->command?->info('Random orders berhasil dibuat.');
    }
}
