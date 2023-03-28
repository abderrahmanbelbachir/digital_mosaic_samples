<?php

namespace App\Models;

use App\Models\RelationShips\BookmarkStoreRelationShips;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class bookmarkedStores extends Model
{
    use HasFactory;
    use SoftDeletes;
    use BookmarkStoreRelationShips;
    protected $guarded = [];
}
