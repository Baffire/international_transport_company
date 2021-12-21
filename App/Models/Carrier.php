<?php

namespace App\Models;

use App\Models\Tariffs\TariffFixed;
use App\Models\Tariffs\TariffPercentage;
use Illuminate\Database\Eloquent\Model;

// Перевозчик
class Carrier extends Model
{
    protected $fillable = [
        'name',
    ];

    public function tariff()
    {
        return $this->morphTo();
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function getBidFee($bid)
    {
        switch ($this->tariff_type) {
            case TariffFixed::class:
                $fee = $this->tariff->amount;
                $fee_currency = $this->tariff->currency;
                break;

            case TariffPercentage::class:
                $fee = $bid->price * $this->tariff->rate / 100;
                $fee_currency = $bid->currency;
                break;
            
            default:
                $fee = 0;
                $fee_currency = $bid->currency;
                break;
        }
        return [
            'fee' => $fee,
            'fee_currency' => $fee_currency,
        ];
    }
}