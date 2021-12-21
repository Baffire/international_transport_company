<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Сделка
class Deal extends Model
{
    protected $fillable = [
        'fee',
        'fee_currency',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }
}