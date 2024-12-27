<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class BusinessListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
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
        'added_by',
        'status',
        'page_views',
        'total_shares',
        'business_open_hours'
    ];

    protected $casts = [
        'business_open_hours' => 'array', // or 'json' depending on your version
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_listings');
    }

    public function getLogoAttribute($value)
    {
        if ($value) {
            // Prepend your AWS S3 bucket URL to the profile_pic path
            return  config('constants.image_url'). $value;
        }
        return null;
    }

    public function getFeaturedImageAttribute($value)
    {
        if ($value) {
            // Prepend your AWS S3 bucket URL to the profile_pic path
            return  config('constants.image_url') . $value;
        }
        return null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function meta()
    {
        return $this->hasMany(BusinessListingMeta::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedbacks::class, 'business_listing_id');
    }
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }
}
