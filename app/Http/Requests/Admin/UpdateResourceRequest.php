<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResourceRequest extends FormRequest
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
            'title' => 'required|max:100|min:3',
            'short_description' => 'required|max:200|min:3',
            //            'feeling_id'=>'required',
            'category_id' => 'required',
            'mood_id' => 'required',
            'activity_id' => 'nullable',
            'description' => 'required',
            //            'heading'=>'required|max:100|min:3',
            'video_link' => 'nullable'
        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => 'The title is required.',
            'title.max' => 'The title may not be greater than 100 characters.',
            'title.min' => 'The title must be at least 3 characters.',
            'short_description.required' => 'The short description is required.',
            'feeling_id.required' => 'Please select a thoughts.',
            'category_id.required' => 'Please select a category.',
            'mood_id.required' => 'Please select a mood.',
            'activity_id.required' => 'Please select an action.',
            'description.required' => 'The description is required.',
            'heading.required' => 'The heading is required.',
            'heading.max' => 'The heading may not be greater than 100 characters.',
            'heading.min' => 'The heading must be at least 3 characters.',
        ];
    }
}
