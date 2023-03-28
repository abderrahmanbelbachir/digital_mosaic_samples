<?php

namespace App\Models;

use App\Models\RelationShips\OrderRelationShips;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaystroProduct  extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    protected $cast = [
        'quantity' => 'int'
    ];
}
