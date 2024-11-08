<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    protected static function boot()
    {
        parent::boot();

        // Generate a UUID before creating a new instance
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}
