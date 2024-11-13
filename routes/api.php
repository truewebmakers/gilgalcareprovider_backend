<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CategoryController,
    BusinessListingController,
    BusinessListingMetaController,
    FeedbackController,
    ImageController,
    DashboardController,
    SubscriptionController,
    SubscriptionPlanController

};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/signup', [UserController::class, 'signup'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/check-email', [UserController::class, 'checkEmail'])->name('email');



// create subscription

Route::post('/create/subsscription', [SubscriptionController::class, 'createSubscription']);



Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/update/profile/{id}', [UserController::class, 'updateProfile'])->name('update.profile');
    Route::post('/update/password/{id}', [UserController::class, 'updatePassword'])->name('update.password');
    Route::get('/getProfile/{id}', [UserController::class, 'getProfile'])->name('get.profile');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::post('/get-current-plan', [SubscriptionPlanController::class, 'getCurrentPlan']);
    Route::post('/cancel-subscription', [SubscriptionPlanController::class, 'cancelSubscription']);
});

Route::get('categories/get-pb/all', [CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->prefix('categories')->group(function () {
    Route::get('/get/all', [CategoryController::class, 'index']); // Get all categories
    Route::post('/store', [CategoryController::class, 'store']); // Create a new category
    Route::get('/get/{id}', [CategoryController::class, 'show']); // Get a specific category
    Route::post('/update/{id}', [CategoryController::class, 'update']); // Update a specific category
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy']); // Delete a specific category
});

Route::get('listing/get-pb/all', [BusinessListingController::class, 'index']);
Route::get('listing/search', [BusinessListingController::class, 'SearchBusinessListing']);
Route::get('listing/get-pb/{id}', [BusinessListingController::class, 'show']);
Route::get('listing/top-ten-trending', [BusinessListingController::class, 'TopTenTrendingBusinessListing']);



Route::get('/stats/{id}', [BusinessListingController::class, 'getListingStats']);
Route::post('/increment-page-views/{id}', [BusinessListingController::class, 'incrementPageViews']);
Route::post('/increment-shares/{id}', [BusinessListingController::class, 'incrementShares']);


Route::get('/plan/getall', [SubscriptionPlanController::class, 'index']);
Route::middleware('auth:sanctum')->prefix('plan')->group(function () {
    Route::post('/store', [SubscriptionPlanController::class, 'store']);
    Route::post('/update/{id}', [SubscriptionPlanController::class, 'update']);
    Route::post('/delete/{id}', [SubscriptionPlanController::class, 'destroy']);

    Route::get('/get/{plan_id}', [SubscriptionPlanController::class, 'index']);

});

Route::middleware('auth:sanctum')->prefix('listing')->group(function () {
    Route::post('/get/all/{id}', [BusinessListingController::class, 'index']); // Get all listings
    Route::post('/store', [BusinessListingController::class, 'store']); // Create a new listing
    Route::get('/get/{id}', [BusinessListingController::class, 'show']); // Get a specific listing
    Route::post('/update/{id}', [BusinessListingController::class, 'update']); // Update a specific listing
    Route::delete('/delete/{id}', [BusinessListingController::class, 'destroy']); // Delete a specific listing
    Route::post('/upload-image', [ImageController::class, 'uploadTemporaryImage']);


});

// Routes for Business Listing Meta CRUD operations
Route::middleware('auth:sanctum')->prefix('listing-meta')->group(function () {
    Route::post('/get/all', [BusinessListingMetaController::class, 'index']); // Get all meta
    Route::post('/store', [BusinessListingMetaController::class, 'store']); // Create a new meta
    Route::post('/get/{id}', [BusinessListingMetaController::class, 'show']); // Get a specific meta
    Route::post('/update/{id}', [BusinessListingMetaController::class, 'update']); // Update a specific meta
    Route::delete('/delete/{id}', [BusinessListingMetaController::class, 'destroy']); // Delete a specific meta
});

Route::prefix('feedback')->group(function () {
    Route::post('/store', [FeedbackController::class, 'store']);
    Route::post('/update/{id}', [FeedbackController::class, 'update']);
    Route::get('/business/{businessListingId}', [FeedbackController::class, 'getFeedbackByBusinessListing']);
    Route::get('/get/{id}', [FeedbackController::class, 'getFeedback']);
});


// Route::middleware('auth:sanctum')->prefix('feedback')->group(function () {
//     Route::post('/store', [FeedbackController::class, 'store']);
//     Route::post('/update/{id}', [FeedbackController::class, 'update']);
//     Route::get('/business/{businessListingId}', [FeedbackController::class, 'getFeedbackByBusinessListing']);
//     Route::get('/get/{id}', [FeedbackController::class, 'getFeedback']);
// });

Route::middleware('auth:sanctum')->prefix('dashboard')->group(function () {
    Route::get('/review-count', [DashboardController::class, 'getReviewCount']);
    Route::get('/listing-count', [DashboardController::class, 'getListCounts']);
});
