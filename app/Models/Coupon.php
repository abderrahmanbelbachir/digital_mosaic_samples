<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\RelationShips\CouponRelationShips;


class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CouponRelationShips;
    protected $guarded = [];

    protected $cast = [
        'value' => 'double',

    ];
}
