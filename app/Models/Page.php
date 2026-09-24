<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = [];

    public static function getPageDetailsWithMedia($where= [])
    {
    	$query = self::select(
    		'pages.*',
    		'media_files.filename'
    	)
    		->leftJoin('media_files', 'media_files.id', 'pages.media_file_id')
    		->where($where)
    		->first();

    	return $query;
    }

    public function setMetaKeywordsAttribute($value)
    {
        $this->attributes['meta_keywords'] = json_encode($value);
    }

    // Define an accessor to get the keywords as an array
    public function getMetaKeywordsAttribute($value)
    {
        return json_decode($value);
    }
}
