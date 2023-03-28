<?php

namespace App\Http\Controllers;

use App\Models\bookmarkedProducts;
use App\Models\bookmarkedStores;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class BookmarkedProductsController extends Controller
{
    public function index() {
        $bookmarkedProducts = bookmarkedProducts::paginate(10);
        return $bookmarkedProducts;
    }

    public function store(Request $request) {
        $user = User::findOrFail($request->userId);
        $bookmark = [
            'userId' => $request->userId,
            'productId' => $request->productId
        ];
        $user->bookmarkProducts()->create($bookmark);
    }

    public function migrate(Request $request) {
        $inputs = [];
        $user = User::where('firebaseId' , $request->userId)->first();
        if ($user && $user->id) {
            $inputs['userId'] = $user->id;
            foreach ($request->products as $productId) {
                $product = Product::where('firebaseId' , $productId)->first();
                if ($product && $product->id) {
                    $inputs['productId'] = $product->id;
                    $bookmarkedProduct = bookmarkedProducts::create($inputs);
                } else {
                    return 'product not found : '.$productId;
                }
            }
            return 'product created successfully !!';
        } else {
            return 'user not found : '.$request->userId;
        }
    }

    public function show(Request $request , $id) {
        $bookmarkedProduct = bookmarkedProducts::findOrFail($id);
        return $bookmarkedProduct;
    }

    public function update(Request $request , $id) {
        bookmarkedProducts::where('id' , $id)->update($request);
        return 'product updated successfully !!';
    }

    public function destroy(Request $request , $id) {
        bookmarkedProducts::where('productId' , $request->productId)->where('userId' , $request->userId)->delete();
        return 'product destroyed successfully';
    }

    public function getUserBookmarkProducts(Request $request , $userId) {
        $user = User::where('id' , $userId)->first();
        return $user->bookmarkProducts()->with('product')->get();
    }

    public function getUserBookmarkProductsCount(Request $request , $userId) {
        $user = User::where('id' , $userId)->first();
        if ($user && $user->id) {
            return $user->bookmarkProducts()->count();
        } else {
            return 'no bookmarked products found';
        }
    }
}
