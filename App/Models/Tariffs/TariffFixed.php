<?php

namespace App\Models\Tariffs;

use Illuminate\Database\Eloquent\Model;

// Фикс. сумма
class TariffFixed extends Model
{
    protected $table = 'tariff_fixed';

    protected $fillable = [
        'amount',
        'currency',
    ];
}