<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\TestingSeeder;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Requests\Item\ItemStoreRequest;
use App\Http\Requests\User\UserStoreRequest;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\Item\ItemUpdateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use Database\Seeders\TruncateAllTablesSeeder;
use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemControllerTest extends TestCase
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
    public function index_return_items_list(): void
    {
        $itemsCount = 5;
        Item::factory()->count($itemsCount)->create();
        $response = $this->getJson(route('items.index'));
        $response->assertOk();
        $response->assertJsonStructure([]);
        $response->assertJsonCount($itemsCount, "body.data");
    }

    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            ItemController::class,
            'store',
            ItemStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
        $body = [
            "name" => "Armadura de Bronce",
            "item_type_id" => 2 
        ];
        $response = $this->postJson(route('items.store'), $body );

        $response->assertOk();
        $response->assertJsonStructure([]);

        $items = Item::query()
        ->where('name', $body['name'])
        ->where('item_type_id', $body['item_type_id'])
        ->get();
        $this->assertCount(1, $items);
    }

    /**
     * @test
     */
    public function store_returns_error_with_bad_request()
    {
        $body = [
            "name" => "Armadura de Bronce",
        ];
        $response = $this->postJson(route('items.store'), $body );

        $response->assertStatus(422);
        $response->assertJsonStructure([]);
    }
    
    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            ItemController::class,
            'update',
            ItemUpdateRequest::class
        );
    }
    
    /**
     * @test
     */
    public function update_updates()
    {
        $item = Item::factory()->create();
        $body = [
            "name" => "Armadura de Bronce",
            "item_type_id" => 2
        ];
        $response = $this->putJson(route('items.update',$item->id), $body );

        $response->assertOk();
        $response->assertJsonStructure([]);

        $items = Item::query()
        ->where('id', $item->id)
        ->where('name', $body['name'])
        ->where('item_type_id', $body['item_type_id'])
        ->get();
        $this->assertCount(1, $items);
    }

    /**
     * @test
     */
    public function update_returns_error_with_bad_request()
    {
        $item = Item::factory()->create();
        $body = [
            "name" => "Armadura de Bronce",
        ];
        $response = $this->putJson(route('items.update',$item->id), $body );

        $response->assertStatus(422);
        $response->assertJsonStructure([]);
    }
}
