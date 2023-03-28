<?php


namespace App\Models\RelationShips;


use App\Models\Store;

trait BookmarkStoreRelationShips
{

    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'magasinId');
    }

}