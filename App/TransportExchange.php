<?php

namespace App;

use App\Models\Bid;
use App\Models\Carrier;
use App\Models\Deal;
use App\Models\Order;
use App\Models\Provider;
use App\Models\Reply;
use Illuminate\Database\Eloquent\Model;

class TransportExchange extends Model
{
    public function createProvider($name)
    {
        $provider = new Provider([
            'name' => $name,
        ]);
        $provider->save();
        return $provider;
    }
    
    public function createCarrier($name, $tariff)
    {
        $carrier = new Carrier([
            'name' => $name,
        ]);
        if ($tariff) {
            $carrier->tariff()->associate($tariff);
        }
        $carrier->save();
        return $carrier;
    }
    
    public function createTariff($tariffType, $tariffData)
    {
        $tariff = new $tariffType;
        foreach ($tariffData as $key => $value) {
            $tariff->$key = $value;
        }
        $tariff->save();
        return $tariff;
    }

    public function placeOrder(
        $provider,
        $description,
        $load_from,
        $load_to,
        $unload_from,
        $unload_to,
        $cargo_weight,
        $cargo_height,
        $cargo_volume,
        $load_country,
        $load_city,
        $load_address,
        $unload_country,
        $unload_city,
        $unload_address
    )
    {
        $order = new Order([
            'description' => $description,
            'load_from' => $load_from,
            'load_to' => $load_to,
            'unload_from' => $unload_from,
            'unload_to' => $unload_to,
            'cargo_weight' => $cargo_weight,
            'cargo_height' => $cargo_height,
            'cargo_volume' => $cargo_volume,
            'load_country' => $load_country,
            'load_city' => $load_city,
            'load_address' => $load_address,
            'unload_country' => $unload_country,
            'unload_city' => $unload_city,
            'unload_address' => $unload_address,
        ]);
        $order->provider()->associate($provider);
        $order->save();
        return $order;
    }
    
    public function placeBid($carrier, $order, $price, $currency, $comment)
    {
        $bid = new Bid([
            'price' => $price,
            'currency' => $currency,
            'comment' => $comment,
        ]);
        $bid->carrier()->associate($carrier);
        $bid->order()->associate($order);
        $bid->save();
        return $bid;
    }
    
    public function makeDeal($order, $bid, $carrier)
    {
        $fee = $carrier->getBidFee($bid);
        $deal = new Deal([
            'fee' => $fee['fee'],
            'fee_currency' => $fee['fee_currency'],
        ]);
        $deal->order()->associate($order);
        $deal->bid()->associate($bid);
        $deal->save();
        return $deal;
    }
    
    public function archiveOrder($order)
    {
        $order->is_archived = true;
        $order->save();
        return $order;
    }
    
    public function addComment($commenter, $bid, $comment)
    {
        $reply = new Reply([
            'comment' => $comment,
        ]);
        $reply->commenter()->associate($commenter);
        $reply->bid()->associate($bid);
        $reply->save();
        return $reply;
    }
    
    public function getActiveOrdersBetweenCountries($sourceCountry, $destCountry)
    {
        $orders = Order::where('load_country', $sourceCountry)
            ->where('unload_country', $destCountry)
            ->where('is_archived', false)
            ->whereDoesntHave('bids', function ($bids) {
                $bids->has('deal');
            })
            ->get()
            ->toArray();
        return $orders;
    }
    
    public function getCarrierFeesForPeriod($carrier, $period_from, $period_to)
    {
        $bids = $carrier->bids()
            ->whereHas('deal', function($q) use($period_from, $period_to) {
                $q->whereDate('created_at','>=', $period_from)
                    ->whereDate('created_at','<=', $period_to);
            })
            ->with('deal')
            ->get();
        
        $fees = [];
        
        foreach ($bids as $bid) {
            $fees[] = $carrier->getBidFee($bid);
        }
        
        return $fees;
    }
    
    public function getDeals()
    {
        $deals = Deal::query()->get();
        return $deals;
    }
}