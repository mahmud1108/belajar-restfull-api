<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::query()->limit(1)->first();
        for ($i = 0; $i < 20; $i++) {
            Address::create([
                'contact_id' => $contact->id,
                'street' => 'test' . $i,
                'city' => 'test' . $i,
                'province' => 'test' . $i,
                'country' => 'test' . $i,
                'postal_code' => 'test' . $i,
            ]);
        }
    }
}
