<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'المدير العام',
            'email' => 'admin@newsroom.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Create editor users
        User::create([
            'name' => 'محرر الأخبار السياسية',
            'email' => 'politics@newsroom.com',
            'password' => Hash::make('password'),
            'role' => 'editor'
        ]);

        User::create([
            'name' => 'محرر الأخبار الرياضية',
            'email' => 'sports@newsroom.com',
            'password' => Hash::make('password'),
            'role' => 'editor'
        ]);

        User::create([
            'name' => 'محرر التكنولوجيا',
            'email' => 'tech@newsroom.com',
            'password' => Hash::make('password'),
            'role' => 'editor'
        ]);
    }
}
