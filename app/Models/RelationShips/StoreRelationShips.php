<?php


namespace App\Models\RelationShips;


use App\Models\Order;
use App\Models\Product;

trait StoreRelationShips
{

    public function orders()
    {
        return $this->hasMany(Order::class, 'magasinId', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'magasinId', 'id');
    }

}