<?php

namespace App\Http\Requests\Attack;

use Illuminate\Foundation\Http\FormRequest;

class AttackStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'defending_user_id' => ['required', 'integer', 'exists:users,id'],
            'attack_type_id' => ['required', 'integer', 'exists:attack_types,id']
        ];
    }
}
