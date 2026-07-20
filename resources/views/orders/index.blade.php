@extends('layouts.app')

@section('title', 'Orders')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Orders</h1>
            <p class="mb-0 text-muted small">Kelola transaksi order dari halaman ini.</p>
        </div>
        <div class="d-flex align-items-center" style="gap: 6px;">
            <div class="dropdown">
                <button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    @foreach (['1d' => '1D', '7d' => '7D', '30d' => '30D', 'this_month' => 'This Month', 'this_year' => 'This Year'] as $key => $label)
                        <a class="dropdown-item"
                            href="{{ route('orders.exportPdf', array_merge(request()->only(['search', 'status']), ['period' => $key])) }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if (auth()->user()->hasPermission('orders.create'))
                <a href="{{ route('orders.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Order
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('orders.index') }}" id="filterForm">
        <input type="hidden" name="period" id="periodInput" value="{{ $activePeriod ?? '7d' }}">

        <div class="card shadow border-0 mb-4">
            <div class="card-body py-3">
                <div class="row align-items-end">

                    {{-- Search --}}
                    <div class="col-md-3 col-sm-12 mb-2">
                        <label class="small text-muted font-weight-bold d-block mb-1">Cari</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Invoice atau customer..." value="{{ $search }}">
                    </div>

                    {{-- Status --}}
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="small text-muted font-weight-bold d-block mb-1">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Semua</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="pending_approval" {{ $status == 'pending_approval' ? 'selected' : '' }}>Menunggu
                                Approval</option>
                            <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Disetujui
                            </option>
                            <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>
                    </div>

                    {{-- Period quick-select --}}
                    <div class="col-auto mb-2">
                        <label class="small text-muted font-weight-bold d-block mb-1">Periode</label>
                        <div class="btn-group btn-group-sm" role="group">
                            @foreach (['1d' => '1D', '7d' => '7D', '30d' => '30D', 'this_month' => 'This Month', 'this_year' => 'This Year'] as $key => $label)
                                <button type="button"
                                    class="btn {{ ($activePeriod ?? '7d') === $key ? 'btn-primary' : 'btn-outline-secondary' }} period-btn"
                                    data-period="{{ $key }}">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Custom date range --}}
                    <div class="col-auto mb-2">
                        <label class="small text-muted font-weight-bold d-block mb-1">Rentang Tanggal</label>
                        <div class="d-flex align-items-center" style="gap:4px;">
                            <input type="date" name="from" id="inputFrom" class="form-control form-control-sm"
                                style="width:135px;"
                                value="{{ $from ? $from->format('Y-m-d') : '' }}">
                            <span class="text-muted small">-&gt;</span>
                            <input type="date" name="to" id="inputTo" class="form-control form-control-sm"
                                style="width:135px;"
                                value="{{ $to ? $to->format('Y-m-d') : '' }}">
                        </div>
                    </div>

                    {{-- Submit & Reset --}}
                    <div class="col-auto mb-2">
                        <label class="d-block mb-1" style="visibility:hidden; font-size:12px;">.</label>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search fa-xs mr-1"></i> Filter
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm ml-1">Reset</a>
                    </div>

                    {{-- per_page disimpan agar tetap saat form disubmit --}}
                    <input type="hidden" name="per_page" value="{{ $perPage }}">

                </div>

                {{-- Active period label --}}
                @if ($activePeriod)
                    <div class="mt-2">
                        <span class="text-muted small">
                            Filter aktif:
                            @if ($activePeriod === '1d')
                                <span class="badge badge-info">Hari ini</span>
                            @elseif($activePeriod === '7d')
                                <span class="badge badge-info">7 Hari Terakhir</span>
                            @elseif($activePeriod === '30d')
                                <span class="badge badge-info">30 Hari Terakhir</span>
                            @elseif($activePeriod === 'this_month')
                                <span class="badge badge-info">Bulan Ini</span>
                            @elseif($activePeriod === 'this_year')
                                <span class="badge badge-info">Tahun Ini</span>
                            @elseif($activePeriod === 'custom' && $from && $to)
                                <span class="badge badge-info">
                                    {{ $from->format('d M Y') }} - {{ $to->format('d M Y') }}
                                </span>
                            @endif
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <x-data-table title="Daftar Orders" :paginator="$orders" :per-page="$perPage">
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
                        <span class="badge {{ $order->status_badge }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center" style="gap: 4px;">
                            @if ($order->status === 'pending' && auth()->user()->hasPermission('orders.create'))
                                <a href="{{ route('orders.items', $order->id) }}"
                                    class="btn btn-warning btn-sm font-weight-bold" title="Lanjutkan: Tambah/Edit Produk">
                                    <i class="fas fa-play fa-xs mr-1"></i> Lanjutkan
                                </a>
                            @elseif ($order->status === 'rejected' && auth()->user()->hasPermission('orders.create'))
                                <a href="{{ route('orders.items', $order->id) }}"
                                    class="btn btn-warning btn-sm font-weight-bold" title="Edit & Resubmit">
                                    <i class="fas fa-redo fa-xs mr-1"></i> Edit & Resubmit
                                </a>
                            @elseif ($order->status === 'pending_approval' && auth()->user()->hasPermission('orders.approve'))
                                <a href="{{ route('orders.approve', $order->id) }}"
                                    class="btn btn-info btn-sm font-weight-bold" title="Review Approval">
                                    <i class="fas fa-clipboard-check fa-xs mr-1"></i> Review
                                </a>
                            @elseif ($order->status === 'approved' && auth()->user()->hasPermission('orders.create'))
                                <a href="{{ route('orders.payment', $order->id) }}"
                                    class="btn btn-success btn-sm font-weight-bold" title="Lanjutkan: Pembayaran">
                                    <i class="fas fa-cash-register fa-xs mr-1"></i> Bayar
                                </a>
                            @endif

                            <div class="dropdown position-static">
                                <button class="btn btn-light btn-sm border btn-action-menu" type="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                    <a class="dropdown-item" href="{{ route('orders.show', $order->id) }}">Detail</a>
                                    <a class="dropdown-item" href="{{ route('orders.exportOrderPdf', $order->id) }}">Export PDF</a>
                                    @if (auth()->user()->hasPermission('orders.edit'))
                                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}">Edit</a>
                                    @endif
                                    @if (auth()->user()->hasPermission('orders.delete'))
                                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"
                                                onclick="return confirm('Yakin hapus order ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Tidak ada order yang cocok dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-data-table>

@endsection

@push('scripts')
    <script>
        // Period quick-select buttons
        document.querySelectorAll('.period-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('periodInput').value = this.dataset.period;
                document.getElementById('inputFrom').value = '';
                document.getElementById('inputTo').value = '';
                document.getElementById('filterForm').submit();
            });
        });

        // Custom date range: auto-submit when both dates are valid
        ['inputFrom', 'inputTo'].forEach(function(id) {
            document.getElementById(id).addEventListener('change', function() {
                const from = document.getElementById('inputFrom').value;
                const to = document.getElementById('inputTo').value;
                if (from && to && from <= to) {
                    document.getElementById('periodInput').value = '';
                    document.getElementById('filterForm').submit();
                }
            });
        });
    </script>
@endpush
