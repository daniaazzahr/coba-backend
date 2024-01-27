<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('users', [UsersController::class, 'index']);
Route::post('user', [UsersController::class, 'createUser']);
Route::get('user/{id}', [UsersController::class, 'getUserID']);
Route::put('user/{id}', [UsersController::class, 'editUser']);
Route::delete('user/{id}', [UsersController::class, 'deleteUser']);
Route::get('users', [UsersController::class, 'filterUsers']);

// AUTHENTICATION
Route::post('registrasi', [AuthController::class, 'registrasiUser']);
