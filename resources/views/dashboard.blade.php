@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════
     HEADING + GLOBAL DATE FILTER
══════════════════════════════════════════════════════════════════ --}}
<div class="d-sm-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <p class="mb-0 text-muted small">
            Selamat datang, <strong>{{ auth()->user()->name }}</strong>
            @if(auth()->user()->role)
                &mdash; {{ auth()->user()->role->name }}
            @endif
        </p>
    </div>

    {{-- ── Filter bar ──────────────────────────────────────────── --}}
    <div class="d-flex align-items-center flex-wrap" style="gap:6px;">

        {{-- Period toggle buttons --}}
        @foreach(['1d' => '1D', '7d' => '7D', '30d' => '30D'] as $key => $label)
            <a href="{{ route('dashboard', ['period' => $key]) }}"
               class="btn btn-sm {{ $activePeriod === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $label }}
            </a>
        @endforeach

        {{-- Custom date range --}}
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center" style="gap:4px;" id="customRangeForm">
            <input type="date" name="from" class="form-control form-control-sm" style="width:140px;"
                   value="{{ $activePeriod === 'custom' ? $from->format('Y-m-d') : '' }}"
                   id="inputFrom" placeholder="Dari">
            <span class="text-muted small">→</span>
            <input type="date" name="to" class="form-control form-control-sm" style="width:140px;"
                   value="{{ $activePeriod === 'custom' ? $to->format('Y-m-d') : '' }}"
                   id="inputTo" placeholder="Sampai">
            <button type="submit" class="btn btn-sm {{ $activePeriod === 'custom' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-sync-alt fa-sm"></i>
            </button>
        </form>
    </div>
</div>

{{-- Period label badge --}}
<p class="text-muted small mb-3">
    Menampilkan data: <span class="font-weight-bold text-primary">{{ $periodLabel }}</span>
</p>

{{-- ══════════════════════════════════════════════════════════════════
     ROW 1 – SUMMARY CARDS (period-filtered)
══════════════════════════════════════════════════════════════════ --}}
@isset($ordersCount)
<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Orders Paid</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ordersCount }}</div>
                        <div class="text-xs text-muted">{{ $periodLabel }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp {{ number_format($revenueTotal, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-muted">{{ $periodLabel }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-money-bill-wave fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Orders Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ordersPending }}</div>
                        <div class="text-xs text-muted">{{ $periodLabel }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Orders Cancelled</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ordersCancelled }}</div>
                        <div class="text-xs text-muted">{{ $periodLabel }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-times-circle fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endisset

{{-- ROW 2 – TOTAL CARDS (not date-filtered) --}}
@php
    $showRow2 = isset($totalCustomers) || isset($totalProducts) || isset($totalUsers) || isset($lowStockCount);
@endphp

@if($showRow2)
<div class="row">

    @isset($totalCustomers)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Customers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCustomers }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-user-friends fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    @endisset

    @isset($totalProducts)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Produk</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-box fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    @endisset

    @isset($totalUsers)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    @endisset

    @isset($lowStockCount)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stok Rendah</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockCount }}</div>
                        <div class="text-xs text-muted">Produk ≤ 10 pcs</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    @endisset

</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     CHART – Order Trend (Paid / Pending / Cancelled)
══════════════════════════════════════════════════════════════════ --}}
@isset($chartData)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-1"></i> Tren Order &mdash; {{ $periodLabel }}
                </h6>
                <div class="d-flex" style="gap:12px; font-size:12px;">
                    <span><span class="d-inline-block rounded-circle mr-1" style="width:10px;height:10px;background:#1cc88a;"></span>Paid</span>
                    <span><span class="d-inline-block rounded-circle mr-1" style="width:10px;height:10px;background:#f6c23e;"></span>Pending</span>
                    <span><span class="d-inline-block rounded-circle mr-1" style="width:10px;height:10px;background:#e74a3b;"></span>Cancelled</span>
                </div>
            </div>
            <div class="card-body">
                <div style="position:relative; height:260px;">
                    <canvas id="orderChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endisset

{{-- ══════════════════════════════════════════════════════════════════
     ROW 3 – 3 TABLES SIDE BY SIDE
══════════════════════════════════════════════════════════════════ --}}
@php
    $hasTable = isset($recentOrders) || isset($lowStockProducts) || isset($topCustomers);
@endphp

@if($hasTable)
<div class="row">

    {{-- ── Recent Orders ──────────────────────────────────────── --}}
    @isset($recentOrders)
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card shadow border-0 h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Order Terbaru</h6>
                <a href="{{ route('orders.index') }}" class="btn btn-xs btn-outline-primary btn-sm py-0">
                    Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="small">Invoice</th>
                                <th class="small">Customer</th>
                                <th class="small">Total</th>
                                <th class="small">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="small">
                                        <a href="{{ route('orders.show', $order->id) }}" class="font-weight-bold text-primary">
                                            {{ $order->invoice_num }}
                                        </a>
                                    </td>
                                    <td class="small">{{ Str::limit($order->customer->name ?? '-', 12) }}</td>
                                    <td class="small">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($order->status === 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($order->status === 'pending')
                                            <span class="badge badge-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge badge-danger">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4 small">
                                        Belum ada order.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endisset

    {{-- ── Stok Rendah ─────────────────────────────────────────── --}}
    @isset($lowStockProducts)
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card shadow border-0 h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Stok Rendah
                </h6>
                @if($lowStockCount > 0)
                    <span class="badge badge-danger">{{ $lowStockCount }}</span>
                @endif
            </div>
            <div class="card-body p-0">
                @if($lowStockProducts->isEmpty())
                    <div class="text-center text-muted py-5 small">
                        <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                        Semua stok aman.
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($lowStockProducts as $product)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                <div>
                                    <div class="font-weight-bold small">{{ $product->name }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $product->category->name ?? '-' }}</div>
                                </div>
                                <span class="badge {{ $product->stock === 0 ? 'badge-danger' : 'badge-warning text-dark' }}">
                                    {{ $product->stock }} pcs
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    @if($lowStockCount > 8)
                        <div class="text-center py-2">
                            <a href="{{ route('products.index') }}" class="small text-muted">
                                +{{ $lowStockCount - 8 }} produk lainnya →
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endisset

    {{-- ── Top Customers ───────────────────────────────────────── --}}
    @isset($topCustomers)
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card shadow border-0 h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-trophy mr-1"></i> Customer Terbanyak Order
                </h6>
                <span class="text-muted small">{{ $periodLabel }}</span>
            </div>
            <div class="card-body p-0">
                @if($topCustomers->isEmpty())
                    <div class="text-center text-muted py-5 small">
                        Belum ada transaksi di periode ini.
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($topCustomers as $i => $row)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-bold text-muted mr-2" style="width:18px; font-size:12px;">
                                        {{ $i + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-weight-bold small">
                                            {{ $row->customer->name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-muted" style="font-size:11px;">
                                            Rp {{ number_format($row->total_spent, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge badge-success">{{ $row->order_count }} order</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    @endisset

</div>
@endif

{{-- Fallback jika tidak ada permission sama sekali --}}
@php
    $hasAnyData = isset($ordersCount) || isset($totalCustomers) || isset($totalProducts) || isset($totalUsers);
@endphp

@if(!$hasAnyData)
<div class="row">
    <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-lock fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Akses Terbatas</h5>
                <p class="text-muted mb-0">Akun Anda belum memiliki permission untuk melihat data dashboard.</p>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@isset($chartData)
<script>
(function () {
    const chartData = @json($chartData);

    const ctx = document.getElementById('orderChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Paid',
                    data: chartData.paid,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28,200,138,0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'Pending',
                    data: chartData.pending,
                    borderColor: '#f6c23e',
                    backgroundColor: 'rgba(246,194,62,0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'Cancelled',
                    data: chartData.cancelled,
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231,74,59,0.05)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.35,
                    fill: false,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function(items) {
                            return items[0].label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
})();
</script>
@endisset

<script>
    // Auto-submit custom range when both dates are filled
    const inputFrom = document.getElementById('inputFrom');
    const inputTo   = document.getElementById('inputTo');

    if (inputFrom && inputTo) {
        [inputFrom, inputTo].forEach(function(el) {
            el.addEventListener('change', function() {
                if (inputFrom.value && inputTo.value && inputFrom.value <= inputTo.value) {
                    document.getElementById('customRangeForm').submit();
                }
            });
        });
    }
</script>
@endpush
