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
                <label for="name">Nama Role <span class="text-danger">*</span></label>
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

            <div class="form-group">
                <label class="font-weight-bold">Permissions</label>

                @if($permissions->isEmpty())
                    <p class="text-muted small">Belum ada permission yang tersedia.</p>
                @else
                    @foreach($permissions as $group => $groupPerms)
                        <div class="card mb-2 border">
                            <div class="card-header py-2 bg-light">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input group-check"
                                        id="group_{{ Str::slug($group) }}"
                                        data-group="{{ Str::slug($group) }}"
                                    >
                                    <label class="custom-control-label font-weight-bold" for="group_{{ Str::slug($group) }}">
                                        {{ $group }}
                                    </label>
                                </div>
                            </div>
                            <div class="card-body py-2">
                                <div class="row">
                                    @foreach($groupPerms as $perm)
                                        <div class="col-md-3 col-sm-6">
                                            <div class="custom-control custom-checkbox mb-1">
                                                <input
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $perm->id }}"
                                                    class="custom-control-input perm-check perm-group-{{ Str::slug($group) }}"
                                                    id="perm_{{ $perm->id }}"
                                                    {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}
                                                >
                                                <label class="custom-control-label" for="perm_{{ $perm->id }}">
                                                    {{ $perm->label ?: $perm->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @error('permissions')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-warning">Update</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Sync group checkbox dengan children-nya
    document.querySelectorAll('.group-check').forEach(function (groupBox) {
        var group = groupBox.dataset.group;
        var children = document.querySelectorAll('.perm-group-' + group);

        // Set state awal group checkbox
        var checkedCount = Array.from(children).filter(c => c.checked).length;
        groupBox.checked = checkedCount === children.length && children.length > 0;
        groupBox.indeterminate = checkedCount > 0 && checkedCount < children.length;

        groupBox.addEventListener('change', function () {
            children.forEach(c => c.checked = this.checked);
        });

        children.forEach(function (child) {
            child.addEventListener('change', function () {
                var checked = Array.from(children).filter(c => c.checked).length;
                groupBox.checked = checked === children.length;
                groupBox.indeterminate = checked > 0 && checked < children.length;
            });
        });
    });
</script>
@endpush
@endsection
