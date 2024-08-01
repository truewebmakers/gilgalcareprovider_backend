<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class BusinessListingMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_listing_id',
        'gallery_image',
        'uuid'
    ];

    public function businessListing()
    {
        return $this->belongsTo(BusinessListing::class);
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
