<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PelatihanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Pelatihan::with('pegawai');

            // Filter by nama_pelatihan
            if ($request->has('nama_pelatihan')) {
                $query->where('nama_pelatihan', 'like', '%' . $request->nama_pelatihan . '%');
            }

            // Filter by jenis_pelatihan
            if ($request->has('jenis_pelatihan')) {
                $query->where('jenis_pelatihan', 'like', '%' . $request->jenis_pelatihan . '%');
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by pegawai_id
            if ($request->has('pegawai_id')) {
                $query->where('pegawai_id', $request->pegawai_id);
            }

            // Filter by tanggal_mulai range
            if ($request->has('tanggal_mulai_dari') && $request->has('tanggal_mulai_sampai')) {
                $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai_dari, $request->tanggal_mulai_sampai]);
            } elseif ($request->has('tanggal_mulai_dari')) {
                $query->where('tanggal_mulai', '>=', $request->tanggal_mulai_dari);
            } elseif ($request->has('tanggal_mulai_sampai')) {
                $query->where('tanggal_mulai', '<=', $request->tanggal_mulai_sampai);
            }

            // Filter by tanggal_selesai range
            if ($request->has('tanggal_selesai_dari') && $request->has('tanggal_selesai_sampai')) {
                $query->whereBetween('tanggal_selesai', [$request->tanggal_selesai_dari, $request->tanggal_selesai_sampai]);
            } elseif ($request->has('tanggal_selesai_dari')) {
                $query->where('tanggal_selesai', '>=', $request->tanggal_selesai_dari);
            } elseif ($request->has('tanggal_selesai_sampai')) {
                $query->where('tanggal_selesai', '<=', $request->tanggal_selesai_sampai);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $pelatihan = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $pelatihan,
                'message' => 'Pelatihan retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve pelatihan data',
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
                'pegawai_id' => 'required|exists:tb_pegawai,id',
                'nama_pelatihan' => 'required|string|max:255',
                'jenis_pelatihan' => 'required|string|max:100',
                'deskripsi' => 'nullable|string',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'penyelenggara' => 'required|string|max:255',
                'lokasi' => 'required|string|max:255',
                'biaya' => 'nullable|numeric',
                'status' => 'required|in:Terdaftar,Berjalan,Selesai,Dibatalkan',
                'sertifikat' => 'nullable|string|max:255',
                'catatan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if pegawai exists
            $pegawai = Pegawai::find($request->pegawai_id);
            if (!$pegawai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pegawai not found'
                ], 404);
            }

            $pelatihan = Pelatihan::create($request->all());

            return response()->json([
                'status' => 'success',
                'data' => $pelatihan,
                'message' => 'Pelatihan created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create pelatihan',
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
            $pelatihan = Pelatihan::with('pegawai')->find($id);

            if (!$pelatihan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pelatihan not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $pelatihan,
                'message' => 'Pelatihan retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve pelatihan',
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
            $pelatihan = Pelatihan::find($id);
            
            if (!$pelatihan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pelatihan not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'pegawai_id' => 'sometimes|required|exists:tb_pegawai,id',
                'nama_pelatihan' => 'sometimes|required|string|max:255',
                'jenis_pelatihan' => 'sometimes|required|string|max:100',
                'deskripsi' => 'nullable|string',
                'tanggal_mulai' => 'sometimes|required|date',
                'tanggal_selesai' => 'sometimes|required|date|after_or_equal:tanggal_mulai',
                'penyelenggara' => 'sometimes|required|string|max:255',
                'lokasi' => 'sometimes|required|string|max:255',
                'biaya' => 'nullable|numeric',
                'status' => 'sometimes|required|in:Terdaftar,Berjalan,Selesai,Dibatalkan',
                'sertifikat' => 'nullable|string|max:255',
                'catatan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pelatihan->update($request->all());

            return response()->json([
                'status' => 'success',
                'data' => $pelatihan,
                'message' => 'Pelatihan updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update pelatihan',
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
            $pelatihan = Pelatihan::find($id);
            
            if (!$pelatihan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pelatihan not found'
                ], 404);
            }

            $pelatihan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Pelatihan deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete pelatihan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pelatihan by pegawai ID.
     *
     * @param  int  $pegawaiId
     * @return \Illuminate\Http\Response
     */
    public function getByPegawai($pegawaiId)
    {
        try {
            $pegawai = Pegawai::find($pegawaiId);
            
            if (!$pegawai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pegawai not found'
                ], 404);
            }

            $pelatihan = Pelatihan::where('pegawai_id', $pegawaiId)->get();

            return response()->json([
                'status' => 'success',
                'data' => $pelatihan,
                'message' => 'Pelatihan for pegawai retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve pelatihan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
