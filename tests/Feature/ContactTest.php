<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\DoubleUserSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{

    public function testCreateContactSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->post(
            '/api/contact',
            [
                'first_name' => 'mahmud',
                'last_name' => 'awaludin',
                'email' => 'email@gmail.com',
                'phone' => '090383849'
            ],
            [
                // mengambil dari token yang di UserSeeder::class
                "Authorization" => 'admin'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'mahmud',
                    'last_name' => 'awaludin',
                    'email' => 'email@gmail.com',
                    'phone' => '090383849'
                ]
            ]);
    }

    public function testCreateContactFailedUnauthorized()
    {
        $this->seed(UserSeeder::class);
        $this->post(
            '/api/contact',
            [
                'first_name' => 'mahmud',
                'last_name' => 'awaludin',
                'email' => 'email@gmail.com',
                'phone' => '090383849'
            ],
            [
                // mengambil dari token yang di UserSeeder::class
                "Authorization" => 'asd'
            ]
        )->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testContactCreateFailed()
    {
        $this->seed(UserSeeder::class);
        $this->post(
            '/api/contact',
            [
                'first_name' => '',
                'last_name' => 'awaludin',
                'email' => 'email',
                'phone' => '090383849'
            ],
            [
                // mengambil dari token yang di UserSeeder::class
                "Authorization" => 'admin'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        "The first name field is required."
                    ],
                    'email' => [
                        "The email field must be a valid email address."
                    ],
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get(
            'api/contact/' . $contact->id,
            [
                "Authorization" => 'admin'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test',
                    'last_name' => 'test',
                    'email' => 'test@gmail.com',
                    'phone' => '209345879243',
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get(
            'api/contact/' . $contact->id + 1,
            [
                "Authorization" => 'admin'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([DoubleUserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get(
            'api/contact/' . $contact->id,
            [
                "Authorization" => 'admin2'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put(
            'api/contact/' . $contact->id,
            [
                'first_name' => 'udpate',
                'last_name' => 'update',
                'email' => 'update@gmail.com',
                'phone' => '1111111'
            ],
            [
                "Authorization" => 'admin'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'udpate',
                    'last_name' => 'update',
                    'email' => 'update@gmail.com',
                    'phone' => '1111111'
                ]
            ]);
    }

    public function testUpdateUnauthorized()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        // mendapatkan data pertama
        $contact = Contact::query()->limit(1)->first();

        $this->put(
            'api/contact/' . $contact->id,
            [
                'first_name' => 'udpate',
                'last_name' => 'update',
                'email' => 'update@gmail.com',
                'phone' => '1111111'
            ],
            [
                "Authorization" => 'salah'
            ]
        )->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }

    public function testUpdateOtherUserContact()
    {
        $this->seed([DoubleUserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put(
            'api/contact/' . $contact->id,
            [
                'first_name' => 'udpate',
                'last_name' => 'update',
                'email' => 'update@gmail.com',
                'phone' => '1111111'
            ],
            [
                "Authorization" => 'admin2'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        // mendapatkan data pertama
        $contact = Contact::query()->limit(1)->first();

        $this->put(
            'api/contact/' . $contact->id,
            [
                'first_name' => '',
                'last_name' => 'update',
                'email' => 'update@gmail.com',
                'phone' => '1111111'
            ],
            [
                "Authorization" => 'admin'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        "The first name field must be at least 4 characters.",
                        "The first name field is required."
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contact/' . $contact->id, [], [
            "Authorization" => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteOtherUserContact()
    {
        $this->seed([DoubleUserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contact/' . $contact->id, [], [
            "Authorization" => 'admin2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testDeleteWrongId()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contact/' . $contact->id + 1, [], [
            "Authorization" => 'admin'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testDeleteUnauthorized()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contact/' . $contact->id + 1, [], [])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
}
