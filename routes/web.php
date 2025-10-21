<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruta de prueba en web para verificar funcionamiento
Route::get('/test-web', function () {
    return response()->json([
        'success' => true,
        'message' => 'Ruta web funcionando correctamente',
        'timestamp' => now()
    ]);
});
