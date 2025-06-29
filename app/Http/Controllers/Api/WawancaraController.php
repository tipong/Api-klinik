<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wawancara;
use App\Models\LamaranPekerjaan;
use App\Models\HasilSeleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WawancaraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Wawancara::with(['lamaranPekerjaan', 'lamaranPekerjaan.lowonganPekerjaan', 'lamaranPekerjaan.pelamar']);

            // Filter by lamaran_id
            if ($request->has('lamaran_id')) {
                $query->where('lamaran_id', $request->lamaran_id);
            }

            // Filter by jadwal_wawancara range
            if ($request->has('jadwal_dari') && $request->has('jadwal_sampai')) {
                $query->whereBetween('jadwal_wawancara', [$request->jadwal_dari, $request->jadwal_sampai]);
            } elseif ($request->has('jadwal_dari')) {
                $query->where('jadwal_wawancara', '>=', $request->jadwal_dari);
            } elseif ($request->has('jadwal_sampai')) {
                $query->where('jadwal_wawancara', '<=', $request->jadwal_sampai);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by pewawancara
            if ($request->has('pewawancara')) {
                $query->where('pewawancara', 'like', '%' . $request->pewawancara . '%');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $wawancara = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $wawancara,
                'message' => 'Wawancara retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve wawancara data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lamaran_id' => 'required|exists:tb_lamaran_pekerjaan,id',
                'jadwal_wawancara' => 'required|date',
                'lokasi' => 'required|string|max:255',
                'pewawancara' => 'required|string|max:255',
                'metode' => 'required|in:Tatap Muka,Online,Telepon',
                'status' => 'required|in:Terjadwal,Selesai,Batal,Pending',
                'catatan' => 'nullable|string',
                'hasil' => 'nullable|in:Lulus,Tidak Lulus,Pending',
                'feedback' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if lamaran exists
            $lamaran = LamaranPekerjaan::find($request->lamaran_id);
            if (!$lamaran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lamaran not found'
                ], 404);
            }

            $wawancara = Wawancara::create($request->all());

            // If wawancara has a result, create/update HasilSeleksi
            if ($request->has('hasil') && $request->hasil != 'Pending') {
                // Check if HasilSeleksi already exists
                $hasilSeleksi = HasilSeleksi::where('lamaran_id', $request->lamaran_id)->first();
                
                $statusHasil = $request->hasil == 'Lulus' ? 'Diterima' : 'Ditolak';
                $dataHasil = [
                    'lamaran_id' => $request->lamaran_id,
                    'tanggal_hasil' => now(),
                    'status' => $statusHasil,
                    'catatan' => $request->feedback ?? 'Hasil wawancara: ' . $request->hasil,
                ];

                if ($hasilSeleksi) {
                    $hasilSeleksi->update($dataHasil);
                } else {
                    HasilSeleksi::create($dataHasil);
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $wawancara,
                'message' => 'Wawancara created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create wawancara',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $wawancara = Wawancara::with(['lamaranPekerjaan', 'lamaranPekerjaan.lowonganPekerjaan', 'lamaranPekerjaan.pelamar'])->find($id);

            if (!$wawancara) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wawancara not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $wawancara,
                'message' => 'Wawancara retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve wawancara',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $wawancara = Wawancara::find($id);
            
            if (!$wawancara) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wawancara not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'lamaran_id' => 'sometimes|required|exists:tb_lamaran_pekerjaan,id',
                'jadwal_wawancara' => 'sometimes|required|date',
                'lokasi' => 'sometimes|required|string|max:255',
                'pewawancara' => 'sometimes|required|string|max:255',
                'metode' => 'sometimes|required|in:Tatap Muka,Online,Telepon',
                'status' => 'sometimes|required|in:Terjadwal,Selesai,Batal,Pending',
                'catatan' => 'nullable|string',
                'hasil' => 'nullable|in:Lulus,Tidak Lulus,Pending',
                'feedback' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $wawancara->update($request->all());

            // If wawancara has a result, create/update HasilSeleksi
            if ($request->has('hasil') && $request->hasil != 'Pending') {
                // Check if HasilSeleksi already exists
                $hasilSeleksi = HasilSeleksi::where('lamaran_id', $wawancara->lamaran_id)->first();
                
                $statusHasil = $request->hasil == 'Lulus' ? 'Diterima' : 'Ditolak';
                $dataHasil = [
                    'lamaran_id' => $wawancara->lamaran_id,
                    'tanggal_hasil' => now(),
                    'status' => $statusHasil,
                    'catatan' => $request->feedback ?? 'Hasil wawancara: ' . $request->hasil,
                ];

                if ($hasilSeleksi) {
                    $hasilSeleksi->update($dataHasil);
                } else {
                    HasilSeleksi::create($dataHasil);
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $wawancara,
                'message' => 'Wawancara updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update wawancara',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $wawancara = Wawancara::find($id);
            
            if (!$wawancara) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wawancara not found'
                ], 404);
            }

            $wawancara->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Wawancara deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete wawancara',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wawancara by lamaran ID.
     *
     * @param  int  $lamaranId
     * @return \Illuminate\Http\Response
     */
    public function getByLamaran($lamaranId)
    {
        try {
            $lamaran = LamaranPekerjaan::find($lamaranId);
            
            if (!$lamaran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lamaran not found'
                ], 404);
            }

            $wawancara = Wawancara::with(['lamaranPekerjaan', 'lamaranPekerjaan.lowonganPekerjaan', 'lamaranPekerjaan.pelamar'])
                ->where('lamaran_id', $lamaranId)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $wawancara,
                'message' => 'Wawancara for lamaran retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve wawancara data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
