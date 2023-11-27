<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\ListAddressSeeder;
use Database\Seeders\UserSeeder;
use Faker\Extension\AddressExtension;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Psy\Command\EditCommand;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact  = Contact::query()->limit(1)->first();
        $this->post(
            '/api/contact/' . $contact->id . '/address',
            [
                'street' => 'test street',
                'city' => 'test city',
                'province' => 'test province',
                'country' => 'test country',
                'postal_code' => '123'
            ],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'test street',
                    'city' => 'test city',
                    'province' => 'test province',
                    'country' => 'test country',
                    'postal_code' => '123'
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post(
            '/api/contact/' . $contact->id . '/address',
            [
                'street' => 'test street',
                'city' => 'test city',
                'province' => 'test province',
                'country' => '',
                'postal_code' => '123123123123123'
            ],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(400)
            ->assertJson(['errors' => [
                'country' => [
                    'The country field is required.'
                ],
                'postal_code' => [
                    'The postal code field must not be greater than 10 characters.'
                ]
            ]]);
    }

    public function testCreateAddressContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post(
            '/api/contact/' . $contact->id + 1 . '/address',
            [
                'street' => 'test street',
                'city' => 'test city',
                'province' => 'test province',
                'country' => 'test country',
                'postal_code' => '123'
            ],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(404)
            ->assertJson(
                [
                    'errors' => [
                        'message' => [
                            'Not found'
                        ]
                    ]
                ]
            );
    }

    public function testGetAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();

        $response = $this->get('/api/contact/' . $contact->id . '/address/' . $address->id, headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'test',
                    'city' => 'test',
                    'province' => 'test',
                    'country' => 'test',
                    'postal_code' => 'test',
                ]
            ]);
    }

    public function testGetAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contact/' . $address->contact_id + 1 . '/address/' . $address->id, headers: [
            'Authorization' => 'admin'
        ])->assertStatus(404)
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
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contact/' . $address->contact_id . '/address/' . $address->id,
            [
                'street' => 'baru',
                'city' => 'city baru',
                'province' => 'test',
                'country' => 'test',
                'postal_code' => 'test',
            ],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(200)
            ->assertJson(
                [
                    'data' => [
                        'street' => 'baru',
                        'city' => 'city baru',
                        'province' => 'test',
                        'country' => 'test',
                        'postal_code' => 'test',
                    ]
                ]
            );
    }

    public function testUpdateFieldRequired()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contact/' . $address->contact_id . '/address/' . $address->id,
            [
                'street' => 'baru',
                'city' => 'city baru',
                'province' => 'test',
                'country' => '',
                'postal_code' => 'test',
            ],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'country' => [
                            'The country field is required.'
                        ]
                    ]
                ]
            );
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contact/' . $address->contact_id + 1 . '/address/' . $address->id,
            [
                'street' => 'baru',
                'city' => 'city baru',
                'province' => 'test',
                'country' => 'test',
                'postal_code' => 'test',
            ],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(404)
            ->assertJson(
                [
                    'errors' => [
                        'message' => [
                            'Not found'
                        ]
                    ]
                ]
            );
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contact/' . $address->contact_id . '/address/' . $address->id,
            [],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(200)
            ->assertJson(
                [
                    'data' => true
                ]
            );
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contact/' . $address->contact_id + 1 . '/address/' . $address->id,
            [],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(404)
            ->assertJson(
                [
                    'errors' => [
                        'message' => [
                            'Not found'
                        ]
                    ]
                ]
            );
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, ListAddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $response =   $this->get('/api/contact/' . $contact->id . '/address', [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->json();
    }

    public function testListNotfound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, ListAddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contact/' . $contact->id + 1 . '/address', [
            'Authorization' => 'admin'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }
}
