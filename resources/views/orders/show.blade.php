@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Order</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            Kembali
        </a>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Order</h6>
        </div>

        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3 font-weight-bold">Invoice</div>
                <div class="col-md-9">{{ $order->invoice_num }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 font-weight-bold">Customer</div>
                <div class="col-md-9">{{ $order->customer->name ?? '-' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 font-weight-bold">Kasir</div>
                <div class="col-md-9">{{ $order->user->name ?? '-' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 font-weight-bold">Tanggal Order</div>
                <div class="col-md-9">{{ $order->order_date ? $order->order_date->format('d M Y H:i') : '-' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 font-weight-bold">Status</div>
                <div class="col-md-9">{{ ucfirst($order->status) }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 font-weight-bold">Total Amount</div>
                <div class="col-md-9">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 font-weight-bold">Tax</div>
                <div class="col-md-9">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 font-weight-bold">Grand Total</div>
                <div class="col-md-9">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
            </div>

            <hr>

            <h6 class="font-weight-bold text-primary mb-3">Detail Produk</h6>

            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $detail)
                            <tr>
                                <td>{{ $detail->product->name ?? '-' }}</td>
                                <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td>{{ $detail->qty }}</td>
                                <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h6 class="font-weight-bold text-primary mb-3">Payment</h6>

            @forelse ($order->payments as $payment)
                <div class="row mb-2">
                    <div class="col-md-3 font-weight-bold">Method</div>
                    <div class="col-md-9">{{ strtoupper($payment->payment_method) }}</div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3 font-weight-bold">Amount</div>
                    <div class="col-md-9">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3 font-weight-bold">Paid At</div>
                    <div class="col-md-9">{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : '-' }}</div>
                </div>
            @empty
                <p class="text-muted">Belum ada data payment.</p>
            @endforelse
        </div>
    </div>
@endsection