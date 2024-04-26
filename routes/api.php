<?php

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('users')->name('users.')->middleware('auth:sanctum') ->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');

    Route::get('/{user}', [UserController::class, 'detail'])->name('detail');
    Route::post('/', [UserController::class, 'create'])->name('create');

    Route::put('/{user}', [UserController::class, 'update'])->name('update-put');

    Route::patch('/{user}', [UserController::class, 'update'])->name('update-patch');

    Route::delete('/{user}', [UserController::class, 'delete'])->name('delete');
});


Route::apiResource('products', ProductController::class);

Route::post('login', [AuthController::class, 'login']);
Route::get('token', [AuthController::class, 'getToken'])->middleware('auth:sanctum');

Route::post('refresh-token', [AuthController::class, 'refreshToken']);

Route::get('passport-token' , function(){
    $user = User::find(1);
    $tokenResult = $user -> createToken('auth_api');

    // Thiết lập expires time logout
    $token = $tokenResult-> token;
    $token -> expires_at = Carbon::now()-> addMinutes(60);

    // Trả về accessToken đã tạo
    $accessToken = $tokenResult-> accessToken;

    //Trả expires
    $expires = Carbon::parse($token -> expires_at) -> toDateTimeString();

    $response = [
        'accessToken' => $accessToken,
        'expires' => $expires
    ];

    return $response;


});
