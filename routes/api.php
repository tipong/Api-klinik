<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\GajiController;
use App\Http\Controllers\Api\PosisiController;
use App\Http\Controllers\Api\LowonganPekerjaanController;
use App\Http\Controllers\Api\LamaranPekerjaanController;
use App\Http\Controllers\Api\HasilSeleksiController;
use App\Http\Controllers\Api\WawancaraController;
use App\Http\Controllers\Api\PelatihanController;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public Routes for Job Applications
Route::prefix('lowongan')->group(function () {
    Route::get('/', [LowonganPekerjaanController::class, 'index']);
    Route::get('/{id}', [LowonganPekerjaanController::class, 'show']);
    Route::post('/apply', [LamaranPekerjaanController::class, 'store']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    });

    // Dashboard
    Route::get('/dashboard', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard data',
            'data' => [
                'message' => 'Welcome to HRD Management System Dashboard',
            ],
        ]);
    });

    // Admin and HRD routes
    Route::middleware('role:admin,hrd')->group(function () {
        // Pegawai Management
        Route::apiResource('pegawai', PegawaiController::class);
        Route::get('pegawai/{id}/absensi', [AbsensiController::class, 'getByPegawai']);
        Route::get('pegawai/{id}/gaji', [GajiController::class, 'getByPegawai']);
        Route::get('pegawai/{id}/pelatihan', [PelatihanController::class, 'getByPegawai']);
        
        // Posisi Management
        Route::apiResource('posisi', PosisiController::class);
        
        // Lowongan Management
        Route::post('/lowongan', [LowonganPekerjaanController::class, 'store']);
        Route::put('/lowongan/{id}', [LowonganPekerjaanController::class, 'update']);
        Route::delete('/lowongan/{id}', [LowonganPekerjaanController::class, 'destroy']);
        
        // Lamaran Management
        Route::get('/lamaran', [LamaranPekerjaanController::class, 'index']);
        Route::get('/lamaran/{id}', [LamaranPekerjaanController::class, 'show']);
        Route::put('/lamaran/{id}', [LamaranPekerjaanController::class, 'update']);
        Route::delete('/lamaran/{id}', [LamaranPekerjaanController::class, 'destroy']);
        
        // Wawancara Management
        Route::apiResource('wawancara', WawancaraController::class);
        Route::get('/lamaran/{id}/wawancara', [WawancaraController::class, 'getByLamaran']);
        
        // Hasil Seleksi Management
        Route::apiResource('hasil-seleksi', HasilSeleksiController::class);
        Route::get('/lamaran/{id}/hasil', [HasilSeleksiController::class, 'getByLamaran']);
        
        // Pelatihan Management
        Route::apiResource('pelatihan', PelatihanController::class);
        
        // Gaji Management
        Route::apiResource('gaji', GajiController::class);
    });
    
    // Absensi routes - accessible by all staff/pegawai
    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsensiController::class, 'index']);
        Route::post('/', [AbsensiController::class, 'store']);
        Route::get('/{id}', [AbsensiController::class, 'show']);
        Route::get('/user/today', [AbsensiController::class, 'getUserTodayAttendance']);
        Route::get('/user/history', [AbsensiController::class, 'getUserAttendanceHistory']);
    });

    // Test endpoint to verify authentication
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $request->user(),
            ],
        ]);
    });
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is running',
        'timestamp' => now(),
    ]);
});
