<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('roles.view'), 403);

        $search  = $request->search;
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        $roles = Role::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('roles.index', compact('roles', 'search', 'perPage'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('roles.manage'), 403);

        return view('roles.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('roles.manage'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create($validated);

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function show(Role $role)
    {
        abort_unless(auth()->user()->hasPermission('roles.view'), 403);

        $role->load('permissions');

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        abort_unless(auth()->user()->hasPermission('roles.manage'), 403);

        $role->load('permissions');
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        abort_unless(auth()->user()->hasPermission('roles.manage'), 403);

        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate.');
    }

    public function destroy(Role $role)
    {
        abort_unless(auth()->user()->hasPermission('roles.manage'), 403);

        if ($role->users()->exists()) {
            return redirect()->route('roles.index')->with('error', 'Role tidak bisa dihapus karena masih dipakai user.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
