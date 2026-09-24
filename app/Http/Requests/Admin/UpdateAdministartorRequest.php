<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdministartorRequest extends FormRequest
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
            'first_name'            => 'required|max:32',
            'last_name'             => 'max:32',
            'phone'                 => 'max:24',
            'image'                 => 'file|mimes:jpeg,jpg,png|max:5000',
            'password_confirmation' => 'required_if:password,!==,""|same:password',
            'admin_role_id'         => 'nullable|exists:admin_roles,id',
        ];
    }
}
