@extends('layouts.app')

@section('title', 'Review Approval Order')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Review Approval Order</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali ke Daftar</a>
    </div>

    <div class="d-flex align-items-center mb-4 small font-weight-bold text-gray-600">
        <span class="badge badge-success px-3 py-2">1. Data Order <i class="fas fa-check ml-1"></i></span>
        <div class="mx-2" style="height:2px;width:40px;background:#1cc88a;"></div>
        <span class="badge badge-success px-3 py-2">2. Produk <i class="fas fa-check ml-1"></i></span>
        <div class="mx-2" style="height:2px;width:40px;background:#1cc88a;"></div>
        <span class="badge badge-primary px-3 py-2">3. Approval HO</span>
        <div class="mx-2" style="height:2px;width:40px;background:#d1d3e2;"></div>
        <span class="badge badge-light px-3 py-2 text-muted">4. Pembayaran</span>
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

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Order</h6>
                    <small class="text-muted">{{ $order->invoice_num }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="px-4 py-3 border-bottom small bg-light">
                        <div class="row">
                            <div class="col-6">
                                <span class="text-muted">Customer</span><br>
                                <strong>{{ $order->customer->name }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Dibuat oleh</span><br>
                                <strong>{{ $order->user->name ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <span class="text-muted">Tanggal Order</span><br>
                                <strong>{{ $order->order_date->format('d M Y H:i') }}</strong>
                            </div>
                        </div>
                    </div>

                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-center" width="60">Qty</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails as $detail)
                                <tr>
                                    <td>{{ $detail->product->name ?? '-' }}</td>
                                    <td>{{ $detail->product->category->name ?? '-' }}</td>
                                    <td class="text-center">{{ $detail->qty }}</td>
                                    <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Keputusan Approval</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.processApproval', $order) }}" method="POST">
                        @csrf

                        <div class="mb-3 p-3 bg-light rounded border">
                            <div class="small text-muted mb-1">Total Order</div>
                            <div class="h5 font-weight-bold text-primary mb-0">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small">Catatan Penolakan</label>
                            <textarea name="rejection_note" class="form-control @error('rejection_note') is-invalid @enderror"
                                rows="3" placeholder="Wajib diisi jika menolak order..."
                                id="rejection_note">{{ old('rejection_note') }}</textarea>
                            @error('rejection_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Hanya diperlukan saat menolak order.</small>
                        </div>

                        <button type="submit" name="action" value="approve"
                            class="btn btn-success btn-block mb-2"
                            onclick="return confirm('Setujui order ini?')">
                            <i class="fas fa-check mr-1"></i> Setujui Order
                        </button>

                        <button type="submit" name="action" value="reject"
                            class="btn btn-danger btn-block"
                            onclick="return confirmReject()">
                            <i class="fas fa-times mr-1"></i> Tolak Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function confirmReject() {
        const note = document.getElementById('rejection_note').value.trim();
        if (!note) {
            alert('Harap isi catatan penolakan terlebih dahulu.');
            document.getElementById('rejection_note').focus();
            return false;
        }
        return confirm('Tolak order ini? Pastikan catatan penolakan sudah benar.');
    }
</script>
@endpush
