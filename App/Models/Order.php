<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Заказ
class Order extends Model
{
    protected $fillable = [
        'description',
        'load_from',
        'load_to',
        'unload_from',
        'unload_to',
        'cargo_weight',
        'cargo_height',
        'cargo_volume',
        'load_country',
        'load_city',
        'load_address',
        'unload_country',
        'unload_city',
        'unload_address',
        'is_archived',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}