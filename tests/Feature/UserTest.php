<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

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

    public function testGetSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/user/current', [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'admin',
                    'name' => 'admin'
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->get('/api/user/current')->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/user/current', [
            'Authorized' => 'token salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed(UserSeeder::class);
        $old_user = User::where('username', 'admin')->first();
        $this->patch(
            '/api/user/current',
            [
                'password' => 'password'
            ],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'admin',
                    'name' => 'admin'
                ]
            ]);

        $new_user = User::where('username', 'admin')->first();

        // digunakan untuk mengecek password apakah old dengan new password masih sama, jika sudah berbeda maka benar
        self::assertNotEquals($old_user->password, $new_user->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);
        $old = User::where('username', 'admin')->first();

        $this->patch(
            '/api/user/current',
            [
                'name' => 'nama baru'
            ],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(200)
            ->assertJson(
                [
                    'data' => [
                        'username' => 'admin',
                        'name' => 'nama baru'
                    ]
                ]
            );

        $new = User::where('username', 'admin')->first();
        self::assertNotEquals($old->name, $new->name);
    }

    public function testUpdateFailed()
    {
        $this->seed(UserSeeder::class);
        $this->patch(
            '/api/user/current',
            [
                'name' => 'as'
            ],
            [
                'Authorization' => 'admin'
            ]
        )->assertStatus(200)
            ->assertJson(
                [
                    'errors' => [
                        'name' => [
                            "The name field must be at least 4 characters."
                        ]
                    ]
                ]
            );
    }

    public function testUserLogout()
    {
        $this->seed(UserSeeder::class);
        $this->delete('/api/user/logout', [], [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);

        $user = User::where('username', 'admin')->first();
        $this->assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed(UserSeeder::class);
        $this->delete(
            '/api/user/logout',
            [],
            [
                "Authorization" => 'salah token'
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
}
