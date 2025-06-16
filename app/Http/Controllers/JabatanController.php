<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JabatanController extends Controller
{
    public function index()
    {
        return view('pages.super_admin.managejabatan');
    }

    // Ambil semua data jabatan
    public function getJabatan()
    {
        try {
            $jabatan = Jabatan::orderBy('created_at', 'desc')->get();
            return response()->json([
                'status' => 'success',
                'data' => $jabatan
            ]);
        } catch (\Exception $e) {
            Log::error('Error mengambil data jabatan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data jabatan'
            ], 500);
        }
    }

    // Tambah jabatan baru
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_jabatan' => 'required|string|max:255|unique:tbl_jabatan,nama_jabatan',
                'status' => 'required|in:aktif,nonaktif'
            ]);

            $jabatan = Jabatan::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Jabatan berhasil ditambahkan',
                'data' => $jabatan
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error menambah jabatan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // Update jabatan
    public function update(Request $request, $id)
    {
        try {
            $jabatan = Jabatan::findOrFail($id);

            $validated = $request->validate([
                'nama_jabatan' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tbl_jabatan')->ignore($id)
                ],
                'status' => 'required|in:aktif,nonaktif'
            ]);

            $jabatan->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Jabatan berhasil diperbarui',
                'data' => $jabatan
            ]);
        } catch (\Exception $e) {
            Log::error('Error mengupdate jabatan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // Hapus jabatan
    public function destroy($id)
    {
        try {
            $jabatan = Jabatan::findOrFail($id);

            // Cek apakah jabatan masih digunakan oleh user
            if ($jabatan->users()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jabatan tidak dapat dihapus karena masih digunakan oleh user'
                ], 422);
            }

            $jabatan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Jabatan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error menghapus jabatan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // Toggle status jabatan
    public function toggleStatus($id)
    {
        try {
            $jabatan = Jabatan::findOrFail($id);
            $jabatan->status = $jabatan->status === 'aktif' ? 'nonaktif' : 'aktif';
            $jabatan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Status jabatan berhasil diubah',
                'data' => $jabatan
            ]);
        } catch (\Exception $e) {
            Log::error('Error mengubah status jabatan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }
} 