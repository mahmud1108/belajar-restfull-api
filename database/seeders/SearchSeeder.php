<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'admin')->first();
        for ($i = 0; $i < 20; $i++) {
            Contact::create([
                'first_name' => 'first_name' . $i,
                'last_name' => 'last_name' . $i,
                'email' => 'test' . $i . '@gmail.com',
                'phone' => '123466766' . $i,
                'user_id' => $user->id
            ]);
        }
    }
}
