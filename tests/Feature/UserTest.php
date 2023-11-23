<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->postJson(
            '/api/users',
            [
                'username' => 'mahmud',
                'name' => 'Mahmud Awaludin',
                'password' => 'password'
            ]
        )->assertStatus(201)
            ->assertJson(
                [
                    "data" => [
                        'username' => "mahmud",
                        "name" => 'Mahmud Awaludin'
                    ]
                ]
            );
    }

    public function testRegisterFailed()
    {
        $this->postJson(
            '/api/users',
            [
                'username' => '',
                'name' => '',
                'password' => ''
            ]
        )->assertUnprocessable(400)
            ->assertJson(
                [
                    "errors" => [
                        'username' => [
                            "The username field is required."
                        ],
                        "password" => [
                            "The password field is required."
                        ],
                        'name' => [
                            "The name field is required."
                        ]
                    ]
                ]
            );
    }

    public function testRegisterUsernameAlreadyExist()
    {
        $this->testRegisterSuccess();

        $this->postJson(
            '/api/users',
            [
                'username' => 'mahmud',
                'name' => 'Mahmud Awaludin',
                'password' => 'password'
            ]
        )->assertUnprocessable(400)
            ->assertJson(
                [
                    "errors" => [
                        'username' => [
                            "The username has already been taken."
                        ]
                    ]
                ]
            );
    }

    public function testloginSuccess()
    {
        $user = new User;
        $user->username = 'admin';
        $user->password = Hash::make('admin');
        $user->name = 'admin';
        $user->save();

        $this->post('api/users/login', [
            'username' => 'admin',
            'password' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'admin',
                    'name' => null
                ]
            ]);

        $user_exist = User::where('username', 'admin')->first();

        // untuk memastikan token telah tersedia
        self::assertNotNull($user_exist->token);
    }

    public function testLoginUsernameNotFound()
    {
        $this->post('/api/users/login', [
            'username' => 'admin',
            'password' => 'admin'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'username or password wrong.'
                    ]
                ]
            ]);
    }
}
