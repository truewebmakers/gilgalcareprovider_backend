<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedbacks extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'name',
        'email',
        'review',
        'rating',
        'business_listing_id',
        'user_id'
    ];

    // Define the relationship to the BusinessListing model if necessary

    public function businessListing()
    {
        return $this->belongsTo(BusinessListing::class, 'business_listing_id');
    }
}
