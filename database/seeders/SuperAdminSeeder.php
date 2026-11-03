<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@aurorapos.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '+880000000000',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles(['Super Admin']);
    }
}
