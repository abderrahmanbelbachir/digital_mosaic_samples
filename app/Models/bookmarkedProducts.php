<?php

namespace App\Models;

use App\Models\RelationShips\BookmarkProductRelationShips;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class bookmarkedProducts extends Model
{
    use HasFactory;
    use SoftDeletes;
    use BookmarkProductRelationShips;
    protected $guarded = [];
}
