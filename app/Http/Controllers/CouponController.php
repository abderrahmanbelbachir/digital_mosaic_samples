<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $coupons = Coupon::whereNotNull('code');
        if (isset($request->keyword)) {
            if($request->filterType == 'id'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('id', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'code'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('code', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'type'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('type', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'influencer'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('influencer', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'categories'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('categories', 'like', '%' . $request->keyword . '%');
                });
            }
           
            else{
                $coupons = $coupons
                ->where(function($q) use ($request) {
                    $q->where('id', 'like', '%'.$request->keyword.'%')
                        ->orWhere('code', 'like', '%'.$request->keyword.'%')
                        ->orWhere('type', 'like', '%'.$request->keyword.'%')
                        ->orWhere('influencer', 'like', '%'.$request->keyword.'%')
                        ->orWhere('categories', 'like', '%'. $request->keyword. '%');
                });
            }            

        }
        if(isset($request->sortColumn)){
            if(isset($request->sortType)){
                $coupons = $coupons->orderBy($request->sortColumn , $request->sortType);
            }
            else{
                $coupons = $coupons->orderBy($request->sortColumn);
            }
        }
        else{
            $coupons = $coupons->orderBy('created_at' , 'desc');
        }
        return $coupons->paginate(10);
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
        $coupon = Coupon::create([
            'code' => $request->code,
            'value' => $request->value,
            'type' => $request->type,
            'expiry_date' => $request->expiry_date,
            'start_date' => $request->start_date,
            'categories' => json_encode($request->categories),
            'influencer' => $request->influencer,
            'influencer_commission' => $request->influencer_commission,



        ]);
    return 'coupon created successfully !!';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $coupon = Coupon::findOrFail($id);
        return $coupon;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        $inputs = $request->only(['value', 'type', ]);
    Coupon::where('id' , $id)->update($inputs);
    return 'coupon updated successfully !!';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Coupon::destroy($id);
        return 'coupon destroyed successfully';
    }


    public function getCouponByCode($code)
    {
        $coupon = Coupon::where('code', $code)->first();
        return $coupon;
    }
    
    public function getInfluencerCoupons(Request $request){

        $coupons = Coupon::whereNotNull('code')
        ->whereNotNull('influencer')
        ->whereNotNull('influencer_commission')
        ->
        with(['orders' => function ($query) {
            $query->get();
        }])->withCount('orders');

        if (isset($request->keyword)) {
            $keyword = $request->keyword;
            if($request->filterType == 'id'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('id', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'code'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('code', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'type'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('type', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'influencer'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('influencer', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'categories'){
                $coupons = $coupons->where(function($query) use ($request) {
                    $query->where('categories', 'like', '%' . $request->keyword . '%');
                });
            }
           
            else{
                $coupons = $coupons
                ->where(function($q) use ($request , $keyword) {
                    $q->where('id', 'like', '%'.$request->keyword.'%')
                        ->orWhere('code', 'like', '%'.$request->keyword.'%')
                        ->orWhere('type', 'like', '%'.$request->keyword.'%')
                        ->orWhere('influencer', 'like', '%'.$request->keyword.'%')
                        ->orWhere('categories', 'like', '%'. $request->keyword. '%');
                });
            }            

        }
        if(isset($request->sortColumn)){
            if(isset($request->sortType)){
                $coupons = $coupons->orderBy($request->sortColumn , $request->sortType);
            }
            else{
                $coupons = $coupons->orderBy($request->sortColumn);
            }
        }
        else{
            $coupons = $coupons->orderBy('created_at' , 'desc');
        }
        return $coupons->paginate(10);
    }
}
