<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
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
            'site_title'      => 'required|max:190',
            'contact_email'   => 'required|email:strict,filter|max:190',
            'footer_sentence' => 'required|max:65535',
            'contact_phone'   => 'max:190',
            'address'         => 'max:190',
            'facebook'        => 'max:190',
            'twitter'         => 'max:190',
            'pinterest'       => 'max:190',
            'pinterest'       => 'max:190',
            'copyright'       => 'max:65535',
            'footer_scripts'  => 'max:65535'
        ];
    }
}
