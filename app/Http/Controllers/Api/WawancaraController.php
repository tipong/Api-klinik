<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wawancara;
use App\Models\LamaranPekerjaan;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WawancaraController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $query = Wawancara::with(['lamaranPekerjaan.lowonganPekerjaan.posisi', 'user']);
            
            // Filter by user for non-admin roles
            if (!$user->isAdmin() && !$user->isHrd()) {
                $query->where('id_user', $user->id_user);
            }
            
            // Filter by lamaran
            if ($request->filled('id_lamaran_pekerjaan')) {
                $query->where('id_lamaran_pekerjaan', $request->id_lamaran_pekerjaan);
            }
            
            // Filter by lowongan pekerjaan
            if ($request->filled('id_lowongan_pekerjaan')) {
                $query->whereHas('lamaranPekerjaan', function ($subQuery) use ($request) {
                    $subQuery->where('id_lowongan_pekerjaan', $request->id_lowongan_pekerjaan);
                });
            }
            
            // Filter by date range
            if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
                $query->whereBetween('tanggal_wawancara', [$request->tanggal_dari, $request->tanggal_sampai]);
            } elseif ($request->filled('tanggal_dari')) {
                $query->where('tanggal_wawancara', '>=', $request->tanggal_dari);
            } elseif ($request->filled('tanggal_sampai')) {
                $query->where('tanggal_wawancara', '<=', $request->tanggal_sampai);
            }
            
            // Filter by hasil
            if ($request->filled('hasil')) {
                $query->where('hasil', $request->hasil);
            }
            
            $wawancara = $query->orderBy('tanggal_wawancara', 'desc')->paginate(15);
            
            return $this->successResponse($wawancara, 'Data wawancara berhasil diambil');
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_lamaran_pekerjaan' => 'required|exists:tb_lamaran_pekerjaan,id_lamaran_pekerjaan',
                'id_user' => 'required|exists:tb_user,id_user',
                'tanggal_wawancara' => 'required|date|after:now',
                'lokasi' => 'required|string|max:255',
                'catatan' => 'nullable|string|max:500',
                'hasil' => 'nullable|in:pending,diterima,ditolak',
            ]);
            
            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }
            
            // Check if lamaran exists and is valid
            $lamaran = LamaranPekerjaan::find($request->id_lamaran_pekerjaan);
            if (!$lamaran) {
                return $this->errorResponse('Lamaran pekerjaan tidak ditemukan', 404);
            }
            
            // Check if wawancara already exists for this lamaran
            $existingWawancara = Wawancara::where('id_lamaran_pekerjaan', $request->id_lamaran_pekerjaan)->first();
            if ($existingWawancara) {
                return $this->errorResponse('Wawancara untuk lamaran ini sudah ada', 400);
            }
            
            $wawancara = Wawancara::create(array_merge($request->all(), [
                'hasil' => $request->hasil ?? 'pending'
            ]));
            
            return $this->successResponse(
                $wawancara->load(['lamaranPekerjaan.lowonganPekerjaan.posisi', 'user']),
                'Wawancara berhasil dijadwalkan',
                201
            );
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menjadwalkan wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $wawancara = Wawancara::with(['lamaranPekerjaan.lowonganPekerjaan.posisi', 'user'])->find($id);
            
            if (!$wawancara) {
                return $this->notFoundResponse('Wawancara tidak ditemukan');
            }
            
            $user = $request->user();
            
            // Check if user is allowed to view this wawancara
            if (!$user->isAdmin() && !$user->isHrd() && $user->id_user !== $wawancara->id_user) {
                return $this->forbiddenResponse('Anda tidak memiliki akses untuk melihat data wawancara ini');
            }
            
            return $this->successResponse($wawancara, 'Data wawancara berhasil diambil');
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $wawancara = Wawancara::find($id);
            
            if (!$wawancara) {
                return $this->notFoundResponse('Wawancara tidak ditemukan');
            }
            
            $validator = Validator::make($request->all(), [
                'tanggal_wawancara' => 'sometimes|required|date',
                'lokasi' => 'sometimes|required|string|max:255',
                'catatan' => 'nullable|string|max:500',
                'hasil' => 'sometimes|required|in:pending,diterima,ditolak',
            ]);
            
            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }
            
            $wawancara->update($request->only([
                'tanggal_wawancara',
                'lokasi',
                'catatan',
                'hasil'
            ]));
            
            return $this->successResponse(
                $wawancara->fresh(['lamaranPekerjaan.lowonganPekerjaan.posisi', 'user']),
                'Wawancara berhasil diperbarui'
            );
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal memperbarui wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $wawancara = Wawancara::find($id);
            
            if (!$wawancara) {
                return $this->notFoundResponse('Wawancara tidak ditemukan');
            }
            
            $wawancara->delete();
            
            return $this->successResponse(null, 'Wawancara berhasil dihapus');
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Get wawancara by lamaran pekerjaan
     */
    public function getByLamaran(Request $request, string $id_lamaran)
    {
        try {
            $lamaran = LamaranPekerjaan::find($id_lamaran);
            
            if (!$lamaran) {
                return $this->notFoundResponse('Lamaran pekerjaan tidak ditemukan');
            }
            
            $wawancara = Wawancara::with(['lamaranPekerjaan.lowonganPekerjaan.posisi', 'user'])
                                ->where('id_lamaran_pekerjaan', $id_lamaran)
                                ->get();
            
            return $this->successResponse($wawancara, 'Data wawancara untuk lamaran berhasil diambil');
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Get today's wawancara
     */
    public function getTodaySchedule(Request $request)
    {
        try {
            $today = now()->format('Y-m-d');
            
            $wawancara = Wawancara::with(['lamaranPekerjaan.lowonganPekerjaan.posisi', 'user'])
                                ->whereDate('tanggal_wawancara', $today)
                                ->orderBy('tanggal_wawancara', 'asc')
                                ->get();
            
            return $this->successResponse($wawancara, 'Jadwal wawancara hari ini berhasil diambil');
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil jadwal wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Update hasil wawancara
     */
    public function updateHasil(Request $request, string $id)
    {
        try {
            $wawancara = Wawancara::find($id);
            
            if (!$wawancara) {
                return $this->notFoundResponse('Wawancara tidak ditemukan');
            }
            
            $validator = Validator::make($request->all(), [
                'hasil' => 'required|in:pending,diterima,ditolak',
                'catatan' => 'nullable|string|max:500',
            ]);
            
            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }
            
            $wawancara->update([
                'hasil' => $request->hasil,
                'catatan' => $request->catatan
            ]);
            
            return $this->successResponse(
                $wawancara->fresh(['lamaranPekerjaan.lowonganPekerjaan.posisi', 'user']),
                'Hasil wawancara berhasil diperbarui'
            );
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal memperbarui hasil wawancara: ' . $e->getMessage());
        }
    }
}
