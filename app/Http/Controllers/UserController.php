<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('users.view'), 403);

        $search = $request->search;
        $role = $request->role;

        $users = User::with('role')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->where('role_id', $role);
            })
            ->latest()
            ->get();

        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'search', 'role'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('users.create'), 403);

        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('users.create'), 403);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id'  => $validated['role_id'] ?? null,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function show(User $user)
    {
        abort_unless(auth()->user()->hasPermission('users.view'), 403);

        $user->load('role.permissions');

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        abort_unless(auth()->user()->hasPermission('users.edit'), 403);

        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user()->hasPermission('users.edit'), 403);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $data = [
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'role_id' => $validated['role_id'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        abort_unless(auth()->user()->hasPermission('users.delete'), 403);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}
