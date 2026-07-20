<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Orders</title>
    <style>
        body {
            color: #222;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 4px;
        }

        .muted {
            color: #666;
        }

        .summary {
            margin: 16px 0;
            width: 100%;
        }

        .summary td {
            background: #f3f6f9;
            border: 1px solid #d9e2ec;
            padding: 8px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #d8dde6;
            padding: 7px;
            vertical-align: top;
        }

        th {
            background: #eef2f7;
            font-weight: bold;
            text-align: left;
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
    <h1>Laporan Orders</h1>
    <div class="muted">
        Periode: {{ $periodLabel }}<br>
        @if ($from && $to)
            Tanggal: {{ $from->format('d M Y H:i') }} - {{ $to->format('d M Y H:i') }}<br>
        @endif
        @if ($search)
            Pencarian: {{ $search }}<br>
        @endif
        @if ($status)
            Status: {{ ucfirst(str_replace('_', ' ', $status)) }}<br>
        @endif
        Dicetak: {{ now()->format('d M Y H:i') }}
    </div>

    <table class="summary">
        <tr>
            <td>
                <strong>Total Order</strong><br>
                {{ $orders->count() }}
            </td>
            <td>
                <strong>Total Amount</strong><br>
                Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}
            </td>
            <td>
                <strong>Total Pajak</strong><br>
                Rp {{ number_format($orders->sum('tax_amount'), 0, ',', '.') }}
            </td>
            <td>
                <strong>Grand Total</strong><br>
                Rp {{ number_format($orders->sum('grand_total'), 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 16%;">Invoice</th>
                <th style="width: 18%;">Customer</th>
                <th style="width: 14%;">Kasir</th>
                <th style="width: 14%;">Tanggal</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 8%;" class="text-center">Item</th>
                <th style="width: 16%;" class="text-right">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $order->invoice_num }}</td>
                    <td>{{ $order->customer->name ?? '-' }}</td>
                    <td>{{ $order->user->name ?? '-' }}</td>
                    <td>{{ $order->order_date ? $order->order_date->format('d M Y H:i') : '-' }}</td>
                    <td>{{ $order->status_label }}</td>
                    <td class="text-center">{{ $order->order_details_count }}</td>
                    <td class="text-right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center muted">Tidak ada order untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
