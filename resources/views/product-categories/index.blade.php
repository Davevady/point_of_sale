@extends('layouts.app')

@section('title', 'Product Categories')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Product Categories</h1>
            <p class="mb-0 text-muted small">Kelola kategori produk dari halaman ini.</p>
        </div>

        @if (auth()->user()->hasPermission('categories.create'))
            <a href="{{ route('product-categories.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kategori
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <x-filter-bar :action="route('product-categories.index')">
        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
        <div class="col-md-4 col-sm-12">
            <x-filter-input name="search" label="Search" placeholder="Cari nama atau deskripsi kategori..."
                :value="request('search')" />
        </div>
    </x-filter-bar>

    <x-data-table title="Daftar Kategori Produk" :paginator="$productCategories" :per-page="$perPage">
        <thead class="thead-light">
            <tr>
                <th width="70">No</th>
                <th>Nama</th>
                <th>Total Produk</th>
                <th width="100" class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($productCategories as $productCategory)
                <tr>
                    <td>{{ $productCategories->firstItem() + $loop->index }}</td>
                    <td>{{ $productCategory->name }}</td>
                    <td>
                        <a href="{{ route('products.index', ['category' => $productCategory->id]) }}"
                            class="font-weight-bold text-primary">
                            {{ $productCategory->products_count }}
                        </a>
                    </td>
                    <td class="text-center">
                        <div class="dropdown position-static">
                            <button class="btn btn-light btn-sm border btn-action-menu" type="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <a class="dropdown-item"
                                    href="{{ route('product-categories.show', $productCategory->id) }}">
                                    Detail
                                </a>

                                @if (auth()->user()->hasPermission('categories.edit'))
                                    <a class="dropdown-item"
                                        href="{{ route('product-categories.edit', $productCategory->id) }}">
                                        Edit
                                    </a>
                                @endif

                                @if (auth()->user()->hasPermission('categories.delete'))
                                    <form action="{{ route('product-categories.destroy', $productCategory->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"
                                            onclick="return confirm('Yakin hapus kategori ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        Tidak ada kategori yang cocok dengan pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-data-table>
@endsection
