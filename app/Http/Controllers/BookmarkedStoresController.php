<?php

namespace App\Http\Controllers;

use App\Models\bookmarkedProducts;
use App\Models\bookmarkedStores;
use App\Models\Card;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class BookmarkedStoresController extends Controller
{
    public function index() {
        $bookmarkedStores = bookmarkedStores::paginate(10);
        return $bookmarkedStores;
    }

    public function store(Request $request) {
        $inputs = [
            'magasinId' => $request->magasinId,
            'userId' => $request->userId
        ];
        $user = User::where('id' , $request->userId)->first();
        $user->bookmarkStores()->create($inputs);
        return 'product created successfully !!';
    }

    public function migrate(Request $request) {
        $inputs = [];
        $user = User::where('firebaseId' , $request->userId)->first();
        if ($user && $user->id) {
            $inputs['userId'] = $user->id;
            foreach ($request->stores as $storeId) {
                $storeUser = User::where('firebaseId' , $storeId)->first();
                $store = $storeUser->store;
                if ($store && $store->id) {
                    $inputs['magasinId'] = $store->id;
                    $bookmarkedProduct = bookmarkedStores::create($inputs);
                } else {
                    return 'store not found : '.$store;
                }
            }
            return 'store bookmark created successfully !!';
        } else {
            return 'user not found : '.$request->userId;
        }
    }

    public function show(Request $request , $id) {
        $bookmarkedStore = bookmarkedStores::findOrFail($id);
        return $bookmarkedStore;
    }

    public function update(Request $request , $id) {
        bookmarkedStores::where('id' , $id)->update($request);
        return 'product updated successfully !!';
    }

    public function destroy(Request $request , $id) {
        bookmarkedStores::where([['magasinId' , $request->magasinId] , ['userId' , $request->userId]])->delete();
        return 'product destroyed successfully';
    }

    public function getUserBookmarkStores(Request $request , $userId) {
        $user = User::where('id' , $userId)->first();
        return $user->bookmarkStores()->with('store')->get();
    }

    public function getUserBookmarkStoresCount(Request $request , $userId) {
        $user = User::where('id' , $userId)->first();
        if ($user && $user->id) {
            return $user->bookmarkStores()->count();
        } else {
            return 'not bookmarks found';
        }
    }
}
