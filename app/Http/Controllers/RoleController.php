<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $roles = Role::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return view('roles.index', compact('roles', 'search'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create($validated);

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update($validated);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return redirect()->route('roles.index')->with('error', 'Role tidak bisa dihapus karena masih dipakai user.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}