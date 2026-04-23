@extends('layouts.app')

@section('title', 'Edit Status Order')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Status Order</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $payment = $order->payments->first();
        $currentStatus = old('status', $order->status);
    @endphp

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Status Order</h6>
        </div>

        <div class="card-body">
            <form action="{{ route('orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Customer</label>
                            <input type="text" class="form-control" value="{{ $order->customer->name ?? '-' }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Order</label>
                            <input type="text" class="form-control"
                                value="{{ optional($order->order_date)->format('d-m-Y H:i') }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>

                                @if (!in_array($order->status, ['pending', 'cancelled']))
                                    <option value="paid" {{ $currentStatus == 'paid' ? 'selected' : '' }}>
                                        Paid
                                    </option>
                                @endif

                                <option value="cancelled" {{ $currentStatus == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <input type="text" class="form-control"
                                value="{{ optional($payment)->payment_method ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipe Pajak</label>
                            <input type="text" class="form-control" value="{{ $order->tax_type }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nilai Pajak</label>
                            <input type="text" class="form-control" value="{{ $order->tax_value }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Waktu Pembayaran</label>
                            <input type="text" class="form-control"
                                value="{{ optional(optional($payment)->paid_at)->format('d-m-Y H:i') ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="font-weight-bold text-primary mb-3">Item Produk</h6>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->orderDetails as $detail)
                                <tr>
                                    <td>{{ $detail->product->name ?? '-' }}</td>
                                    <td>{{ $detail->product->category->name ?? '-' }}</td>
                                    <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td>{{ $detail->qty }}</td>
                                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada item</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Pajak</th>
                                <th>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Grand Total</th>
                                <th>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div>
                    <button type="submit" class="btn btn-warning">Update Status</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection