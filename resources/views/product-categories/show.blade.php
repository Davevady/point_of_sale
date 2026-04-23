@extends('layouts.app')

@section('title', 'Detail Kategori Produk')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Kategori Produk</h1>
        <a href="{{ route('product-categories.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            Kembali
        </a>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Kategori Produk</h6>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Nama Kategori</div>
                <div class="col-md-9">{{ $productCategory->name }}</div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 font-weight-bold">Dibuat pada</div>
                <div class="col-md-9">
                    {{ $productCategory->created_at ? $productCategory->created_at->format('d M Y H:i') : '-' }}
                </div>
            </div>

            <a href="{{ route('product-categories.edit', $productCategory->id) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>
@endsection