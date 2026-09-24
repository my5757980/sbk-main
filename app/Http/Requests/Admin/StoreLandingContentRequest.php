<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandingContentRequest extends FormRequest
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
            'section1_title' => 'required|string|max:255',
            'section1_sub_title' => 'required|string|max:255',
            'apple_link' => 'required|url|max:65000',
            'google_link' => 'required|url|max:65000',
            'section1_image' => 'nullable|image|max:2048|required_without:previous_section1_image',
            'section2_title' => 'required|string|max:255',
            'section2_sub_title' => 'required',
            'section2_content' => 'required|string|max:65000',
            'section2_image' => 'nullable|image|max:2048|required_without:previous_section2_image',
            'section3_content' => 'required|string|max:65000',
            'section3_image' => 'nullable|image|max:2048|required_without:previous_section3_image',
            'section4_title' => 'required|string|max:255',
            'section4_subtitle' => 'required|string|max:65000',
            'section4_image' => 'nullable|image|max:2048|required_without:previous_section4_image',
            'section4_title2' => 'required|string|max:255',
            'section4_subtitle2' => 'required|string|max:65000',
            'section4_image2' => 'nullable|image|max:2048|required_without:previous_section4_image2',
            'section4_title3' => 'required|string|max:255',
            'section4_subtitle3' => 'required|string|max:65000',
            'section4_image3' => 'nullable|image|max:2048|required_without:previous_section4_image3',
        ];
    }
}
