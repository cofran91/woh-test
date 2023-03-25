<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Attack;
use App\Models\UserItem;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\TestingSeeder;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AttackController;
use App\Http\Requests\Item\ItemStoreRequest;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\Item\ItemUpdateRequest;
use Database\Seeders\TruncateAllTablesSeeder;
use JMac\Testing\Traits\AdditionalAssertions;
use App\Http\Requests\Attack\AttackStoreRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttackControllerTest extends TestCase
{
    use AdditionalAssertions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTablesSeeder::class);
        $this->seed(TestingSeeder::class);
        Sanctum::actingAs(User::factory()->create(['rol_id' => 1, 'life' => 100]), ['*']);
    }

    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            AttackController::class,
            'store',
            AttackStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_returns_error_with_bad_request()
    {
        $userTwo = User::factory()->create(['id' => 2]);
        $body = [
            "defending_user_id" => 3,
            "attack_type_id" => $userTwo->id,
        ];
        $response = $this->postJson(route('attacks.store'), $body );

        $response->assertStatus(422);
        $response->assertJsonStructure([]);
    }

    /**
     * @test
     */
    public function store_returns_error_when_try_to_attack_yourself()
    {
        $userOne = Sanctum::actingAs(User::factory()->create(), ['*']);
        $body = [
            "defending_user_id" => $userOne->id,
            "attack_type_id" => 2,
        ];
        $response = $this->postJson(route('attacks.store'), $body );

        $response->assertStatus(422);
        $response->assertJsonStructure([]);
        $response->assertExactJson([
            "success" => false,
            "message" => 'you can not attack yourself',
            "body" => null
        ]);
    }
    
    /**
     * @test
     */
    public function store_returns_error_when_attacking_user_is_dead()
    {
        $userOne = Sanctum::actingAs(User::factory()->create(['life' => 0]), ['*']);
        $userTwo = User::factory()->create();
        $body = [
            "defending_user_id" => $userTwo->id,
            "attack_type_id" => 2,
        ];
        $response = $this->postJson(route('attacks.store'), $body );

        $response->assertStatus(422);
        $response->assertJsonStructure([]);
        $response->assertExactJson([
            "success" => false,
            "message" => 'you can not attack because you have no life',
            "body" => null
        ]);
    }
    
    /**
     * @test
     */
    public function store_returns_error_when_try_to_attack_dead_user()
    {
        $userTwo = User::factory()->create(['life' => 0]);
        $body = [
            "defending_user_id" => $userTwo->id,
            "attack_type_id" => 2,
        ];
        $response = $this->postJson(route('attacks.store'), $body );

        $response->assertStatus(422);
        $response->assertJsonStructure([]);
        $response->assertExactJson([
            "success" => false,
            "message" => 'the player who wants to attack is already dead',
            "body" => null
        ]);
    }

    /**
     * @test
     */
    public function store_returns_error_when_try_to_attack_ulti_and_is_not_enable()
    {
        $userOne = Sanctum::actingAs(User::factory()->create(['life'=>100]), ['*']);
        $userTwo = User::factory()->create();
        $body = [
            "defending_user_id" => $userTwo->id,
            "attack_type_id" => 2,
        ];
        $response = $this->postJson(route('attacks.store'), $body );

        $bodyTwo = [
            "defending_user_id" => $userTwo->id,
            "attack_type_id" => 3,
        ];
        $response = $this->postJson(route('attacks.store'), $bodyTwo );

        $response->assertStatus(422);
        $response->assertJsonStructure([]);
        $response->assertExactJson([
            "success" => false,
            "message" => 'attack not allowed',
            "body" => null
        ]);
    }
    
    /**
     * @test
     */
    public function store_saves()
    {
        $userOne = Sanctum::actingAs(User::factory()->create(['life'=>100]), ['*']);
        $userTwo = User::factory()->create();
        $body = [
            "defending_user_id" => $userTwo->id,
            "attack_type_id" => 2,
        ];
        $response = $this->postJson(route('attacks.store'), $body );

        $response->assertOk();
        $response->assertJsonStructure([]);

        $attack = Attack::query()
        ->where('attacking_user_id', $userOne->id)
        ->where('defending_user_id', $userTwo->id)
        ->get();
        $this->assertCount(1, $attack);
    }
}
