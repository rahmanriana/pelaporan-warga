<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (User::query()->count() > 0) {
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => 'admin@pelaporan.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Warga',
            'email' => 'warga@pelaporan.test',
            'password' => Hash::make('warga123'),
            'role' => 'warga',
        ]);
    }
}
