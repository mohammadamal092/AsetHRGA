<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@hrga.local'],
            ['name' => 'Admin HRGA', 'password' => Hash::make('password')]
        );

        foreach ([
            'PC / Desktop', 'Laptop', 'Monitor', 'Keyboard', 'Mouse', 'Printer & Scanner',
            'Telepon / HP', 'Jaringan (Router/Switch)', 'Meja', 'Kursi', 'AC', 'Lainnya',
        ] as $nama) {
            Kategori::firstOrCreate(['nama' => $nama]);
        }
    }
}
