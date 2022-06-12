<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductCategoryController;

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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::put('update-photo', [UserController::class, 'updatePhoto']);
    Route::get('user', [UserController::class, 'fetch']);
    Route::put('user', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('autologin', [UserController::class, 'autoLogin']);

});

Route::get('get-all-products', [ProductController::class, 'getAll']);
Route::post('add-products', [ProductController::class, 'addProduct']);
Route::put('edit-products', [ProductController::class, 'editProduct']);
Route::post('del-products', [ProductController::class, 'deleteProduct']);


Route::get('get-category', [ProductCategoryController::class, 'getCategory']);
Route::post('add-category', [ProductCategoryController::class, 'addCategory']);

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
