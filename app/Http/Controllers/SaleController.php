<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    //

    public function index() {
        $orders = Order::paginate(10);
        return $orders;
    }

    public function store(Request $request) {
        $inputs = $request->only(['userId', 'userName', 'totalPrice',
            'delivred', 'delivredAt', 'status', 'approvedAt', 'rejectedAt', 'received',
            'receivedAt', 'canceled','canceledAt', 'deliveryAddress', 'customerPhone', 'wilaya', 'commune',
            'deliveryPrice', 'codeCommune' , 'totalWithDeliveryPrice', 'delivredBy', 'maystroId',
            'deliveryAborted', 'deliveryPostPoned', 'deliveryAbortedAt', 'deliveryPostponedAt']);
        // json : products /
        if (isset($inputs['products'])) {
            $inputs['products'] = json_encode($inputs['products']);
            $storeUser = Store::where('id' , $request->magasinId)->first();
            $customer = User::where('id' , $request->userId)->first();
            $inputs['magasinId'] = $storeUser->store->id;
            $inputs['userId'] = $customer->id;
            $order = Order::create($inputs);
            return 'order created successfully !!';
        } else {
            return 'order without products !!!';
        }
    }

    public function show(Request $request , $id) {
        $order = Order::findOrFail($id);
        return $order;
    }

    public function destroy(Request $request , $id) {
        Order::destroy($id);
        return 'product destroyed successfully';
    }

    public function getStoreSalesWithCount($magasinId) {
        $sales = Order::where([['magasinId' , $magasinId] , ['status' , 'approved']]);
        return ['results' => $sales->paginate(10) , 'count' => $sales->count()];
    }

    public function getAllStoreSales(Request $request , $magasinId) {
        $orders = Order::where('magasinId' , $magasinId)
            ->where('status' , 'approved')->orderBy('approvedAt' , 'desc')->get();
        return $orders;
    }

    public function getCustomerShoppingWithCount($userId) {
        $sales = Order::where([['userId' , $userId] , ['status' , 'approved']]);
        return ['results' => $sales->paginate(10) , 'count' => $sales->count()];
    }

    public function getAllCustomerShopping(Request $request , $userId) {
        $orders = Order::where('userId' , $userId)
            ->where('status' , 'approved')->with(['store' => function ($query) {
                $query->withTrashed()->select('id', 'title' , 'mobile' , 'address' , 'reviews');
            }])->get();
        return $orders;
    }
}
