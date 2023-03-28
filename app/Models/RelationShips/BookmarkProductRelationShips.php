<?php


namespace App\Models\RelationShips;


use App\Models\Product;

trait BookmarkProductRelationShips
{

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'productId');
    }

}