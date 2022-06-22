<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\CompanyController;

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
    //user
    Route::put('update-photo', [UserController::class, 'updatePhoto']);
    Route::get('verified', [UserController::class, 'verificationLogin']);
    Route::get('user', [UserController::class, 'fetch']);
    Route::put('user', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('autologin', [UserController::class, 'autoLogin']);

});

//ccow
Route::post('add-ccow', [CompanyController::class, 'addCcow']);
Route::get('get-ccow', [CompanyController::class, 'getCcow']);

//mitra
Route::post('add-mitra', [CompanyController::class, 'addMitra']);
Route::get('get-mitra', [CompanyController::class, 'getMitra']);

//submitra
Route::post('add-submitra', [CompanyController::class, 'addSubmitra']);
Route::get('get-submitra', [CompanyController::class, 'getSubMitra']);

//product
Route::get('get-all-products', [ProductController::class, 'getAll']);
Route::post('add-products', [ProductController::class, 'addProduct']);
Route::put('edit-products', [ProductController::class, 'editProduct']);
Route::post('del-products', [ProductController::class, 'deleteProduct']);

//category
Route::get('get-category', [ProductCategoryController::class, 'getCategory']);
Route::post('add-category', [ProductCategoryController::class, 'addCategory']);

//employee
Route::post('add-employee', [EmployeeController::class, 'addEmployee']);
Route::get('get-all-employee', [EmployeeController::class, 'getAllEmployee']);
Route::get('get-employee', [EmployeeController::class, 'getEmployee']);
Route::get('get-permit-employee', [EmployeeController::class, 'getEmployeeByPermit']);
Route::put('edit-employee', [EmployeeController::class, 'editEmployee']);

//user login
Route::post('login-permit', [EmployeeController::class, 'permitLogin']);
Route::get('token', [UserController::class, 'getUserById']);
Route::post('login', [UserController::class, 'login']);
Route::post('login-employee', [UserController::class, 'loginEmployee']);
Route::post('register', [UserController::class, 'register']);
Route::post('registerOTP', [UserController::class, 'otpRegister']);


