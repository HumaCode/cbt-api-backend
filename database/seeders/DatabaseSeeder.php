<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        // Create Super Admin User
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@cbt.com',
            'password' => bcrypt('password123'),
            'is_active' => '1',
        ]);
        $admin->assignRole('Super Admin');

        // Create a default Peserta User
        $peserta = User::create([
            'name' => 'Peserta Ujian',
            'username' => 'peserta',
            'email' => 'peserta@cbt.com',
            'password' => bcrypt('password123'),
            'is_active' => '1',
        ]);
        $peserta->assignRole('Peserta');
    }
}
