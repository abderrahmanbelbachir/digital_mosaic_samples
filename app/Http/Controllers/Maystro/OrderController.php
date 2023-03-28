<?php

namespace App\Http\Controllers\Maystro;

use App\Http\Controllers\Controller;
use App\Models\MaystroProduct;
use App\Models\Order;
use App\Models\Store;
use App\Services\MaystroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    public function sendPremiumOrderToMaystro(Request $request , $orderId) {
        $order = Order::where('id' , $orderId)->with(['products' => function ($q) {
            $q->with(['details']);
        }])->first();
        $products = $order->products;
        $maystroOrder = [];
        $maystroOrder['products'] = [];
        $maystroOrder['product_price'] = 0;
        foreach ($products as $product) {
            $productname = $product->details->name;
            if (isset($product->properties)) {
                foreach ($product->properties as $property) {
                    if (isset($property->values)) {
                        foreach($property->values as $value) {
                            if (isset($value->subProperties) && !empty($value->subProperties)) {
                                foreach($value->subProperties as $key => $subProperty) {
                                    $productname = $product->details->name . ' ' . $value->value . ' ' . $key;
                                    foreach($subProperty->values as $subPropertyValue) {
                                        $productname = $product->details->name . ' ' . $value->value . ' ' . $key . ' ' . $subPropertyValue->value;

                                        $maystroProduct = MaystroProduct::where('name' , $productname)
                                            ->where('magasinId' , $order->magasinId)->first();


                                        $maystroInputs = [
                                            'product_id' => $maystroProduct->id,
                                            'quantity' => $subPropertyValue->quantity,
                                        ];
                                        array_push($maystroOrder['products'] , $maystroInputs);
                                        $maystroOrder['product_price'] = $maystroOrder['product_price'] + ($product->price * $product->orderQuantity);

                                    }
                                }
                            } else {
                                $productname = $product->name . ' ' . $value->value;

                                $maystroProduct = MaystroProduct::where('name' , $productname)
                                    ->where('magasinId' , $order->magasinId)->first();

                                $maystroInputs = [
                                    'product_id' => $maystroProduct->id,
                                    'quantity' => $value->quantity,
                                ];
                                array_push($maystroOrder['products'] , $maystroInputs);
                                $maystroOrder['product_price'] = $maystroOrder['product_price'] + ($product->price * $product->quantity);

                            }

                        }
                    }
                }
            } else {
                $maystroProduct = MaystroProduct::where('name' , $product->details->name)
                    ->where('magasinId' , $order->magasinId)->first();

                $maystroInputs = [
                    'product_id' => $maystroProduct->id,
                    'quantity' => $product->quantity,
                ];
                array_push($maystroOrder['products'] , $maystroInputs);
                $maystroOrder['product_price'] = $maystroOrder['product_price'] + ($product->price * $product->quantity);

            }
        }

        $maystroOrder['destination_text'] = $order->deliveryAddress;
        $maystroOrder['customer_name'] = $order->userName;
        $maystroOrder['customer_phone'] = $order->customerPhone;
        $maystroOrder['express'] = false;
        $maystroOrder['wilaya'] = $order->wilaya;
        $maystroOrder['commune'] = $order->commune;
        $maystroOrder['note_to_driver'] = 'empty';

        if ($order->totalWithDeliveryPrice) {
            $maystroOrder['product_price'] = $order->totalWithDeliveryPrice;
        } else if ($order->deliveryPrice && $order->totalPrice) {
            $maystroOrder['product_price'] = $order->totalPrice + $order->deliveryPrice;
        }

        $maystroService = new MaystroService();
        $maystroStore = $maystroService->authenticateStore($order->magasinId);
        $token = $maystroStore['token'];
        $response = Http::withHeaders([
            'Authorization'=> 'Token ' . $token
        ])->post(env('MAYSTRO_URL') . 'stores/orders_store/', $maystroOrder);

        Order::where('id' , $orderId)->update([

            'maystroId' => $response['display_id'],
            'maystro_db_id' => $response['id'] ,

        ]);
        return $response;
    }

    public function sendOrderToMaystro(Request $request , $orderId) {
        $order = Order::where('id' , $orderId)->
        with(['products' => function ($q) {
            $q->with(['details']);
        }])->first();
        $products = $order->orderProducts;
        $maystroOrder = [];
        $maystroOrder['products'] = [];
        $maystroOrder['product_price'] = 0;
        foreach ($products as $product) {

            $maystroInputs = [
                'product_id' => $product->productId,
                'quantity' => $product->quantity,
            ];
            array_push($maystroOrder['products'] , $maystroInputs);
            $maystroOrder['product_price'] = $maystroOrder['product_price'] + ($product->details->price * $product->quantity);
        }

        $maystroOrder['destination_text'] = $order->deliveryAddress;
        $maystroOrder['customer_name'] = $order->userName;
        $maystroOrder['customer_phone'] = $order->customerPhone;
        $maystroOrder['express'] = false;
        $maystroOrder['wilaya'] = $order->wilaya;
        $maystroOrder['commune'] = $order->commune;
        $maystroOrder['note_to_driver'] = $order->notes ? $order->notes : 'empty';

        if ($order->totalWithDeliveryPrice) {
            $maystroOrder['product_price'] = $order->totalWithDeliveryPrice;
        } else if ($order->deliveryPrice && $order->totalPrice) {
            $maystroOrder['product_price'] = $order->totalPrice + $order->deliveryPrice;
        }

        $maystroService = new MaystroService();
        $maystroStore = $maystroService->authenticateStore($order->magasinId);
        $token = $maystroStore['token'];
        $response = Http::withHeaders([
            'Authorization'=> 'Token ' . $token
        ])->post(env('MAYSTRO_URL') . 'stores/orders_store/', $maystroOrder);

        if (!isset($response['display_id']) || !$response['display_id']) {
            return [$response];
        }
        Order::where('id' , $orderId)->update(
            [
                'maystroId' => $response['display_id'] ,
                'maystro_db_id' => $response['id'] ,

            ]
        );
        return $response;
    }

    public function cancelMaystroOrder($magasinId , $orderId) {
        $maystroService = new MaystroService();
        $maystroStore = $maystroService->authenticateStore($magasinId);
        $token = $maystroStore['token'];
        $input = ['status' => '50' , 'id' => $orderId];
        $response = Http::withHeaders([
            'Authorization'=> 'Token ' . $token
        ])->post(env('MAYSTRO_URL') . 'shared/status/'.$token, $input);

    }

    public function refreshOrdersStatus() {
        $stores = Store::whereHas('orders', function($query) {
            $query->whereNotNull('maystroId');
        })->whereNotNull('validatedAt')->get();
        $maystroOrders = [];
        foreach ($stores as $store) {
            $page = 1;
            $endOfResults = false;
            while (!$endOfResults) {
                $maystroService = new MaystroService();
                $maystroStore = $maystroService->authenticateStore($store->id);
                $token = $maystroStore['token'];
                $input = [];
                $response = Http::withHeaders([
                    'Authorization'=> 'Token ' . $token
                ])->get(env('MAYSTRO_URL') . 'stores/orders/?page='.$page, []);
                $maystroOrders = array_merge($maystroOrders , $response['list']['results']);
                if (($page*19) < $response['list']['count']) {
                    $page = $page + 1;
                } else {
                    $endOfResults = true;
                }
            }
        }

        foreach ($maystroOrders as $maystroOrder) {
            $parsedOrder = Order::where('maystroId' , $maystroOrder['display_id'])->first();

            if ($maystroOrder['aborted_at'] && $maystroOrder['postponed_at']
                && $parsedOrder) {

                $abortedDate = Date::parse($maystroOrder['aborted_at'])
                    ->format('Y-m-d H:i:s');
                $postponedAt = Date::parse($maystroOrder['postponed_at'])
                    ->format('Y-m-d H:i:s');

                if ($abortedDate > $postponedAt) {
                    $inputs = [
                        'deliveryAborted' => true,
                        'deliveryAbortedAt' => Date::parse($maystroOrder['aborted_at'])
                            ->format('Y-m-d H:i:s'),
                        'delivred' => false,
                        'deliveryPostPoned' => false,
                    ];
                } else {
                    $inputs = [
                        'deliveryPostPoned' => true,
                        'deliveryPostponedAt' => Date::parse($maystroOrder['postponed_at'])
                            ->format('Y-m-d H:i:s'),
                        'delivred' => false,
                        'deliveryAborted' => false,
                    ];
                }
                $parsedOrder->update($inputs);


            } else if ($maystroOrder['delivered_at'] && $maystroOrder['postponed_at']
                && $parsedOrder) {

                $deliveredAt = Date::parse($maystroOrder['delivered_at'])
                    ->format('Y-m-d H:i:s');
                $postponedAt = Date::parse($maystroOrder['postponed_at'])
                    ->format('Y-m-d H:i:s');

                if ($deliveredAt > $postponedAt) {
                    $inputs = [
                        'deliveryAborted' => false,
                        'deliveryAbortedAt' => Date::parse($maystroOrder['delivered_at'])
                            ->format('Y-m-d H:i:s'),
                        'delivred' => true,
                        'deliveryPostPoned' => false,
                    ];
                } else {
                    $inputs = [
                        'deliveryPostPoned' => true,
                        'deliveryPostponedAt' => Date::parse($maystroOrder['postponed_at'])
                            ->format('Y-m-d H:i:s'),
                        'delivred' => false,
                        'deliveryAborted' => false,
                    ];
                }
                $parsedOrder->update($inputs);


            }else if ($maystroOrder['delivered_at'] && $parsedOrder && !$parsedOrder->delivred) {
                $inputs = [
                    'delivred' => true,
                    'delivredAt' => Date::parse($maystroOrder['delivered_at'])->format('Y-m-d H:i:s'),
                    'deliveryAborted' => false,
                    'deliveryPostPoned' => false,

                ];
                $parsedOrder->update($inputs);

            } elseif ($maystroOrder['aborted_at'] && $parsedOrder && !$parsedOrder->deliveryAborted) {
                $inputs = [
                    'deliveryAborted' => true,
                    'deliveryAbortedAt' => Date::parse($maystroOrder['aborted_at'])->format('Y-m-d H:i:s')
                ];
                $parsedOrder->update($inputs);

            } elseif ($maystroOrder['postponed_at'] && $parsedOrder && !$parsedOrder->deliveryPostPoned) {
                $inputs = [
                    'deliveryPostPoned' => true,
                    'deliveryPostponedAt' => Date::parse($maystroOrder['postponed_at'])->format('Y-m-d H:i:s')
                ];
                $parsedOrder->update($inputs);
            } elseif (!$maystroOrder['delivered_at'] && !$maystroOrder['aborted_at'] && !$maystroOrder['postponed_at']
                && $parsedOrder && ($parsedOrder->delivred || $parsedOrder->deliveryAborted || $parsedOrder->deliveryPostPoned)) {

                $inputs = [
                    'delivred' => false,
                    'delivredAt' => null,
                    'deliveryAborted' => false,
                    'deliveryAbortedAt' => null,
                    'deliveryPostPoned' => false,
                    'deliveryPostponedAt' => null

                ];
                $parsedOrder->update($inputs);
            }
        }

        return 'orders status refreshed !';

    }

    public function refreshOrdersId() {
        $stores = Store::whereHas('orders', function($query) {
            $query->whereNotNull('maystroId');
        })->get();
        $maystroOrders = [];
        foreach ($stores as $store) {
            $page = 1;
            $endOfResults = false;
            while (!$endOfResults) {
                $maystroService = new MaystroService();
                $maystroStore = $maystroService->authenticateStore($store->id);
                $token = $maystroStore['token'];
                $input = [];
                $response = Http::withHeaders([
                    'Authorization'=> 'Token ' . $token
                ])->get(env('MAYSTRO_URL') . 'stores/orders/?page='.$page, []);
                $maystroOrders = array_merge($maystroOrders , $response['list']['results']);
                if (($page*19) < $response['list']['count']) {
                    $page = $page + 1;
                } else {
                    $endOfResults = true;
                }
            }
        }
        foreach ($maystroOrders as $maystroOrder) {
            $inputs = [
                'maystro_db_id' => $maystroOrder['id']
            ];
            Order::where('maystroId' , $maystroOrder['display_id'])->update($inputs);
        }
        return 'orders id refreshed !';

    }
}
