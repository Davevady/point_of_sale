@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Role</h1>
    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary shadow-sm">
        Kembali
    </a>
</div>

<div class="card shadow mb-4 border-0">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Edit Role</h6>
    </div>

    <div class="card-body">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Role</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $role->name) }}"
                    placeholder="Contoh: Admin"
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-warning">Update</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection