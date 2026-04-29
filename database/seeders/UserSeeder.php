<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin',
            ],
            [
                'name' => 'Sales User',
                'email' => 'sales@example.com',
                'password' => Hash::make('password'),
                'role' => 'Sales',
            ],
            [
                'name' => 'Head Office User',
                'email' => 'headoffice@example.com',
                'password' => Hash::make('password'),
                'role' => 'Head Office',
            ],
        ];

        foreach ($users as $data) {
            $role = Role::where('name', $data['role'])->first();

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                    'role_id' => $role?->id,
                ]
            );
        }
    }
}