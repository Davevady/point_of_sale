@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
    <style>
        .already-selected {
            display: none !important;
        }
    </style>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Produk</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali ke Daftar</a>
    </div>

    <div class="d-flex align-items-center mb-4 small font-weight-bold text-gray-600">
        <span class="badge badge-success px-3 py-2">1. Data Order <i class="fas fa-check ml-1"></i></span>
        <div class="mx-2" style="height:2px;width:40px;background:#1cc88a;"></div>
        <span class="badge badge-primary px-3 py-2">2. Produk</span>
        <div class="mx-2" style="height:2px;width:40px;background:#d1d3e2;"></div>
        <span class="badge badge-light px-3 py-2 text-muted">3. Approval HO</span>
        <div class="mx-2" style="height:2px;width:40px;background:#d1d3e2;"></div>
        <span class="badge badge-light px-3 py-2 text-muted">4. Pembayaran</span>
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
            @if ($errors->has('stock_error'))
                <div class="mt-3 pt-3 border-top border-danger">
                    <p class="mb-2 font-weight-bold">Tindakan Alternatif:</p>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.querySelector('button[value=\'suspend\']').click()">
                        <i class="fas fa-pause mr-1"></i> Simpan & Tunda
                    </button>
                    @if(auth()->user()->hasPermission('orders.edit'))
                        <form action="{{ route('orders.update', $order) }}" method="POST" class="d-inline ml-1">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin membatalkan order ini?')">
                                <i class="fas fa-times mr-1"></i> Batalkan Order
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Order</h6>
                </div>
                <div class="card-body small">
                    <div class="mb-2">
                        <span class="text-muted">Invoice</span><br>
                        <strong>{{ $order->invoice_num }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Customer</span><br>
                        <strong>{{ $order->customer->name }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Tanggal</span><br>
                        <strong>{{ $order->order_date->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Pajak</span><br>
                        <strong>
                            @if ($order->tax_type === 'percent')
                                {{ number_format($order->tax_value, 2) }}%
                            @else
                                Rp {{ number_format($order->tax_value, 0, ',', '.') }}
                            @endif
                        </strong>
                    </div>
                    <div class="mb-0">
                        <span class="text-muted">Status</span><br>
                        <span class="badge badge-warning">{{ ucfirst($order->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Item Produk</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.storeItems', $order) }}" method="POST">
                        @csrf

                        <div id="order-items">
                            @php $oldItems = old('items'); @endphp

                            @if ($oldItems)
                                @foreach ($oldItems as $index => $item)
                                    <div class="row order-item mb-2">
                                        <div class="col-md-8">
                                            <label>Produk <span class="text-danger">*</span></label>
                                            <x-searchable-product-select
                                                name="items[{{ $index }}][product_id]"
                                                placeholder="Pilih Produk"
                                                :options="$products"
                                                :value="$item['product_id'] ?? null"
                                            />
                                        </div>
                                        <div class="col-md-3">
                                            <label>Qty <span class="text-danger">*</span></label>
                                            <input type="number" name="items[{{ $index }}][qty]"
                                                class="form-control" min="1" value="{{ $item['qty'] ?? 1 }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif ($order->orderDetails->count())
                                @foreach ($order->orderDetails as $index => $detail)
                                    <div class="row order-item mb-2">
                                        <div class="col-md-8">
                                            <label>Produk <span class="text-danger">*</span></label>
                                            <x-searchable-product-select
                                                name="items[{{ $index }}][product_id]"
                                                placeholder="Pilih Produk"
                                                :options="$products"
                                                :value="$detail->product_id"
                                            />
                                        </div>
                                        <div class="col-md-3">
                                            <label>Qty <span class="text-danger">*</span></label>
                                            <input type="number" name="items[{{ $index }}][qty]"
                                                class="form-control" min="1" value="{{ $detail->qty }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row order-item mb-2">
                                    <div class="col-md-8">
                                        <label>Produk <span class="text-danger">*</span></label>
                                        <x-searchable-product-select
                                            name="items[0][product_id]"
                                            placeholder="Pilih Produk"
                                            :options="$products"
                                            :value="null"
                                        />
                                    </div>
                                    <div class="col-md-4">
                                        <label>Qty <span class="text-danger">*</span></label>
                                        <input type="number" name="items[0][qty]" class="form-control" min="1" value="1">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <button type="button" class="btn btn-sm btn-info mb-3" id="add-item">+ Tambah Item</button>

                        <div class="d-flex justify-content-between">
                            <button type="submit" name="action" value="suspend" class="btn btn-secondary">
                                <i class="fas fa-pause mr-1"></i> Simpan & Tunda
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-1"></i> Submit untuk Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let itemIndex = {{ old('items') ? count(old('items')) : max(1, $order->orderDetails->count()) }};

    @php
        $productOptionsHtml = '';
        foreach ($products as $p) {
            $label = $p->name . ' - ' . ($p->category->name ?? '-') . ' - Rp ' . number_format($p->price, 0, ',', '.');
            $productOptionsHtml .= '<li class="px-3 py-2 searchable-select-opt" data-value="' . $p->id . '" data-label="' . htmlspecialchars($label, ENT_QUOTES) . '">' . htmlspecialchars($label, ENT_QUOTES) . '</li>';
        }
    @endphp
    const productOptionsHtml = `{!! addslashes($productOptionsHtml) !!}`;

    document.getElementById('add-item')?.addEventListener('click', function () {
        const container = document.getElementById('order-items');
        const uid = 'sps_dyn_' + Date.now();

        const html = `
        <div class="row order-item mb-2">
            <div class="col-md-8">
                <label>Produk <span class="text-danger">*</span></label>
                <div class="form-group mb-0">
                    <div class="searchable-select-wrapper" id="${uid}" style="position:relative;">
                        <input type="hidden" name="items[${itemIndex}][product_id]" id="${uid}_val" value="">
                        <div class="form-control d-flex justify-content-between align-items-center searchable-select-trigger" id="${uid}_trigger">
                            <span id="${uid}_display" class="text-muted">Pilih Produk</span>
                            <i class="fas fa-chevron-down fa-xs text-muted ml-2" style="flex-shrink:0;"></i>
                        </div>
                        <div class="searchable-select-dropdown border rounded bg-white shadow-sm" id="${uid}_dropdown" style="display:none;position:absolute;z-index:1050;width:100%;top:calc(100% + 2px);left:0;">
                            <div class="p-2 border-bottom">
                                <input type="text" class="form-control form-control-sm" id="${uid}_search" placeholder="Cari...">
                            </div>
                            <ul class="list-unstyled mb-0" id="${uid}_list" style="max-height:220px;overflow-y:auto;">
                                <li class="px-3 py-2 searchable-select-opt text-muted" data-value="" data-label="Pilih Produk">Pilih Produk</li>
                                ${productOptionsHtml}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label>Qty <span class="text-danger">*</span></label>
                <input type="number" name="items[${itemIndex}][qty]" class="form-control" min="1" value="1">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
        window.initSearchableSelect(uid);
        itemIndex++;

        updateProductOptions();
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            const items = document.querySelectorAll('.order-item');
            if (items.length > 1) {
                e.target.closest('.order-item').remove();
                updateProductOptions();
            }
        }

        if (e.target.closest('.searchable-select-opt')) {
            setTimeout(updateProductOptions, 50);
        }
    });

    function updateProductOptions() {
        const selectedIds = Array.from(document.querySelectorAll('input[name^="items"][name$="[product_id]"]'))
            .map(input => input.value)
            .filter(val => val !== '');
            
        document.querySelectorAll('.searchable-select-wrapper').forEach(wrapper => {
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const currentVal = hiddenInput ? hiddenInput.value : '';
            
            wrapper.querySelectorAll('.searchable-select-opt').forEach(opt => {
                const val = opt.dataset.value;
                if (!val) return;
                
                if (selectedIds.includes(val) && val !== currentVal) {
                    opt.classList.add('already-selected');
                } else {
                    opt.classList.remove('already-selected');
                }
            });
        });
    }

    setTimeout(updateProductOptions, 100);
</script>
@endpush
