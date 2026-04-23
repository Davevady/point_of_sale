<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $customers = Customer::all();
        $products = Product::all();

        if ($users->isEmpty() || $customers->isEmpty() || $products->isEmpty()) {
            $this->command?->warn('Seeder Order dilewati karena users/customers/products belum tersedia.');
            return;
        }

        $orderSeeds = [
            [
                'user_email' => 'admin@example.com',
                'customer_nik' => '3173010101010001',
                'status' => 'paid',
                'payment_method' => 'cash',
                'items' => [
                    ['product_name' => 'Laptop Asus Vivobook', 'qty' => 1],
                    ['product_name' => 'Mouse Wireless Logitech', 'qty' => 2],
                ],
                'tax_amount' => 50000,
            ],
            [
                'user_email' => 'kasir@example.com',
                'customer_nik' => '3173010101010002',
                'status' => 'pending',
                'payment_method' => 'transfer',
                'items' => [
                    ['product_name' => 'Meja Kantor Minimalis', 'qty' => 1],
                ],
                'tax_amount' => 25000,
            ],
            [
                'user_email' => 'owner@example.com',
                'customer_nik' => '3173010101010003',
                'status' => 'cancelled',
                'payment_method' => 'qris',
                'items' => [
                    ['product_name' => 'Tas Laptop', 'qty' => 3],
                ],
                'tax_amount' => 15000,
            ],
        ];

        foreach ($orderSeeds as $index => $seed) {
            $user = User::where('email', $seed['user_email'])->first();
            $customer = Customer::where('nik', $seed['customer_nik'])->first();

            if (!$user || !$customer) {
                continue;
            }

            $invoiceNum = 'INV-' . now()->format('Ymd') . '-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'invoice_num' => $invoiceNum,
                'order_date' => now()->subDays($index),
                'tax_amount' => $seed['tax_amount'],
                'total_amount' => 0,
                'grand_total' => 0,
                'status' => $seed['status'],
            ]);

            $totalAmount = 0;

            foreach ($seed['items'] as $item) {
                $product = Product::where('name', $item['product_name'])->first();

                if (!$product) {
                    continue;
                }

                $price = $product->price;
                $qty = $item['qty'];
                $subtotal = $price * $qty;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $price,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $grandTotal = $totalAmount + $seed['tax_amount'];

            $order->update([
                'total_amount' => $totalAmount,
                'grand_total' => $grandTotal,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $seed['payment_method'],
                'amount' => $seed['status'] === 'paid' ? $grandTotal : 0,
                'paid_at' => $seed['status'] === 'paid' ? now()->subDays($index) : null,
            ]);
        }
    }
}