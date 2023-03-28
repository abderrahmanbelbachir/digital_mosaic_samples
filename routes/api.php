<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookmarkedStoresController;
use App\Http\Controllers\BookmarkedProductsController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UtilitiesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HomePromotionController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\editionsController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\CouponController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api')
    ->apiResource('/products', ProductController::class);

Route::middleware('api')
    ->apiResource('/caisse', CaisseController::class);

Route::middleware('api')
    ->apiResource('/editions', editionsController::class);

Route::middleware('api')
    ->apiResource('/coupons', CouponController::class);

Route::middleware('api')
    ->get('/coupons/filter/{code}', [CouponController::class, 'getCouponByCode']);
    Route::middleware('api')
    ->get('/influencers_coupons', [CouponController::class, 'getInfluencerCoupons']);

Route::middleware('api')
    ->post('/products/migrate', [ProductController::class, 'migrate']);

Route::middleware('api')
    ->get('/products/store/{storeId}', [ProductController::class, 'getProductsByStoreId']);

Route::middleware('api')
    ->get('/products/Home/Categories/list', [ProductController::class, 'getCategoriesProducts']);

Route::middleware('api')
    ->get('/products/subCategory/newArrivals', [ProductController::class, 'getSubcategoryNewArrivals']);

Route::middleware('api')
    ->get('/products/store/count/{storeId}', [ProductController::class, 'getStoreProductsAndCount']);

Route::middleware('api')
    ->get('/products/all/statistics', [ProductController::class, 'getProductsStatistics']);

Route::middleware('api')
    ->get('/products/home/list', [ProductController::class, 'getProductsHome']);

Route::middleware('api')
    ->get('/products/admin/list', [ProductController::class, 'getAllProducts']);

Route::middleware('api')
    ->get('/products/searchBar/list', [ProductController::class, 'getProductsForSearchBar']);

Route::middleware('api')
    ->get('/products-by-id-list', [ProductController::class, 'getProductsByIdList']);

Route::middleware('api')
    ->delete('/delete-store-products/{magasinId}', [ProductController::class, 'deleteProductsByStoreId']);

Route::middleware('api')
    ->post('/validate-store-products/{magasinId}', [ProductController::class, 'validateProductsByStoreId']);

Route::middleware('api')
    ->delete('/products/category/{magasinId}/{category}',
        [ProductController::class, 'deleteProductByCategory']);

Route::middleware('api')
    ->post('/products/activate-free-delivery',
        [ProductController::class, 'activateFreeDeliveryForProductsByCategory']);

Route::middleware('api')
    ->post('/products/deactivate-free-delivery',
        [ProductController::class, 'deActivateFreeDeliveryForProductsByCategory']);

Route::middleware('api')
    ->apiResource('/users', UserController::class);

Route::middleware('api')
    ->post('/users/migrate', [UserController::class, 'migrate']);

Route::middleware('api')
    ->post('/users/mobile/{phone}', [UserController::class, 'getUserByPhone']);

Route::middleware('api')
    ->post('/users-with-trash/mobile/{phone}', [UserController::class, 'getUserByPhoneWithTrash']);

Route::middleware('api')
    ->post('/users/email/{email}', [UserController::class, 'getUserByEmail']);

Route::middleware('api')
    ->post('/all-users', [UserController::class, 'getAllCustomers']);

Route::middleware('api')
    ->post('/all-users-admin', [UserController::class, 'getAllUsers']);

Route::middleware('api')
    ->get('/refresh-validation-status', [UserController::class, 'refreshIsValidating']);

Route::middleware('api')
    ->apiResource('/stores', StoreController::class);

Route::middleware('api')
    ->post('/stores/migrate', [StoreController::class, 'migrate']);

Route::middleware('api')
    ->post('/stores/switch/{id}', [StoreController::class, 'switchToStore']);

Route::middleware('api')
    ->post('/stores/switch-to-customer/{id}', [StoreController::class, 'switchToCustomer']);

Route::middleware('api')
    ->post('/fill-maystro-delivery', [StoreController::class, 'fillMaystroDeliveryType']);

Route::middleware('api')
    ->get('/stores/home/list', [StoreController::class, 'getHomeStores']);

Route::middleware('api')
    ->get('/stores/searchBar/list', [StoreController::class, 'getStoresForSearchBar']);

Route::middleware('api')
    ->apiResource('/orders', OrderController::class);

Route::middleware('api')
    ->get('/orders/all/statistics', [OrderController::class, 'getOrdersStatistics']);
    Route::middleware('api')
    ->get('/orders/filter/delivery', [OrderController::class, 'getOrdersForDelivery']);
Route::middleware('api')
    ->get('/orders/all/placetta-delivery/statistics',
     [OrderController::class, 'getOrdersStatisticsForDelivery']);


Route::middleware('api')
    ->post('/orders/migrate', [OrderController::class, 'migrate']);

Route::middleware('api')
    ->get('/orders/store/count/{magasinId}', [OrderController::class, 'getStoreOrdersWithCount']);

Route::middleware('api')
    ->get('/orders/store/{magasinId}', [OrderController::class, 'getAllStoreOrders']);

Route::middleware('api')
    ->get('/orders/customer/count/{userId}', [OrderController::class, 'getCustomerOrdersWithCount']);

Route::middleware('api')
    ->get('/orders/customer/{userId}', [OrderController::class, 'getAllCustomerOrders']);

Route::middleware('api')
    ->post('/orders/cancel/{orderId}', [OrderController::class, 'cancelOrder']);
Route::middleware('api')
    ->post('/orders/invoice', [OrderController::class, 'downloadPDF']);
Route::middleware('api')
    ->post('/orders/commissions/{magasinId}', [OrderController::class, 'fixCommissions']);
Route::middleware('api')
    ->apiResource('/cards', CardController::class);

Route::middleware('api')
    ->post('/cards/migrate', [CardController::class, 'migrate']);

Route::middleware('api')
    ->get('/cards/user/{userId}', [CardController::class, 'userCards']);

Route::middleware('api')
    ->delete('/cards/user/{userId}', [CardController::class, 'deleteUserCards']);

Route::middleware('api')
    ->apiResource('/bookmarkedStores', BookmarkedStoresController::class);

Route::middleware('api')
    ->apiResource('/bookmarkedProducts', BookmarkedProductsController::class);

Route::middleware('api')
    ->get('/bookmarkedStores/user/{userId}', [BookmarkedStoresController::class, 'getUserBookmarkStores']);

Route::middleware('api')
    ->get('/bookmarkedProducts/user/{userId}', [BookmarkedProductsController::class, 'getUserBookmarkProducts']);

Route::middleware('api')
    ->get('/bookmarkedStores/user/count/{userId}', [BookmarkedStoresController::class, 'getUserBookmarkStoresCount']);

Route::middleware('api')
    ->post('/bookmarkedStores/migrate', [BookmarkedStoresController::class, 'migrate']);

Route::middleware('api')
    ->get('/bookmarkedProducts/user/count/{userId}', [BookmarkedProductsController::class, 'getUserBookmarkProductsCount']);

Route::middleware('api')
    ->post('/bookmarkedProducts/migrate', [BookmarkedProductsController::class, 'migrate']);


Route::middleware('api')
    ->apiResource('/categories', CategoryController::class);
    Route::middleware('api')
    ->apiResource('/book-categories', BookCategoryController::class);

Route::middleware('api')
    ->post('/categories/migrate', [CategoryController::class, 'migrate']);

Route::middleware('api')
    ->post('/categories/update-status', [CategoryController::class, 'updateCategoryStatus']);

Route::middleware('api')
    ->get('/fixCategories', [CategoryController::class, 'fixCategories']);

    Route::middleware('api')
    ->get('/categories-for-coupon', [CategoryController::class, 'getCategoriesList']);

Route::middleware('api')
    ->get('/fixBazarkomOrder', [OrderController::class, 'fixBazarkomOrder']);

Route::middleware('api')
    ->get('/deleteProductsWithNoStore', [ProductController::class, 'deleteProductsWithNoStore']);


Route::middleware('api')
    ->apiResource('/marks', MarkController::class);

Route::middleware('api')
    ->post('/marks/migrate', [MarkController::class, 'migrate']);

Route::middleware('api')
    ->apiResource('/notifications', NotificationController::class);

Route::middleware('api')
    ->apiResource('/sales', SaleController::class);

Route::middleware('api')
    ->get('/sales/store/count/{magasinId}', [SaleController::class, 'getStoreSalesWithCount']);

Route::middleware('api')
    ->get('/sales/store/{magasinId}', [SaleController::class, 'getAllStoreSales']);

Route::middleware('api')
    ->get('/sales/customer/count/{userId}', [SaleController::class, 'getCustomerShoppingWithCount']);

Route::middleware('api')
    ->get('/sales/customer/{userId}', [SaleController::class, 'getAllCustomerShopping']);


Route::middleware('api')
    ->post('/log-message', [UtilitiesController::class, 'logMessage']);

Route::middleware('api')
    ->get('/dashboard/statistics', [DashboardController::class, 'getDashboardStatistics']);

Route::middleware('api')
    ->get('/dashboard/statistics/debug/yassine', [DashboardController::class, 'getDashboardStatisticsDebug']);

Route::middleware('api')
    ->get('/dashboard/statistics/payments', [DashboardController::class, 'getPaymentStatistics']);


    Route::middleware('api')
    ->get('/dashboard/statistics/getIncomes', [DashboardController::class, 'getTotalIncomes']);

Route::middleware('api')
    ->apiResource('/payments', PaymentController::class);
    Route::middleware('api')
    ->get('/payments/filter/unpaid-orders', [PaymentController::class, 'getUnpaidOrders']);
    Route::middleware('api')
    ->get('/payments/filter/payments-track', [PaymentController::class, 'getPayemntsTrack']);
    Route::middleware('api')
    ->get('/payments/filter/payments-history', [PaymentController::class, 'getPayemntsHistory']);


/*
Route::middleware('api')
    ->get('/payments/filter/stores', [PaymentController::class, 'showStores']);

Route::middleware('api')
    ->get('/setComissions', [PaymentController::class, 'setComission5']);
*/

Route::middleware('api')
    ->post('/unAuthorizedRequest', [UtilitiesController::class, 'unAuthorizedRequest'])->name('unAuthorizedRequest');

Route::middleware('api')
    ->post('/refresh-products-discounts', [ProductController::class, 'refreshProductsDiscounts']);


Route::post('/run/migrations' , [\App\Http\Controllers\UserController::class , 'runMigration']);
Route::post('/run/clear-cache' , [\App\Http\Controllers\UserController::class , 'clearCache']);

Route::post('/migrations/reset-1-steps' , [\App\Http\Controllers\UserController::class , 'resetMigration']);


Route::middleware('api')
    ->get('/discounts/store/{id}', [ProductController::class, 'getDiscountedProducts'])->name('getDiscountedProducts');

Route::middleware('api')
    ->get('/promotions', [ProductController::class, 'getAllDiscountedProducts'])
    ->name('getAllDiscountedProducts');

Route::middleware('api')
    ->post('/refresh-products-stock', [ProductController::class, 'refreshProductsStock'])
    ->name('refreshProductsStock');

Route::middleware('api')
    ->apiResource('/home-promotions', HomePromotionController::class);

Route::post('/update-home-place' , [\App\Http\Controllers\UtilitiesController::class , 'fillHomePlaceForStores']);

Route::post('/sync-validated-stores' , [\App\Http\Controllers\StoreController::class ,
    'syncValidatedStores']);



Route::middleware('api')
    ->apiResource('/books', BookController::class);

Route::middleware('api')
    ->get('/books/home/list', [BookController::class, 'getBooksHome']);

Route::middleware('api')
    ->get('/search-books', [BookController::class, 'searchBooks']);

Route::middleware('api')
    ->get('/authors', [BookController::class, 'getAuthors']);
Route::middleware('api')

    ->get('/books-categories', [BookController::class, 'getBookCategories']);

Route::middleware('api')
    ->get('/books/fix', [BookController::class, 'fixBookStore']);
    Route::middleware('api')
    ->get('/fix-book-pictures', [BookController::class, 'updateBooksWithoutPic']);

Route::middleware('api')
    ->get('/books/searchBar/list', [BookController::class, 'getProductsForSearchBar']);


Route::middleware('api')
    ->get('/generateBooks', [BookController::class, 'createbooks']);

Route::middleware('api')
    ->get('/disableBooks/{id}', [BookController::class, 'disableBooks']);

Route::middleware('api')
    ->get('/enableBooks/{id}', [BookController::class, 'enableBooks']);


Route::middleware('api')
    ->post('/send-sms', [\App\Http\Controllers\SMSController::class, 'sendAuthSms']);

Route::middleware('api')
    ->post('/verify-code', [\App\Http\Controllers\SMSController::class, 'confirmSmsCode']);
