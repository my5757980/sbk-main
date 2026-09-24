<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyListing extends Model
{
    use HasFactory;

    protected $guarded;

    protected $appends = ['amenities_relation','features_relation','property_safety_relation','images_url','document_url','tax_document_url','avg_rating'];

    public function getImagesAttribute($value) {
        return json_decode($value);
    }

    public function getDocumentAttribute($value) {
        return json_decode($value);
    }

    public function getTaxDocumentAttribute($value) {
        return json_decode($value);
    }

    public function getAmenitiesAttribute($value) {
        return json_decode($value);
    }

    public function getFeaturesAttribute($value) {
        return json_decode($value);
    }

    public function getSafetyMeasuresAttribute($value) {
        return json_decode($value);
    }

    public function propertyViewRelation()
    {
        return $this->hasOne(PropertyView::class,'id','property_view');
    }

    public function getPropertySafetyRelationAttribute()
    {
        return PropertySafety::whereIn('id', $this->safety_measures)->get();
//        return $this->hasOne(PropertySafety::class,'id','safety_measures');
    }

    public function getAvgRatingAttribute()
    {
        $rating = 0;
        $ratings = Review::where('property_id', $this->id)
            ->get();
        foreach ($ratings as $k => $v) {
            $rating += $v->star;
        }
        if ($rating == 0) {
            $rating = 0;
        } else {
            $total_rating = count($ratings);
            $rating = $rating / $total_rating;
        }
        return round($rating, 2);
    }

    public function getImagesUrlAttribute()
    {
        $imageFilenames = $this->images;
        $imageUrls = [];
        foreach ($imageFilenames as $filename) {
            $imageUrl = asset("uploads/" . $filename);
            $imageUrls[] = $imageUrl;
        }

        return $imageUrls;
    }

    public function getDocumentUrlAttribute()
    {
        $imageFilenames = $this->document;
        $imageUrls = [];
        foreach ($imageFilenames as $filename) {
            $imageUrl = asset("uploads/" . $filename);
            $imageUrls[] = $imageUrl;
        }

        return $imageUrls;
    }

    public function getTaxDocumentUrlAttribute()
    {
        $imageFilenames = $this->tax_document;
        $imageUrls = [];
        foreach ($imageFilenames as $filename) {
            $imageUrl = asset("uploads/" . $filename);
            $imageUrls[] = $imageUrl;
        }

        return $imageUrls;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'property_id');
    }

    public function getRatingAvgAttribute()
    {
        return 0;
    }

    public function propertyTypeRelation()
    {
        return $this->hasOne(PropertyType::class,'id','type');
    }

    public function getAmenitiesRelationAttribute()
    {
        return PropertyAmenities::whereIn('id', $this->amenities)->get();
    }

    public function getFeaturesRelationAttribute()
    {
        return PropertyFeature::whereIn('id', $this->features)->get();
    }
}
