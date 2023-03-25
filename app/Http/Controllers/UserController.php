<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Item\ItemResource;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;

class UserController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->jsonSuccess( 
            UserResource::collection(
                User::all()
            )->response()->getData(true)
        );
    }

    /**
     * @param UserStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = new User();
            $user = $user->create( $request->all() );
            if ($user) {
                DB::commit();
                $user->refresh();

                return $this->jsonSuccess(
                    UserResource::make($user),
                    200,
                    'User created successfully'
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
     * @param UserUpdateRequest $request
     * @param integer $id
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, $id): JsonResponse
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return $this->jsonError(null, 404);
        }
        try {
            DB::beginTransaction();

            $user = User::where('id', $id)->first();
            if ($user->update( $request->all() )) {
                DB::commit();
                $user->refresh();

                return $this->jsonSuccess(
                    UserResource::make($user),
                    200,
                    'User updated successfully'
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
        $user = User::where('id', $id)->first();
        if (!$user) {
            return $this->jsonError(null, 404);
        }
        try {
            if ($user->delete()) {
                return $this->jsonSuccess();
            }
            return $this->jsonError();
    
        } catch (\Throwable $th) {
            return $this->jsonError(null, $th->getCode(), $th->getMessage());
        }
    }

    /**
     * @return JsonResponse
     */
    public function inventory(): JsonResponse
    {
        $user = auth()->user();
        return $this->jsonSuccess( 
            ItemResource::collection(
                $user->items
            )->response()->getData(true)
        );
    }
    
    /**
     * @return JsonResponse
     */
    public function equipment(): JsonResponse
    {
        $user = auth()->user();
        return $this->jsonSuccess( 
            ItemResource::collection(
                $user->items()->wherePivot('equipped', 1)->get()
            )->response()->getData(true)
        );
    }
}
