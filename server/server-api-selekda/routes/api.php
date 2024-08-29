<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [\App\Http\Controllers\Api\AuthUserController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthUserController::class, 'login']);

Route::post('/loginadmin', [\App\Http\Controllers\Api\AuthAdminController::class, 'login']);
Route::post('/regisadmin', [\App\Http\Controllers\Api\AuthAdminController::class, 'register']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthUserController::class, 'logout']);
    Route::post('/logoutadmin', [\App\Http\Controllers\Api\AuthAdminController::class, 'logout']);
});
