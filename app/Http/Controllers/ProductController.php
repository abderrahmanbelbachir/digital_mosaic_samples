<?php

namespace App\Http\Controllers;

use App\Mail\ProductCreated;
use App\Mail\StoreCreated;
use App\Models\MaystroProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProductController extends Controller
{
    //
    public function index(Request $request)
    {
        $productName = '';
        $category = '';
        $products = Product::whereNotNull('validatedAt')
            ->whereNotNull('pictures')
            ->whereHas('store' , function ($query) {
                $query->whereNotNull('validatedAt');
            })
            ->with(['store' => function ($query) {
                $query->whereNotNull('validatedAt');
            }])->orderBy('created_at', 'desc');
        if ($request->productName) {
            $products = $products->where('name', 'like', '%' . $request->productName . '%');
        }
        if ($request->category) {
            $products = $products->where('category', 'like', '%' . $request->category . '%');
        }
        if ($request->mark) {
            $products = $products->where('mark', 'like', '%' . $request->mark . '%');
        }
        if ($request->wilaya) {
            $products = $products->where('wilaya', 'like', '%' . $request->wilaya . '%');
        }
        if ($request->minPrice && $request->maxPrice) {
            $products = $products->whereBetween('price', [intval($request->minPrice), intval($request->maxPrice)]);
        }
        if ($request->magasinId) {
            $products = $products->where('magasinId' , $request->magasinId);
        }
        if ($request->subCategories) {
            foreach ($request->subCategories as $subCategory) {
                $products = $products->where('subCategory', 'like', '%' . $subCategory . '%');
            }
        }
        return $products->paginate(10);
    }

    public function store(Request $request)
    {
        $store = Store::where('id', $request->magasinId)->first();
        if ($store) {
            $inputs = $request->only(['name', 'category', 'description', 'subCategory',
                'price', 'stockDispo', 'mark', 'pictures', 'isPublished', 'quantityOnOrder',
                'reviews', 'ratingAverage', 'properties', 'discounts', 'homePlace', 'step', 'deliveredBy',
                'validatedAt', 'wilaya', 'magasinId']);
            if (!isset($request->wilaya)) {
                $inputs['wilaya'] = $store->wilaya;
            }

            if (isset($inputs['pictures'])) {
                $inputs['pictures'] = json_encode($inputs['pictures']);
            }

            if (isset($inputs['subCategory'])) {
                $inputs['subCategory'] = json_encode($inputs['subCategory']);
            }
            if (isset($inputs['properties'])) {
                $inputs['properties'] = json_encode($inputs['properties']);
            }
            if (isset($inputs['reviews'])) {
                $inputs['reviews'] = json_encode($inputs['reviews']);
            }
            if (isset($inputs['discounts'])) {
                $inputs['discounts'] = json_encode($inputs['discounts']);
            }
            if ($store->isFreeDelivery && $store->isFreeDelivery == 1) {
                $inputs['isFreeDelivery'] = 1;
            } else {
                $inputs['isFreeDelivery'] = 0;
            }
            // return $inputs;
            $product = Product::create($inputs);
            $productService = new ProductService();
            $productService->updateStockDispo($product);
           // Mail::to('Contact@placetta.com')->send(new ProductCreated($product , $store));
            Mail::to('Samia.bouibed@placetta.com')->send(new ProductCreated($product , $store));
            return $product->id;
        }
        return ['store not found !!! ', $request->all()];
    }

    public function migrate(Request $request)
    {
        $productExist = Product::where('firebaseId', $request->id)->count();
        if ($productExist === 0) {
            $user = User::where('firebaseId', $request->magasinId)->first();
            if ($user) {
                $inputs = $request->only(['name', 'category', 'description', 'subCategory',
                    'price', 'stockDispo', 'mark', 'pictures', 'isPublished', 'quantityOnOrder',
                    'reviews', 'ratingAverage', 'properties', 'discounts', 'homePlace', 'step', 'deliveredBy',
                    'validatedAt', 'wilaya']);
                $inputs['magasinId'] = $user->store->id;
                $inputs['firebaseId'] = $request->id;
                if (!isset($request->wilaya)) {
                    $inputs['wilaya'] = $user->store->wilaya;
                }

                if (isset($inputs['pictures'])) {
                    $inputs['pictures'] = json_encode($inputs['pictures']);
                }

                if (isset($inputs['subCategory'])) {
                    $inputs['subCategory'] = json_encode($inputs['subCategory']);
                }
                if (isset($inputs['properties'])) {
                    $inputs['properties'] = json_encode($inputs['properties']);
                }
                if (isset($inputs['reviews'])) {
                    $inputs['reviews'] = json_encode($inputs['reviews']);
                }
                if (isset($inputs['discounts'])) {
                    $inputs['discounts'] = json_encode($inputs['discounts']);
                }
                // return $inputs;
                $product = Product::create($inputs);
                return 'product created successfully !!';
            }
            return ['store not found !!! ', $request->all()];
        }
        return 'product already exist !!!';
    }

    public function show(Request $request, $id)
    {
        $product = Product::where('id' , $id)->with(['store' => function ($query) {
            $query->select('id' , 'title' , 'picture' , 'delivery' , 'deliveryType', 'isFreeDelivery');
        }])->first();
        if ($product->pictures) {
            $product->pictures = json_decode($product->pictures);
        }

        if ($product->subCategory) {
            $product->subCategory = json_decode($product->subCategory);
        }
        if ($product->properties) {
            $product->properties = json_decode($product->properties);
        }
        if ($product->reviews) {
            $product->reviews = json_decode($product->reviews);
        }
        if ($product->discounts) {
            $product->discounts = json_decode($product->discounts);
        }
        return $product;
    }

    public function update(Request $request, $id)
    {
        $inputs = $request->only(['name', 'category', 'description', 'subCategory',
            'price', 'stockDispo', 'mark','magasinId', 'pictures', 'isPublished', 'quantityOnOrder',
            'reviews', 'ratingAverage', 'properties', 'discounts', 'homePlace', 'step', 'deliveredBy',
            'validatedAt', 'wilaya', 'id']);
        if (isset($inputs['pictures'])) {
            $inputs['pictures'] = json_encode($inputs['pictures']);
        }

        if (isset($inputs['subCategory'])) {
            $inputs['subCategory'] = json_encode($inputs['subCategory']);
        }
        if (isset($inputs['properties'])) {
            $inputs['properties'] = json_encode($inputs['properties']);
        }
        if (isset($inputs['reviews'])) {
            $inputs['reviews'] = json_encode($inputs['reviews']);
        }
        if (isset($inputs['discounts'])) {
            $inputs['discounts'] = json_encode($inputs['discounts']);
        }
        Product::where('id', $id)->update($inputs);
        $productService = new ProductService();
        $productService->updateStockDispo(Product::where('id', $id)->first());

        return 'product updated successfully !!';
    }

    public function getProductsByStoreId(Request $request, $storeId)
    {
        $products = Product::where('magasinId', $storeId);
        if (isset($request->allProducts)) {
            return $products->orderBy('created_at', 'desc')->get();
        } else {
            $products = $products->whereNotNull('validatedAt')
                ->whereNotNull('pictures');
        }
        return $products->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getStoreProductsAndCount(Request $request, $storeId)
    {
        $products = Product::where('magasinId', $storeId)->where('isPublished' , 1)
           ->orderBy('created_at', 'desc');
        if (isset($request->allProducts)) {
            return ['results' => $products->get(), 'count' => $products->count()];
        }
        return ['results' => $products->whereNotNull('validatedAt')
        ->whereNotNull('pictures')->paginate(10), 'count' => $products->whereNotNull('validatedAt')
        ->whereNotNull('pictures')->count()];
    }

    public function destroy(Request $request, $id)
    {
        $productOrdersInprogress = Order::where([['products', 'like', '"id":"' . $id . '"'], ['status', 'inProgress']])
            ->count();
        if ($productOrdersInprogress > 0) {
            return response()->json([
                'message' => 'products have an inprogress order', 'inProgressOrder' => true], 404);
        } else {
            Product::where('id', $id)->delete();
        }

        return 'product destroyed successfully';
    }

    public function getProductsHome()
    {

         $products = Product::whereNotNull('validatedAt')
           ->whereHas('store' , function ($query) {
               $query->whereNotNull('validatedAt');
           })
           ->with(['store' => function ($query) {
               $query->whereNotNull('validatedAt');
           }])
           ->withCount('orders')
           ->having('orders_count' , '>' , 0)
           ->inRandomOrder()
           ->limit(10)
           ->get();
         return $products;

        /* $stores = Store::whereNotNull('validatedAt')
            ->withCount('orders')
            ->orderBy('orders_count' , 'desc')
            ->limit(10)->get();

            $products= [];
        foreach($stores as $store){
            $product = Product::whereNotNull('validatedAt')
            ->whereHas('store' , function ($query) {
                $query->whereNotNull('validatedAt');
            })
            ->with(['store' => function ($query) {
            $query->whereNotNull('validatedAt');
            }])->where('magasinId', $store->id)
            ->orderBy('validatedAt' , 'desc')
            ->first();
            array_push($products, $product);
        }
       return $products;*/
    }

    public function deleteProductByCategory($magasinId, $category)
    {
        Product::where([['category', $category], ['magasinId', $magasinId]])
            ->update(['isPublished' => false]);
    }

    public function activateFreeDeliveryForProductsByCategory(Request $request)
    {
        foreach($request->categories as $key => $category){
            $products = Product::where('isPublished', 1)->whereNotNull('pictures')
        ->whereNotNull('validatedAt')->where('category', $category)
            ->update(['isFreeDelivery' => 1]);
        }
           return 'done';
    }
    public function deActivateFreeDeliveryForProductsByCategory(Request $request)
    {
        foreach($request->categories as $key => $category){
        $products = Product::where('isPublished', 1)->whereNotNull('pictures')
        ->whereNotNull('validatedAt')->where('category', $category)
            ->update(['isFreeDelivery' => 0]);
        }
            return 'done';
    }

    public function getProductsStatistics()
    {
        $validatedProducts = Product::where('isPublished', 1)
            ->whereNotNull('pictures')->whereNotNull('validatedAt')->count();
        $notValidatedProducts = Product::whereNotNull('pictures')
        ->whereNull('validatedAt')->count();
        $publishedProducts = Product::where('isPublished', 1)
            ->whereNotNull('pictures')->count();
        $notPublishedProducts = Product::where('isPublished', 0)
            ->whereNotNull('pictures')->count();
        $allProducts = Product::whereNotNull('pictures')->count();
        return response()->json([
            'validatedProducts' => $validatedProducts,
            'publishedProducts' => $publishedProducts,
            'notPublishedProducts' => $notPublishedProducts,
            'notValidatedProducts' => $notValidatedProducts,
            'allProducts' => $allProducts
        ], 200);
    }

    public function getAllProducts(Request $request)
    {
        $products = Product::whereNotNull('pictures')->where('isBook', 0)
            ->with(['store' => function ($query) {
                $query->select('id' , 'title');
            }]);
        if (isset($request->keyword)) {
            if($request->filterType == 'id'){
                $products = $products->where(function($query) use ($request) {
                    $query->where('id', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'name'){
                $products = $products->where(function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'magasinId'){
                $products = $products->where(function($query) use ($request) {
                    $query->where('magasinId', 'like', '%' . $request->keyword . '%');
                });
            }
            else if($request->filterType == 'store'){
                $keyword = $request->keyword;
                $products = $products->whereHas('store', function($query) use ($keyword) {
                    $query->where('title' , 'like' , '%'.$keyword.'%');
                });
            }
            else{
                $products = $products->where(function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->keyword . '%')
                        ->orWhere('id', 'like', '%' . $request->keyword . '%')
                        ->orWhere('category', 'like', '%' . $request->keyword . '%')
                        ->orWhere('subCategory', 'like', '%' . $request->keyword . '%')
                        ->orWhere('mark', 'like', '%' . $request->keyword . '%')
                        ->orWhere('wilaya', 'like', '%' . $request->keyword . '%')
                        ->orWhere('magasinId', 'like', '%' . $request->keyword . '%')
                        ->orWhereHas('store', function($query) use ($request) {
                            $query->where('title' , 'like' , '%'.$request->keyword.'%');
                        });
                });
            }
        }
        if (isset($request->notPublished)) {
            $products = $products->where('isPublished' , 0);
        }
        else if (isset($request->validated)) {
            $products = $products->where('isPublished', 1)
            ->whereNotNull('pictures')->whereNotNull('validatedAt');
        }
        else if (isset($request->notValidated)) {
            $products = $products->whereNotNull('pictures')->whereNull('validatedAt');
        }
        else {
            $products = $products->where('isPublished' , 1);
        }
        if(isset($request->sortColumn)){
            if(isset($request->sortType)){
                $products = $products->orderBy($request->sortColumn , $request->sortType);
            }
            else{
                $products = $products->orderBy($request->sortColumn);
            }
        }
        else{
            $products = $products->orderBy('created_at' , 'desc');
        }
        return $products->paginate(10);
    }

    public function getSubcategoryNewArrivals(Request $request)
    {
        $products = Product::whereNotNull('validatedAt')
            ->whereNotNull('pictures')
            ->whereHas('store' , function ($query) {
                $query->whereNotNull('validatedAt');
            })
            ->with(['store' => function ($query) {
                $query->whereNotNull('validatedAt');
            }])
            ->where('subCategory', 'like', '%' . $request->subCategory . '%')
            ->orderBy('created_at', 'desc');

        return $products->paginate(10);
    }

    public function getProductsForSearchBar(Request $request)
    {
        $products = Product::whereNotNull('validatedAt')
            ->whereNotNull('pictures')
            ->whereHas('store' , function ($query) {
                $query->whereNotNull('validatedAt');
            })
            ->with(['store' => function ($query) {
                $query->whereNotNull('validatedAt');
            }])
            ->orderBy('created_at', 'desc');

        if (isset($request->keyword)) {
            $products = $products->where(function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('category', 'like', '%' . $request->keyword . '%')
                    ->orWhere('subCategory', 'like', '%' . $request->keyword . '%')
                    ->orWhere('mark', 'like', '%' . $request->keyword . '%')
                    ->orWhere('wilaya', 'like', '%' . $request->keyword . '%');
            });
        }

        if (isset($request->magasinId)) {
            $products = $products->where('magasinId' , $request->magasinId);
        }

        return $products->paginate(3);
    }

    public function getCategoriesProducts(Request $request) {
        $productName = '';
        $category = '';
        $categoryRequest = '';
        $productsResult = [];
        $products = Product::whereNotNull('validatedAt')
            ->whereNotNull('pictures')
            ->whereHas('store' , function ($query) {
                $query->whereNotNull('validatedAt');
            })
            ->with(['store' => function ($query) {
                $query->whereNotNull('validatedAt');
            }])->orderBy('created_at', 'desc');

        foreach($request->categories as $category ) {
            $categoryRequest = $categoryRequest.' / '. $category;
            $productsResult[$category] =
                Product::whereNotNull('validatedAt')
                    ->whereNotNull('pictures')
                    ->whereHas('store' , function ($query) {
                        $query->whereNotNull('validatedAt');
                    })
                    ->with(['store' => function ($query) {
                        $query->whereNotNull('validatedAt');
                    }])->orderBy('created_at', 'desc')
                    ->where('category', 'like', '%' . $category . '%')
                ->limit(10)->get();
        }
        return $productsResult;

        return $products->paginate(10);
    }

    public function getProductsByIdList(Request $request) {
        $idList = $request->id_list;
        $products = Product::whereIn('id' , $idList)->get();
        return $products;
    }

    public function deleteProductsByStoreId($magasinId) {
        $productOrdersInprogress = Order::where([['magasinId', $magasinId], ['status', 'inProgress']])
            ->count();
        if ($productOrdersInprogress > 0) {
            return response()->json([
                'message' => 'products have an inprogress order', 'inProgressOrder' => true], 404);
        } else {
            Product::where('magasinId' , $magasinId)->delete();
        }

        return 'store products deleted successfully!';
    }

    public function validateProductsByStoreId($magasinId , Request $request) {
        Product::where('magasinId' , $magasinId)->update(['validatedAt' , $request->validatedAt]);
        return 'store products validated successfully!';
    }

    public function deleteProductsWithNoStore() {
        Product::where('magasinId' , 207)->delete();
        Product::where('magasinId' , 213)->delete();
        Product::where('magasinId' , 214)->delete();
        return 'products deleted';
    }

    public function refreshProductsDiscounts() {
        $products = Product::whereJsonContains('discounts' , ['isActivated' => true])->get();
        foreach ($products as $product) {
            $product->discounts = json_decode($product->discounts);
            foreach ($product->discounts as $discount) {
                $date = new \DateTime($discount->endDate);
                $today = new \DateTime();
                if ($discount->isActivated && $date < $today) {
                    $discount->isActivated = false;
                }
            }

            Product::where('id' , $product->id)->update(['discounts' => json_encode($product->discounts)]);

        }
        return sizeof($products);
    }

    public function getDiscountedProducts($id) {
        $products = Product::where('magasinId' , $id)
            ->whereJsonContains('discounts' , ['isActivated' => true])->get();
        return $products;
    }

    public function getAllDiscountedProducts() {
        $products = Product::where('isPublished', 1)
        ->whereNotNull('pictures')->whereNotNull('validatedAt')->whereJsonContains('discounts' ,
        ['isActivated' => true])->with(['store' => function ($query) {
            $query->select('id' , 'title');
        }])->get();
        return $products;
    }

    public function refreshProductsStock() {
        $products = Product::whereNotNull('validatedAt')
            ->whereNotNull('pictures')
            ->whereHas('store' , function ($query) {
                $query->whereNotNull('validatedAt');
            })
            ->orderBy('created_at', 'desc')->get();
        $productService = new ProductService();
        foreach ($products as $product) {
            $productService->updateStockDispo($product);
        }
    }
}
