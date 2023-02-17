<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::group(['middleware' => 'api'], function ($route) {
    Route::Post('/register', [UserController::class, 'register']);
    Route::Post('/login', [UserController::class, 'login']);
    Route::Post('/profile', [UserController::class, 'profile']);
    Route::Post('/refresh', [UserController::class, 'refresh']);
    Route::Post('/logout', [UserController::class, 'logout']);
    Route::Post('/profile-update', [UserController::class, 'updateprofile']);
    Route::Post('/delete', [UserController::class, 'delete']);
    Route::Post('/uploadproducts', [UserController::class, 'uploadproducts']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
