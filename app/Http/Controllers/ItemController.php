<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\UserItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Item\ItemResource;
use App\Http\Requests\Item\ItemStoreRequest;
use App\Http\Requests\Item\ItemUpdateRequest;

class ItemController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->jsonSuccess( 
            ItemResource::collection(
                Item::all()
            )->response()->getData(true)
        );
    }

    /**
     * @param ItemStoreRequest $request
     * @return JsonResponse
     */
    public function store(ItemStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $item = new Item();
            $item = $item->create( $request->all() );
            if ($item) {
                DB::commit();
                $item->refresh();
                return $this->jsonSuccess(
                    ItemResource::make($item),
                    200,
                    'Item created successfully'
                );
            } else {
                DB::rollback();
                return $this->jsonError();
            }
        } catch (\Throwable $th) {
            return $this->jsonError(null, 500, $th->getMessage());
        }
    }

    /**
     * @param ItemUpdateRequest $request
     * @param integer $id
     * @return JsonResponse
     */
    public function update(ItemUpdateRequest $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $item = Item::where('id', $id)->first();
            if (!$item) {
                return $this->jsonError(null, 404);
            }
            if ($item->update( $request->all() )) {
                DB::commit();
                $item->refresh();
                return $this->jsonSuccess(
                    ItemResource::make($item),
                    200,
                    'Item updated successfully'
                );
            } else {
                DB::rollback();
                return $this->jsonError();
            }
        } catch (\Throwable $th) {
            return $this->jsonError(null, $th->getCode(), $th->getMessage());
        }
    }

    /**
     * @param integer $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $item = Item::where('id', $id)->first();
        if (!$item) {
            return $this->jsonError(null, 404);
        }
        try {
            if ($item->delete()) {
                return $this->jsonSuccess();
            }
            return $this->jsonError();
    
        } catch (\Throwable $th) {
            return $this->jsonError(null, $th->getCode(), $th->getMessage());
        }
    }

    /**
     * @param integer $id
     * @return JsonResponse
     */
    public function buyItem($id): JsonResponse
    {
        $item = Item::where('id', $id)->first();
        if (!$item) {
            return $this->jsonError(null, 404);
        }

        $user = auth()->user();
        if ($user->items()->wherePivot('item_id','=', $id)->first()) {
            return $this->jsonError(null, 422, 'Item has already been buyed');
        }
        try {
            $user->items()->attach($item);
            return $this->jsonSuccess(
                ItemResource::make($item),
                200,
                'Item buyed successfully'
            );
    
        } catch (\Exception $e) {
            return $this->jsonError(null, $e->getCode(), $e->getMessage());
        }
    }
    
    /**
     * @param integer $id
     * @return JsonResponse
     */
    public function equipItem($id): JsonResponse
    {
        $item = Item::where('id', $id)->first();
        if (!$item) {
            return $this->jsonError(null, 404);
        }
        $user = auth()->user();

        $userItem = UserItem::where(['item_id' => $id , 'user_id' => $user->id])->first();
        if (!$userItem) {
            return $this->jsonError(null, 422, 'Item not buyed');
        }
        if ($userItem->equipped == 1) {
            return $this->jsonError(null, 422, 'Item equipped already');
        }
        try {
            DB::beginTransaction();
            $oldEquippedItems = $user->items()->where('item_type_id','=', $item->item_type_id)->update(['equipped' => 0]);
            
            if ( $userItem->update(['equipped' => 1]) ) {
                $user->refresh();
                $item->refresh();
                
                $itemsEquipped = $user->items()->wherePivot('equipped', 1)->get();
                $attack = 5 + $itemsEquipped->sum('attack');
                $defense = 5 + $itemsEquipped->sum('defense');
                $user->update([
                    'attack' => $attack,
                    'defense' => $defense
                ]);
                DB::commit();
                return $this->jsonSuccess(
                    ItemResource::make($item),
                    200,
                    'Item equipped successfully'
                );
            } else {
                DB::rollback();
                return $this->jsonError();
            }
        } catch (\Exception $e) {
            return $this->jsonError(null, $e->getCode(), $e->getMessage());
        }
    }
}
