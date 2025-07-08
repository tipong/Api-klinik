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
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\TreatmentController;
use App\Http\Controllers\Api\CustomerController;

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

    // Dashboard Statistics
    Route::get('/dashboard/stats', [AuthController::class, 'getDashboardStats']);

    // Admin and HRD routes (Full Management Access)
    Route::middleware('admin')->group(function () {
        // User Management (Admin and HRD access)
        Route::get('/users', [AuthController::class, 'getAllUsers']);
        Route::get('/user/{id}', [AuthController::class, 'getUserById']);
        
        // User Management (Admin only for sensitive operations)
        Route::middleware('role:admin')->group(function () {
            Route::put('/users/{id}/status', [AuthController::class, 'updateUserStatus']);
            Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
        });
        
        // Treatment Management
        Route::apiResource('treatments', TreatmentController::class);
        Route::get('treatments/categories', [TreatmentController::class, 'getCategories']);
        Route::get('treatments/role/{role}', [TreatmentController::class, 'getAvailableForRole']);
        
        // Customer Management
        Route::apiResource('customers', CustomerController::class);
        Route::get('customers/{id}/appointments', [CustomerController::class, 'getAppointments']);
        Route::get('/customer-statistics', [CustomerController::class, 'getStatistics']);
        
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
        
        // Gaji Management
        Route::apiResource('gaji', GajiController::class);
    });

    // Front Office routes (Customer service, appointments, basic operations)
    Route::middleware('role:admin,hrd,front_office')->group(function () {
        Route::prefix('front-office')->group(function () {
            Route::get('/dashboard', function () {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Front Office Dashboard',
                    'data' => ['role' => 'front_office', 'access' => 'customer_service']
                ]);
            });
            
            // Appointment management for front office
            Route::apiResource('appointments', AppointmentController::class);
            
            // Customer registration and management
            Route::post('customers/register', [CustomerController::class, 'store']);
            Route::get('customers', [CustomerController::class, 'index']);
            Route::get('customers/{id}', [CustomerController::class, 'show']);
            Route::put('customers/{id}', [CustomerController::class, 'update']);
        });
    });

    // Kasir routes (Payment processing, transactions)
    Route::middleware('role:admin,hrd,kasir')->group(function () {
        Route::prefix('kasir')->group(function () {
            Route::get('/dashboard', function () {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Kasir Dashboard',
                    'data' => ['role' => 'kasir', 'access' => 'payment_processing']
                ]);
            });
            
            // Payment and transaction management
            Route::get('appointments', [AppointmentController::class, 'index']);
            Route::put('appointments/{id}/payment', [AppointmentController::class, 'update']);
            Route::get('customers', [CustomerController::class, 'index']);
        });
    });

    // Dokter routes (Medical consultation, patient records)
    Route::middleware('role:admin,hrd,dokter')->group(function () {
        Route::prefix('dokter')->group(function () {
            Route::get('/dashboard', function () {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Dokter Dashboard',
                    'data' => ['role' => 'dokter', 'access' => 'medical_consultation']
                ]);
            });
            
            // Medical staff specific routes
            Route::get('appointments', [AppointmentController::class, 'getStaffAppointments']);
            Route::get('treatments', [TreatmentController::class, 'getAvailableForRole', 'dokter']);
            Route::put('appointments/{id}', [AppointmentController::class, 'update']);
        });
    });

    // Beautician routes (Beauty treatments, services)
    Route::middleware('role:admin,hrd,beautician')->group(function () {
        Route::prefix('beautician')->group(function () {
            Route::get('/dashboard', function () {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Beautician Dashboard',
                    'data' => ['role' => 'beautician', 'access' => 'beauty_treatments']
                ]);
            });
            
            // Beauty staff specific routes
            Route::get('appointments', [AppointmentController::class, 'getStaffAppointments']);
            Route::get('treatments', [TreatmentController::class, 'getAvailableForRole', 'beautician']);
            Route::put('appointments/{id}', [AppointmentController::class, 'update']);
        });
    });

    // Pelanggan routes (Customer portal, bookings, history)
    Route::middleware('role:admin,hrd,pelanggan')->group(function () {
        Route::prefix('pelanggan')->group(function () {
            Route::get('/dashboard', function () {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pelanggan Dashboard',
                    'data' => ['role' => 'pelanggan', 'access' => 'customer_portal']
                ]);
            });
            
            // Customer specific routes
            Route::get('appointments', [AppointmentController::class, 'index']);
            Route::get('appointments/{id}', [AppointmentController::class, 'show']);
            Route::get('treatments', [TreatmentController::class, 'index']);
            Route::get('profile', [CustomerController::class, 'show']);
            Route::put('profile', [CustomerController::class, 'update']);
        });
    });
    
    // Absensi routes - accessible by all staff/pegawai
    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsensiController::class, 'index']);
        Route::post('/', [AbsensiController::class, 'store']);
        Route::get('/{id}', [AbsensiController::class, 'show']);
        Route::get('/user/today', [AbsensiController::class, 'getUserTodayAttendance']);
        Route::get('/user/history', [AbsensiController::class, 'getUserAttendanceHistory']);
    });

    // Pelatihan routes - accessible by all staff roles (admin, hrd, front_office, kasir, dokter, beautician)
    Route::middleware('role:admin,hrd,front_office,kasir,dokter,beautician')->prefix('pelatihan')->group(function () {
        Route::get('/', [PelatihanController::class, 'index']);
        Route::get('/{id}', [PelatihanController::class, 'show']);
        
        // Admin and HRD can create, update, delete pelatihan
        Route::middleware('role:admin,hrd')->group(function () {
            Route::post('/', [PelatihanController::class, 'store']);
            Route::put('/{id}', [PelatihanController::class, 'update']);
            Route::delete('/{id}', [PelatihanController::class, 'destroy']);
        });
    });

    // ==============================================
    // COMPREHENSIVE API ROUTES FOR ALL TABLES
    // ==============================================
    
    // User Management Routes
    Route::prefix('users')->middleware('admin')->group(function () {
        Route::get('/', [AuthController::class, 'getAllUsers']);
        Route::get('/{id}', [AuthController::class, 'getUserbyiD']);
        Route::put('/{id}', [AuthController::class, 'updateUser']);
        Route::delete('/{id}', [AuthController::class, 'deleteUser']);
        Route::put('/{id}/status', [AuthController::class, 'updateUserStatus']);
    });

    // Pegawai Management Routes
    Route::prefix('pegawai')->middleware('admin')->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);
        Route::post('/', [PegawaiController::class, 'store']);
        Route::get('/{id}', [PegawaiController::class, 'show']);
        Route::put('/{id}', [PegawaiController::class, 'update']);
        Route::delete('/{id}', [PegawaiController::class, 'destroy']);
        Route::get('/{id}/absensi', [AbsensiController::class, 'getByPegawai']);
        Route::get('/{id}/gaji', [GajiController::class, 'getByPegawai']);
        Route::get('/{id}/pelatihan', [PelatihanController::class, 'getByPegawai']);
        Route::get('/active', [PegawaiController::class, 'getActive']);
        Route::get('/by-posisi/{id_posisi}', [PegawaiController::class, 'getByPosisi']);
    });

    // Absensi Management Routes
    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsensiController::class, 'index']);
        Route::post('/', [AbsensiController::class, 'store']);
        Route::get('/{id}', [AbsensiController::class, 'show']);
        Route::put('/{id}', [AbsensiController::class, 'update']);
        Route::delete('/{id}', [AbsensiController::class, 'destroy']);
        Route::post('/check-in', [AbsensiController::class, 'checkIn']);
        Route::post('/check-out', [AbsensiController::class, 'checkOut']);
        Route::get('/today', [AbsensiController::class, 'getTodayAttendance']);
        Route::get('/pegawai/{id}', [AbsensiController::class, 'getByPegawai']);
        Route::get('/report/{periode}', [AbsensiController::class, 'getReport']);
    });

    // Gaji Management Routes
    Route::prefix('gaji')->group(function () {
        Route::get('/', [GajiController::class, 'index']);
        Route::post('/', [GajiController::class, 'store']);
        Route::get('/{id}', [GajiController::class, 'show']);
        Route::put('/{id}', [GajiController::class, 'update']);
        Route::delete('/{id}', [GajiController::class, 'destroy']);
        Route::get('/pegawai/{id}', [GajiController::class, 'getByPegawai']);
        Route::get('/periode/{tahun}/{bulan}', [GajiController::class, 'getByPeriode']);
        Route::post('/calculate', [GajiController::class, 'generateGaji']);
        Route::put('/{id}/status', [GajiController::class, 'updateStatus']);
        
        // New salary calculation endpoints
        Route::post('/generate', [GajiController::class, 'generateGaji']);
        Route::post('/auto-generate-monthly', [GajiController::class, 'autoGenerateMonthlyGaji']);
        Route::get('/preview', [GajiController::class, 'previewCalculation']);
        Route::get('/statistics', [GajiController::class, 'statistics']);
    });

    // Posisi Management Routes
    Route::prefix('posisi')->group(function () {
        Route::get('/', [PosisiController::class, 'index']);
        Route::post('/', [PosisiController::class, 'store']);
        Route::get('/{id}', [PosisiController::class, 'show']);
        Route::put('/{id}', [PosisiController::class, 'update']);
        Route::delete('/{id}', [PosisiController::class, 'destroy']);
        Route::get('/{id}/pegawai', [PosisiController::class, 'getPegawai']);
        Route::get('/{id}/lowongan', [PosisiController::class, 'getLowongan']);
        Route::get('/statistics', [PosisiController::class, 'statistics']);
    });

    // Lowongan Pekerjaan Management Routes
    Route::prefix('lowongan-pekerjaan')->group(function () {
        Route::get('/', [LowonganPekerjaanController::class, 'index']);
        Route::post('/', [LowonganPekerjaanController::class, 'store']);
        Route::get('/{id}', [LowonganPekerjaanController::class, 'show']);
        Route::put('/{id}', [LowonganPekerjaanController::class, 'update']);
        Route::delete('/{id}', [LowonganPekerjaanController::class, 'destroy']);
        Route::get('/active', [LowonganPekerjaanController::class, 'getActive']);
        Route::get('/{id}/lamaran', [LowonganPekerjaanController::class, 'getLamaran']);
        Route::put('/{id}/status', [LowonganPekerjaanController::class, 'updateStatus']);
    });

    // Lamaran Pekerjaan Management Routes
    Route::prefix('lamaran-pekerjaan')->group(function () {
        Route::get('/', [LamaranPekerjaanController::class, 'index']);
        Route::post('/', [LamaranPekerjaanController::class, 'store']);
        Route::get('/{id}', [LamaranPekerjaanController::class, 'show']);
        Route::put('/{id}', [LamaranPekerjaanController::class, 'update']);
        Route::delete('/{id}', [LamaranPekerjaanController::class, 'destroy']);
        Route::get('/lowongan/{id}', [LamaranPekerjaanController::class, 'getByLowongan']);
        Route::get('/user/{id}', [LamaranPekerjaanController::class, 'getByUser']);
        Route::put('/{id}/status', [LamaranPekerjaanController::class, 'updateStatus']);
        Route::post('/{id}/cv-download', [LamaranPekerjaanController::class, 'downloadCV']);
    });

    // Wawancara Management Routes
    Route::prefix('wawancara')->group(function () {
        Route::get('/', [WawancaraController::class, 'index']);
        Route::post('/', [WawancaraController::class, 'store']);
        Route::get('/{id}', [WawancaraController::class, 'show']);
        Route::put('/{id}', [WawancaraController::class, 'update']);
        Route::delete('/{id}', [WawancaraController::class, 'destroy']);
        Route::get('/lamaran/{id}', [WawancaraController::class, 'getByLamaran']);
        Route::get('/today', [WawancaraController::class, 'getTodaySchedule']);
        Route::put('/{id}/hasil', [WawancaraController::class, 'updateHasil']);
    });

    // Hasil Seleksi Management Routes
    Route::prefix('hasil-seleksi')->group(function () {
        Route::get('/', [HasilSeleksiController::class, 'index']);
        Route::post('/', [HasilSeleksiController::class, 'store']);
        Route::get('/{id}', [HasilSeleksiController::class, 'show']);
        Route::put('/{id}', [HasilSeleksiController::class, 'update']);
        Route::delete('/{id}', [HasilSeleksiController::class, 'destroy']);
        Route::get('/lowongan/{id}', [HasilSeleksiController::class, 'getByLowongan']);
        Route::get('/user/{id}', [HasilSeleksiController::class, 'getByUser']);
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
