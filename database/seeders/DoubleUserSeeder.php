<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoubleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'name' => 'admin',
            'token' => 'admin'
        ]);

        User::create([
            'username' => 'admin2',
            'password' => Hash::make('admin2'),
            'name' => 'admin2',
            'token' => 'admin2'
        ]);
    }
}
