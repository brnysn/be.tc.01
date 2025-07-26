<?php

namespace Database\Seeders;

use App\Models\User;
use App\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() > 0) {
            return;
        }

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@b2btechus.com',
            'password' => Hash::make('password'),
            'role' => Roles::Admin->label(),
        ]);

        User::factory()->create([
            'name' => 'Customer',
            'email' => 'customer@b2btechus.com',
            'password' => Hash::make('password'),
            'role' => Roles::Customer->label(),
        ]);

        User::factory()->count(30)->customer()->create();
    }
}
