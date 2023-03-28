<?php


namespace App\Models\RelationShips;


use App\Models\Store;

trait OrderRelationShips
{

    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'magasinId');
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'orderId', 'id');
    }
}
