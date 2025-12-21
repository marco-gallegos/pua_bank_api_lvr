<?php

use App\Http\Controllers\API\BankAccountsController;
use App\Http\Controllers\API\CategoriesController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\LogoutController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', LoginController::class);
    Route::post('logout', LogoutController::class)->middleware('auth:api');
});


Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    Route::apiResource('bank-account', BankAccountsController::class);
    Route::apiResource('category', CategoriesController::class);
});
