<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'term',
        'stripe_price_id','features'
    ];

    protected $casts = [
        'features' => 'array', // Automatically casts JSON to array
    ];
}
