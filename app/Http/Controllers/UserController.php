<?php

namespace App\Http\Controllers;

use App\Mail\CustomerCreated;
use App\Mail\WelcomeMail;
use App\Mail\OrderCreated;
use App\Models\Card;
use App\Models\Notification;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    //
    public function index(Request $request) {
        $users = User::where('accountType' , 'customer')->withCount(['orders' => function ($query) {
            $query->where('status', 'inProgress')
            ->orWhere(function($q){
                $q->where('delivred', 1)->where('deliveryAborted', 0);
            })
            ->orWhere(function($q){
                $q->where('status', 'approved')->where('deliveryAborted', 0);
            });
        }]);
        if (isset($request->keyword)) {
            if($request->filterType == 'id'){
                $users = $users->where('id' , 'like' , '%'.$request->keyword.'%');
            }
            else if($request->filterType == 'fullName'){
                $users = $users->where('fullName' , 'like' , '%'.$request->keyword.'%');
            }
            else if($request->filterType == 'wilaya'){
                $users = $users->where('wilaya' , 'like' , '%'.$request->keyword.'%');
            }
            else if($request->filterType == 'mobile'){
                $users = $users->where('mobile' , 'like' , '%'.$request->keyword.'%');
            }
            else{
                $users = $users->where(function($q) use ($request) {
                    $q->where('fullName' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('wilaya' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('commune' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('id' , 'like' , '%'.$request->keyword.'%')
                        ->orWhere('mobile' , 'like' , '%'.$request->keyword.'%');
                });
            }
        }
        if(isset($request->sortColumn)){
            if(isset($request->sortType)){
                $users = $users->orderBy($request->sortColumn , $request->sortType);
            }
            else{
                $users = $users->orderBy($request->sortColumn);
            }
        }
        else{
            $users = $users->orderBy('created_at' , 'desc');
        }
        if(isset($request->all)){
            return $users->get();
        }
        else{
            return $users->paginate(10);
        }
    }

    public function store(Request $request) {
        $userInputs = $request->only(['fullName' , 'picture' , 'wilaya', 'commune',
            'lat', 'lng', 'mobile', 'address', 'country' , 'wilayaCode' , 'communeId',
            'codeCommune' , 'isValidated' , 'ratingAverage' ,'deliveryPrice' ,
            'hasFreeDelivery' , 'addressList' , 'reviews' , 'accountType', 'email']);
        // $userInputs['accountType'] = 'customer';
        if (isset($userInputs['addressList'])) {
            $userInputs['addressList'] = json_encode($userInputs['addressList']);
        }
        if (isset($userInputs['reviews'])) {
            $userInputs['reviews'] = json_encode($userInputs['reviews']);
        }

        /* if (isset($request->notifications)) {
            foreach($request->notifications as $notification) {
                $sender = User::where('id' , $notification->sender_id)->first();
                $receiver = User::where('id' , $notification->receiver_id)->first();
                $notificationObject = [
                    'message' => $notification->message,
                    'sender' => $notification->sender,
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'body' => $notification->body,
                    'isSeen' => $notification->isSeen,
                    'senderPicture' => $notification->senderPicture,
                    'type' => $notification->type
                ];

                Notification::create($notificationObject);
            }
        } */
        $user = User::create($userInputs);
        if (isset($userInputs['email'])) {
        Mail::to($user->email)->send(new WelcomeMail($user));
        }
        //Mail::to('Contact@placetta.com')->send(new CustomerCreated($user));
        Mail::to('Zakarya.fares@placetta.com')->send(new CustomerCreated($user));
        Mail::to('O.abdelmalek13@gmail.com')->send(new CustomerCreated($user));
        Mail::to('Samia.bouibed@placetta.com')->send(new CustomerCreated($user));
        return $user;
    }

    public function migrate(Request $request) {
        $userExist = User::where('firebaseId' , $request->id)->count();
        if ($userExist === 0) {
            if (isset($request->email) && strpos($request->email, '@') !== false) {
                $userEmail = User::where('email' , $request->email)->count();
                if ($userEmail > 0) {
                    return 'email already exists : '. $request->email;
                }
            }
            $userInputs = $request->only(['fullName' , 'picture' , 'wilaya',
                'commune', 'lat', 'lng', 'mobile', 'address', 'country' ,
                'wilayaCode' , 'communeId', 'codeCommune' , 'isValidated' ,
                'ratingAverage' ,'deliveryPrice' , 'hasFreeDelivery' , 'addressList' ,
                'reviews' , 'accountType' , 'email' , 'created_at']);
            $userInputs['firebaseId'] = $request->id;
            // $userInputs['accountType'] = 'customer';
            if (isset($userInputs['addressList'])) {
                $userInputs['addressList'] = json_encode($userInputs['addressList']);
            }
            if (isset($userInputs['reviews'])) {
                $userInputs['reviews'] = json_encode($userInputs['reviews']);
            }
            $user = User::create($userInputs);
            return $user;
        }
        return 'user already exist : ' . $request->fullName . $request->id;
    }

    public function show(Request $request , $id) {
        $user = User::findOrFail($id);
        if ($user->addressList) {
            $user->addressList = json_decode($user->addressList);
        }
        if ($user->reviews) {
            $user->reviews = json_decode($user->reviews);
        }
        return $user;
    }

    public function update(Request $request , $id) {
        $inputs = $request->only(['fullName' , 'email' , 'address' , 'mobile', 'country' ,
            'accountType' , 'wilaya' , 'wilayaCode' , 'commune' , 'communeId', 'picture',
            'lat' , 'lng' , 'isValidated' , 'addressList', 'reviews', 'ratingAverage',
            'deliveryPrice', 'codeCommune', 'hasFreeDelivery']);
            if (isset($inputs['addressList'])) {
                $inputs['addressList'] = json_encode($inputs['addressList']);
            }
            if (isset($inputs['reviews'])) {
                $inputs['reviews'] = json_encode($inputs['reviews']);
            }

        User::where('id' , $id)->update($inputs);

        if(isset($inputs['email'])){
            // $user = User::create($inputs);
            $user = User::where('id' , $id)->first();
            Mail::to($user->email)->send(new WelcomeMail($user));
        }

        return 'user updated successfully !!';
    }

    public function getUserByPhone($phone) {
        $user = User::where('mobile' , $phone)->first();
        return $user;
    }

    public function getUserByPhoneWithTrash(Request $request , $phone) {
        $user = User::where('mobile' , $phone)->withTrashed()->get();
        /*if (isset($request->convertToCustomer) && $request->convertToCustomer == 'true') {
            User::where('mobile' , $phone)->withTrashed()->update(['deleted_at' => null]);
        }*/
        return $user;
    }

    public function getUserByEmail($email) {
        $user = User::where('email' , $email)->first();
        return $user;
    }

    public function getHomeStores(Request $request) {
        $stores = Store::whereNotNull('homePlace')->orderBy('homePlace' , 'asc')->get();
        return $stores;
    }

    public function getAllCustomers() {
        $users = User::whereNotNull('mobile')->where('accountType' , 'customer')->get();
        return $users;
    }

    public function getAllUsers() {
        $users = User::whereNotNull('mobile')->get();
        return $users;
    }

    public function destroy(Request $request , $id) {
        User::destroy($id);
        return 'user destroyed successfully';
    }

    public function runMigration() {
        \Artisan::call('migrate',
            array(
                '--path' => 'database/migrations',
                '--force' => true));
        return 'migration in progress';
    }

    public function clearCache() {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');

        return 'cache cleared successfully';
    }

    public function resetMigration() {
        \Artisan::call('migrate:rollback',
            array(
                '--path' => 'database/migrations',
                '--force' => true,
                '--step' => 1));
        return 'reset 1 steps migration successfully';
    }

    public function refreshMigration() {
        \Artisan::call('migrate:refresh',
            array(
                '--path' => 'database/migrations',
                '--force' => true
            ));
        return 'reset All steps migration successfully';
    }

    public function refreshIsValidating() {
        $users = User::whereNotNull('fullName')->where(function($query) {
           $query->whereNotNull('address')->orWhereNotNull('addressList');
        })
            ->where(function($query) {
                $query->whereNotNull('mobile');
            })

            ->where(function($query) {
                $query->whereNotNull('wilaya');
            })
            ->where(function($query) {
                $query->whereNotNull('commune');
            })
            ->update(['isValidated' => true]);
        return $users;
    }

    public function refreshDeliveryPrice() {
        $deliveryPriceArray = [];
        $deliveryPriceArray[2] = 700;
        $deliveryPriceArray[3] = 900;
        $deliveryPriceArray[4] = 700;
        $deliveryPriceArray[5] = 700;
        $deliveryPriceArray[6] = 600;
        $deliveryPriceArray[7] = 900;
        $deliveryPriceArray[8] = 1000;
        $deliveryPriceArray[9] = 600;
        $deliveryPriceArray[10] = 600;
        $deliveryPriceArray[12] = 900;
        $deliveryPriceArray[13] = 700;
        $deliveryPriceArray[14] = 750;
        $deliveryPriceArray[15] = 600;
        $deliveryPriceArray[16] = 400;
        $deliveryPriceArray[17] = 900;
        $deliveryPriceArray[18] = 700;
        $deliveryPriceArray[19] = 600;
        $deliveryPriceArray[20] = 750;
        $deliveryPriceArray[21] = 600;
        $deliveryPriceArray[22] = 700;
        $deliveryPriceArray[23] = 750;
        $deliveryPriceArray[24] = 700;
        $deliveryPriceArray[25] = 600;
        $deliveryPriceArray[26] = 600;
        $deliveryPriceArray[27] = 600;
        $deliveryPriceArray[28] = 800;
        $deliveryPriceArray[29] = 600;
        $deliveryPriceArray[30] = 900;
        $deliveryPriceArray[31] = 600;
        $deliveryPriceArray[32] = 1000;
        $deliveryPriceArray[34] = 700;
        $deliveryPriceArray[35] = 600;
        $deliveryPriceArray[36] = 750;
        $deliveryPriceArray[38] = 700;
        $deliveryPriceArray[39] = 900;
        $deliveryPriceArray[40] = 900;
        $deliveryPriceArray[41] = 800;
        $deliveryPriceArray[42] = 600;
        $deliveryPriceArray[43] = 600;
        $deliveryPriceArray[44] = 600;
        $deliveryPriceArray[45] = 1000;
        $deliveryPriceArray[46] = 700;
        $deliveryPriceArray[47] = 900;
        $deliveryPriceArray[48] = 700;

        // return $deliveryPriceArray[18];
        $users = User::whereNotNull('wilayaCode')->get();
        foreach ($users as $user) {
            if (isset($deliveryPriceArray[$user->wilayaCode])) {
                $user->deliveryPrice = $deliveryPriceArray[$user->wilayaCode];
                $user->update();
            }
        }

        return 'updated delivery prices';
    }


}
