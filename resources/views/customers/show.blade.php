@extends('layouts.app')

@section('title', 'Detail Customer')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Customer</h1>
        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            Kembali
        </a>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Customer</h6>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">ID</div>
                <div class="col-md-9">{{ $customer->id }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Nama</div>
                <div class="col-md-9">{{ $customer->name }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">NIK</div>
                <div class="col-md-9">{{ $customer->nik }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Email</div>
                <div class="col-md-9">{{ $customer->email ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">No. Telepon</div>
                <div class="col-md-9">{{ $customer->no_tlp ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Alamat</div>
                <div class="col-md-9">{{ $customer->address ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Dokumen KK</div>
                <div class="col-md-9">
                    @if ($customer->doc_kk)
                        <a href="{{ asset('storage/' . $customer->doc_kk) }}" target="_blank" class="text-primary">
                            Lihat Dokumen
                        </a>
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 font-weight-bold">Dibuat pada</div>
                <div class="col-md-9">
                    {{ $customer->created_at ? $customer->created_at->format('d M Y H:i') : '-' }}
                </div>
            </div>

            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>
@endsection