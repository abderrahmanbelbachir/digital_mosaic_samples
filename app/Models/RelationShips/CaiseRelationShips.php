<?php


namespace App\Models\RelationShips;

use App\Models\User;

trait CaiseRelationShips
{

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}