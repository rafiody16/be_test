<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\CutiController;

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::middleware('role:employee')->group(function () {
        Route::post('/cuti/ajukan', [CutiController::class, 'ajukanCuti']);
        Route::delete('/cuti/cancel', [CutiController::class, 'cancelPermintaanCuti']);
        Route::get('/cuti/status', [CutiController::class, 'cekStatusCuti']);
        Route::get('/cuti/riwayat', [CutiController::class, 'riwayatCuti']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/auth/insert', [AuthController::class, 'insert']);
        
        Route::get('/cuti', [CutiController::class, 'getAll']);
        Route::get('/cuti/{id}', [CutiController::class, 'getById']);
        Route::post('/cuti/{id}/approve', [CutiController::class, 'approveCuti']);
        Route::post('/cuti/{id}/reject', [CutiController::class, 'rejectCuti']);
    });
});
