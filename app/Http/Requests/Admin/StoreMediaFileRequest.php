<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaFileRequest extends FormRequest
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
            'alt_text'  => 'required|max:190',
            'file'      => 'required|file|mimes:jpeg,jpg,png|max:5000',
            'caption'   => 'required|max:190'
        ];
    }
}
