<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ChannelController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::get('/', function () {
    $user = User::cursor()->filter(fn ($user) => $user->id > 1)->values()->all();
    dd($user);
    // User::with('notifications')->latest()->limit(10)->dd()->get();
    // User::all()->dd();
});

Route::group([
    'middleware' => ['throttle'],
    'namespace' => '\Laravel\Passport\Http\Controllers',
], function ($router) {
    $router->post('login', [
        'as' => 'auth.login',
        'uses' => AccessTokenController::class . '@issueToken'
    ]);
});

Route::post('register', [
    'as' => 'auth.register',
    'uses' => AuthController::class . '@register'
]);

Route::post('register-verify', [
    'as' => 'auth.register.verify',
    'uses' => AuthController::class . '@registerVerify'
]);

Route::post('resend-verification-code', [
    'as' => 'auth.register.resend.verfication.code',
    'uses' => AuthController::class . '@resendVerificationCode'
]);

Route::post('change-email', [
    // 'middlware' => ['auth:api'],
    'as' => 'change.email',
    'uses' => UserController::class . '@changeEmail'

])->middleware('auth:api');

Route::post('change-email-submit', [
    // 'middlware' => ['auth:api'],
    'as' => 'change.email.submit',
    'uses' => UserController::class . '@changeEmailSubmit'

])->middleware('auth:api');


// Route::group(['middleware' => ['auth:api'], 'prefix' => '/channel'], function ($router) {
//     Route::put('/{id?}', [
//         'as' => 'channel.update',
//         'uses' => ChannelController::class . '@update'
//     ]);
// });

Route::middleware(['auth:api'])->prefix('/channel')->group(function () {
    Route::put('/{id?}', [
        'as' => 'channel.update',
        'uses' => ChannelController::class . '@update'
    ]);

    Route::match(['post','put'],'/', [
        'as' => 'channel.upload.banner',
        'uses' => ChannelController::class . '@uploadBanner'
    ]);
});


// Route::post('register', [AuthController::class, 'register'])->name('register');
// Route::post('login', [AuthenticationController::class, 'login'])->name('login');