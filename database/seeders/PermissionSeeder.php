<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Orders
            ['name' => 'orders.view',   'label' => 'Lihat Order',   'group' => 'Orders'],
            ['name' => 'orders.create', 'label' => 'Buat Order',    'group' => 'Orders'],
            ['name' => 'orders.edit',   'label' => 'Edit Order',    'group' => 'Orders'],
            ['name' => 'orders.delete', 'label' => 'Hapus Order',   'group' => 'Orders'],

            // Products
            ['name' => 'products.view',   'label' => 'Lihat Produk',   'group' => 'Products'],
            ['name' => 'products.create', 'label' => 'Tambah Produk',  'group' => 'Products'],
            ['name' => 'products.edit',   'label' => 'Edit Produk',    'group' => 'Products'],
            ['name' => 'products.delete', 'label' => 'Hapus Produk',   'group' => 'Products'],

            // Customers
            ['name' => 'customers.view',   'label' => 'Lihat Customer',   'group' => 'Customers'],
            ['name' => 'customers.create', 'label' => 'Tambah Customer',  'group' => 'Customers'],
            ['name' => 'customers.edit',   'label' => 'Edit Customer',    'group' => 'Customers'],
            ['name' => 'customers.delete', 'label' => 'Hapus Customer',   'group' => 'Customers'],

            // Categories
            ['name' => 'categories.view',   'label' => 'Lihat Kategori',   'group' => 'Categories'],
            ['name' => 'categories.create', 'label' => 'Tambah Kategori',  'group' => 'Categories'],
            ['name' => 'categories.edit',   'label' => 'Edit Kategori',    'group' => 'Categories'],
            ['name' => 'categories.delete', 'label' => 'Hapus Kategori',   'group' => 'Categories'],

            // Users
            ['name' => 'users.view',   'label' => 'Lihat User',   'group' => 'Users'],
            ['name' => 'users.create', 'label' => 'Tambah User',  'group' => 'Users'],
            ['name' => 'users.edit',   'label' => 'Edit User',    'group' => 'Users'],
            ['name' => 'users.delete', 'label' => 'Hapus User',   'group' => 'Users'],

            // Roles
            ['name' => 'roles.view',   'label' => 'Lihat Role',   'group' => 'Roles'],
            ['name' => 'roles.manage', 'label' => 'Kelola Role',  'group' => 'Roles'],

            // Permissions
            ['name' => 'permissions.view',   'label' => 'Lihat Permission',   'group' => 'Permissions'],
            ['name' => 'permissions.manage', 'label' => 'Kelola Permission',  'group' => 'Permissions'],

            // Reports
            ['name' => 'reports.view', 'label' => 'Lihat Laporan', 'group' => 'Reports'],
        ];

        foreach ($permissions as $data) {
            Permission::firstOrCreate(['name' => $data['name']], $data);
        }

        $this->assignRolePermissions();
    }

    private function assignRolePermissions(): void
    {
        $all = Permission::pluck('id', 'name');

        $map = [
            'Admin' => $all->keys()->all(),

            'Sales' => [
                'orders.view', 'orders.create', 'orders.edit',
                'products.view',
                'customers.view', 'customers.create', 'customers.edit',
                'categories.view',
            ],

            'Head Office' => [
                'orders.view',
                'products.view',
                'customers.view',
                'categories.view',
                'users.view',
                'roles.view',
                'permissions.view',
                'reports.view',
            ],
        ];

        foreach ($map as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                continue;
            }

            $ids = collect($permissionNames)
                ->map(fn($name) => $all->get($name))
                ->filter()
                ->all();

            $role->permissions()->sync($ids);
        }
    }
}
