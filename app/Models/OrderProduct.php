<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{

    public $table = "order_product_relationship";

    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
}