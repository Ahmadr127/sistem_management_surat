<?php

namespace App\Http\Controllers;

use App\Models\SuratUnitManager;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\SuratUnitManagerFile;
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
     * Helper method untuk memperbaiki data file yang hilang
     */
    private function fixMissingFileData()
    {
        try {
            $surats = SuratUnitManager::all();
            $uploadDir = public_path('uploads/surat_unit_manager');
            $files = glob($uploadDir . '/*');
            
            foreach ($files as $file) {
                $filename = basename($file);
                $filePath = 'uploads/surat_unit_manager/' . $filename;
                
                // Cek apakah file sudah ada di database
                if (!SuratUnitManagerFile::where('file_path', $filePath)->exists()) {
                    // Ambil surat pertama yang belum memiliki file
                    $surat = SuratUnitManager::whereDoesntHave('files')->first();
                    
                    if ($surat) {
                        $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        $originalName = 'lampiran_' . $surat->id . '.' . $extension;
                        
                        SuratUnitManagerFile::create([
                            'surat_unit_manager_id' => $surat->id,
                            'file_path' => $filePath,
                            'original_name' => $originalName,
                            'file_type' => mime_content_type($file),
                            'file_size' => filesize($file)
                        ]);
                        
                        Log::info("Fixed missing file data: {$filename} -> Surat ID: {$surat->id}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error fixing missing file data: ' . $e->getMessage());
        }
    }

    /**
     * Helper method untuk memastikan direktori upload ada dan dapat ditulis
     */
    private function ensureUploadDirectory($path)
    {
        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true)) {
                throw new \Exception('Gagal membuat direktori upload: ' . $path);
            }
        }
        
        if (!is_writable($path)) {
            throw new \Exception('Direktori upload tidak dapat ditulis: ' . $path);
        }
        
        return true;
    }

    /**
     * Helper method untuk validasi dan memproses file upload dengan lebih aman
     */
    private function processFileUpload($file, $uploadDir, $suratUnitManager)
    {
        try {
            // Validasi file lebih ketat
            if (!$file || !$file->isValid()) {
                Log::warning('File tidak valid: ' . ($file ? $file->getClientOriginalName() : 'null'));
                return false;
            }
            
            // Cek apakah file temporary masih ada dan dapat dibaca
            $tempPath = $file->getPathname();
            if (!file_exists($tempPath) || !is_readable($tempPath)) {
                Log::error('Error uploading file: The "' . $tempPath . '" file does not exist or is not readable.');
                return false;
            }
            
            // Cek ukuran file
            if (!$file->getSize() || $file->getSize() <= 0) {
                Log::warning('File kosong atau tidak valid: ' . $file->getClientOriginalName());
                return false;
            }
            
            // Cek ukuran maksimal (2MB)
            if ($file->getSize() > 2 * 1024 * 1024) {
                Log::warning('File terlalu besar: ' . $file->getClientOriginalName() . ' (' . $file->getSize() . ' bytes)');
                return false;
            }
            
            // Generate nama file yang unik
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $fullPath = $uploadDir . '/' . $fileName;
            
            // Copy file terlebih dahulu untuk memastikan tidak hilang
            if (!copy($tempPath, $fullPath)) {
                Log::error('Gagal copy file dari temporary: ' . $tempPath . ' ke ' . $fullPath);
                return false;
            }
            
            // Verifikasi file berhasil di-copy
            if (!file_exists($fullPath) || filesize($fullPath) !== $file->getSize()) {
                Log::error('File tidak berhasil di-copy dengan benar: ' . $fullPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                return false;
            }
            
            // Simpan informasi file ke database
            $fileData = [
                'surat_unit_manager_id' => $suratUnitManager->id,
                'file_path' => 'uploads/surat_unit_manager/' . $fileName,
                'original_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize()
            ];
            
            $savedFile = SuratUnitManagerFile::create($fileData);
            
            if (!$savedFile) {
                // Hapus file fisik jika gagal simpan ke database
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                Log::error('Gagal menyimpan data file ke database: ' . $file->getClientOriginalName());
                return false;
            }
            
            Log::info('File berhasil diupload: ' . $file->getClientOriginalName() . ' dengan ID: ' . $savedFile->id);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
            // Hapus file fisik jika ada error
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }
            return false;
        }
    }

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
                'perusahaanData',
                'files'
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
            if ($request->has('status')) {
                $query->byStatusManager($request->status);
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
                                     ->paginate(10)->withQueryString();

            // Perbaiki data file yang hilang jika ada
            $this->fixMissingFileData();

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
                'files.*' => [
                    'nullable',
                    'file',
                    'mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,ppt,pptx,zip,rar',
                    'max:2048', // 2MB per file (sesuai upload_max_filesize PHP)
                    function ($attribute, $value, $fail) {
                        if ($value && !$value->isValid()) {
                            $fail('File tidak valid atau rusak.');
                        }
                    }
                ],
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

                $suratUnitManager->save();

                // Proses file jika ada
                if ($request->hasFile('files')) {
                    $files = $request->file('files');
                    
                    // Validasi files array
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    
                    $uploadDir = public_path('uploads/surat_unit_manager');
                    $this->ensureUploadDirectory($uploadDir);
                    
                    $successCount = 0;
                    $totalFiles = count($files);
                    
                    foreach ($files as $file) {
                        if ($this->processFileUpload($file, $uploadDir, $suratUnitManager)) {
                            $successCount++;
                        }
                    }
                    
                    // Log hasil upload
                    if ($successCount > 0) {
                        Log::info("Berhasil upload {$successCount} dari {$totalFiles} file untuk surat ID: {$suratUnitManager->id}");
                    }
                    
                    if ($successCount === 0 && $totalFiles > 0) {
                        Log::warning("Tidak ada file yang berhasil diupload dari {$totalFiles} file untuk surat ID: {$suratUnitManager->id}");
                    }
                }

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
            
            // Check if it's a file upload error
            if (strpos($e->getMessage(), 'SplFileInfo::getSize') !== false || 
                strpos($e->getMessage(), 'file does not exist') !== false) {
                $errorMessage = 'Terjadi kesalahan saat memproses file. Pastikan file yang dipilih valid, tidak rusak, dan ukurannya tidak melebihi 2MB.';
            } else {
                $errorMessage = 'Terjadi kesalahan saat menyimpan surat: ' . $e->getMessage();
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
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

            // Load files relationship
            $suratUnitManager->load('files');

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

            // Cek kepemilikan
            if ($suratUnitManager->unit_id !== $user->id) {
                return redirect()->route('surat-unit-manager.index')->with('error', 'Anda tidak memiliki hak untuk mengedit surat ini.');
            }

            // Cek apakah statusnya bisa diedit
            if (!in_array($suratUnitManager->status_manager, ['pending', 'rejected'])) {
                return redirect()->route('surat-unit-manager.index')->with('error', 'Surat ini tidak dapat diedit karena sudah diproses atau disetujui.');
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

            // Load files relationship
            $suratUnitManager->load('files');

            return view('pages.surat_unit_manager.edit', compact('suratUnitManager', 'manager', 'perusahaans'));

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman edit.');
        }
    }

    /**
     * Mengupdate surat unit manager di database
     */
    public function update(Request $request, SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();

            // Cek kepemilikan
            if ($suratUnitManager->unit_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak untuk mengupdate surat ini'
                ], 403);
            }

            // Cek apakah statusnya bisa diupdate
            if (!in_array($suratUnitManager->status_manager, ['pending', 'rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat ini tidak dapat diupdate karena sudah diproses atau disetujui'
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
                            return;
                        }
                        $exists = SuratUnitManager::where('nomor_surat', $value)
                                         ->where('id', '!=', $suratUnitManager->id)
                                         ->exists();
                        if ($exists) {
                            $fail('Nomor surat sudah digunakan.');
                        }
                    }
                ],
                'tanggal_surat' => 'required|date',
                'perihal' => 'required',
                'isi_surat' => 'required',
                'jenis_surat' => 'required|in:internal,eksternal',
                'sifat_surat' => 'required|in:normal,urgent',
                'files.*' => [
                    'nullable',
                    'file',
                    'mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,ppt,pptx,zip,rar',
                    'max:2048', // 2MB per file (sesuai upload_max_filesize PHP)
                    function ($attribute, $value, $fail) {
                        if ($value && !$value->isValid()) {
                            $fail('File tidak valid atau rusak.');
                        }
                    }
                ],
                'perusahaan' => 'required|in:' . implode(',', $activeCodes),
                'keterangan_unit' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed.', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                $suratUnitManager->nomor_surat = $request->nomor_surat;
                $suratUnitManager->tanggal_surat = $request->tanggal_surat;
                $suratUnitManager->perihal = $request->perihal;
                $suratUnitManager->isi_surat = $request->isi_surat;
                $suratUnitManager->perusahaan = $request->perusahaan;
                $suratUnitManager->jenis_surat = $request->jenis_surat;
                $suratUnitManager->sifat_surat = $request->sifat_surat;
                $suratUnitManager->keterangan_unit = $request->keterangan_unit;
                
                // Jika surat yang diupdate adalah surat yang ditolak, reset statusnya menjadi pending
                if ($suratUnitManager->status_manager === 'rejected') {
                    $suratUnitManager->status_manager = 'pending';
                    $suratUnitManager->keterangan_manager = null; // Kosongkan keterangan dari manager
                    $suratUnitManager->waktu_review_manager = null;
                }

                // Proses file jika ada
                if ($request->hasFile('files')) {
                    // Hapus file lama jika ada
                    foreach ($suratUnitManager->files as $oldFile) {
                        try {
                            if (file_exists(public_path($oldFile->file_path))) {
                                unlink(public_path($oldFile->file_path));
                            }
                            $oldFile->delete();
                        } catch (\Exception $e) {
                            Log::error('Error deleting old file: ' . $e->getMessage());
                            continue;
                        }
                    }

                    $files = $request->file('files');
                    
                    // Validasi files array
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    
                    $uploadDir = public_path('uploads/surat_unit_manager');
                    $this->ensureUploadDirectory($uploadDir);
                    
                    $successCount = 0;
                    $totalFiles = count($files);
                    
                    foreach ($files as $file) {
                        if ($this->processFileUpload($file, $uploadDir, $suratUnitManager)) {
                            $successCount++;
                        }
                    }
                    
                    // Log hasil upload
                    if ($successCount > 0) {
                        Log::info("Berhasil upload {$successCount} dari {$totalFiles} file untuk surat ID: {$suratUnitManager->id}");
                    }
                    
                    if ($successCount === 0 && $totalFiles > 0) {
                        Log::warning("Tidak ada file yang berhasil diupload dari {$totalFiles} file untuk surat ID: {$suratUnitManager->id}");
                    }
                }

                $suratUnitManager->save();
                Log::info('Surat has been saved successfully.');

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
                Log::error('Exception during database transaction.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@update: ' . $e->getMessage());
            
            // Check if it's a file upload error
            if (strpos($e->getMessage(), 'SplFileInfo::getSize') !== false || 
                strpos($e->getMessage(), 'file does not exist') !== false) {
                $errorMessage = 'Terjadi kesalahan saat memproses file. Pastikan file yang dipilih valid, tidak rusak, dan ukurannya tidak melebihi 2MB.';
            } else {
                $errorMessage = 'Terjadi kesalahan saat memperbarui surat: ' . $e->getMessage();
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
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
            foreach ($suratUnitManager->files as $file) {
                if (file_exists(public_path($file->file_path))) {
                    unlink(public_path($file->file_path));
                }
                $file->delete();
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
            if (!$suratUnitManager->files->count()) {
                return redirect()->back()->with('error', 'Tidak ada file yang tersedia');
            }

            // Jika hanya ada satu file, download langsung
            if ($suratUnitManager->files->count() === 1) {
                $file = $suratUnitManager->files->first();
                if (!file_exists(public_path($file->file_path))) {
                    return redirect()->back()->with('error', 'File tidak ditemukan');
                }
                return response()->download(public_path($file->file_path), $file->original_name);
            }

            // Jika ada multiple files, buat zip
            $zip = new \ZipArchive();
            $zipName = 'surat_' . $suratUnitManager->id . '_' . time() . '.zip';
            $zipPath = public_path('uploads/temp/' . $zipName);
            
            if (!file_exists(public_path('uploads/temp'))) {
                mkdir(public_path('uploads/temp'), 0755, true);
            }

            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                foreach ($suratUnitManager->files as $file) {
                    if (file_exists(public_path($file->file_path))) {
                        $zip->addFile(public_path($file->file_path), $file->original_name);
                    }
                }
                $zip->close();
                
                return response()->download($zipPath)->deleteFileAfterSend();
            }

            return redirect()->back()->with('error', 'Gagal membuat file zip');

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@download: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file');
        }
    }

    /**
     * Download file individual
     */
    public function downloadFile(SuratUnitManager $suratUnitManager, $fileId)
    {
        try {
            $file = $suratUnitManager->files()->findOrFail($fileId);
            
            if (!file_exists(public_path($file->file_path))) {
                return redirect()->back()->with('error', 'File tidak ditemukan');
            }

            return response()->download(public_path($file->file_path), $file->original_name);
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@downloadFile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file');
        }
    }

    /**
     * Preview file surat unit manager
     */
    public function preview(SuratUnitManager $suratUnitManager)
    {
        try {
            if (!$suratUnitManager->files->count()) {
                return redirect()->back()->with('error', 'Tidak ada file yang tersedia');
            }

            // Jika hanya ada satu file, preview langsung
            if ($suratUnitManager->files->count() === 1) {
                $file = $suratUnitManager->files->first();
                if (!file_exists(public_path($file->file_path))) {
                    return redirect()->back()->with('error', 'File tidak ditemukan');
                }

                $filePath = public_path($file->file_path);
                $fileType = mime_content_type($filePath);

                if (strpos($fileType, 'image/') === 0) {
                    return response()->file($filePath);
                } elseif ($fileType === 'application/pdf') {
                    return response()->file($filePath);
                } else {
                    return response()->download($filePath);
                }
            }

            // Jika ada multiple files, redirect ke halaman detail
            return redirect()->route('surat-unit-manager.show', $suratUnitManager)
                           ->with('info', 'Surat memiliki multiple files. Silakan pilih file yang ingin di-preview.');

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@preview: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat preview file');
        }
    }

    /**
     * Preview file individual
     */
    public function previewFile(SuratUnitManager $suratUnitManager, $fileId)
    {
        try {
            $file = $suratUnitManager->files()->findOrFail($fileId);
            
            if (!file_exists(public_path($file->file_path))) {
                return redirect()->back()->with('error', 'File tidak ditemukan');
            }

            $filePath = public_path($file->file_path);
            $fileType = mime_content_type($filePath);

            if (strpos($fileType, 'image/') === 0) {
                return response()->file($filePath);
            } elseif ($fileType === 'application/pdf') {
                return response()->file($filePath);
            } else {
                return response()->download($filePath, $file->original_name);
            }
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerController@previewFile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat preview file');
        }
    }
} 