<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CarController;

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

Route::get('/customers/{customer}/show-car', [CustomerController::class, 'showCar']);
Route::post('/customers/{customer}/assign-car/{car}', [CustomerController::class, 'assignCar']);
Route::post('/customers/{customer}/unassign-car', [CustomerController::class, 'unassignCar']);

Route::get('/cars/{car}/show-customer', [CarController::class, 'showCustomer']);

Route::resources([
    'customers' => CustomerController::class,
    'cars' => CarController::class
]);
