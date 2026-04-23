@extends('layouts.app')

@section('title', 'Orders')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Orders</h1>
            <p class="mb-0 text-muted small">Kelola transaksi order dari halaman ini.</p>
        </div>

        <a href="{{ route('orders.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Order
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <x-filter-bar :action="route('orders.index')">
        <div class="col-md-4 col-sm-12">
            <x-filter-input
                name="search"
                label="Search"
                placeholder="Cari invoice atau customer..."
                :value="request('search')"
            />
        </div>

        <div class="col-md-3 col-sm-12 mt-3 mt-md-0">
            <div class="form-group mb-0">
                <label for="status" class="small text-muted font-weight-bold">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>
    </x-filter-bar>

    <x-data-table title="Daftar Orders" subtitle="Total order: {{ $orders->count() }}">
        <thead class="thead-light">
            <tr>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Kasir</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Grand Total</th>
                <th width="160" class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->invoice_num }}</td>
                    <td>{{ $order->customer->name ?? '-' }}</td>
                    <td>{{ $order->user->name ?? '-' }}</td>
                    <td>{{ $order->order_date ? $order->order_date->format('d M Y H:i') : '-' }}</td>
                    <td>
                        <span class="badge
                            {{ $order->status === 'paid' ? 'badge-success' : '' }}
                            {{ $order->status === 'pending' ? 'badge-warning' : '' }}
                            {{ $order->status === 'cancelled' ? 'badge-danger' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1" style="gap: 4px;">
                            @if ($order->status === 'pending')
                                @if ($order->order_details_count === 0)
                                    <a href="{{ route('orders.items', $order->id) }}"
                                        class="btn btn-warning btn-sm font-weight-bold"
                                        title="Lanjutkan: Tambah Produk">
                                        <i class="fas fa-play fa-xs mr-1"></i> Lanjutkan
                                    </a>
                                @else
                                    <a href="{{ route('orders.payment', $order->id) }}"
                                        class="btn btn-warning btn-sm font-weight-bold"
                                        title="Lanjutkan: Pembayaran">
                                        <i class="fas fa-play fa-xs mr-1"></i> Lanjutkan
                                    </a>
                                @endif
                            @endif

                            <div class="dropdown position-static">
                                <button class="btn btn-light btn-sm border btn-action-menu" type="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                    <a class="dropdown-item" href="{{ route('orders.show', $order->id) }}">
                                        Detail
                                    </a>
                                    <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}">
                                        Edit
                                    </a>
                                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"
                                            onclick="return confirm('Yakin hapus order ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Tidak ada order yang cocok dengan pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-data-table>
@endsection