<?php

namespace App\Models;

use App\Models\RelationShips\CaiseRelationShips;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caisse extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CaiseRelationShips;
    protected $guarded = [];

    protected $cast = [
        'amount' => 'double',
        'currency_base' => 'double'
    ];
}
