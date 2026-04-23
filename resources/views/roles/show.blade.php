@extends('layouts.app')

@section('title', 'Detail Role')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Role</h1>
    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary shadow-sm">
        Kembali
    </a>
</div>

<div class="card shadow mb-4 border-0">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informasi Role</h6>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">ID</div>
            <div class="col-md-9">{{ $role->id }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Nama Role</div>
            <div class="col-md-9">{{ $role->name }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Total User</div>
            <div class="col-md-9">{{ $role->users()->count() }}</div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 font-weight-bold">Dibuat pada</div>
            <div class="col-md-9">
                {{ $role->created_at ? $role->created_at->format('d M Y H:i') : '-' }}
            </div>
        </div>

        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">Edit</a>
    </div>
</div>
@endsection