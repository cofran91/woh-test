<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->id)->whereNull('deleted_at')],
            'password' => ['required'],
            'rol_id' => ['nullable', 'integer', 'exists:rols,id'],
            'user_type_id' => ['nullable', 'integer', 'exists:user_types,id', 'requiredIf:rol_id,2'],
        ];
    }
}
