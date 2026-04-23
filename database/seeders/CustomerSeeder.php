<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'nik' => '3173010101010001',
                'address' => 'Jl. Mawar No. 10, Jakarta',
                'no_tlp' => '081234567890',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'nik' => '3173010101010002',
                'address' => 'Jl. Melati No. 5, Bandung',
                'no_tlp' => '082345678901',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@example.com',
                'nik' => '3173010101010003',
                'address' => 'Jl. Kenanga No. 7, Surabaya',
                'no_tlp' => '083456789012',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@example.com',
                'nik' => '3173010101010004',
                'address' => 'Jl. Anggrek No. 12, Yogyakarta',
                'no_tlp' => '084567890123',
            ],
            [
                'name' => 'Rizky Pratama',
                'email' => 'rizky@example.com',
                'nik' => '3173010101010005',
                'address' => 'Jl. Dahlia No. 3, Semarang',
                'no_tlp' => '085678901234',
            ],
        ];

        foreach ($customers as $data) {
            Customer::updateOrCreate(
                ['nik' => $data['nik']],
                $data
            );
        }
    }
}