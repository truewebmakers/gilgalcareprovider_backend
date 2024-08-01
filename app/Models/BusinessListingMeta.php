<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessListingMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_listing_id',
        'gallery_image',
    ];

    public function businessListing()
    {
        return $this->belongsTo(BusinessListing::class);
    }
}
