<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Комментарий к ставке (обмен данными между грузодателем и перевозчиком)
class Reply extends Model
{
    protected $fillable = [
        'comment',
    ];

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    public function commenter()
    {
        return $this->morphTo();
    }
}