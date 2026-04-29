<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user->hasPermission('orders.view')) {
            $data['ordersToday'] = Order::where('status', 'paid')
                ->whereDate('order_date', today())
                ->count();

            $data['revenueToday'] = Order::where('status', 'paid')
                ->whereDate('order_date', today())
                ->sum('grand_total');

            $data['ordersThisMonth'] = Order::where('status', 'paid')
                ->whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year)
                ->count();

            $data['revenueThisMonth'] = Order::where('status', 'paid')
                ->whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year)
                ->sum('grand_total');

            $data['ordersPending'] = Order::where('status', 'pending')->count();

            $data['recentOrders'] = Order::with(['customer', 'payments'])
                ->latest()
                ->limit(8)
                ->get();
        }

        if ($user->hasPermission('customers.view')) {
            $data['totalCustomers'] = Customer::count();
        }

        if ($user->hasPermission('products.view')) {
            $data['totalProducts'] = Product::count();
            $data['lowStockProducts'] = Product::with('category')
                ->where('stock', '<=', 10)
                ->orderBy('stock')
                ->limit(8)
                ->get();
            $data['lowStockCount'] = Product::where('stock', '<=', 10)->count();
        }

        if ($user->hasPermission('users.view')) {
            $data['totalUsers'] = User::count();
        }

        return view('dashboard', $data);
    }
}
