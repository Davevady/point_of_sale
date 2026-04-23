@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Customers</h1>
            <p class="mb-0 text-muted small">Kelola data customer dari halaman ini.</p>
        </div>

        <a href="{{ route('customers.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Customer
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <x-filter-bar :action="route('customers.index')">
        <div class="col-md-4 col-sm-12">
            <x-filter-input
                name="search"
                label="Search"
                placeholder="Cari nama, email, atau NIK..."
                :value="request('search')"
            />
        </div>
    </x-filter-bar>

    <x-data-table title="Daftar Customer" subtitle="Total customer: {{ $customers->count() }}">
        <thead class="thead-light">
            <tr>
                <th width="70">No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Email</th>
                <th>No. Tlp</th>
                <th>Dokumen KK</th>
                <th width="100" class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($customers as $index => $customer)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->nik }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td>{{ $customer->no_tlp ?? '-' }}</td>
                    <td>
                        @if ($customer->doc_kk)
                            <a href="{{ asset('storage/' . $customer->doc_kk) }}" target="_blank" class="text-primary">
                                Lihat Dokumen
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="dropdown position-static">
                            <button class="btn btn-light btn-sm border btn-action-menu" type="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <a class="dropdown-item" href="{{ route('customers.show', $customer->id) }}">
                                    Detail
                                </a>

                                <a class="dropdown-item" href="{{ route('customers.edit', $customer->id) }}">
                                    Edit
                                </a>

                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"
                                        onclick="return confirm('Yakin hapus customer ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Tidak ada customer yang cocok dengan pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-data-table>
@endsection