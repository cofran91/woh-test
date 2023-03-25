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
            return $this->jsonError(null, $th->getCode(), $th->getMessage());
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
}
