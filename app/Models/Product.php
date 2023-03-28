<?php

namespace App\Models;

use App\Models\RelationShips\ProductRelationShips;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ProductRelationShips;
    protected $guarded = [];

    protected $casts = [
        'price' => 'double',
        'stockDispo' => 'int',
        'cardQuantity' => 'int',
        'cardPrice' => 'double',
        'orderQuantity' => 'int',
        'orderPrice' => 'double',
        'isPublished' => 'int',
        'quantityOnOrder' => 'int',
        'ratingAverage' => 'double',
        'homePlace' => 'int',
        'step' => 'int',
        'isFreeDelivery' => 'int',
        'isBook' => 'int',
        'totalPages' => 'int',
        'magasinId' => 'int'
    ];
}
