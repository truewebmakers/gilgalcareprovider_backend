<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{CategoryController,BusinessListingController,BusinessListingMetaController};
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/signup', [UserController::class, 'signup'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/update/profile/{id}', [UserController::class, 'updateProfile'])->name('update.profile');
    Route::post('/update/password/{id}', [UserController::class, 'updatePassword'])->name('update.password');
    Route::get('/getProfile/{id}', [UserController::class, 'getProfile'])->name('get.profile');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');


});

Route::get('/get-pb/all', [CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->prefix('categories')->group(function () {
    Route::get('/get/all', [CategoryController::class, 'index']); // Get all categories
    Route::post('/store', [CategoryController::class, 'store']); // Create a new category
    Route::get('/get/{id}', [CategoryController::class, 'show']); // Get a specific category
    Route::put('/update/{id}', [CategoryController::class, 'update']); // Update a specific category
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy']); // Delete a specific category
});

Route::middleware('auth:sanctum')->prefix('listing')->group(function () {
    Route::post('/get/all/{id}', [BusinessListingController::class, 'index']); // Get all listings
    Route::post('/store', [BusinessListingController::class, 'store']); // Create a new listing
    Route::post('/get/{id}', [BusinessListingController::class, 'show']); // Get a specific listing
    Route::post('/update/{id}', [BusinessListingController::class, 'update']); // Update a specific listing
    Route::delete('/delete/{id}', [BusinessListingController::class, 'destroy']); // Delete a specific listing
});

// Routes for Business Listing Meta CRUD operations
Route::middleware('auth:sanctum')->prefix('listing-meta')->group(function () {
    Route::post('/get/all', [BusinessListingMetaController::class, 'index']); // Get all meta
    Route::post('/store', [BusinessListingMetaController::class, 'store']); // Create a new meta
    Route::post('/get/{id}', [BusinessListingMetaController::class, 'show']); // Get a specific meta
    Route::post('/update/{id}', [BusinessListingMetaController::class, 'update']); // Update a specific meta
    Route::delete('/delete/{id}', [BusinessListingMetaController::class, 'destroy']); // Delete a specific meta
});
