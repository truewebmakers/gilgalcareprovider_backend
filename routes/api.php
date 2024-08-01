<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/signup', [UserController::class, 'signup'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/update/profile/{id}', [UserController::class, 'updateProfile'])->name('update.profile');
    Route::post('/update/password/{id}', [UserController::class, 'updatePassword'])->name('update.password');
});
