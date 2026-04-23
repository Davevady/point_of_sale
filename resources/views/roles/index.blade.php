@extends('layouts.app')

@section('title', 'Roles')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Roles</h1>
            <p class="mb-0 text-muted small">Kelola role user aplikasi dari halaman ini.</p>
        </div>

        <a href="{{ route('roles.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Role
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

    <x-filter-bar :action="route('roles.index')">
        <div class="col-md-4 col-sm-12">
            <x-filter-input name="search" label="Search" placeholder="Cari nama role..." :value="request('search')" />
        </div>
    </x-filter-bar>

    <x-data-table title="Daftar Role" subtitle="Total role: {{ $roles->count() }}">
        <thead class="thead-light">
            <tr>
                <th width="80">No</th>
                <th>Nama Role</th>
                <th>Total User</th>
                <th width="100" class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($roles as $index => $role)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        <a href="{{ route('users.index', ['role' => $role->id]) }}" class="font-weight-bold text-primary">
                            {{ $role->users()->count() }}
                        </a>
                    </td>
                    <td class="text-center">
                        <div class="dropdown position-static">
                            <button class="btn btn-light btn-sm border btn-action-menu" type="button"
                                data-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <a class="dropdown-item" href="{{ route('roles.show', $role->id) }}">
                                    Detail
                                </a>

                                <a class="dropdown-item" href="{{ route('roles.edit', $role->id) }}">
                                    Edit
                                </a>

                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"
                                        onclick="return confirm('Yakin hapus role ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        Tidak ada role yang cocok dengan pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-data-table>
@endsection
