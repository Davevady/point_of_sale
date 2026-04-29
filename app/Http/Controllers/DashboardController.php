<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        [$from, $to, $activePeriod] = $this->resolveDateRange($request);

        $periodLabel = match($activePeriod) {
            '1d'     => 'Hari Ini',
            '30d'    => '30 Hari Terakhir',
            'custom' => $from->format('d M Y') . ' – ' . $to->format('d M Y'),
            default  => '7 Hari Terakhir',
        };

        $data = [
            'from'        => $from,
            'to'          => $to,
            'activePeriod' => $activePeriod,
            'periodLabel' => $periodLabel,
        ];

        if ($user->hasPermission('orders.view')) {
            // Summary cards – all filtered by period
            $data['ordersCount']    = Order::where('status', 'paid')
                ->whereBetween('order_date', [$from, $to])
                ->count();

            $data['revenueTotal']   = (float) Order::where('status', 'paid')
                ->whereBetween('order_date', [$from, $to])
                ->sum('grand_total');

            $data['ordersPending']  = Order::where('status', 'pending')
                ->whereBetween('order_date', [$from, $to])
                ->count();

            $data['ordersCancelled'] = Order::where('status', 'cancelled')
                ->whereBetween('order_date', [$from, $to])
                ->count();

            // Chart data – daily breakdown of all statuses in period
            $data['chartData'] = $this->buildChartData($from, $to);

            // Recent orders – latest 8, not date-filtered
            $data['recentOrders'] = Order::with(['customer', 'payments'])
                ->latest()
                ->limit(8)
                ->get();
        }

        if ($user->hasPermission('customers.view')) {
            $data['totalCustomers'] = Customer::count();

            // Top customers by paid order count in period
            $data['topCustomers'] = Order::select('customer_id')
                ->selectRaw('COUNT(*) as order_count')
                ->selectRaw('SUM(grand_total) as total_spent')
                ->with('customer')
                ->where('status', 'paid')
                ->whereBetween('order_date', [$from, $to])
                ->groupBy('customer_id')
                ->orderByDesc('order_count')
                ->limit(8)
                ->get();
        }

        if ($user->hasPermission('products.view')) {
            $data['totalProducts']   = Product::count();
            $data['lowStockProducts'] = Product::with('category')
                ->where('stock', '<=', 10)
                ->orderBy('stock')
                ->limit(8)
                ->get();
            $data['lowStockCount']   = Product::where('stock', '<=', 10)->count();
        }

        if ($user->hasPermission('users.view')) {
            $data['totalUsers'] = User::count();
        }

        return view('dashboard', $data);
    }

    private function resolveDateRange(Request $request): array
    {
        $period = $request->input('period', '7d');

        if ($request->filled('from') && $request->filled('to')) {
            $from         = Carbon::parse($request->input('from'))->startOfDay();
            $to           = Carbon::parse($request->input('to'))->endOfDay();
            $activePeriod = 'custom';
        } else {
            $to   = now()->endOfDay();
            $from = match($period) {
                '1d'  => now()->startOfDay(),
                '30d' => now()->subDays(29)->startOfDay(),
                default => now()->subDays(6)->startOfDay(), // 7d
            };
            $activePeriod = $period;
        }

        return [$from, $to, $activePeriod];
    }

    private function buildChartData(Carbon $from, Carbon $to): array
    {
        $rows = Order::selectRaw('DATE(order_date) as date, status, COUNT(*) as count')
            ->whereBetween('order_date', [$from, $to])
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $labels    = [];
        $paid      = [];
        $pending   = [];
        $cancelled = [];

        $cursor = $from->copy()->startOfDay();
        $end    = $to->copy()->startOfDay();

        while ($cursor <= $end) {
            $date      = $cursor->format('Y-m-d');
            $dayRows   = $rows->get($date, collect());

            $labels[]    = $cursor->format('d M');
            $paid[]      = (int) ($dayRows->firstWhere('status', 'paid')['count'] ?? 0);
            $pending[]   = (int) ($dayRows->firstWhere('status', 'pending')['count'] ?? 0);
            $cancelled[] = (int) ($dayRows->firstWhere('status', 'cancelled')['count'] ?? 0);

            $cursor->addDay();
        }

        return compact('labels', 'paid', 'pending', 'cancelled');
    }
}
