<?php


namespace App\Models\RelationShips;


use App\Models\bookmarkedProducts;
use App\Models\bookmarkedStores;
use App\Models\Card;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Store;

trait ProductRelationShips
{

    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'magasinId');
    }

    public function orders()
    {
        return $this->hasMany(OrderProduct::class, 'productId', 'id');
    }



}