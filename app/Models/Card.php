<?php

namespace App\Models;

use App\Models\RelationShips\CardRelationShips;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CardRelationShips;
    protected $guarded = [];

    protected $cast = [
        'totalPrice' => 'double',
        'deliveryPrice' => 'double',
        'totalWithDelivery' => 'double',
    ];
}
