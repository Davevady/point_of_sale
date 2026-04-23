@extends('layouts.app')

@section('title', 'Tambah Order')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Order</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
    </div>

    <div class="d-flex align-items-center mb-4 small font-weight-bold">
        <span class="badge badge-primary px-3 py-2">1. Data Customer</span>
        <div class="mx-2" style="height:2px;width:40px;background:#d1d3e2;"></div>
        <span class="badge badge-light px-3 py-2 text-muted">2. Produk</span>
        <div class="mx-2" style="height:2px;width:40px;background:#d1d3e2;"></div>
        <span class="badge badge-light px-3 py-2 text-muted">3. Pembayaran</span>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Customer</h6>
        </div>

        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf

                <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">

                <div class="form-group">
                    <label for="customer_nik">NIK</label>
                    <div class="input-group" style="max-width: 420px;">
                        <input type="text" name="customer_nik" id="customer_nik" class="form-control"
                            value="{{ old('customer_nik') }}" placeholder="Masukkan NIK customer">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btn-find-customer">Cari</button>
                        </div>
                    </div>
                    <small id="customer_lookup_info" class="form-text text-muted">
                        Masukkan NIK lalu klik "Cari".
                    </small>
                    @error('customer_nik')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name">Nama Customer</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control"
                                value="{{ old('customer_name') }}" placeholder="Masukkan nama customer">
                            @error('customer_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_email">Email</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control"
                                value="{{ old('customer_email') }}" placeholder="Masukkan email (opsional)">
                            @error('customer_email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_no_tlp">No. Telepon</label>
                            <input type="text" name="customer_no_tlp" id="customer_no_tlp" class="form-control"
                                value="{{ old('customer_no_tlp') }}" placeholder="Masukkan nomor telepon (opsional)">
                            @error('customer_no_tlp')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_address">Alamat</label>
                            <textarea name="customer_address" id="customer_address" rows="2" class="form-control"
                                placeholder="Masukkan alamat (opsional)">{{ old('customer_address') }}</textarea>
                            @error('customer_address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-2">
                    <button type="submit" class="btn btn-primary">
                        Lanjut: Tambah Produk <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const nikInput             = document.getElementById('customer_nik');
    const findCustomerBtn      = document.getElementById('btn-find-customer');
    const customerIdInput      = document.getElementById('customer_id');
    const customerNameInput    = document.getElementById('customer_name');
    const customerEmailInput   = document.getElementById('customer_email');
    const customerPhoneInput   = document.getElementById('customer_no_tlp');
    const customerAddressInput = document.getElementById('customer_address');
    const lookupInfo           = document.getElementById('customer_lookup_info');

    const customerFields = [customerNameInput, customerEmailInput, customerPhoneInput, customerAddressInput];

    function setFieldsReadonly(readonly) {
        customerFields.forEach(function (field) {
            if (readonly) {
                field.setAttribute('readonly', 'readonly');
                field.classList.add('bg-light');
            } else {
                field.removeAttribute('readonly');
                field.classList.remove('bg-light');
            }
        });
    }

    function clearCustomerFields() {
        customerIdInput.value = '';
        customerNameInput.value = '';
        customerEmailInput.value = '';
        customerPhoneInput.value = '';
        customerAddressInput.value = '';
        setFieldsReadonly(false);
    }

    async function findCustomerByNik() {
        const nik = nikInput.value.trim();

        if (!nik) {
            lookupInfo.innerText = 'NIK wajib diisi dulu.';
            lookupInfo.className = 'form-text text-danger';
            clearCustomerFields();
            nikInput.focus();
            return;
        }

        lookupInfo.innerText = 'Mencari customer...';
        lookupInfo.className = 'form-text text-muted';

        try {
            const response = await fetch(
                `{{ route('customers.find-by-nik') }}?nik=${encodeURIComponent(nik)}`,
                { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } }
            );

            if (!response.ok) throw new Error();

            const result = await response.json();

            if (result.found) {
                customerIdInput.value      = result.customer.id;
                customerNameInput.value    = result.customer.name ?? '';
                customerEmailInput.value   = result.customer.email ?? '';
                customerPhoneInput.value   = result.customer.no_tlp ?? '';
                customerAddressInput.value = result.customer.address ?? '';

                setFieldsReadonly(true);

                lookupInfo.innerHTML = 'Customer ditemukan. <small class="text-success font-weight-bold">Data dari database (readonly)</small>';
                lookupInfo.className = 'form-text text-success';
            } else {
                clearCustomerFields();
                lookupInfo.innerText = 'Customer belum terdaftar. Silakan isi data baru.';
                lookupInfo.className = 'form-text text-warning';
                customerNameInput.focus();
            }
        } catch {
            lookupInfo.innerText = 'Terjadi error saat mencari customer.';
            lookupInfo.className = 'form-text text-danger';
        }
    }

    // Reset readonly when user types new NIK
    nikInput.addEventListener('input', function () {
        if (customerIdInput.value) {
            clearCustomerFields();
            lookupInfo.innerText = 'NIK berubah. Klik "Cari" lagi.';
            lookupInfo.className = 'form-text text-muted';
        }
    });

    findCustomerBtn.addEventListener('click', findCustomerByNik);
    nikInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); findCustomerByNik(); }
    });
</script>
@endpush
