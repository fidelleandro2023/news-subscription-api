<?php

use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NewsCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Ruta de prueba simple
Route::get('/test-simple', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now()
    ]);
});

// Rutas públicas (sin autenticación)
Route::prefix('v1')->group(function () {
    // Crear suscripción (público)
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    
    // Noticias públicas (solo lectura)
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{id}', [NewsController::class, 'show']);
    
    // Categorías públicas (solo lectura)
    Route::get('/categories', [NewsCategoryController::class, 'index']);
    Route::get('/categories/{id}', [NewsCategoryController::class, 'show']);
});

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Gestión de suscripciones (requiere permisos)
    Route::apiResource('subscriptions', SubscriptionController::class)->except(['store']);
    
    // Rutas adicionales para suscripciones
    Route::get('/subscriptions/active', [SubscriptionController::class, 'getActiveSubscriptions']);
    
    // Gestión de noticias (requiere autenticación y permisos de editor/admin)
    Route::middleware('news.permission:manage')->group(function () {
        Route::apiResource('news', NewsController::class)->except(['index', 'show']);
    });
    
    // Gestión de categorías (requiere autenticación y permisos de editor/admin)
    Route::middleware('news.permission:manage')->group(function () {
        Route::apiResource('categories', NewsCategoryController::class)->except(['index', 'show']);
    });
    
    // Rutas de administrador (requiere rol de Administrador)
    Route::prefix('admin')->middleware('news.permission:admin')->group(function () {
        // Suscripciones
        Route::get('/subscriptions', [AdminController::class, 'getAllSubscriptions']);
        Route::get('/subscriptions/stats', [AdminController::class, 'getSubscriptionStats']);
        Route::patch('/subscriptions/{id}/toggle-status', [AdminController::class, 'toggleSubscriptionStatus']);
        Route::delete('/subscriptions/bulk-delete', [AdminController::class, 'bulkDeleteSubscriptions']);
        
        // Noticias
        Route::get('/news', [NewsController::class, 'getAllNews']);
        Route::patch('/news/{id}/toggle-publish', [NewsController::class, 'togglePublishStatus']);
        Route::post('/news/{id}/send-notification', [NewsController::class, 'sendNotification']);
    });
});

// Rutas de prueba (siempre disponibles para testing)
Route::prefix('v1/test')->group(function () {
    Route::get('/subscriptions/active', [SubscriptionController::class, 'testGetActiveSubscriptions']);
    Route::get('/subscriptions/check/{email}', [SubscriptionController::class, 'testCheckByEmail']);
    Route::get('/subscriptions', [SubscriptionController::class, 'testIndex']);
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'testShow']);
});