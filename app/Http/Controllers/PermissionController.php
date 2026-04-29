<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('permissions.view'), 403);

        $search = $request->search;
        $group  = $request->group;

        $permissions = Permission::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%");
            }))
            ->when($group, fn($q) => $q->where('group', $group))
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        $groups = Permission::select('group')->distinct()->orderBy('group')->pluck('group');

        return view('permissions.index', compact('permissions', 'search', 'group', 'groups'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('permissions.manage'), 403);

        $groups = Permission::select('group')->distinct()->orderBy('group')->pluck('group');

        return view('permissions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('permissions.manage'), 403);

        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:permissions,name',
            'label' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:255',
        ]);

        Permission::create($validated);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil ditambahkan.');
    }

    public function edit(Permission $permission)
    {
        abort_unless(auth()->user()->hasPermission('permissions.manage'), 403);

        $groups = Permission::select('group')->distinct()->orderBy('group')->pluck('group');

        return view('permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, Permission $permission)
    {
        abort_unless(auth()->user()->hasPermission('permissions.manage'), 403);

        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'label' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:255',
        ]);

        $permission->update($validated);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil diupdate.');
    }

    public function destroy(Permission $permission)
    {
        abort_unless(auth()->user()->hasPermission('permissions.manage'), 403);

        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil dihapus.');
    }
}
