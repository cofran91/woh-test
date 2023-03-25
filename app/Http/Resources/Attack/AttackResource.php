<?php

namespace App\Http\Resources\Attack;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AttackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'effect' => $this->effect,
            'attacking_user' => UserResource::make($this->attackingUser),
            'defending_user' => UserResource::make($this->defendingUser)
        ];
    }
}
