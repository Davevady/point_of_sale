@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pembayaran</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="d-flex align-items-center mb-4 small font-weight-bold">
        <span class="badge badge-success px-3 py-2">1. Data Order <i class="fas fa-check ml-1"></i></span>
        <div class="mx-2" style="height:2px;width:40px;background:#1cc88a;"></div>
        <span class="badge badge-success px-3 py-2">2. Produk <i class="fas fa-check ml-1"></i></span>
        <div class="mx-2" style="height:2px;width:40px;background:#1cc88a;"></div>
        <span class="badge badge-success px-3 py-2">3. Approval HO <i class="fas fa-check ml-1"></i></span>
        <div class="mx-2" style="height:2px;width:40px;background:#1cc88a;"></div>
        <span class="badge badge-primary px-3 py-2">4. Pembayaran</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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

    <form action="{{ route('orders.processPayment', $order) }}" method="POST" id="payment-form">
        @csrf

        <div class="row">
            {{-- Kiri: Ringkasan + Pajak --}}
            <div class="col-md-7 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Ringkasan Order</h6>
                        <small class="text-muted">{{ $order->invoice_num }}</small>
                    </div>

                    <div class="card-body p-0">
                        {{-- Info customer --}}
                        <div class="px-4 py-3 border-bottom small bg-light">
                            <div class="row">
                                <div class="col-6">
                                    <span class="text-muted">Customer</span><br>
                                    <strong>{{ $order->customer->name }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted">Tanggal</span><br>
                                    <strong>{{ $order->order_date->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel produk --}}
                        <table class="table table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center" width="60">Qty</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $detail)
                                    <tr>
                                        <td>{{ $detail->product->name ?? '-' }}</td>
                                        <td class="text-center">{{ $detail->qty }}</td>
                                        <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Pajak & Total --}}
                        <div class="px-4 py-3 border-top">
                            <div class="row align-items-end mb-3">
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-muted mb-1">Tipe Pajak <span class="text-danger">*</span></label>
                                    <select name="tax_type" id="tax_type" class="form-control form-control-sm">
                                        <option value="percent" {{ old('tax_type', $order->tax_type) === 'percent' ? 'selected' : '' }}>Persen (%)</option>
                                        <option value="amount" {{ old('tax_type', $order->tax_type) === 'amount' ? 'selected' : '' }}>Nominal (Rp)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-muted mb-1" id="tax_value_label">Nilai Pajak</label>
                                    <input type="number" name="tax_value" id="tax_value"
                                        class="form-control form-control-sm"
                                        value="{{ old('tax_value', $order->tax_value) }}"
                                        min="0" step="0.01" placeholder="0">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Subtotal Produk</span>
                                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted" id="tax-label-display">Pajak</span>
                                <span id="tax-amount-display">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between font-weight-bold border-top pt-2 mt-1">
                                <span>Grand Total</span>
                                <span class="text-primary h5 mb-0" id="grand-total-display">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Metode & Kalkulator --}}
            <div class="col-md-5 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Metode Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        @php $payment = $order->payments->first(); @endphp

                        <div class="form-group">
                            <label for="payment_method">Metode <span class="text-danger">*</span></label>
                            <select name="payment_method" id="payment_method" class="form-control">
                                <option value="cash" {{ old('payment_method', optional($payment)->payment_method ?? 'cash') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ old('payment_method', optional($payment)->payment_method) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="qris" {{ old('payment_method', optional($payment)->payment_method) == 'qris' ? 'selected' : '' }}>QRIS</option>
                            </select>
                            @error('payment_method')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kalkulator Cash --}}
                        <div id="cash-calculator" class="border rounded p-3 bg-light mb-3">
                            <p class="font-weight-bold small text-muted mb-2">
                                <i class="fas fa-calculator mr-1"></i> Kalkulator Kembalian
                            </p>

                            <div class="form-group mb-2">
                                <label class="small">Grand Total</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control font-weight-bold text-primary"
                                        id="grand-total-calc" readonly>
                                </div>
                            </div>

                            <div class="form-group mb-2">
                                <label class="small">Uang Diterima</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" id="amount_received" class="form-control"
                                        placeholder="0" min="0">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="small font-weight-bold">Kembalian</span>
                                <span id="change-display" class="font-weight-bold h6 mb-0 text-muted">—</span>
                            </div>
                        </div>

                        {{-- Grand Total besar --}}
                        <div class="text-center border rounded p-3 mb-3 bg-light">
                            <div class="small text-muted mb-1">Total yang harus dibayar</div>
                            <div class="h4 font-weight-bold text-success mb-0" id="grand-total-big">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </div>
                        </div>

                        <button type="submit" name="action" value="pay" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-check mr-1"></i> Proses Pembayaran
                        </button>
                        <button type="submit" name="action" value="suspend" class="btn btn-outline-secondary btn-block btn-sm">
                            <i class="fas fa-pause mr-1"></i> Tunda Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const totalAmount    = {{ $order->total_amount }};
    const taxTypeSelect  = document.getElementById('tax_type');
    const taxValueInput  = document.getElementById('tax_value');
    const taxLabelDisp   = document.getElementById('tax-label-display');
    const taxAmountDisp  = document.getElementById('tax-amount-display');
    const grandTotalDisp = document.getElementById('grand-total-display');
    const grandTotalCalc = document.getElementById('grand-total-calc');
    const grandTotalBig  = document.getElementById('grand-total-big');
    const taxValueLabel  = document.getElementById('tax_value_label');

    const paymentMethod  = document.getElementById('payment_method');
    const cashCalc       = document.getElementById('cash-calculator');
    const amountReceived = document.getElementById('amount_received');
    const changeDisplay  = document.getElementById('change-display');

    let currentGrandTotal = totalAmount;

    function formatRp(value) {
        return 'Rp ' + Math.round(value).toLocaleString('id-ID');
    }

    function recalcTax() {
        const type  = taxTypeSelect.value;
        const value = parseFloat(taxValueInput.value) || 0;

        let taxAmount = 0;
        if (type === 'percent') {
            taxAmount = (totalAmount * value) / 100;
            taxLabelDisp.textContent  = 'Pajak (' + value + '%)';
            taxValueLabel.textContent = 'Nilai Pajak (%)';
        } else {
            taxAmount = value;
            taxLabelDisp.textContent  = 'Pajak (Nominal)';
            taxValueLabel.textContent = 'Nilai Pajak (Rp)';
        }

        currentGrandTotal = totalAmount + taxAmount;

        taxAmountDisp.textContent  = formatRp(taxAmount);
        grandTotalDisp.textContent = formatRp(currentGrandTotal);
        grandTotalBig.textContent  = formatRp(currentGrandTotal);
        grandTotalCalc.value       = Math.round(currentGrandTotal).toLocaleString('id-ID');

        recalcChange();
    }

    function recalcChange() {
        const received = parseFloat(amountReceived.value) || 0;
        if (received === 0) {
            changeDisplay.textContent = '—';
            changeDisplay.className   = 'font-weight-bold h6 mb-0 text-muted';
            return;
        }

        const change = received - currentGrandTotal;
        changeDisplay.textContent = formatRp(Math.abs(change));

        if (change >= 0) {
            changeDisplay.className = 'font-weight-bold h6 mb-0 text-success';
        } else {
            changeDisplay.textContent = '- ' + formatRp(Math.abs(change));
            changeDisplay.className   = 'font-weight-bold h6 mb-0 text-danger';
        }
    }

    function toggleCashCalc() {
        cashCalc.style.display = paymentMethod.value === 'cash' ? 'block' : 'none';
    }

    taxTypeSelect.addEventListener('change', recalcTax);
    taxValueInput.addEventListener('input', recalcTax);
    paymentMethod.addEventListener('change', toggleCashCalc);
    amountReceived.addEventListener('input', recalcChange);

    // Init
    recalcTax();
    toggleCashCalc();
</script>
@endpush
