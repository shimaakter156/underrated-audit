<?php

use App\Http\Controllers\Api\MobileApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Web\SettingController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);
Route::group(['middleware' => 'jwt:api'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::get('app-supporting-data', [SettingController::class, 'appSupportingData']);
    Route::get('get-location-list',[MobileApiController::class,'getLocation']);
    Route::post('get-user-location-list/{userId}',[MobileApiController::class,'getUserLocation']);
    Route::get('get-product-list',[MobileApiController::class,'getProduct']);
    Route::post('store-market-price',[MobileApiController::class,'store']);
    Route::get('get-wholesale-market-price-list',[MobileApiController::class,'index']);

});

Route::group(['middleware' => ['jwt:api']], function () {
    Route::get('encoded-result/{param}',[CommonController::class,'encode']);
    Route::get('decoded-result/{param}',[CommonController::class,'decode']);

    // ADMIN USERS
    Route::group(['prefix' => 'user'],function () {
        Route::post('add', [UserController::class, 'store']);
        Route::post('update', [UserController::class, 'update']);
        Route::post('list', [UserController::class, 'index']);

        Route::get('modal',[CommonController::class,'userModalData']);
        Route::get('get-user-info/{staffId}',[UserController::class,'getUserInfo']);
        Route::post('reset-password',[UserController::class,'updatePassword']);
        Route::post('password-change',[UserController::class,'passwordChange']);
    });
//    Route::get('/send-test-email', function () {
//        $toEmail = 'rabiul@aci-bd.com';
//
//        try {
//            Mail::to($toEmail)->send(new ApprovedEmail('AMS advance approval request.', [
//                'title' => 'Advance Approval Request',
//                'msg' => 'A new request is waiting for your approval.',
//                'link' => 'my-approvals',
//                'linkName' => 'My Approvals',
//                'linkShow' => true
//            ]));
//
//            Log::info('Test email sent successfully to: ' . $toEmail);
//            return 'Test email sent successfully!';
//        } catch (\Exception $e) {
//            Log::error('Failed to send test email to: ' . $toEmail, [
//                'error' => $e->getMessage(),
//                'trace' => $e->getTraceAsString()
//            ]);
//
//            return response()->json([
//                'status' => 'error',
//                'message' => 'Failed to send email',
//                'error' => $e->getMessage()
//            ], 500);
//        }
//    });


});

//Route::get('test-mail/{email}',[ReportController::class,'sendPHPMailerEmail']);