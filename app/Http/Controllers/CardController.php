<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
{
    //
    public function index() {
        $cards = Card::paginate(10);
        return $cards;
    }

    public function store(Request $request) {
        $inputs = $request->only(['products', 'totalPrice', 'deliveryPrice', 'totalWithDelivery']);
        $inputs['userId'] = $request->userId;
        $inputs['magasinId'] = $request->magasinId;
        if ($inputs['products']) {
            $inputs['products'] = json_encode($inputs['products']);
        }
        $card = Card::create($inputs);
        return 'product created successfully !!';
    }

    public function migrate(Request $request) {
        $cardExist = Card::where('userId' , $request->userId)->where('magasinId' , $request->magasinId)->count();
        if ($cardExist > 0) {
            $inputs = $request->only(['products', 'totalPrice', 'deliveryPrice', 'totalWithDelivery']);
            $user = User::where('firebaseId' , $request->userId)->first();
            $storeUser = User::where('firebaseId' , $request->magasinId)->first();
            $inputs['userId'] = $user->id;
            $inputs['magasinId'] = $storeUser->store->id;
            if ($inputs['products']) {
                $inputs['products'] = json_encode($inputs['products']);
            }
            $card = Card::create($inputs);
            return 'product created successfully !!';
        }
        return 'card already exist';
    }

    public function show(Request $request , $id) {
        $card = Card::findOrFail($id);
        return $card;
    }

    public function update(Request $request , $id) {
        $inputs = $request->only(['products', 'totalPrice', 'deliveryPrice', 'totalWithDelivery']);
        $inputs['userId'] = $request->userId;
        $inputs['magasinId'] = $request->magasinId;
        if ($inputs['products']) {
            $inputs['products'] = json_encode($inputs['products']);
        }
        Card::where('userId' , $request->userId)->where('magasinId' , $request->magasinId)->update($inputs);
        return 'card updated successfully !!';
    }

    public function destroy(Request $request , $id) {
        Card::destroy($id);
        return 'product destroyed successfully';
    }

    public function userCards($userId) {
        $cards = Card::where('userId' , $userId)->with('store')->get();
        return $cards;
    }

    public function deleteUserCards($userId) {
        $cards = Card::where('userId' , $userId)->delete();
        return $cards;
    }
}
