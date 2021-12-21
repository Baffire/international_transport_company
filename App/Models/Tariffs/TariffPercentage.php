<?php

namespace App\Models\Tariffs;

use Illuminate\Database\Eloquent\Model;

// Процент от суммы
class TariffPercentage extends Model
{
    protected $table = 'tariff_percentage';

    protected $fillable = [
        'rate',
    ];
}