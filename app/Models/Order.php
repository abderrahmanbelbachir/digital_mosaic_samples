<?php

namespace App\Models;

use App\Models\RelationShips\OrderRelationShips;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    use OrderRelationShips;
    protected $guarded = [];

    protected $casts = [
        'magasinId' => 'int',
        'userId' => 'int',
        'totalPrice' => 'double',
        'delivred' => 'int',
        'received' => 'int',
        'canceled' => 'int',
        'deliveryPrice' => 'double',
        'codeCommune' => 'int',
        'totalWithDeliveryPrice' => 'double',
        'deliveryAborted' => 'int',
        'deliveryPostPoned' => 'int',
        'paid_to_store' => 'int',
        'payment_received' => 'int',
        'commission' => 'int',

    ];
}
