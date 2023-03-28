<?php


namespace App\Services;


use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductService
{

    public function updateStockDispo($product) {
        $stockDispo = 0;
        if (isset($product->properties)) {
            foreach (json_decode($product->properties) as $property) {
                if (isset($property->values)) {
                    foreach($property->values as $value) {
                        if (isset($value->subProperties)) {
                            foreach($value->subProperties as $key => $subProperty) {
                                foreach($subProperty->values as $subPropertyValue) {
                                    $stockDispo = $stockDispo + $subPropertyValue->quantity;
                                }
                            }
                        } else {
                            $stockDispo = $stockDispo + $value->quantity;
                        }
                    }
                }
            }
        } else {
            $stockDispo = $product->stockDispo;
        }
        Product::where('id' , $product->id)->update(['stockDispo' => $stockDispo]);
    }

}