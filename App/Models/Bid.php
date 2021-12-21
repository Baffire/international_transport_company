<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Ставка
class Bid extends Model
{
    protected $fillable = [
        'price',
        'currency',
        'comment',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function deal()
    {
        return $this->hasOne(Deal::class);
    }
}