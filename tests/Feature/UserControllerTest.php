<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\TestingSeeder;
use App\Http\Controllers\UserController;
use App\Http\Requests\User\UserStoreRequest;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\User\UserUpdateRequest;
use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\TruncateAllTablesSeeder;

class UserControllerTest extends TestCase
{
    use AdditionalAssertions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTablesSeeder::class);
        $this->seed(TestingSeeder::class);
        Sanctum::actingAs(User::factory()->create(['rol_id' => 1]), ['*']);
    }

    /**
     * @test
     */
    public function index_return_users_list(): void
    {
        $usersCount = 5;
        User::factory()->count($usersCount)->create();
        // sumamos 1 por el usuario auntenticado
        $usersCount = 6;
        $response = $this->getJson(route('users.index'));
        $response->assertOk();
        $response->assertJsonStructure([]);
        $response->assertJsonCount($usersCount, "body.data");
    }

    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            UserController::class,
            'store',
            UserStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
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

        $users = User::query()
        ->where('email', $body['email'])
        ->get();
        $this->assertCount(1, $users);
    }

    /**
     * @test
     */
    public function store_returns_error_with_bad_request()
    {
        $body = [
            "name" => "cliente uno",
            "email" => "clienteone@woh.com"
        ];
        $response = $this->postJson(route('users.store'), $body );

        $response->assertStatus(422);
        $response->assertJsonStructure([]);
    }
    
    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            UserController::class,
            'update',
            UserUpdateRequest::class
        );
    }
    
    /**
     * @test
     */
    public function update_updates()
    {
        $user = User::factory()->create();
        $body = [
            "name" => "cliente uno",
            "email" => "clienteone@woh.com",
            "password" => 123456
        ];
        $response = $this->putJson(route('users.update',$user->id), $body );

        $response->assertOk();
        $response->assertJsonStructure([]);

        $users = User::query()
        ->where('id', $user->id)
        ->where('email', $body['email'])
        ->get();
        $this->assertCount(1, $users);
    }

    /**
     * @test
     */
    public function update_returns_error_with_bad_request()
    {
        $user = User::factory()->create();
        $body = [
            "name" => "cliente uno",
            "email" => "clienteone@woh.com"
        ];
        $response = $this->putJson(route('users.update',$user->id), $body );

        $response->assertStatus(422);
        $response->assertJsonStructure([]);
    }
}
