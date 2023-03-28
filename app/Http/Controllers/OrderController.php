<?php

namespace App\Http\Controllers;

use App\Mail\OrderCreated;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\MaystroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PDF;

class OrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $orders = Order::with(['store' => function ($query) {
            $query->withTrashed()->select('id', 'title' , 'mobile');
        }]);
        if($request->status == 'inProgress')
    {
        $orders = $orders->where('status', 'inProgress');
    }
	else if($request->status == 'approved'){
        $orders = $orders->where('status', 'approved');
    }
	else if($request->status == 'canceled'){
        $orders = $orders->whereIn('status', ['canceled','canceled_by_store']);
    }
	else if($request->status == 'rejected'){
        $orders = $orders->where('status', 'rejected');
    }
    else if($request->status == 'delivered'){
        $orders = $orders->where('delivred', 1)->where('deliveryAborted', 0);
    }
	else if($request->status == 'deliveryInProgress'){
        $orders = $orders->where('status', 'approved')
        ->where('delivred', 0)->where('deliveryAborted', 0);
    }
	else if($request->status == 'deliveryAborted'){
        $orders = $orders->where('status', 'approved')
        ->where('deliveryAborted', 1);
    }
    else if($request->status == 'deliveryPostPoned'){
        $orders = $orders->where('status', 'approved')
        ->where('delivred', 0)->where('deliveryPostPoned', 1);
    }
	else if($request->status == 'deliveredByPlacetta'){
        $orders = $orders->where('delivred', 1)
        ->where('delivredBy', 'placetta')->where('deliveryAborted', 0);
    }
	else if($request->status == 'deliveredByMaystro'){
        $orders = $orders->where('delivred', 1)
        ->where('deliveryAborted', 0)
        ->WhereNull('delivredBy');
    }
        if (isset($request->keyword)) {
            $keyword = $request->keyword;

            if($request->filterType == 'id'){
                $orders = $orders->where('id', 'like', '%' . $request->keyword . '%');
            }
            else if($request->filterType == 'name'){
                $orders = $orders->where('userName', 'like', '%' . $request->keyword . '%');
            }
            else if($request->filterType == 'wilaya'){
                $orders = $orders->where('wilaya', 'like', '%' . $request->keyword . '%');
            }
            else if($request->filterType == 'mobile'){
                $orders = $orders->where('customerPhone', 'like', '%' . $request->keyword . '%');
            }
             else if($request->filterType == 'coupon'){
                $orders = $orders->where('coupon', 'like', '%' . $request->coupon . '%');
            }
            else if($request->filterType == 'store'){
            $orders = $orders->whereHas('store', function($query) use ($keyword) {
                    $query->where('title' , 'like' , '%'.$keyword.'%')->withTrashed();
                });
            }
            else{
                $orders = $orders
                ->where(function($q) use ($request , $keyword) {
                    $q->where('userName', 'like', '%'.$request->keyword.'%')
                        ->orWhere('userId', 'like', '%'.$request->keyword.'%')
                        ->orWhere('maystroId', 'like', '%'.$request->keyword.'%')
                        ->orWhere('id', 'like', '%'.$request->keyword.'%')
                        ->orWhere('wilaya', 'like', '%'.$request->keyword.'%')
                        ->orWhere('coupon', 'like', '%'.$request->keyword.'%')
                        ->orWhereHas('store', function($query) use ($keyword) {
                            $query->where('title' , 'like' , '%'.$keyword.'%')->withTrashed();
                        });
                });
            }

        }
        if(isset($request->sortColumn)){
            if(isset($request->sortType)){
                $orders = $orders->orderBy($request->sortColumn , $request->sortType);
            }
            else{
                $orders = $orders->orderBy($request->sortColumn);
            }
        }
        else{
            $orders = $orders->orderBy('created_at' , 'desc');
        }
        if(isset($request->from)){
            $orders = $orders->whereBetween('created_at',
        array($request->from, $request->to));
        }
        if(isset($request->all)){
            return $orders->get();
        }
        else{
            return $orders->paginate(10);
        }
    }

    public function store(Request $request)
    {
        $inputs = $request->only(['userId', 'userName', 'totalPrice',
            'delivred', 'delivredAt', 'status', 'approvedAt', 'rejectedAt', 'received',
            'receivedAt', 'canceled', 'canceledAt', 'deliveryAddress', 'customerPhone', 'wilaya', 'commune',
            'deliveryPrice', 'codeCommune', 'totalWithDeliveryPrice', 'delivredBy', 'maystroId',
            'notes', 'magasinId', 'deliveryAborted', 'deliveryPostPoned',
            'deliveryAbortedAt', 'deliveryPostponedAt' , 'products', 'coupon','commission']);
        // json : products /
        $store = Store::findOrFail($inputs['magasinId']);
        if (isset($inputs['products'])) {
            $inputs['products'] = json_encode($inputs['products']);
            $inputs['commission'] = $store->commission;
            $order = Order::create($inputs);
            //Mail::to('Contact@placetta.com')->send(new OrderCreated($order));
            Mail::to('Zakarya.fares@placetta.com')->send(new OrderCreated($order));
            Mail::to('O.abdelmalek13@gmail.com')->send(new OrderCreated($order));
            return response()->json([
                'message' => 'order without products',
                'order' => $order], 200);
        } else {
            return response()->json([
                'message' => 'order without products'], 404);
        }
    }

    public function migrate(Request $request)
    {
        $existOrder = Order::where('firebaseId', $request->id)->count();
        if ($existOrder === 0) {
            $inputs = $request->only(['userId', 'userName', 'totalPrice',
                'delivred', 'delivredAt', 'status', 'approvedAt', 'rejectedAt', 'received',
                'receivedAt', 'canceled', 'canceledAt', 'deliveryAddress', 'customerPhone', 'wilaya', 'commune',
                'deliveryPrice', 'codeCommune', 'totalWithDeliveryPrice', 'delivredBy', 'maystroId',
                'deliveryAborted', 'deliveryPostPoned', 'deliveryAbortedAt', 'deliveryPostponedAt',
                'products' , 'coupon' , 'created_at']);
            // json : products /
            if (isset($inputs['products'])) {
                $inputs['products'] = json_encode($inputs['products']);
                $storeUser = User::where('firebaseId', $request->magasinId)->first();
                $customer = User::where('firebaseId', $request->userId)->first();
                $inputs['magasinId'] = $storeUser->store->id;
                $inputs['userId'] = $customer->id;
                $inputs['firebaseId'] = $request->id;
                $order = Order::create($inputs);
                return $order->id;
            } else {
                return 'order without products !!!';
            }
        }
        return 'order already exist!!!';
    }

    public function show(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        return $order;
    }
    public function downloadPDF(Request $request){
        $size = $request->size;
        $orientation = $request->orientation;
        $placettaLogo = $request->placettaLogo;
        $maystroLogo = $request->maystroLogo;
        $PhoneToShow = $request->PhoneToShow;
        $barcodes = $request->barcodes;
        $communes = $request->communeNames;
        $orders = Order::with(['store' => function ($query) {
            $query->withTrashed()->select('id', 'title' , 'mobile');
        }])->whereIn('id', $request->orders)->get();
        $pdf= PDF::loadView('invoice', compact('orders','size','placettaLogo','maystroLogo'
        ,'PhoneToShow','barcodes','communes'));

        $pdf=$pdf->setPaper($size, $orientation);
        return $pdf->stream($request->invoice_title . '.pdf');


      }
    public function update(Request $request, $id)
    {
        $inputs = $request->only(['userId', 'userName', 'totalPrice',
            'delivred', 'delivredAt', 'status', 'approvedAt', 'rejectedAt', 'received',
            'receivedAt', 'canceled', 'canceledAt', 'deliveryAddress', 'customerPhone', 'wilaya', 'commune',
            'deliveryPrice', 'notes', 'codeCommune', 'totalWithDeliveryPrice',
             'delivredBy', 'maystroId', 'deliveryAborted', 'deliveryPostPoned',
              'deliveryAbortedAt', 'deliveryPostponedAt', 'products',
              'payment_received', 'paid_to_store' , 'coupon','commission',
              'payment_received_by', 'paid_to_store_by',
              'payment_received_at', 'paid_to_store_at']);

        if (isset($request->magasinId)) {
            $inputs['magasinId'] = $request->magasinId;
        }
        if (isset($request->commission)) {
            $inputs['commission'] = $request->commission;
            }
        if (isset($request->userId)) {
            $inputs['userId'] =  $request->userId;
        }

        // json : products /
        if (isset($inputs['products'])) {
            $inputs['products'] = json_encode($inputs['products']);
        }
        if (isset($inputs['approvedAt'])) {
            $datetime = strtotime($inputs['approvedAt']);
            $inputs['approvedAt'] = date("Y-m-d h:i:s", $datetime);
        }
        if (isset($inputs['delivredAt'])) {
            $delivrytime = strtotime($inputs['delivredAt']);
            $inputs['delivredAt'] = date("Y-m-d h:i:s", $delivrytime);
        }

        if (isset($inputs['receivedAt'])) {
            $datetime = strtotime($inputs['receivedAt']);
            $inputs['receivedAt'] = date("Y-m-d h:i:s", $datetime);
        }
        if (isset($inputs['rejectedAt'])) {
            $datetime = strtotime($inputs['rejectedAt']);
            $inputs['rejectedAt'] = date("Y-m-d h:i:s", $datetime);
        }
        if (isset($inputs['canceledAt'])) {
            $datetime = strtotime($inputs['canceledAt']);
            $inputs['canceledAt'] = date("Y-m-d h:i:s", $datetime);
        }
        if (isset($inputs['deliveryAbortedAt'])) {
            $datetime = strtotime($inputs['deliveryAbortedAt']);
            $inputs['deliveryAbortedAt'] = date("Y-m-d h:i:s", $datetime);
        }
        if (isset($inputs['deliveryPostponedAt'])) {
            $datetime = strtotime($inputs['deliveryPostponedAt']);
            $inputs['deliveryPostponedAt'] = date("Y-m-d h:i:s", $datetime);
        }
        Order::where('id', $id)->update($inputs);
        return 'product updated successfully !!';
    }

    public function getStoreOrdersWithCount(Request $request, $magasinId)
    {
        $orders = Order::where([['magasinId', $magasinId], ['status', 'inProgress']])->orderBy('created_at', 'desc');
        return ['results' => $orders->paginate(10), 'count' => $orders->count()];

    }

    public function getAllStoreOrders(Request $request, $magasinId)
    {
        $orders = Order::where('magasinId', $magasinId)
            ->where('status', 'inProgress')
            ->orWhere('status', 'rejected')->orderBy('updated_at' , 'desc')->get();
        return $orders;
    }

    public function getCustomerOrdersWithCount(Request $request, $userId)
    {
        $orders = Order::where([['userId', $userId], ['status', 'inProgress']])->orderBy('created_at', 'desc');
        return ['results' => $orders->paginate(10), 'count' => $orders->count()];

    }

    public function getAllCustomerOrders(Request $request, $userId)
    {
        $orders = Order::where('userId', $userId)
            ->with(['store' => function ($query) {
                $query->withTrashed()->select('id', 'title' , 'mobile' , 'address' , 'reviews');
            }])->where(function($q) use ($request) {
                $q->where('status', 'inProgress')
                    ->orWhere('status', 'rejected');
            })->get();
        return $orders;
    }

    public function getOrdersStatistics(Request $request)
    {
        if(isset($request->from)){
            $allOrders = Order::whereBetween('created_at',
        array($request->from, $request->to))->count();
        $inProgressOrders = Order::where('status', 'inProgress')->count();
        $approvedOrders = Order::where('status', 'approved')->whereBetween('approvedAt',
        array($request->from, $request->to))->count();
        $cancelledOrders = Order::whereIn('status', ['canceled','canceled_by_store'])->whereBetween('canceledAt',
        array($request->from, $request->to))->count();
        $rejectedOrders = Order::where('status', 'rejected')->whereBetween('rejectedAt',
        array($request->from, $request->to))->count();
        $deliveredOrders = Order::where('delivred', 1)->where('deliveryAborted', 0)->whereBetween('delivredAt',
        array($request->from, $request->to))->count();
        $deliveryInProgressOrders = Order::where('status', 'approved')
            ->where('delivred', 0)->where('deliveryAborted', 0)->count();
        $deliveryAbortedOrders = Order::where('status', 'approved')
            ->where('deliveryAborted', 1)->whereBetween('deliveryAbortedAt',
            array($request->from, $request->to))->count();
        $deliveryPostPonedOrders = Order::where('status', 'approved')
            ->where('delivred', 0)->where('deliveryPostPoned', 1)->whereBetween('deliveryPostponedAt',
            array($request->from, $request->to))->count();

        $deliveredByPlacettaOrders = Order::where('delivred', 1)
            ->where('delivredBy', 'placetta')->where('deliveryAborted', 0)->whereBetween('delivredAt',
            array($request->from, $request->to))->count();

        $deliveredByMaystroOrders = Order::where('delivred', 1)
            ->where('deliveryAborted', 0)
            ->WhereNull('delivredBy')->whereBetween('delivredAt',
            array($request->from, $request->to))->count();
        }
        else{
            $allOrders = Order::count();
            $inProgressOrders = Order::where('status', 'inProgress')->count();
            $approvedOrders = Order::where('status', 'approved')->count();
            $cancelledOrders = Order::whereIn('status', ['canceled','canceled_by_store'])->count();
            $rejectedOrders = Order::where('status', 'rejected')->count();
            $deliveredOrders = Order::where('delivred', 1)->where('deliveryAborted', 0)->count();
            $deliveryInProgressOrders = Order::where('status', 'approved')
                ->where('delivred', 0)->where('deliveryAborted', 0)->count();
            $deliveryAbortedOrders = Order::where('status', 'approved')
                ->where('deliveryAborted', 1)->count();
            $deliveryPostPonedOrders = Order::where('status', 'approved')
                ->where('delivred', 0)->where('deliveryPostPoned', 1)->count();
            $deliveredByPlacettaOrders = Order::where('delivred', 1)
                ->where('delivredBy', 'placetta')->where('deliveryAborted', 0)->count();
            $deliveredByMaystroOrders = Order::where('delivred', 1)
                ->where('deliveryAborted', 0)
                ->WhereNull('delivredBy')->count();
        }

        return response()->json([
            'allOrders' => $allOrders,
            'inProgressOrders' => $inProgressOrders,
            'approvedOrders' => $approvedOrders,
            'cancelledOrders' => $cancelledOrders,
            'rejectedOrders' => $rejectedOrders,
            'deliveredOrders' => $deliveredOrders,
            'deliveryInProgressOrders' => $deliveryInProgressOrders,
            'deliveryAbortedOrders' => $deliveryAbortedOrders,
            'deliveryPostPonedOrders' => $deliveryPostPonedOrders,
            'deliveredByPlacettaOrders' => $deliveredByPlacettaOrders,
            'deliveredByMaystroOrders' => $deliveredByMaystroOrders
        ], 200);
    }


    public function getOrdersForDelivery(Request $request)
    {
        $orders = Order::with(['store' => function ($query) {
            $query->withTrashed()->select('id', 'title' , 'mobile');
        }]);
	if($request->status == 'not_picked_up'){
        $orders = $orders->where('status', 'approved')
        ->where('delivred', 0)->where('deliveryAborted', 0)
        ->WhereNull('picked_up_at');
    }
    else if($request->status == 'picked_up'){
        $orders = $orders->where('status', 'approved')
        ->where('delivred', 0)->where('deliveryAborted', 0)
        ->whereNotNull('picked_up_at');
    }
    else if($request->status == 'aborted'){
        $orders = $orders->where('delivred', 0)
        ->where('delivredBy', 'placetta')->where('deliveryAborted', 1)
        ->whereNotNull('picked_up_at');
    }
	else if($request->status == 'delivered'){
        $orders = $orders->where('delivred', 1)
        ->where('delivredBy', 'placetta')->where('deliveryAborted', 0)
        ->whereNotNull('picked_up_at');
    }
            return $orders->get();
    }

    public function getOrdersStatisticsForDelivery(Request $request)
    {
            $notPickedUpOrders = Order::where('status', 'approved')->where('delivredBy', 'placetta')
                ->where('delivred', 0)->where('deliveryAborted', 0)->WhereNull('picked_up_at')
                ->count();
            $pickedUpOrders = Order::where('status', 'approved')->where('delivredBy', 'placetta')
                ->where('delivred', 0)->where('deliveryAborted', 0)->whereNotNull('picked_up_at')
                ->count();
            $abortedOrders = Order::where('status', 'approved')->where('delivredBy', 'placetta')
                ->where('delivred', 0)->where('deliveryAborted', 1)->whereNotNull('picked_up_at')
                ->count();
            $deliveredOrders = Order::where('delivred', 1)
                ->where('delivredBy', 'placetta')->where('deliveryAborted', 0)
                ->whereNotNull('picked_up_at')->count();


        return response()->json([
            'deliveryInProgressOrders' => $deliveryInProgressOrders,
            'deliveredByPlacettaOrders' => $deliveredByPlacettaOrders
        ], 200);
    }

    public function generateOrderProductRelationShips() {
        $orders = Order::get();

        foreach ($orders as $order) {
            $products = json_decode($order->products);
            foreach ($products as $product) {
                $productSQL = Product::where('firebaseId' , $product->id)->first();
                if ($productSQL) {
                    OrderProduct::create([
                        'productId' => $productSQL->id,
                        'orderId' => $order->id
                    ]);
                }
            }
        }
    }

    public function destroy(Request $request, $id)
    {
        Order::destroy($id);
        return 'product destroyed successfully';
    }

    public function checkBazitaOrder() {
        $order = Order::where('id' , 267)->first();
        $products = json_decode($order->products);
        foreach ($products as $product) {
            if ($product->id === 2037) {
                $product->id = 2101;
            }

            if ($product->id === 2041) {
                $product->id = 2104;
            }

        }
        return ['order' => $order , 'products' => $products];
    }

    public function fixBazitaOrder() {
        $order = Order::where('id' , 267)->first();
        $products = json_decode($order->products);
        foreach ($products as $product) {
            if ($product->id === 2037) {
                $product->id = 2101;
            }

            if ($product->id === 2041) {
                $product->id = 2104;
            }

        }

        $order->update(['products' => json_encode($products)]);
    }

    public function fixBazarkomOrder() {
        Order::where('id' , 270)->update([
            'totalPrice' => 9500,
            'totalWithDeliveryPrice' => 9500
        ]);
        return 'order fixed';
    }
    public function fixCommissions($magasinId) {
        $store = Store::findOrFail($magasinId);
        Order::where('magasinId' , $magasinId)->update([
        'commission' => $store->commission
        ]);
        return 'order fixed';
        }

    public function cancelOrder($orderId) {
        $order = Order::where('id' , $orderId)->first();
        Order::where('id' , $orderId)->update([
            'status' => 'rejected',
            'rejectedAt' => $inputs['rejectedAt'] = date("Y-m-d h:i:s")
        ]);

        if ($order->maystro_db_id) {
            $maystroService = new MaystroService();
            $maystroStore = $maystroService->authenticateStore($order->magasinId);
            $token = $maystroStore['token'];
            $input = ['status' => '50' , 'token' => $token];
            $response = Http::withHeaders([
                'Authorization'=> 'Token ' . $token
            ])->patch(env('MAYSTRO_URL') . 'shared/status/'.$order->maystro_db_id, $input);
        }

        return ['order cancelled successfully !!'];
    }
}
