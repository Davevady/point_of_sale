@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <p class="mb-0 text-muted small">Selamat datang, <strong>{{ auth()->user()->name }}</strong>
            @if(auth()->user()->role)
                — {{ auth()->user()->role->name }}
            @endif
        </p>
    </div>
    <span class="text-muted small">{{ now()->translatedFormat('l, d F Y') }}</span>
</div>

{{-- ======================== STAT CARDS ======================== --}}
@isset($ordersToday)
<div class="row">

    {{-- Orders Hari Ini --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Orders Hari Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ordersToday }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pendapatan Hari Ini --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Pendapatan Hari Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp {{ number_format($revenueToday, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders Bulan Ini --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Orders Bulan Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ordersThisMonth }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pendapatan Bulan Ini --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pendapatan Bulan Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endisset

{{-- Row kedua: Customers, Products, Users, Orders Pending --}}
@php
    $showRow2 = isset($totalCustomers) || isset($totalProducts) || isset($totalUsers) || isset($ordersPending);
@endphp

@if($showRow2)
<div class="row">

    @isset($totalCustomers)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Customers
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCustomers }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                    </div>
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
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Produk
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endisset

    @isset($totalUsers)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endisset

    @isset($ordersPending)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Orders Pending
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ordersPending }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endisset

</div>
@endif

{{-- ======================== TABEL BAWAH ======================== --}}
<div class="row">

    {{-- Recent Orders --}}
    @isset($recentOrders)
    <div class="{{ isset($lowStockProducts) ? 'col-xl-8' : 'col-12' }} mb-4">
        <div class="card shadow border-0">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Order Terbaru</h6>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" class="font-weight-bold text-primary">
                                            {{ $order->invoice_num }}
                                        </a>
                                    </td>
                                    <td>{{ $order->customer->name ?? '-' }}</td>
                                    <td>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($order->status === 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($order->status === 'pending')
                                            <span class="badge badge-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge badge-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">
                                        {{ $order->created_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endisset

    {{-- Low Stock Products --}}
    @isset($lowStockProducts)
    <div class="{{ isset($recentOrders) ? 'col-xl-4' : 'col-12' }} mb-4">
        <div class="card shadow border-0">
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
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="mb-0">Semua stok aman.</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($lowStockProducts as $product)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                <div>
                                    <div class="font-weight-bold small">{{ $product->name }}</div>
                                    <div class="text-muted small">{{ $product->category->name ?? '-' }}</div>
                                </div>
                                <span class="badge {{ $product->stock === 0 ? 'badge-danger' : 'badge-warning text-dark' }} ml-2">
                                    {{ $product->stock }} pcs
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    @if($lowStockCount > 8)
                        <div class="text-center py-2">
                            <a href="{{ route('products.index') }}" class="small text-muted">
                                +{{ $lowStockCount - 8 }} produk lainnya
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endisset

</div>

{{-- Fallback: tidak ada data apapun yang tampil --}}
@php
    $hasAnyData = isset($ordersToday) || isset($totalCustomers) || isset($totalProducts) || isset($totalUsers);
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
