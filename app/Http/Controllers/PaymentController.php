<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = Order::with(['store' => function ($query) {
            $query->withTrashed()->select('id', 'title');
        }])
            ->where('delivred', 1)
            ->where('deliveryAborted', 0);
        if (isset($request->keyword)) {
            $keyword = $request->keyword;
            if($request->filterType == 'id'){
                $orders = $orders->where('id', 'like', '%'.$request->keyword.'%');
            }
            if($request->filterType == 'customer'){
                $orders = $orders->where('userName', 'like', '%'.$request->keyword.'%');
            }
            if($request->filterType == 'store'){
                $orders = $orders->whereHas('store', function($query) use ($keyword) {
                    $query->where('title' , 'like' , '%'.$keyword.'%')->withTrashed();
                });
            }
            else{
                $orders = $orders
                ->where(function($q) use ($request , $keyword) {
                    $q->where('userName', 'like', '%'.$request->keyword.'%')
                        ->orWhere('userId', 'like', '%'.$request->keyword.'%')
                        ->orWhere('status', 'like', '%'.$request->keyword.'%')
                        ->orWhere('maystroId', 'like', '%'.$request->keyword.'%')
                        ->orWhere('id', 'like', '%'.$request->keyword.'%')
                        ->orWhere('wilaya', 'like', '%'.$request->keyword.'%')
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

        if(isset($request->all)){
            return $orders->get();
        }
        else{
            return $orders->paginate(10);
        }
    }

    public function getPayemntsTrack(Request $request){
        $payments = Order::where('delivred', 1)->where('deliveryAborted', 0)
        ->whereNotNull('payment_received_by')->whereNull('paid_to_store_by')
        ->groupBy(['payment_received_by'])->selectRaw('
        payment_received_by,
        sum(totalPrice*payment_received) as received_amount')->get();
        return $payments;
    }
    
    public function getPayemntsHistory(Request $request)
    {
        $orders = Order::with(['store' => function ($query) {
            $query->withTrashed()->select('id', 'title');
        }])
            ->where('delivred', 1)
            ->where('deliveryAborted', 0)
            ->whereNotNull('payment_received_by');
        if (isset($request->keyword)) {
            $keyword = $request->keyword;
            if($request->filterType == 'id'){
                $orders = $orders->where('id', 'like', '%'.$request->keyword.'%');
            }
            if($request->filterType == 'customer'){
                $orders = $orders->where('userName', 'like', '%'.$request->keyword.'%');
            }
            if($request->filterType == 'store'){
                $orders = $orders->whereHas('store', function($query) use ($keyword) {
                    $query->where('title' , 'like' , '%'.$keyword.'%')->withTrashed();
                });
            }
            if($request->filterType == 'commercial'){
                $orders = $orders
                ->where(function($q) use ($request , $keyword) {
                    $q->where('payment_received_by', 'like', '%'.$request->keyword.'%')
                    ->orWhere('paid_to_store_by', 'like', '%'.$request->keyword.'%');
                });
            }
            else{
                $orders = $orders
                ->where(function($q) use ($request , $keyword) {
                    $q->where('userName', 'like', '%'.$request->keyword.'%')
                        ->orWhere('userId', 'like', '%'.$request->keyword.'%')
                        ->orWhere('status', 'like', '%'.$request->keyword.'%')
                        ->orWhere('maystroId', 'like', '%'.$request->keyword.'%')
                        ->orWhere('id', 'like', '%'.$request->keyword.'%')
                        ->orWhere('wilaya', 'like', '%'.$request->keyword.'%')
                        ->orWhere('payment_received_by', 'like', '%'.$request->keyword.'%')
                        ->orWhere('paid_to_store_by', 'like', '%'.$request->keyword.'%')
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

        if(isset($request->all)){
            return $orders->get();
        }
        else{
            return $orders->paginate(10);
        }
    }

    public function getUnpaidOrders(Request $request)
    {
        $orders = Order::with(['store' => function ($query) {
            $query->withTrashed()->select('id', 'title');
        }])
            ->where('delivred', 1)
            ->where('deliveryAborted', 0)->where('paid_to_store', 0);
            $orders = $orders->orderBy('created_at' , 'desc');
            return $orders->get();
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function showStores(Request $request)
    {
        $stores = Store::whereHas('orders', function($query) {
            $query->where('delivred', 1)
            ->where('deliveryAborted', 0);
        })->get();
        foreach($stores as $store){
            $orders = $store->orders()->whereNotNull('delivredAt')->orderBy('delivredAt', 'asc')->take(5)->update(['commission'=> 0]);
           
        }

        return 'done';
    }

    public function setComission5(Request $request)
    {
        $stores = Store::whereHas('orders', function($query) {
            $query->where('delivred', 1)
                ->where('deliveryAborted', 0);
        })->get();
        foreach($stores as $store){
            $orders = $store->orders()->whereNotNull('delivredAt')->orderBy('delivredAt', 'asc')->update(['commission'=> 5]);

        }

        return 'done';
    }
    public function destroy($id)
    {
        //
    }

}
