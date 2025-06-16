<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the companies.
     */
    public function index()
    {
        try {
            $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();
            return view('pages.super_admin.manageperusahaan', compact('perusahaans'));
        } catch (\Exception $e) {
            Log::error('Error in PerusahaanController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data perusahaan');
        }
    }

    /**
     * Store a newly created company.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode' => 'required|unique:perusahaans,kode|max:20',
                'nama_perusahaan' => 'required|max:100',
                'alamat' => 'nullable',
                'telepon' => 'nullable|max:20',
                'email' => 'nullable|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $perusahaan = Perusahaan::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil ditambahkan',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PerusahaanController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data perusahaan'
            ], 500);
        }
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, $id)
    {
        try {
            $perusahaan = Perusahaan::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'kode' => 'required|max:20|unique:perusahaans,kode,' . $id,
                'nama_perusahaan' => 'required|max:100',
                'alamat' => 'nullable',
                'telepon' => 'nullable|max:20',
                'email' => 'nullable|email',
                'status' => 'nullable|in:aktif,nonaktif'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $perusahaan->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil diperbarui',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PerusahaanController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data perusahaan'
            ], 500);
        }
    }

    /**
     * Remove the specified company.
     */
    public function destroy($id)
    {
        try {
            $perusahaan = Perusahaan::findOrFail($id);
            
            // Check if company is being used in SuratKeluar
            if ($perusahaan->suratKeluar()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perusahaan tidak dapat dihapus karena masih digunakan dalam surat keluar'
                ], 422);
            }
            
            $perusahaan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PerusahaanController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data perusahaan'
            ], 500);
        }
    }

    /**
     * Get all active companies for dropdown.
     */
    public function getForDropdown()
    {
        try {
            $perusahaans = Perusahaan::active()
                ->select('kode', 'nama_perusahaan')
                ->orderBy('nama_perusahaan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $perusahaans
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PerusahaanController@getForDropdown: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data perusahaan'
            ], 500);
        }
    }

    /**
     * Search companies by name.
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('query');
            
            $perusahaans = Perusahaan::where('nama_perusahaan', 'LIKE', "%{$query}%")
                ->orWhere('kode', 'LIKE', "%{$query}%")
                ->select('id', 'kode', 'nama_perusahaan')
                ->orderBy('nama_perusahaan')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $perusahaans
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PerusahaanController@search: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data perusahaan'
            ], 500);
        }
    }

    /**
     * Quick store a new company.
     */
    public function quickStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_perusahaan' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate kode based on nama_perusahaan
            $kode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $request->nama_perusahaan), 0, 5));
            
            // Check if kode exists, if yes append random number
            while (Perusahaan::where('kode', $kode)->exists()) {
                $kode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $request->nama_perusahaan), 0, 3)) . rand(100, 999);
            }

            $perusahaan = Perusahaan::create([
                'kode' => $kode,
                'nama_perusahaan' => $request->nama_perusahaan,
                'status' => 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil ditambahkan',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PerusahaanController@quickStore: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data perusahaan'
            ], 500);
        }
    }
}
