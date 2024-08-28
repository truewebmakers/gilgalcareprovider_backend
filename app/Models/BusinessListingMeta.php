<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class BusinessListingMeta extends Model
{
    use HasFactory;

    protected $table = 'business_listing_meta';

    protected $fillable = [
        'business_listing_id',
        'gallery_image',
        'uuid'
    ];

    public function businessListing()
    {
        return $this->belongsTo(BusinessListing::class);
    }

    public function getGalleryImageAttribute($value)

    {
        if ($value) {
            // Prepend your AWS S3 bucket URL to the profile_pic path
            return  config('constants.image_url'). $value;
        }
        return null;
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
