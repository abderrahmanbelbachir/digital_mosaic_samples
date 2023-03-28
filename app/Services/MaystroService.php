<?php


namespace App\Services;


use App\Models\MaystroProduct;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MaystroService
{

    public function authenticateStore($id) {
        $store = Store::where('id' , $id)->first();
        $response = Http::post(env('MAYSTRO_URL') . 'store/auth/', [
            'username' => $store->mobile,
            'password' => '##Pass.word**',
        ]);
        return $response;
    }

    public function refreshOrdersStatus() {
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

            if ($maystroOrder['delivered_at']) {
                $inputs = [
                    'delivred' => true,
                    'delivredAt' => Date::parse($maystroOrder['delivered_at'])->format('Y-m-d H:i:s')
                ];
                Order::where('maystroId' , $maystroOrder['display_id'])->update($inputs);

            } elseif ($maystroOrder['aborted_at']) {
                $inputs = [
                    'deliveryAborted' => true,
                    'deliveryAbortedAt' => Date::parse($maystroOrder['aborted_at'])->format('Y-m-d H:i:s')
                ];
                Order::where('maystroId' , $maystroOrder['display_id'])->update($inputs);

            } elseif ($maystroOrder['postponed_at']) {
                $inputs = [
                    'deliveryPostPoned' => true,
                    'deliveryPostponedAt' => Date::parse($maystroOrder['postponed_at'])->format('Y-m-d H:i:s')
                ];
                Order::where('maystroId' , $maystroOrder['display_id'])->update($inputs);
            }
        }
        return 'orders status refreshed !';
    }


}
