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

    // Admin routes (Full access to all management features)
    Route::middleware('role:admin,hrd')->group(function () {
        // Posisi Management
        Route::apiResource('posisi', PosisiController::class);
        Route::get('/posisi/{id}/pegawai', [PosisiController::class, 'getPegawai']);
        
        // Pegawai Management
        Route::apiResource('pegawai', PegawaiController::class);
        Route::get('/pegawai/{id}/absensi', [AbsensiController::class, 'getByPegawai']);
        Route::get('/pegawai/{id}/gaji', [GajiController::class, 'getByPegawai']);
        Route::get('/pegawai/active', [PegawaiController::class, 'getActive']);
        Route::get('/pegawai/by-posisi/{id_posisi}', [PegawaiController::class, 'getByPosisi']);
        
        // Recruitment Management
        Route::apiResource('lowongan-pekerjaan', LowonganPekerjaanController::class);
        Route::apiResource('lamaran-pekerjaan', LamaranPekerjaanController::class);
        Route::apiResource('wawancara', WawancaraController::class);
        Route::apiResource('hasil-seleksi', HasilSeleksiController::class);
        Route::get('/lamaran/{id}/hasil', [HasilSeleksiController::class, 'getByLamaran']);
        
        // Gaji Management
        Route::apiResource('gaji', GajiController::class);
        Route::post('/gaji/generate', [GajiController::class, 'generateGaji']);
        Route::get('/gaji/preview', [GajiController::class, 'previewCalculation']);
        
        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [AuthController::class, 'getAllUsers']);
            Route::get('/{id}', [AuthController::class, 'getUserById']);
            Route::put('/{id}', [AuthController::class, 'updateUser']);
            Route::delete('/{id}', [AuthController::class, 'deleteUser']);
        });
    });

    // Staff Dashboard Routes
    Route::middleware('role:admin,hrd,front_office')->prefix('front-office')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json([
                'status' => 'success',
                'message' => 'Front Office Dashboard',
                'data' => ['role' => 'front_office']
            ]);
        });
    });

    Route::middleware('role:admin,hrd,kasir')->prefix('kasir')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json([
                'status' => 'success',
                'message' => 'Kasir Dashboard',
                'data' => ['role' => 'kasir']
            ]);
        });
    });

    Route::middleware('role:admin,hrd,dokter')->prefix('dokter')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json([
                'status' => 'success',
                'message' => 'Dokter Dashboard',
                'data' => ['role' => 'dokter']
            ]);
        });
    });

    Route::middleware('role:admin,hrd,beautician')->prefix('beautician')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json([
                'status' => 'success',
                'message' => 'Beautician Dashboard',
                'data' => ['role' => 'beautician']
            ]);
        });
    });
    
    // Absensi routes - accessible by all staff/pegawai
    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsensiController::class, 'index']);
        Route::post('/', [AbsensiController::class, 'store']);  // Check-in
        Route::get('/today-status', [AbsensiController::class, 'getTodayStatus']);
        Route::get('/{id}', [AbsensiController::class, 'show']);
        Route::post('/{id}/checkout', [AbsensiController::class, 'checkOut']); // Check-out
        
        // Admin/HRD additional routes
        Route::middleware('role:admin,hrd')->group(function () {
            Route::put('/{id}', [AbsensiController::class, 'update']);
            Route::delete('/{id}', [AbsensiController::class, 'destroy']);
            Route::get('/pegawai/{id}', [AbsensiController::class, 'getByPegawai']);
        });
    });

    // Pelatihan routes
    Route::prefix('pelatihan')->group(function () {
        Route::get('/', [PelatihanController::class, 'index']);
        Route::get('/{id}', [PelatihanController::class, 'show']);
        
        // Admin and HRD can create, update, delete pelatihan
        Route::middleware('role:admin,hrd')->group(function () {
            Route::post('/', [PelatihanController::class, 'store']);
            Route::put('/{id}', [PelatihanController::class, 'update']);
            Route::delete('/{id}', [PelatihanController::class, 'destroy']);
        });
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
