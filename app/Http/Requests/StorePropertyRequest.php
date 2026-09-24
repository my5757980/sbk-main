<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'city' => 'required',
            'no_of_beds' => 'required',
            'zip_code' => 'required',
            'address' => 'required',
            'description' => 'required',
            'max_no_of_person' => 'required',
            'no_of_adults' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'statusCode'    => 400,
            "response"      => [
                "data"      => []
            ],
            'message'       => 'Bad Request!',
            'status'        => false,
            'errors'        => $validator->errors()
        ], 400));
    }
}
