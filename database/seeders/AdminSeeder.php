<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@dishub-bogor.go.id'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123!'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );
    }
}
