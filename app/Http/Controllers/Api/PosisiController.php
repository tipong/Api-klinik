<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Posisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PosisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Posisi::query();
        
        // Search by name
        if ($request->filled('search')) {
            $query->where('nama_posisi', 'like', '%' . $request->search . '%');
        }
        
        $posisi = $query->orderBy('nama_posisi')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $posisi
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_posisi' => 'required|string|max:50|unique:tb_posisi,nama_posisi',
            'gaji_pokok' => 'required|numeric|min:0',
            'persen_bonus' => 'nullable|numeric|min:0|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $posisi = Posisi::create([
            'nama_posisi' => $request->nama_posisi,
            'gaji_pokok' => $request->gaji_pokok,
            'persen_bonus' => $request->persen_bonus ?? 0,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Posisi berhasil ditambahkan',
            'data' => $posisi
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $posisi = Posisi::with(['pegawai'])->find($id);
        
        if (!$posisi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Posisi tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $posisi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $posisi = Posisi::find($id);
        
        if (!$posisi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Posisi tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'nama_posisi' => 'sometimes|required|string|max:50|unique:tb_posisi,nama_posisi,' . $id . ',id_posisi',
            'gaji_pokok' => 'sometimes|required|numeric|min:0',
            'persen_bonus' => 'nullable|numeric|min:0|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $posisi->update($request->only([
            'nama_posisi',
            'gaji_pokok',
            'persen_bonus',
        ]));
        
        return response()->json([
            'status' => 'success',
            'message' => 'Posisi berhasil diperbarui',
            'data' => $posisi
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $posisi = Posisi::find($id);
        
        if (!$posisi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Posisi tidak ditemukan'
            ], 404);
        }
        
        // Check if posisi has related data
        if ($posisi->pegawai()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Posisi tidak dapat dihapus karena masih digunakan oleh pegawai'
            ], 400);
        }
        
        $posisi->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Posisi berhasil dihapus'
        ]);
    }
}
