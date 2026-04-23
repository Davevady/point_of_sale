@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Produk</h1>
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            Kembali
        </a>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Produk</h6>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">ID</div>
                <div class="col-md-9">{{ $product->id }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Nama Produk</div>
                <div class="col-md-9">{{ $product->name }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Kategori</div>
                <div class="col-md-9">{{ $product->category->name ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Harga</div>
                <div class="col-md-9">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 font-weight-bold">Stock</div>
                <div class="col-md-9">{{ $product->stock }}</div>
            </div>

            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>
@endsection