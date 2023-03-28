<?php

namespace App\Models;

use App\Models\RelationShips\StoreRelationShips;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory;
    use SoftDeletes;
    use StoreRelationShips;

    protected $guarded = [];

    protected $casts = [
        'rate' => 'double',
        'delivery' => 'int',
        'isPublished' => 'int',
        'ratingAverage' => 'double',
        'planPrice' => 'double',
        'homePlace' => 'int',
        'clickAndCollect' => 'int',
        'validatedAtTime' => 'int',
        'isFreeDelivery' => 'int',
        'commission' => 'int'
    ];
}
