<?php

namespace App\Http\Controllers\Maystro;

use App\Http\Controllers\Controller;
use App\Models\MaystroProduct;
use App\Models\Product;
use App\Models\Store;
use App\Services\MaystroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function authenticateStore(Request $request , $id) {
        $maystroService = new MaystroService();
        $response = $maystroService->authenticateStore($id);
        return $response;
    }

    public function generateMaystroProducts(Request $request , $storeId) {
        $store = Store::where('id' , $storeId)->first();
        $products = $store->products;
        $maystroProducts = [];
        $productname = '';
        foreach ($products as $product) {
            $productname = $product->name;
            if (isset($product->properties)) {
                foreach (json_decode($product->properties) as $property) {
                    if (isset($property->values)) {
                        foreach($property->values as $value) {
                            if (isset($value->subProperties)) {
                                foreach($value->subProperties as $key => $subProperty) {
                                    $productname = $product->name . ' ' . $value->value . ' ' . $key;
                                    foreach($subProperty->values as $subPropertyValue) {
                                        $productname = $product->name . ' ' . $value->value . ' ' . $key . ' ' . $subPropertyValue->value;
                                        $maystroInputs = [
                                            'magasinId' => $store->id,
                                            'placetta_id' => $product->id,
                                            'name' => $productname,
                                        ];
                                        $productExist = MaystroProduct::where('name' , $maystroInputs['name'])
                                            ->where('magasinId' , $store->id)->count();
                                        if ($productExist == 0) {
                                            MaystroProduct::create($maystroInputs);
                                            array_push($maystroProducts , $maystroInputs);
                                        }
                                    }
                                }
                            } else {
                                $productname = $product->name . ' ' . $value->value;
                                $maystroInputs = [
                                    'magasinId' => $store->id,
                                    'placetta_id' => $product->id,
                                    'name' => $productname,
                                ];
                                $productExist = MaystroProduct::where('name' , $maystroInputs['name'])
                                    ->where('magasinId' , $store->id)->count();
                                if ($productExist == 0) {
                                    MaystroProduct::create($maystroInputs);
                                    array_push($maystroProducts , $maystroInputs);
                                }
                            }

                        }
                    }
                }
            } else {
                $maystroInputs = [
                    'magasinId' => $store->id,
                    'placetta_id' => $product->id,
                    'name' => $product->name,
                ];
                $productExist = MaystroProduct::where('name' , $maystroInputs['name'])
                    ->where('magasinId' , $store->id)->count();
                if ($productExist == 0) {
                    MaystroProduct::create($maystroInputs);
                    array_push($maystroProducts , $maystroInputs);
                }
            }
        }

        return $maystroProducts;
    }

    public function sendPremiumProductsToMaystro(Request $request , $storeId) {
        $products = MaystroProduct::where('magasinId' , $storeId)->get();
        $maystroService = new MaystroService();
        $maystroStore = $maystroService->authenticateStore($storeId);
        $token = $maystroStore['token'];
        foreach ($products as $product) {
            $response = Http::withHeaders([
                'Authorization'=> 'Token ' . $token
            ])->post(env('MAYSTRO_URL') . 'stores/product_variant/root_product/', [
                'store_id' => $storeId,
                'product_id' => $product->id,
                'logistical_description' => $product->name,
                'source' => 4,
                'externel_id' => $product->id,
            ]);
        }
        return 'products sent to maystro successfully !';

    }

    public function sendOnePremiumProductsToMaystro(Request $request , $productId) {
        $product = MaystroProduct::where('id' , $productId)->first();
        $maystroService = new MaystroService();
        $maystroStore = $maystroService->authenticateStore($product->magasinId);
        $token = $maystroStore['token'];
        $response = Http::withHeaders([
            'Authorization'=> 'Token ' . $token
        ])->post(env('MAYSTRO_URL') . 'stores/product_variant/root_product/', [
            'store_id' => $product->magasinId,
            'product_id' => $product->id,
            'logistical_description' => $product->name,
            'source' => 4,
            'externel_id' => $product->id,
        ]);
        return 'products sent to maystro successfully !';

    }

    public function updateMaytroProductId($productId) {
        $productCount = MaystroProduct::where('id' , 10000)->count();
        if ($productCount > 0) {
            $lastProduct = MaystroProduct::orderBy('id' , 'desc')->first();
            MaystroProduct::where('id' , $productId)->update(['id' => $lastProduct->id + 1]);
        } else {
            MaystroProduct::where('id' , $productId)->update(['id' => 10000]);
        }
        return 'product refreshed !';
    }

    public function sendProductsToMaystro(Request $request , $storeId) {
        $products = Product::where('magasinId' , $storeId)->get();
        $maystroService = new MaystroService();
        $maystroStore = $maystroService->authenticateStore($storeId);
        $token = $maystroStore['token'];
        foreach ($products as $product) {
            $response = Http::withHeaders([
                'Authorization'=> 'Token ' . $token
            ])->post(env('MAYSTRO_URL') . 'stores/product_variant/root_product/', [
                'store_id' => $storeId,
                'product_id' => $product->id,
                'logistical_description' => $product->name,
                'source' => 4,
                'externel_id' => $product->id,
            ]);
        }
        return 'products sent to maystro successfully !';

    }

    public function getMaystroProducts(Request $request , $storeId) {
        $products = MaystroProduct::where('magasinId' , $storeId)->get();
        return $products;
    }

    public function updatePremiumProductsQuantity(Request $request , $storeId) {
        $store = Store::where('id' , $storeId)->first();
        $products = $store->products;
        foreach ($products as $product) {
            $productname = $product->name;
            if (isset($product->properties)) {
                foreach (json_decode($product->properties) as $property) {
                    if (isset($property->values)) {
                        foreach($property->values as $value) {
                            if (isset($value->subProperties)) {
                                foreach($value->subProperties as $key => $subProperty) {
                                    $productname = $product->name . ' ' . $value->value . ' ' . $key;
                                    foreach($subProperty->values as $subPropertyValue) {
                                        $productname = $product->name . ' ' . $value->value . ' ' . $key . ' ' . $subPropertyValue->value;
                                         MaystroProduct::where('name' , $productname)
                                             ->where('magasinId' , $storeId)->update(['quantity' => $subPropertyValue->quantity]);
                                    }
                                }
                            } else {
                                $productname = $product->name . ' ' . $value->value;
                                 MaystroProduct::where('name' , $productname)
                                     ->where('magasinId' , $storeId)->update(['quantity' => $value->quantity]);
                            }

                        }
                    }
                }
            } else {
                $quantity = 0;
                if ($product->stockDispo) {
                    $quantity = $product->stockDispo;
                }
                 MaystroProduct::where('name' , $product->name)
                     ->where('magasinId' , $storeId)->update(['quantity' => $quantity]);
            }
        }

        return 'maystro products quantity updated !';
    }
}
