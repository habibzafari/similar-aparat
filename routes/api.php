<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::group([
    'middleware' => ['throttle'],
    'namespace' => '\Laravel\Passport\Http\Controllers',
], function ($router) {
    $router->post('login',[
        'as' => 'auth.login',
        'uses' => AccessTokenController::class.'@issueToken'
    ]);
});

Route::post('register', [
    'as' =>'auth.register',
    'uses' => AuthController::class.'@register'
]);

Route::post('register-verify', [
    'as' =>'auth.register.verify',
    'uses' => AuthController::class.'@registerVerify'
]);

Route::post('resend-verification-code', [
    'as' =>'auth.register.resend.verfication.code',
    'uses' => AuthController::class.'@resendVerificationCode'
]);
// Route::post('register', [AuthController::class, 'register'])->name('register');
// Route::post('login', [AuthenticationController::class, 'login'])->name('login');