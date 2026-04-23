@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail User</h1>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi User</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Nama</div>
                <div class="col-md-9">{{ $user->name }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Email</div>
                <div class="col-md-9">{{ $user->email }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Role</div>
                <div class="col-md-9">{{ $user->role ? $user->role->name : '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 font-weight-bold">Dibuat pada</div>
                <div class="col-md-9">
                    {{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 font-weight-bold">Diupdate pada</div>
                <div class="col-md-9">
                    {{ $user->updated_at ? $user->updated_at->format('d M Y H:i') : '-' }}
                </div>
            </div>

            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            @if (auth()->id() !== $user->id)
                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus user ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
