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

// Public Route for Diagnostics
Route::get('/ping', function (Request $request) {
    return response()->json([
        'status' => 'ok',
        'auth_header' => $request->header('Authorization'),
        'bearer_token' => $request->bearerToken(),
    ]);
});

Route::get('/verify-token', function (Request $request) {
    $tokenString = $request->bearerToken();
    if (!$tokenString) return response()->json(['error' => 'No bearer token provided']);
    
    $token = \Laravel\Sanctum\PersonalAccessToken::findToken($tokenString);
    if (!$token) return response()->json(['error' => 'Token not found in DB! Hash mismatch or wrong database.']);
    
    return response()->json([
        'token_id' => $token->id,
        'token_name' => $token->name,
        'tokenable_type' => $token->tokenable_type,
        'tokenable_id' => $token->tokenable_id,
        'user' => $token->tokenable,
    ]);
});

Route::get('/verify-manual', function (Request $request) {
    try {
        $user = auth('api')->authenticate();
        return response()->json(['status' => 'auth_success', 'user' => $user]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'auth_failed',
            'exception_class' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => collect($e->getTrace())->map(fn($t) => ($t['file'] ?? '') . ':' . ($t['line'] ?? ''))->filter()->take(10)->values()
        ]);
    }
});

// Public SSO Login Route
Route::post('/sso/login-via-token', [SsoController::class, 'loginViaToken']);

// Protected Mobile API Routes
Route::middleware([\App\Http\Middleware\ForceSanctumAuth::class])->group(function () {
    
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


