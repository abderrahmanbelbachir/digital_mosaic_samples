<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    //
    public function getDashboardStatistics(Request $request)
    {
        $orders = Order::where([['status','!=','rejected'] ,['status','!=', 'canceled']])->whereBetween('created_at',
            array($request->from_date, $request->to_date))
            ->count();
        $customers = User::whereBetween('created_at',
            array($request->from_date, $request->to_date))
            ->where('accountType' , 'customer')->count();
        $stores = Store::whereBetween('created_at',
            array($request->from_date, $request->to_date))
            ->count();
        $products = Product::whereBetween('created_at',
            array($request->from_date, $request->to_date))->whereNotNull('pictures')
            ->count();
        return response()->json([
            'orders' => $orders,
            'customers' => $customers,
            'stores' => $stores,
            'products' => $products
        ], 200);
    }

    public function getDashboardStatisticsDebug(Request $request)
    {
        $customers = User::whereBetween('created_at',
            array($request->from_date, $request->to_date))->where('accountType' , 'customer')
            ->select('id' , 'created_at' , 'fullName' , 'accountType')->get();

        return $customers;
    }
    public function getPaymentStatistics(Request $request){
        $orders = Order::where('delivred', 1)->where('deliveryAborted', 0)
        ->groupBy(['magasinId', 'commission'])->selectRaw('sum(totalPrice) as sumTotal, count(*) as countOrders,
        commission, magasinId, sum(totalPrice)*(1-commission/100) as storeBenefits,
        sum(totalPrice)*commission/100 as placettaBenefits')->with(['store' => function ($query) {
            $query->withTrashed()->select('id', 'title');
        }])->whereBetween('created_at',
        array($request->from_date, $request->to_date))->paginate(10);
        return $orders;
    }
    public function getTotalIncomes(Request $request){
        $incomes = Order::where('delivred', 1)->where('deliveryAborted', 0)
        ->groupBy(['commission'])->selectRaw('
        sum(totalPrice*commission/100) as placettaIncome,
        sum(totalPrice*(1-commission/100)) as storesIncome,
        commission')->whereBetween('created_at',
        array($request->from_date, $request->to_date))->get();
        return $incomes;
    }
}
