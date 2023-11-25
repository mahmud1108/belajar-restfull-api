<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/user', [
            'username' => 'mahmud',
            'password' => 'mahmud',
            'name' => 'Mahmud Awaludin'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'username' => 'mahmud',
                    'name' => 'Mahmud Awaludin'
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/user', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        "The username field is required."
                    ],
                    'password' => [
                        "The password field is required."
                    ],
                    'name' => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExist()
    {
        $this->testRegisterSuccess();
        $this->post('/api/user', [
            'username' => 'mahmud',
            'password' => 'mahmud',
            'name' => 'Mahmud Awaludin'
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        "username already registered."
                    ],
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/user/login', [
            'username' => 'admin',
            'password' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'admin',
                    'name' => 'admin'
                ]
            ]);

        $user = User::where('username', 'admin')->first();
        assertNotNull($user->token);
    }

    public function testLoginUsernameNotFound()
    {
        // not found karena tidak di seed terlebih dahulu
        $this->post('/api/user/login', [
            'username' => 'admin',
            'password' => 'admin'
            // assert status ini disesuaikan dengan throw error yang ada di user controller
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'username or password wrong.'
                    ]
                ]
            ]);
    }

    public function testLoginWrongPassword()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/user/login', [
            'username' => 'admin',
            'password' => 'wrong password'
            // assert status ini disesuaikan dengan throw error yang ada di user controller
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
