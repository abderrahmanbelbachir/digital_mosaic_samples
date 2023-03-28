<?php


namespace App\Models\RelationShips;


use App\Models\Order;

trait CouponRelationShips
{

    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon', 'code');
    }
}