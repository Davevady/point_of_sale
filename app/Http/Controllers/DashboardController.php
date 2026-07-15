<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
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
            $data['ordersCount'] = Order::where('status', 'paid')
                ->whereHas('payments', fn ($query) => $query->whereBetween('paid_at', [$from, $to]))
                ->count();

            $data['revenueTotal'] = (float) Payment::whereHas(
                'order',
                fn ($query) => $query->where('status', 'paid')
            )->whereBetween('paid_at', [$from, $to])->sum('amount');

            $data['ordersPending']  = Order::where('status', 'pending')
                ->whereBetween('order_date', [$from, $to])
                ->count();

            $data['ordersAwaitingApproval'] = Order::where('status', 'pending_approval')
                ->whereBetween('order_date', [$from, $to])
                ->count();

            $data['ordersApproved'] = Order::where('status', 'approved')
                ->whereBetween('order_date', [$from, $to])
                ->count();

            $data['ordersRejected'] = Order::where('status', 'rejected')
                ->whereBetween('order_date', [$from, $to])
                ->count();

            $data['ordersCancelled'] = Order::where('status', 'cancelled')
                ->whereBetween('order_date', [$from, $to])
                ->count();

            // Chart data – daily breakdown of all statuses in period
            $data['chartData'] = $this->buildChartData($from, $to);

            // Recent orders – latest 8, not date-filtered
            $data['recentOrders'] = Order::with(['customer', 'payments'])
                ->orderByDesc('order_date')
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
                ->whereHas('payments', fn ($query) => $query->whereBetween('paid_at', [$from, $to]))
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

        $labels = [];
        $statuses = [
            'paid'             => ['label' => 'Paid', 'color' => '#1cc88a'],
            'pending'          => ['label' => 'Pending', 'color' => '#f6c23e'],
            'pending_approval' => ['label' => 'Menunggu Approval', 'color' => '#36b9cc'],
            'approved'         => ['label' => 'Disetujui', 'color' => '#4e73df'],
            'rejected'         => ['label' => 'Ditolak', 'color' => '#e74a3b'],
            'cancelled'        => ['label' => 'Dibatalkan', 'color' => '#858796'],
        ];
        $series = array_fill_keys(array_keys($statuses), []);

        $cursor = $from->copy()->startOfDay();
        $end    = $to->copy()->startOfDay();

        while ($cursor <= $end) {
            $date      = $cursor->format('Y-m-d');
            $dayRows   = $rows->get($date, collect());

            $labels[] = $cursor->format('d M');
            foreach (array_keys($statuses) as $status) {
                $series[$status][] = (int) ($dayRows->firstWhere('status', $status)['count'] ?? 0);
            }

            $cursor->addDay();
        }

        return compact('labels', 'statuses', 'series');
    }
}
