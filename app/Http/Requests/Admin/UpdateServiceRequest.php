<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('service') ?? $this->id;

        return [
            'title' => ['required', 'max:191'],
            'menu_title' => ['nullable', 'max:191'],
            'slug' => ['required', 'max:191', Rule::unique('services', 'slug')->ignore($id)],
            'excerpt' => ['nullable', 'max:1000'],
            'primary_cta_label' => ['nullable', 'max:191'],
            'secondary_cta_label' => ['nullable', 'max:191'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'content' => ['nullable'],
            'meta_title' => ['nullable', 'max:191'],
            'meta_description' => ['nullable', 'max:1000'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', Rule::in(array_keys(pageStatuses()))],
        ];
    }
}
