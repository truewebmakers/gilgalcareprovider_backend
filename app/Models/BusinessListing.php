<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_title',
        'listing_description',
        'category_id',
        'tagline',
        'price_range',
        'price_from',
        'price_to',
        'features_information',
        'location',
        'address',
        'map_lat',
        'map_long',
        'email',
        'website',
        'phone',
        'facebook',
        'twitter',
        'google_plus',
        'instagram',
        'featured_image',
        'logo',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function meta()
    {
        return $this->hasMany(BusinessListingMeta::class);
    }
}
