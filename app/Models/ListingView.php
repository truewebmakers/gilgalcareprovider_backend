<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingView extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_listing_id',
        'ip_address',
    ];

    public function businessListing()
    {
        return $this->belongsTo(BusinessListing::class, 'business_listing_id');
    }
}
