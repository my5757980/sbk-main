<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $guarded = [];

    public function getFaviconPathAttribute()
    {
        if (!empty($this->favicon) && file_exists(uploadsDir('front').$this->favicon)) {
            return asset('uploads/front/' . $this->favicon);
        }
        return asset('assets/admin/app-assets/images/ico/favicon.ico');
    }

    public function getLogoPathAttribute()
    {
        if (!empty($this->logo) && file_exists(uploadsDir('front').$this->logo)) {
            return asset('uploads/front/' . $this->logo);
        }
        return asset('assets/admin/app-assets/images/ico/favicon.ico');
    }
}
