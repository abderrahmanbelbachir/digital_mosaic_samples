<?php

namespace App\Http\Controllers;

use App\Mail\CustomerCreated;
use App\Mail\StoreCreated;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StoreController extends Controller
{
    public function index(Request $request) {
        $length = 10;
        if(isset($request->sortColumn) && $request->sortColumn != ''){
            if(isset($request->sortType) && $request->sortType != ''){
                $stores = Store::orderBy($request->sortColumn , $request->sortType);
            }
            else{
                $stores = Store::orderBy($request->sortColumn);
            }
        }
        else{
            $stores = Store::orderBy('created_at' , 'desc');
        }
        if (!isset($request->admin)) {
            $stores = $stores->whereNotNull('validatedAt')
                ->withCount(['products' => function ($query) {
                    $query->whereNotNull('validatedAt')->where('isPublished' , 1);
                }])
                ->having('products_count' , '>' , 0);
        }
        if (isset($request->keyword)) {
            if($request->filterType == 'id'){
                $stores = $stores->where(function($query) use ($request) {
                    $query->where('id' , 'like' , '%'.$request->keyword.'%');
                });
            }
            else if($request->filterType == 'title'){
                $stores = $stores->where(function($query) use ($request) {
                    $query->where('title' , 'like' , '%'.$request->keyword.'%');
                });
            }
            else if($request->filterType == 'storeType'){
                $stores = $stores->where(function($query) use ($request) {
                    $query->where('storeType' , 'like' , '%'.$request->keyword.'%');
                });
            }
            else if($request->filterType == 'wilaya'){
                $stores = $stores->where(function($query) use ($request) {
                    $query->where('wilaya' , 'like' , '%'.$request->keyword.'%');
                });
            }
            else if($request->filterType == 'mobile'){
                $stores = $stores->where(function($query) use ($request) {
                    $query->where('mobile' , 'like' , '%'.$request->keyword.'%');
                });
            }
            else{
                $stores = $stores->where(function($query) use ($request) {
                    $query->where('title' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('wilaya' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('id' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('storeType' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('commune' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('category' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('categories' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('mobile' , 'like' , '%'.$request->keyword.'%');
                });
            }
        }
        if($request->storeValidation == 'validated' && $request->storeValidation != ''){
            $stores = $stores->whereNotNull('validatedAt');
        }
        if($request->storeValidation == 'notValidated' && $request->storeValidation != ''){
            $stores = $stores->whereNull('validatedAt');
        }
        if($request->storeStatus == 'published' && $request->storeStatus != ''){
            $stores = $stores->where('isPublished', 1);
        }
        if($request->storeStatus == 'notPublished' && $request->storeStatus != ''){
            $stores = $stores->where('isPublished', 0);
        }
        if (isset($request->isFreeDelivery)) {
            if($request->isFreeDelivery){
            $stores = $stores->where('isFreeDelivery', 1);
            }
            else{
                $stores = $stores->where('isFreeDelivery', 0);
            }
        }
        if (isset($request->length)) {
            $length = $request->length;
        }

        if ($request->storeTitle) {
            $stores = $stores->where('title' , 'like' , '%'.$request->storeTitle.'%');
        }
        if ($request->storeWilaya) {
            $stores = $stores->where('wilaya' , 'like' , '%'.$request->storeWilaya.'%');
        }
        if ($request->storeCommune) {
            $stores = $stores->where('commune' , 'like' , '%'.$request->storeCommune.'%');
        }
        if ($request->category) {
            $stores = $stores->where('categories' , 'like' , '%'.$request->category.'%');
        }
        if(isset($request->all)){
            return $stores->get();
        }
        else{
            return $stores->paginate($length);
        }
    }

    public function store(Request $request) {
        $userInputs = $request->only(['picture' , 'wilaya', 'commune', 'lat', 'lng', 'mobile',
            'address', 'country' , 'wilayaCode' , 'communeId', 'codeCommune']);
        $userInputs['fullName'] = $request->title;
        $userInputs['accountType'] = 'store';
        $userExists = User::where('mobile' , $userInputs['mobile'])->count();
        if ($userExists > 0) {
            return 'user already exists!';
        }
        $user = User::create($userInputs);
        $inputs = $request->only(['title' , 'picture' , 'category', 'categories', 'wilaya', 'commune',
            'lat', 'lng', 'mobile', 'address', 'registreCommerce', 'rate', 'country', 'delivery',
            'deliveryType', 'storeType', 'isPublished', 'payments', 'reviews', 'ratingAverage', 'paymentAccounts',
            'planType', 'planPrice', 'homePlace', 
            'clickAndCollect', 'validatedAtTime','commission']);
        $inputs['userId'] = $user->id;
        $inputs['deliveryType'] = 'mayestroDelivery';

        if (isset($inputs['categories'])) {
            $inputs['categories'] = json_encode($inputs['categories']);
        }
        if (isset($inputs['payments'])) {
            $inputs['payments'] = json_encode($inputs['payments']);
        }
        if (isset($inputs['reviews'])) {
            $inputs['reviews'] = json_encode($inputs['reviews']);
        }
        if (isset($inputs['paymentAccounts'])) {
            $inputs['paymentAccounts'] = json_encode($inputs['paymentAccounts']);
        }
        $inputs['isFreeDelivery'] = 0;

        $existedStore = Store::where('mobile' , $inputs['mobile'])->count();
        if ($existedStore > 0) {
            return 'store already exists!';
        } else {
            $store = Store::create($inputs);
          //  Mail::to('Contact@placetta.com')->send(new StoreCreated($store));
            Mail::to('Samia.bouibed@placetta.com')->send(new StoreCreated($store));
            return $store;
        }
    }
    public function switchToStore(Request $request, $id) {
        $user = User::where('id', $id)->first();
        $existedStore = Store::where('mobile' , $user->mobile)->count();
        if ($existedStore > 0) {
            return 'store already exists!';
        } else {
        $inputs['userId'] = $id;
        $inputs['title'] = $user->fullName;
        $inputs['picture'] = $user->picture;
        $inputs['wilaya'] = $user->wilaya;
        $inputs['commune'] = $user->commune;
        $inputs['lat'] = $user->lat;
        $inputs['lng'] = $user->lng;
        $inputs['mobile'] = $user->mobile;
        $inputs['address'] = $user->address;
        $inputs['country'] = $user->country;
        $inputs['deliveryType'] = 'mayestroDelivery';
        $inputs['storeType'] = 'free_seller';
        $inputs['isPublished'] = 1;
        $inputs['payments'] = json_encode(["onDelivery"]);
        $inputs['homePlace'] = 99;
        $inputs['isFreeDelivery'] = 0;
        $store = Store::create($inputs);
        User::where('id', $id)->update(['accountType' => 'store']);
          //  Mail::to('Contact@placetta.com')->send(new StoreCreated($store));
            Mail::to('Samia.bouibed@placetta.com')->send(new StoreCreated($store));
            return $store;
        }
    }
    public function migrate(Request $request) {
        $userExist = User::where('firebaseId' , $request->magasinId);
        if ($userExist->count() > 0) {
            $inputs = $request->only(['title' , 'picture' , 'category', 'categories', 'wilaya', 'commune',
                'lat', 'lng', 'mobile', 'address', 'registreCommerce', 'rate', 'country', 'delivery',
                'deliveryType', 'storeType', 'isPublished', 'payments', 'reviews', 'ratingAverage', 'paymentAccounts',
                'planType', 'planPrice', 'homePlace', 'clickAndCollect', 'validatedAtTime' , 'validatedAt' , 'created_at']);
            $inputs['userId'] = $userExist->first()->id;

            if (isset($inputs['categories'])) {
                $inputs['categories'] = json_encode($inputs['categories'] , JSON_UNESCAPED_UNICODE);
            }
            if (isset($inputs['payments'])) {
                $inputs['payments'] = json_encode($inputs['payments']);
            }
            if (isset($inputs['reviews'])) {
                $inputs['reviews'] = json_encode($inputs['reviews']);
            }
            if (isset($inputs['paymentAccounts'])) {
                $inputs['paymentAccounts'] = json_encode($inputs['paymentAccounts']);
            }

            $store = Store::create($inputs);
            return $store;
        }
        return 'user already exist !!!';
    }

    public function show(Request $request , $id) {
        $store = Store::findOrFail($id);
        if ($store->reviews) {
            $store->reviews = json_decode($store->reviews);
        }
        if ($store->categories) {
            $store->categories = json_decode($store->categories);
        }

        if (isset($request->byphone) && $request->byphone === 'true') {
            $store = Store::where('mobile' , $request->phone)->first();
        }
        return $store;
    }

    public function update(Request $request , $id) {
        $inputs = $request->only(['title' , 'picture' , 'category' , 'categories' ,
            'wilaya' , 'commune' , 'lat' , 'lng' , 'mobile', 'address' , 'registreCommerce	' ,
            'rate', 'country' , 'delivery' , 'deliveryType' , 'storeType' , 'isPublished' ,
            'payments' , 'reviews' , 'ratingAverage' , 'paymentAccounts', 'planType',
            'planPrice', 'homePlace', 'clickAndCollect',
             'validatedAtTime', 'validatedAt', 
             'isFreeDelivery','commission']);
        if ($request->magasinId) {
            $user = User::where('firebaseId' , $request->magasinId)->first();
            $user->store->update($inputs);
        } else {
            Store::where('id' , $id)->update($inputs);
        }
        return $inputs;
    }

    public function getHomeStores() {
        $stores = Store::whereNotNull('validatedAt')
            // ->withCount('orders')
            // ->orderBy('orders_count' , 'desc')
            ->whereHas('products', function($query) {
                $query->whereNotNull('validatedAt');
            })
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return $stores;
    }

    public function getStoresForSearchBar(Request $request)
    {
        $stores = Store::orderBy('created_at' , 'desc')
            ->whereNotNull('validatedAt')
            ->withCount(['products' => function ($query) {
                $query->whereNotNull('validatedAt')->where('isPublished' , 1);
            }])
            ->having('products_count' , '>' , 0);
        if (isset($request->keyword)) {
            $stores = $stores->where(function ($query) use ($request) {
                $query->where('title' , 'like' , '%'.$request->keyword.'%')
                    ->orWhere('wilaya' , 'like' , '%'.$request->keyword.'%')
                    ->orWhere('commune' , 'like' , '%'.$request->keyword.'%')
                    ->orWhere('category' , 'like' , '%'.$request->keyword.'%')
                    ->orWhere('categories' , 'like' , '%'.$request->keyword.'%')
                    ->orWhere('mobile' , 'like' , '%'.$request->keyword.'%');
            });
        }
        return $stores->paginate(3);
    }

    public function syncValidatedStores() {
        $stores = Store::whereNotNull('validatedAt')
            ->whereDoesntHave('products', function($query) {
                $query->whereNotNull('validatedAt');
            })
            ->update(['validatedAt' => null]);
        return $stores;
    }

    public function switchToCustomer($id) {
        $store = Store::where('id' , $id)->first();
        User::where('id' , $store->userId)->update(['accountType' => 'customer']);
        Store::destroy($id);
        return 'store switched to customer !!';
    }

    public function fillMaystroDeliveryType() {
        Store::whereNull('deliveryType')->update(['deliveryType' => 'mayestroDelivery']);
        Store::where('delivery' , 0)->update(['delivery' => 1]);
        return 'deliveryType filled !';
    }

    public function destroy(Request $request , $id) {
        $ordersInprogress = Order::where([['magasinId', $id], ['status', 'inProgress']])
        ->count();
        if ($ordersInprogress > 0) {
            return response()->json([
                'message' => 'store have an inprogress order', 'inProgressOrder' => true], 404);
        } else {
            $store = Store::where('id' , $id)->first();
            User::destroy($store->userId);
            // User::where('mobile' , $store->mobile)->delete();
            Store::destroy($id);
            Product::where('magasinId', $id)->delete();
        }
        return 'store destroyed successfully';
    }
}
