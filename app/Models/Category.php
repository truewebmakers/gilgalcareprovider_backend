<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'feature_image',
        'details',
        'location',
        'status'
    ];

    public function getFeatureImageAttribute($value)
    {
        if ($value) {
            // Prepend your AWS S3 bucket URL to the profile_pic path
            return  config('constants.image_url'). $value;
        }
        return null;
    }
    public function businessListings()
        {
            return $this->belongsToMany(BusinessListing::class, 'category_listings');
        }

}
