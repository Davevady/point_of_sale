<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order {{ $order->invoice_num }}</title>
    <style>
        body {
            color: #222;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 4px;
        }

        h2 {
            border-bottom: 1px solid #d8dde6;
            color: #2f5597;
            font-size: 14px;
            margin: 22px 0 10px;
            padding-bottom: 5px;
        }

        .muted {
            color: #666;
        }

        .header {
            border-bottom: 2px solid #2f5597;
            margin-bottom: 16px;
            padding-bottom: 12px;
        }

        .info-table,
        .items-table,
        .totals-table {
            border-collapse: collapse;
            width: 100%;
        }

        .info-table td {
            padding: 4px 0;
        }

        .label {
            color: #555;
            font-weight: bold;
            width: 150px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #d8dde6;
            padding: 8px;
        }

        .items-table th {
            background: #eef2f7;
            text-align: left;
        }

        .totals-table {
            margin-left: auto;
            margin-top: 12px;
            width: 320px;
        }

        .totals-table td {
            padding: 6px 8px;
        }

        .grand-total td {
            border-top: 2px solid #2f5597;
            font-size: 14px;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detail Order</h1>
        <div class="muted">Dicetak: {{ now()->format('d M Y H:i') }}</div>
    </div>

    <h2>Informasi Order</h2>
    <table class="info-table">
        <tr>
            <td class="label">Invoice</td>
            <td>{{ $order->invoice_num }}</td>
        </tr>
        <tr>
            <td class="label">Customer</td>
            <td>{{ $order->customer->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kasir</td>
            <td>{{ $order->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Order</td>
            <td>{{ $order->order_date ? $order->order_date->format('d M Y H:i') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td>{{ $order->status_label }}</td>
        </tr>
        @if ($order->approved_at)
            <tr>
                <td class="label">{{ $order->status === 'rejected' ? 'Ditolak oleh' : 'Disetujui oleh' }}</td>
                <td>{{ $order->approvedBy->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $order->status === 'rejected' ? 'Waktu Penolakan' : 'Waktu Approval' }}</td>
                <td>{{ $order->approved_at->format('d M Y H:i') }}</td>
            </tr>
            @if ($order->rejection_note)
                <tr>
                    <td class="label">Catatan Penolakan</td>
                    <td>{{ $order->rejection_note }}</td>
                </tr>
            @endif
        @endif
    </table>

    <h2>Detail Produk</h2>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th>Produk</th>
                <th style="width: 18%;" class="text-right">Harga</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 20%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->orderDetails as $detail)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $detail->product->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $detail->qty }}</td>
                    <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center muted">Belum ada produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Total Amount</td>
            <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tax</td>
            <td class="text-right">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total">
            <td>Grand Total</td>
            <td class="text-right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h2>Payment</h2>
    @forelse ($order->payments as $payment)
        <table class="info-table">
            <tr>
                <td class="label">Method</td>
                <td>{{ strtoupper($payment->payment_method) }}</td>
            </tr>
            <tr>
                <td class="label">Amount</td>
                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Paid At</td>
                <td>{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : '-' }}</td>
            </tr>
        </table>
    @empty
        <p class="muted">Belum ada data payment.</p>
    @endforelse
</body>
</html>
