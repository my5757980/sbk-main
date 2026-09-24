<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBidStatusRequest extends FormRequest
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
            'bid_id' => 'required',
            'status' => 'required|in:accept,reject',
            'property_ids' => $this->status == 'accept' ? 'required|array|min:1' : '',
            'property_ids.*' => 'integer', // Assuming property_ids is an array of integers
            'is_discount' => $this->status == 'accept' ? 'required|in:true,false' : '',
            'discount' => $this->is_discount ? 'required_if:is_discount,true' : '',
            'discount_type' => $this->is_discount ? 'required_if:is_discount,true' : '',
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
