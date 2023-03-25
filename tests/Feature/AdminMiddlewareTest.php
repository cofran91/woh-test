<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\TestingSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\TruncateAllTablesSeeder;

class AdminMiddlewareTest extends TestCase
{
    use AdditionalAssertions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTablesSeeder::class);
        $this->seed(TestingSeeder::class);
    }

    /**
     * @test
     */
    public function admin_middleware_let_pass_when_user_is_admin()
    {
        Sanctum::actingAs(User::factory()->create(['rol_id' => 1]), ['*']);

        $body = [
            "name" => "cliente uno",
            "email" => "clienteone@woh.com",
            "password" => 123456,
            "rol_id" => 2,
            "user_type_id" => 2 
        ];
        $response = $this->postJson(route('users.store'), $body );

        $response->assertOk();
        $response->assertJsonStructure([]);
    }
    /**
     * @test
     */
    public function admin_middleware_returns_error_when_user_is_not_admin()
    {
        Sanctum::actingAs(User::factory()->create(['rol_id' => 2]), ['*']);

        $response = $this->postJson(route('users.store'));

        $response->assertStatus(401);
        $response->assertJsonStructure([]);
    }
}
