@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Products</h1>
            <p class="mb-0 text-muted small">Kelola data produk dari halaman ini.</p>
        </div>

        <a href="{{ route('products.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Produk
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <x-filter-bar :action="route('products.index')">
        <div class="col-md-4 col-sm-12">
            <x-filter-input
                name="search"
                label="Search"
                placeholder="Cari nama produk..."
                :value="request('search')"
            />
        </div>

        <div class="col-md-3 col-sm-12 mt-3 mt-md-0">
            <x-filter-select
                name="category"
                label="Kategori"
                placeholder="Semua Kategori"
                :options="$categories"
                :value="request('category')"
            />
        </div>
    </x-filter-bar>

    <x-data-table title="Daftar Produk" subtitle="Total produk: {{ $products->count() }}">
        <thead class="thead-light">
            <tr>
                <th width="70">No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stock</th>
                <th width="100" class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->stock }}</td>
                    <td class="text-center">
                        <div class="dropdown position-static">
                            <button class="btn btn-light btn-sm border btn-action-menu" type="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <a class="dropdown-item" href="{{ route('products.show', $product->id) }}">
                                    Detail
                                </a>

                                <a class="dropdown-item" href="{{ route('products.edit', $product->id) }}">
                                    Edit
                                </a>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"
                                        onclick="return confirm('Yakin hapus produk ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Tidak ada produk yang cocok dengan pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-data-table>
@endsection