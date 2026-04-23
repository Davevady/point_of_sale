@extends('layouts.app')

@section('title', 'Tambah Permission')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Permission</h1>
        <a href="{{ route('permissions.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Permission</h6>
        </div>

        <div class="card-body">
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Permission Key <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="Contoh: orders.view">
                            <small class="form-text text-muted">Format: <code>module.aksi</code> (huruf kecil, titik sebagai pemisah)</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="label">Label</label>
                            <input type="text" name="label" id="label"
                                class="form-control @error('label') is-invalid @enderror"
                                value="{{ old('label') }}"
                                placeholder="Contoh: Lihat Order">
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="group">Group</label>
                            <input type="text" name="group" id="group"
                                class="form-control @error('group') is-invalid @enderror"
                                value="{{ old('group') }}"
                                placeholder="Contoh: Orders"
                                list="group-suggestions">
                            <datalist id="group-suggestions">
                                @foreach ($groups as $g)
                                    <option value="{{ $g }}">
                                @endforeach
                            </datalist>
                            <small class="form-text text-muted">Nama modul/grup untuk pengelompokan.</small>
                            @error('group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
