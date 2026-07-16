<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the default Filament admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@tbo.digital'],
            [
                'name' => 'TBO Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
