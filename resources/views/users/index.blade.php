@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Users</h1>
            <p class="mb-0 text-muted small">Kelola akun user aplikasi dari halaman ini.</p>
        </div>

        <a href="{{ route('users.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah User
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <x-filter-bar :action="route('users.index')">
        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
        <div class="col-md-4 col-sm-12">
            <x-filter-input name="search" label="Search" placeholder="Cari nama atau email..." :value="request('search')" />
        </div>

        <div class="col-md-3 col-sm-12 mt-3 mt-md-0">
            <x-filter-select name="role" label="Role" placeholder="Semua Role" :options="$roles" :value="request('role')" />
        </div>
    </x-filter-bar>

    <x-data-table title="Daftar User" :paginator="$users" :per-page="$perPage">
        <thead class="thead-light">
            <tr>
                <th width="80">No</th>
                <th>Nama</th>
                <th>
                    <a href="{{ route('roles.index') }}">Role</a>
                </th>
                <th>Email</th>
                <th width="100" class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $users->firstItem() + $loop->index }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->role->name ?? '-' }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">
                        <div class="dropdown position-static">
                            <button class="btn btn-light btn-sm border btn-action-menu" type="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">Detail
                                </a>

                                <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">Edit
                                </a>

                                @if (auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"
                                            onclick="return confirm('Yakin hapus user ini?')">
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
                        Tidak ada user yang cocok dengan pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-data-table>
@endsection
