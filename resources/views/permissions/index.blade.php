@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Permissions</h1>
            <p class="mb-0 text-muted small">Kelola hak akses aplikasi dari halaman ini.</p>
        </div>

        <a href="{{ route('permissions.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Permission
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <x-filter-bar :action="route('permissions.index')">
        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
        <div class="col-md-4 col-sm-12">
            <x-filter-input
                name="search"
                label="Search"
                placeholder="Cari nama atau label..."
                :value="request('search')"
            />
        </div>

        <div class="col-md-3 col-sm-12 mt-3 mt-md-0">
            <div class="form-group mb-0">
                <label class="small text-muted font-weight-bold">Group</label>
                <select name="group" class="form-control">
                    <option value="">Semua Group</option>
                    @foreach ($groups as $g)
                        <option value="{{ $g }}" {{ request('group') == $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-filter-bar>

    <x-data-table title="Daftar Permissions" :paginator="$permissions" :per-page="$perPage">
        <thead class="thead-light">
            <tr>
                <th width="80">No</th>
                <th>Permission Key</th>
                <th>Label</th>
                <th>Group</th>
                <th width="100" class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($permissions as $permission)
                <tr>
                    <td>{{ $permissions->firstItem() + $loop->index }}</td>
                    <td>
                        <code class="text-primary">{{ $permission->name }}</code>
                    </td>
                    <td>{{ $permission->label ?? '-' }}</td>
                    <td>
                        @if ($permission->group)
                            <span class="badge badge-light border">{{ $permission->group }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="dropdown position-static">
                            <button class="btn btn-light btn-sm border btn-action-menu" type="button"
                                data-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <a class="dropdown-item" href="{{ route('permissions.edit', $permission->id) }}">
                                    Edit
                                </a>

                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"
                                        onclick="return confirm('Yakin hapus permission \'{{ $permission->name }}\'?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Tidak ada permission yang cocok dengan pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-data-table>
@endsection
