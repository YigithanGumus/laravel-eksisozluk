<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('123456')
            ],
            [
                'name' => 'Sanctum User 1',
                'email' => 'sanctum@laravel.com',
                'password' => Hash::make('123456')
            ],
            [
                'name' => 'Sanctum User 2',
                'email' => 'sanctum2@laravel.com',
                'password' => Hash::make('123456')
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
