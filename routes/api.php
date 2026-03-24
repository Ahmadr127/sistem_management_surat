<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SsoController;
use App\Http\Controllers\Api\SuratMasukApiController;
use App\Http\Controllers\Api\DisposisiApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public SSO Login Route
Route::post('/sso/login-via-token', [SsoController::class, 'loginViaToken']);

// Protected Mobile API Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data'   => $request->user()
        ]);
    });

    // Surat Masuk (Inbox)
    Route::get('/surat-masuk', [SuratMasukApiController::class, 'index']);
    Route::get('/surat-masuk/{id}', [SuratMasukApiController::class, 'show']);

    // Disposisi Interactions
    Route::post('/disposisi/{id}/keterangan', [DisposisiApiController::class, 'updateKeterangan']);
    Route::post('/disposisi/{id}/mark-as-read', [DisposisiApiController::class, 'markAsRead']);

});


