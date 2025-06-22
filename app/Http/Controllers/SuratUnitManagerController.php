<?php

namespace App\Http\Controllers;

use App\Models\SuratUnitManager;
use App\Models\User;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SuratUnitManagerController extends Controller
{
    /**
     * Menampilkan daftar surat unit manager
     */
    public function index(Request $request)
    {
        try {
            $query = SuratUnitManager::with([
                'unit.jabatan',
                'manager.jabatan',
                'sekretaris.jabatan',
                'dirut.jabatan',
                'perusahaanData'
            ]);

            // Filter berdasarkan role user yang login
            $user = auth()->user();
            
            if ($user->role == 0) { // Staff/Unit
                $query->byUnit($user->id);
            } elseif ($user->role == 4) { // Manager
                $query->byManager($user->id);
            } elseif ($user->role == 1) { // Sekretaris
                $query->where('status_manager', 'approved');
            } elseif ($user->role == 2) { // Direktur
                $query->where('status_sekretaris', 'approved');
            }

            // Search filter
            if ($request->has('search')) {
                $query->search($request->search);
            }

            // Filter berdasarkan status
            if ($request->has('status_manager')) {
                $query->byStatusManager($request->status_manager);
            }

            if ($request->has('status_sekretaris')) {
                $query->byStatusSekretaris($request->status_sekretaris);
            }

            if ($request->has('status_dirut')) {
                $query->byStatusDirut($request->status_dirut);
            }

            // Filter berdasarkan tanggal
            if ($request->has(['start_date', 'end_date'])) {
                $query->byDateRange($request->start_date, $request->end_date);
            }

            // Filter berdasarkan jenis surat
            if ($request->has('jenis_surat')) {
                $query->byJenisSurat($request->jenis_surat);
            }

            // Filter berdasarkan sifat surat
            if ($request->has('sifat_surat')) {
                $query->bySifatSurat($request->sifat_surat);
            }

            // Order by newest records first
            $suratUnitManager = $query->orderBy('tanggal_surat', 'desc')
                                     ->orderBy('created_at', 'desc')
                                     ->get();

            // Get perusahaan data for dropdown
            $perusahaans = Perusahaan::where('status', 'aktif')
                                     ->orderBy('nama_perusahaan')
                                     ->get();

            return view('pages.surat_unit_manager.index', compact('suratUnitManager', 'perusahaans'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    /**
     * Menampilkan form tambah surat unit manager
     */
    public function create()
    {
        try {
            $user = auth()->user();
            
            // Hanya staff yang bisa membuat surat
            if ($user->role !== 0) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membuat surat');
            }

            // Get manager untuk staff ini
            $manager = null;
            if ($user->manager_id) {
                $manager = User::with('jabatan')->find($user->manager_id);
            }

            // Get perusahaan data for dropdown
            $perusahaans = Perusahaan::where('status', 'aktif')
                                     ->orderBy('nama_perusahaan')
                                     ->get();

            return view('pages.surat_unit_manager.create', compact('manager', 'perusahaans'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman');
        }
    }

    /**
     * Menyimpan surat unit manager baru
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Hanya staff yang bisa membuat surat
            if ($user->role !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk membuat surat'
                ], 403);
            }

            // Get active perusahaan codes for validation
            $activeCodes = Perusahaan::where('status', 'aktif')
                          ->pluck('kode')
                          ->toArray();
            
            // Add RSAZRA to valid codes
            if (!in_array('RSAZRA', $activeCodes)) {
                $activeCodes[] = 'RSAZRA';
            }
            
            // Automatically set perusahaan to RSAZRA for internal letters
            if ($request->jenis_surat === 'internal') {
                $request->merge(['perusahaan' => 'RSAZRA']);
            }

            $validator = Validator::make($request->all(), [
                'nomor_surat' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        // Jika nomor surat mengandung tanda strip (-), maka diperbolehkan
                        if (strpos($value, '-') !== false) {
                            return true;
                        }
                        
                        // Cek apakah nomor surat sudah digunakan
                        $exists = SuratUnitManager::where('nomor_surat', $value)->exists();
                        if ($exists) {
                            $fail('Nomor surat sudah digunakan. Gunakan tanda strip (-) jika ingin menggunakan nomor yang sama.');
                        }
                    }
                ],
                'tanggal_surat' => 'required|date',
                'perihal' => 'required',
                'isi_surat' => 'required',
                'jenis_surat' => 'required|in:internal,eksternal',
                'sifat_surat' => 'required|in:normal,urgent',
                'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                'perusahaan' => 'required|in:' . implode(',', $activeCodes),
                'keterangan_unit' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                $suratUnitManager = new SuratUnitManager();
                $suratUnitManager->nomor_surat = $request->nomor_surat;
                $suratUnitManager->tanggal_surat = $request->tanggal_surat;
                $suratUnitManager->perihal = $request->perihal;
                $suratUnitManager->isi_surat = $request->isi_surat;
                $suratUnitManager->perusahaan = $request->perusahaan;
                $suratUnitManager->jenis_surat = $request->jenis_surat;
                $suratUnitManager->sifat_surat = $request->sifat_surat;
                $suratUnitManager->keterangan_unit = $request->keterangan_unit;
                $suratUnitManager->unit_id = $user->id;
                $suratUnitManager->manager_id = $user->manager_id;
                
                // Set status awal
                $suratUnitManager->status_manager = 'pending';
                $suratUnitManager->status_sekretaris = 'pending';
                $suratUnitManager->status_dirut = 'pending';

                // Proses file jika ada
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $uploadDir = public_path('uploads/surat_unit_manager');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $file->move($uploadDir, $fileName);
                    $suratUnitManager->file_path = 'uploads/surat_unit_manager/' . $fileName;
                }

                $suratUnitManager->save();

                DB::commit();

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Surat berhasil dibuat dan menunggu persetujuan manager',
                        'data' => $suratUnitManager,
                        'redirect_url' => route('surat-unit-manager.index')
                    ]);
                }

                return redirect()->route('surat-unit-manager.index')
                    ->with('success', 'Surat berhasil dibuat dan menunggu persetujuan manager');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@store: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan surat: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan surat: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan detail surat unit manager
     */
    public function show(SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Check access permission
            if ($user->role == 0 && $suratUnitManager->unit_id !== $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke surat ini');
            } elseif ($user->role == 4 && $suratUnitManager->manager_id !== $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke surat ini');
            }

            return view('pages.surat_unit_manager.show', compact('suratUnitManager'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat detail surat');
        }
    }

    /**
     * Menampilkan form edit surat unit manager
     */
    public function edit(SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Hanya unit yang membuat surat yang bisa edit, dan hanya jika belum disetujui manager
            if ($user->role !== 0 || $suratUnitManager->unit_id !== $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit surat ini');
            }

            if ($suratUnitManager->status_manager !== 'pending') {
                return redirect()->back()->with('error', 'Surat tidak dapat diedit karena sudah diproses');
            }

            // Get manager untuk staff ini
            $manager = null;
            if ($user->manager_id) {
                $manager = User::with('jabatan')->find($user->manager_id);
            }

            // Get perusahaan data for dropdown
            $perusahaans = Perusahaan::where('status', 'aktif')
                                     ->orderBy('nama_perusahaan')
                                     ->get();

            return view('pages.surat_unit_manager.edit', compact('suratUnitManager', 'manager', 'perusahaans'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat form edit');
        }
    }

    /**
     * Update surat unit manager
     */
    public function update(Request $request, SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Hanya unit yang membuat surat yang bisa edit, dan hanya jika belum disetujui manager
            if ($user->role !== 0 || $suratUnitManager->unit_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengedit surat ini'
                ], 403);
            }

            if ($suratUnitManager->status_manager !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat tidak dapat diedit karena sudah diproses'
                ], 422);
            }

            // Get active perusahaan codes for validation
            $activeCodes = Perusahaan::where('status', 'aktif')
                          ->pluck('kode')
                          ->toArray();
            
            // Add RSAZRA to valid codes
            if (!in_array('RSAZRA', $activeCodes)) {
                $activeCodes[] = 'RSAZRA';
            }
            
            // Automatically set perusahaan to RSAZRA for internal letters
            if ($request->jenis_surat === 'internal') {
                $request->merge(['perusahaan' => 'RSAZRA']);
            }

            $validator = Validator::make($request->all(), [
                'nomor_surat' => [
                    'required',
                    function ($attribute, $value, $fail) use ($suratUnitManager) {
                        // Jika nomor surat mengandung tanda strip (-), maka diperbolehkan
                        if (strpos($value, '-') !== false) {
                            return true;
                        }
                        
                        // Cek apakah nomor surat sudah digunakan oleh surat lain
                        $exists = SuratUnitManager::where('nomor_surat', $value)
                                  ->where('id', '!=', $suratUnitManager->id)
                                  ->exists();
                        
                        if ($exists) {
                            $fail('Nomor surat sudah digunakan. Gunakan tanda strip (-) jika ingin menggunakan nomor yang sama.');
                        }
                    }
                ],
                'tanggal_surat' => 'required|date',
                'perihal' => 'required',
                'isi_surat' => 'required',
                'jenis_surat' => 'required|in:internal,eksternal',
                'sifat_surat' => 'required|in:normal,urgent',
                'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                'perusahaan' => 'required|in:' . implode(',', $activeCodes),
                'keterangan_unit' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Update surat
                $suratUnitManager->nomor_surat = $request->nomor_surat;
                $suratUnitManager->tanggal_surat = $request->tanggal_surat;
                $suratUnitManager->perihal = $request->perihal;
                $suratUnitManager->isi_surat = $request->isi_surat;
                $suratUnitManager->perusahaan = $request->perusahaan;
                $suratUnitManager->jenis_surat = $request->jenis_surat;
                $suratUnitManager->sifat_surat = $request->sifat_surat;
                $suratUnitManager->keterangan_unit = $request->keterangan_unit;

                // Proses file baru jika ada
                if ($request->hasFile('file')) {
                    // Hapus file lama jika ada
                    if ($suratUnitManager->file_path && file_exists(public_path($suratUnitManager->file_path))) {
                        unlink(public_path($suratUnitManager->file_path));
                    }

                    $file = $request->file('file');
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $uploadDir = public_path('uploads/surat_unit_manager');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $file->move($uploadDir, $fileName);
                    $suratUnitManager->file_path = 'uploads/surat_unit_manager/' . $fileName;
                }

                $suratUnitManager->save();

                DB::commit();

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Surat berhasil diperbarui',
                        'data' => $suratUnitManager,
                        'redirect_url' => route('surat-unit-manager.index')
                    ]);
                }

                return redirect()->route('surat-unit-manager.index')
                    ->with('success', 'Surat berhasil diperbarui');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@update: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui surat: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui surat: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus surat unit manager (soft delete)
     */
    public function destroy(SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Hanya unit yang membuat surat yang bisa hapus, dan hanya jika belum disetujui manager
            if ($user->role !== 0 || $suratUnitManager->unit_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus surat ini'
                ], 403);
            }

            if ($suratUnitManager->status_manager !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat tidak dapat dihapus karena sudah diproses'
                ], 422);
            }

            // Hapus file jika ada
            if ($suratUnitManager->file_path && file_exists(public_path($suratUnitManager->file_path))) {
                unlink(public_path($suratUnitManager->file_path));
            }

            // Soft delete
            $suratUnitManager->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat berhasil dihapus'
                ]);
            }

            return redirect()->route('surat-unit-manager.index')
                ->with('success', 'Surat berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@destroy: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download file surat unit manager
     */
    public function download(SuratUnitManager $suratUnitManager)
    {
        try {
            if (!$suratUnitManager->file_path || !file_exists(public_path($suratUnitManager->file_path))) {
                return redirect()->back()->with('error', 'File tidak ditemukan');
            }

            return response()->download(public_path($suratUnitManager->file_path));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@download: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file');
        }
    }

    /**
     * Preview file surat unit manager
     */
    public function preview(SuratUnitManager $suratUnitManager)
    {
        try {
            if (!$suratUnitManager->file_path || !file_exists(public_path($suratUnitManager->file_path))) {
                return redirect()->back()->with('error', 'File tidak ditemukan');
            }

            $filePath = public_path($suratUnitManager->file_path);
            $fileType = mime_content_type($filePath);

            if (strpos($fileType, 'image/') === 0) {
                return response()->file($filePath);
            } elseif ($fileType === 'application/pdf') {
                return response()->file($filePath);
            } else {
                return response()->download($filePath);
            }
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@preview: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat preview file');
        }
    }
} 