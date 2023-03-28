<?php


namespace App\Models\RelationShips;

use App\Models\bookmarkedProducts;
use App\Models\bookmarkedStores;
use App\Models\Card;
use App\Models\Store;
use App\Models\Order;

trait UserRelationShips
{
    public function store()
    {
        return $this->hasOne(Store::class, 'userId', 'id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'userId', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'userId', 'id');
    }

    public function bookmarkProducts()
    {
        return $this->hasMany(bookmarkedProducts::class, 'userId', 'id');
    }

    public function bookmarkStores()
    {
        return $this->hasMany(bookmarkedStores::class, 'userId', 'id');
    }

}