<?php

namespace App\Models;

use App\Models\RelationShips\UserRelationShips;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;
    use UserRelationShips;

    protected $guarded = [];

    protected $casts = [
        'isFreeDelivery' => 'int',
        'wilayaCode' => 'int',
        'communeId' => 'int',
        'isValidated' => 'int',
        'ratingAverage' => 'double',
        'deliveryPrice' => 'double',
        'codeCommune' => 'int',
        'hasFreeDelivery' => 'int',
    ];
}
