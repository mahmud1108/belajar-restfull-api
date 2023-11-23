<?php

namespace Tests\Feature;

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

    public function testRegisterAlreadyExist()
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
}
