<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserController;

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


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });




Route::group(['middleware'=>'api',
              'prefix'=>'auth'
],function($router){

Route:: post("user-signup","App\Http\Controllers\UserController@userSignUp");

Route::post('user-login',[UserController::class,'userLogin']);

Route::post("user-logout", "App\Http\Controllers\UserController@userLogout");

Route::post("email-verify","App\Http\Controllers\UserController@verifyEmail");

Route::post("resend-pin","App\Http\Controllers\UserController@resendPin");

Route::post("user-forgotpassword","App\Http\Controllers\UserController@forgotPassword");

Route::post("user-verify-pin","App\Http\Controllers\UserController@verifyPin");

Route::post("user-reset-password","App\Http\Controllers\UserController@resetPassword");
});





require __DIR__ .'/admin.php';
// Route::group(['middleware' => ['auth:sanctum']], function () {
//     Route::post("user-logout", "App\Http\Controllers\UserController@userLogout");
// });

Route::group(['middleware'=>'api',
              'prefix'=>'admin'
],function($router){


    Route:: post("admin-signup","App\Http\Controllers\AdminController@adminSignUp");

    Route::post("admin-login", "App\Http\Controllers\AdminController@adminLogin");

    Route::post("admin-logout", "App\Http\Controllers\AdminController@adminLogout");

    Route::post("admin-email-verify","App\Http\Controllers\AdminController@adminVerifyEmail");

    Route::post("admin-resend-pin","App\Http\Controllers\AdminController@adminResendPin");

    Route::post("admin-user-forgotpassword","App\Http\Controllers\AdminController@adminForgotPassword");

    Route::post("admin-user-verify-pin","App\Http\Controllers\AdminController@adminVerifyPin");

    Route::post("admin-user-reset-password","App\Http\Controllers\AdminController@adminResetPassword");

    Route::post("admin-create-sys-user","App\Http\Controllers\AdminController@adminCreateSystemUser");

    Route::patch("admin-update-permission","App\Http\Controllers\AdminController@adminUpdatePermission");

    Route::put("admin-update-sys-user","App\Http\Controllers\AdminController@adminUpdateSystem");

    Route::post("admin-create-categories","App\Http\Controllers\AdminController@createCategory");

    Route::post("admin-show-categories","App\Http\Controllers\AdminController@showCategory");
    });






Route::group(['middleware'=>'api',
              'prefix'=>'user'
],function($router){

    Route::post("user-booking","App\Http\Controllers\BookingController@storeReservation");

});


// Route::group(['middleware'=>'auth', 'prefix'=>'admin'],function($router){
//     Route::post('admin-permission',)
// })

