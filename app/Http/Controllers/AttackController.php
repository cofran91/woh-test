<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\Attack\AttackResource;
use App\Http\Requests\Attack\AttackStoreRequest;
use App\Models\Attack;
use App\Models\AttackType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AttackController extends Controller
{
    /**
     * @param AttackStoreRequest $request
     * @return JsonResponse
     */
    public function store(AttackStoreRequest $request)
    {
        $attackingUser = auth()->user();
        $defendingUser = User::where('id', $request->defending_user_id)->first();
        $attackType = AttackType::where('id', $request->attack_type_id)->first();

        if ($request->defending_user_id == $attackingUser->id) {
            return $this->jsonError(null, 422, 'you can not attack yourself');
        }
        if ($attackingUser->life == 0) {
            return $this->jsonError(null, 422, 'you can not attack because you have no life');
        }
        if ($defendingUser->life == 0) {
            return $this->jsonError(null, 422, 'the player who wants to attack is already dead');
        }
        if ($request->attack_type_id == 3) {
            $lastAttack = Attack::where('attacking_user_id', $attackingUser->id)->orderBy('created_at', 'DESC')->limit(1)->get();
            if ( count($lastAttack) == 0 || $lastAttack[0]->attack_type_id != 1) {
                return $this->jsonError(null, 422, 'attack not allowed');
            }
        }

        $effect = $attackingUser['attack']*$attackType['damage'] - $defendingUser['defense'];

        if ($effect <= 0) {
            $effect = 1;
        }
        if ($effect > $defendingUser->life) {
            $effect = $defendingUser->life;
        }
        $attackData = [
            'attacking_user_id' => $attackingUser->id,
            'defending_user_id' => $defendingUser->id,
            'attack_type_id' => $attackType->id,
            'effect' => $effect,
        ];

        $restLife = $defendingUser->life - $effect;
        try {

            DB::beginTransaction();

            $attack = new Attack();
            $attack = $attack->create( $attackData );
            if ($attack) {
                DB::commit();
                $attack->refresh();
                $defendingUser->update(['life' => $restLife]);
                return $this->jsonSuccess(
                    AttackResource::make($attack),
                    200,
                    'Attack created successfully'
                );
            } else {
                DB::rollback();
                return $this->jsonError();
            }
        } catch (\Throwable $th) {
            return $this->jsonError(null, $th->getCode(), $th->getMessage());
        }
    }
}
