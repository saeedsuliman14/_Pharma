<?php

use App\Http\Controllers\AddressesController;
use App\Http\Controllers\AdminStatisticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\PharmacyBranchesController;
use App\Http\Controllers\InventoryItemsController;
use App\Http\Controllers\OrdersNotificationsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PharmaciesController;
use App\Http\Controllers\PharmacyBranchInfoController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


//Public route
//inventory items table routes
//Search for an inventory item
Route::get('inventoryItems/search/{data}', [InventoryItemsController::class,'search']);
Route::get('inventoryItems/all', [InventoryItemsController::class,'all']);
//phone Api
Route::get('inventoryItems', [InventoryItemsController::class,'index']);
//end of phone Api

//Addresses Controller Route
// Route::resource('Addresses',AddressesController::class);/** uncomment to activate the addresses controller */

//orders controller Routes
Route::get('orders', [OrdersController::class,'index']);
//phone Api
Route::get('orders/allOrdersExceptRejected',[OrdersController::class,'allOrdersExceptRejected']);
Route::get('orders/allOrders', [OrdersController::class,'allOrders']);
// end of phone Api

//pharmacy Branches table controller
Route::resource('PharmaciesBranches', PharmacyBranchesController::class);//modified from */Pharmacies*

/** Register and login routes */
Route::post('/register',[AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
/** end of Reg and Log */


/** Testing Area */
##########################
/** End of Testing Area */

//Private Routes
Route::group(['middleware' => ['auth:sanctum'] ],function () {
    /** Logout route */
    Route::post('/logout', [AuthController::class,'logout']);

    // admin statistics
        //1_122 photo
    Route::get('/admin/viewsData',[AdminStatisticsController::class,'views']);
        //2_122 photo
    Route::get('/admin/salesData',[AdminStatisticsController::class,'sales']);
        //3_122 photo
    Route::get('/admin/getOnlineOrdersStatistics/{time}',[AdminStatisticsController::class,'getOnlineOrdersStatistics']);
        //4_122 photo
    Route::get('/admin/getViewsStatistics/{time}',[AdminStatisticsController::class,'getViewsStatistics']);
        //5_122 photo
    Route::get('/admin/getOrdersLineStatistics/{time}',[AdminStatisticsController::class,'getOrdersLineStatistics']);

});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });





//Public route *2*......
//users table controller route
Route::resource('users', UsersController::class);

//categories table controller route
Route::get('categories', [CategoriesController::class,'index']);

//companies table controller route
Route::get('companies', [CompanyController::class,'index']);

//employees table controller routes
Route::get('employees', [EmployeesController::class,'index']);
Route::delete('employees/{employee}',[EmployeesController::class,'destroy']);

//OrdersNotifications table controller route
Route::get('ordersNotifications', [OrdersNotificationsController::class,'index']);

//suppliers table controller route
Route::get('suppliers', [SuppliersController::class,'index']);

//Products table Controller routes
Route::post("products/upload", [ProductsController::class, 'uploadPhoto']);
Route::get('products', [ProductsController::class,'index']);

//pharmacies table controller route
Route::get('pharmacies', [PharmaciesController::class,'index']);

//pharmacy branch info controller
Route::get("pharmacyBranchInfo/{type}/{id}", [PharmacyBranchInfoController::class, 'moreInfo']);
Route::get('pharmacyBranchInfo', [PharmacyBranchInfoController::class,'index']);
