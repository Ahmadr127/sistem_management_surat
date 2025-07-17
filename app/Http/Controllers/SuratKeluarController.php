<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\User;
use App\Models\Disposisi;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SuratKeluarController extends Controller
{
    /**
     * Menampilkan daftar surat keluar
     */
    public function index(Request $request)
    {
        try {
            // Ambil data surat keluar dengan eager loading disposisi dan tujuan disposisi
            $query = SuratKeluar::with([
                'disposisi.tujuan',
                'creator.jabatan',
                'perusahaanData'
            ]);

            // Search filter
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter berdasarkan perusahaan
        if ($request->has('perusahaan')) {
            $query->byPerusahaan($request->perusahaan);
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

            // Order by newest records first - tanggal_surat desc, then created_at desc
            $suratKeluar = $query->orderBy('tanggal_surat', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->get();
            
            $users = User::select('id', 'name', 'email')
                        ->where('role', '!=', 3)
                        ->orderBy('name')
                        ->get();

            // Get perusahaan data for dropdown
            $perusahaans = Perusahaan::where('status', 'aktif')
                                     ->orderBy('nama_perusahaan')
                                     ->get();

            return view('pages.surat.index', compact('suratKeluar', 'users', 'perusahaans'));
        } catch (\Exception $e) {
            \Log::error('Error in SuratKeluarController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    /**
     * Menyimpan surat keluar baru
     */
    public function store(Request $request)
    {
        try {
            // Get active perusahaan codes for validation
            $activeCodes = Perusahaan::where('status', 'aktif')
                          ->pluck('kode')
                          ->toArray();
            
            // Add RSAZRA to valid codes to ensure it's always accepted
            if (!in_array('RSAZRA', $activeCodes)) {
                $activeCodes[] = 'RSAZRA';
            }
            
            \Log::info('SuratKeluarController@store: Beginning validation', [
                'input' => $request->all()
            ]);
                          
            $validator = Validator::make($request->all(), [
                'nomor_surat' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        // Jika nomor surat mengandung tanda strip (-), maka diperbolehkan
                        if (strpos($value, '-') !== false) {
                            return true;
                        }
                        
                        // Cek apakah nomor surat sudah digunakan
                        $exists = SuratKeluar::where('nomor_surat', $value)->exists();
                        if ($exists) {
                            $fail('Nomor surat sudah digunakan. Gunakan tanda strip (-) jika ingin menggunakan nomor yang sama.');
                        }
                    }
                ],
                'tanggal_surat' => 'required|date',
                'perihal' => 'required',
                'jenis_surat' => 'required|in:internal,eksternal',
                'sifat_surat' => 'required|in:normal,urgent',
                'file' => 'nullable|array',
                'file.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png',
                'perusahaan' => 'required|in:' . implode(',', $activeCodes)
            ]);

            if ($validator->fails()) {
                \Log::warning('SuratKeluarController@store: Validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                } else {
                    // Untuk request biasa, redirect back dengan error ke view
                    return back()
                        ->withInput()
                        ->with('validationErrors', $validator->errors());
                }
            }
            
            DB::beginTransaction();

            try {
                $suratKeluar = new SuratKeluar();
                $suratKeluar->nomor_surat = $request->nomor_surat;
                $suratKeluar->tanggal_surat = $request->tanggal_surat;
                $suratKeluar->perihal = $request->perihal;
                $suratKeluar->perusahaan = $request->perusahaan;
                $suratKeluar->jenis_surat = $request->jenis_surat;
                $suratKeluar->sifat_surat = $request->sifat_surat;
                $suratKeluar->created_by = auth()->id();
                $suratKeluar->save();

                // Proses dan simpan semua file jika ada
                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $file) {
                        $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
                        $uploadDir = public_path('uploads/surat_keluar');
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        $file->move($uploadDir, $fileName);
                        $filePath = 'uploads/surat_keluar/' . $fileName;
                        $originalName = $file->getClientOriginalName();
                        $fileType = $file->getClientMimeType();
                        $suratKeluar->files()->create([
                            'file_path' => $filePath,
                            'file_type' => $fileType,
                            'original_name' => $originalName,
                        ]);
                    }
                }
                
                // Check if there's disposisi data that needs to be saved
                if ($request->has('tujuan_disposisi') && !empty($request->tujuan_disposisi)) {
                    \Log::info('SuratKeluarController@store: Found disposisi targets, creating disposisi', [
                        'surat_id' => $suratKeluar->id,
                        'tujuan_disposisi' => $request->tujuan_disposisi,
                        'has_keterangan' => $request->has('keterangan_pengirim')
                    ]);
                    
                    try {
                        // Create a disposisi record
                        $disposisi = new Disposisi();
                        $disposisi->surat_keluar_id = $suratKeluar->id;
                        
                        // --- LOGIKA BARU: Surat antar manager/sekretaris tanpa approval ---
                        $tujuanIds = (array) $request->tujuan_disposisi;
                        $tujuanRoles = User::whereIn('id', $tujuanIds)->pluck('role', 'id')->toArray();
                        $pengirimRole = auth()->user()->role;
                        $pengirim = auth()->user();

                        // Cek logika khusus manager ke GM atau manager keuangan
                        $isManagerToGMKeu = false;
                        if ($pengirimRole == 4 && $pengirim->general_manager_id) {
                            $hasGM = false;
                            $hasKeuangan = false;
                            foreach ($tujuanRoles as $id => $role) {
                                if ($role == 6 && $id == $pengirim->general_manager_id) {
                                    $hasGM = true;
                                }
                                if ($role == 7) {
                                    $hasKeuangan = true;
                                }
                            }
                            if ($hasGM || $hasKeuangan) {
                                $isManagerToGMKeu = true;
                            }
                        }

                        $isAntarManSek = in_array($pengirimRole, [1,4]) && collect($tujuanRoles)->every(fn($r) => in_array($r, [1,4]));
                        
                        if ($pengirimRole == 5) { // Sekretaris ASP
                            $disposisi->status_sekretaris = 'approved';
                            $disposisi->status_dirut = 'pending';
                        } elseif ($pengirimRole == 8) { // Direktur ASP
                            $disposisi->status_sekretaris = 'approved';
                            $disposisi->status_dirut = 'pending';
                        } elseif ($isManagerToGMKeu) {
                            $disposisi->status_sekretaris = 'approved';
                            $disposisi->status_dirut = 'approved';
                        } elseif ($isAntarManSek) {
                            $disposisi->status_sekretaris = 'approved';
                            $disposisi->status_dirut = 'approved';
                        } else {
                            // Alur lama
                            if ($pengirimRole == 1) { // Sekretaris
                                $disposisi->status_sekretaris = 'approved';
                            } else {
                                $disposisi->status_sekretaris = 'pending';
                            }
                            $disposisi->status_dirut = 'pending';
                        }
                        // --- END LOGIKA BARU ---
                        $disposisi->keterangan_pengirim = $request->keterangan_pengirim ?? null;
                        $disposisi->created_by = auth()->id();
            
            $disposisi->save();
            
                        \Log::info('SuratKeluarController@store: Disposisi created', [
                            'disposisi_id' => $disposisi->id,
                            'surat_id' => $suratKeluar->id
                        ]);
                        
                        // Attach users to the disposisi
                        $disposisi->tujuan()->attach($tujuanIds);
                        
                        \Log::info('SuratKeluarController@store: Attached tujuan to disposisi', [
                            'disposisi_id' => $disposisi->id,
                            'tujuan_count' => count($tujuanIds),
                            'tujuan_ids' => $tujuanIds
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('SuratKeluarController@store: Error creating disposisi', [
                            'surat_id' => $suratKeluar->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e; // Rethrow to trigger rollback
                    }
                } else {
                    \Log::info('SuratKeluarController@store: No disposisi data provided, skipping disposisi creation');
                }
                
                // Commit transaction
            DB::commit();

                // Check if it's an AJAX request
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Surat keluar berhasil disimpan',
                        'data' => $suratKeluar,
                        'redirect_url' => route('suratkeluar.index')
                    ]);
                }
                
                // For non-AJAX requests, redirect with a flash message
            return redirect()->route('suratkeluar.index')
                    ->with('success', 'Surat keluar berhasil disimpan');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('SuratKeluarController@store: Transaction error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error in SuratKeluarController@store: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan surat keluar: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan surat keluar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan detail surat keluar
     */
    public function show(SuratKeluar $suratKeluar)
    {
        return view('pages.surat.suratkeluar.show', compact('suratKeluar'));
    }

    /**
     * Menampilkan form edit surat keluar
     */
    public function edit(SuratKeluar $suratKeluar)
    {
        try {
            // Get users for disposisi with their jabatan
            $users = User::with('jabatan')
                ->where('id', '!=', auth()->id())
                ->where('status_akun', 'aktif')
                ->get();
            
            // Load perusahaanData relation and files
            $suratKeluar->load(['perusahaanData', 'disposisi.tujuan', 'files']);
            
            // Log loaded files for debugging
            \Log::info('Files loaded for SuratKeluar ID: ' . $suratKeluar->id, [
                'files_count' => $suratKeluar->files->count(),
                'files' => $suratKeluar->files->toArray()
            ]);
            
            // Get selected users for disposisi
            $selectedUsers = [];
            if ($suratKeluar->disposisi) {
                $selectedUsers = $suratKeluar->disposisi->tujuan->pluck('id')->toArray();
            }

            // Get perusahaan data for dropdown
            $perusahaans = Perusahaan::where('status', 'aktif')
                          ->orderBy('nama_perusahaan')
                          ->get();
        
        return view('pages.surat.editsuratkeluar', [
            'surat' => $suratKeluar,
            'users' => $users,
                'selectedUsers' => $selectedUsers,
                'perusahaans' => $perusahaans
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in SuratKeluarController@edit: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return redirect()->route('suratkeluar.index')
                ->with('error', 'Terjadi kesalahan saat membuka form edit: ' . $e->getMessage());
        }
    }

    /**
     * Update surat keluar
     */
    public function update(Request $request, SuratKeluar $suratKeluar)
    {
        try {
            DB::beginTransaction();
            
            // Get active perusahaan codes for validation
            $activeCodes = Perusahaan::where('status', 'aktif')
                          ->pluck('kode')
                          ->toArray();
            
            // Add RSAZRA to valid codes to ensure it's always accepted
            if (!in_array('RSAZRA', $activeCodes)) {
                $activeCodes[] = 'RSAZRA';
            }
            
            \Log::info('SuratKeluarController@store: Beginning validation', [
                'input' => $request->all()
            ]);
                          
            $validator = Validator::make($request->all(), [
                'nomor_surat' => [
                    'required',
                    function ($attribute, $value, $fail) use ($suratKeluar) {
                        // Jika nomor surat mengandung tanda strip (-), maka diperbolehkan
                        if (strpos($value, '-') !== false) {
                            return true;
                        }
                        
                        // Cek apakah nomor surat sudah digunakan oleh surat lain (selain surat ini)
                        $exists = SuratKeluar::where('nomor_surat', $value)
                                  ->where('id', '!=', $suratKeluar->id)
                                  ->exists();
                        
                        if ($exists) {
                            $fail('Nomor surat sudah digunakan. Gunakan tanda strip (-) jika ingin menggunakan nomor yang sama.');
                        }
                    }
                ],
                'tanggal_surat' => 'required|date',
                'perihal' => 'required',
                'jenis_surat' => 'required|in:internal,eksternal',
                'sifat_surat' => 'required|in:normal,urgent',
                'file' => 'nullable|array',
                'file.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png',
                'perusahaan' => 'required|in:' . implode(',', $activeCodes),
                'tujuan_disposisi' => 'nullable|array',
                'tujuan_disposisi.*' => 'exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Handle file upload if provided
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $originalName = $file->getClientOriginalName();
                    $fileType = $file->getClientMimeType();
                    
                    // Create uploads directory if it doesn't exist
                    $uploadDir = public_path('uploads/surat_keluar');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Move the file to the uploads directory
                    $file->move($uploadDir, $fileName);
                    $filePath = 'uploads/surat_keluar/' . $fileName;
                    
                    // Create or update file record
                    $suratKeluar->files()->create([
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'original_name' => $originalName,
                    ]);
                }
            }
            
            // Update SuratKeluar
            $suratKeluar->nomor_surat = $request->nomor_surat;
            $suratKeluar->tanggal_surat = $request->tanggal_surat;
            $suratKeluar->perihal = $request->perihal;
            $suratKeluar->perusahaan = $request->perusahaan;
            $suratKeluar->jenis_surat = $request->jenis_surat;
            $suratKeluar->sifat_surat = $request->sifat_surat;
            
            $suratKeluar->save();
        
            // Update or create disposisi if tujuan_disposisi is provided
            if ($request->has('tujuan_disposisi') && is_array($request->tujuan_disposisi) && count($request->tujuan_disposisi) > 0) {
                $disposisi = $suratKeluar->disposisi ?? new Disposisi();
                $disposisi->surat_keluar_id = $suratKeluar->id;
                $disposisi->keterangan_pengirim = $request->keterangan_pengirim;
                
                // Set status based on user role
                if (auth()->user()->role == 5) { // Sekretaris ASP
                    $disposisi->status_sekretaris = 'approved';
                    $disposisi->status_dirut = 'pending';
                } elseif (auth()->user()->role == 8) { // Direktur ASP
                    $disposisi->status_sekretaris = 'approved';
                    $disposisi->status_dirut = 'pending';
                } elseif (auth()->user()->role == 1) { // Sekretaris
                    $disposisi->status_sekretaris = 'approved';
                    $disposisi->status_dirut = 'pending';
                } else {
                    $disposisi->status_sekretaris = 'pending';
                    $disposisi->status_dirut = 'pending';
                }
                
                $disposisi->save();
                
                // Sync tujuan disposisi
                $disposisi->tujuan()->sync($request->tujuan_disposisi);
            }
            
            DB::commit();
            
            // Load the files relationship for the response
            $suratKeluar->load(['files', 'creator', 'disposisi.tujuan']);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat keluar berhasil diperbarui',
                    'data' => $suratKeluar,
                    'redirect_url' => route('suratkeluar.index')
                ]);
            }
            
            return redirect()->route('suratkeluar.index')
                ->with('success', 'Surat keluar berhasil diperbarui');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in SuratKeluarController@update: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui surat keluar: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui surat keluar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus surat keluar (soft delete)
     */
    public function destroy(SuratKeluar $suratKeluar)
    {
        try {
            // Hanya hapus record secara soft delete
            $suratKeluar->delete();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat keluar berhasil dihapus'
                ]);
            }

            return redirect()
                ->route('suratkeluar.index')
                ->with('success', 'Surat keluar berhasil dihapus');

        } catch (\Exception $e) {
            \Log::error('Error saat menghapus surat keluar: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download file surat keluar.
     * If there are multiple files, it creates a zip archive.
     * If there is one file, it downloads directly.
     */
    public function download(SuratKeluar $suratKeluar)
    {
        try {
            \Log::info('Download request for surat_keluar_id: ' . $suratKeluar->id);
            $suratKeluar->load('files');
            $files = $suratKeluar->files;

            if ($files->isEmpty()) {
                \Log::warning('No files to download for surat_keluar_id: ' . $suratKeluar->id);
                return redirect()->back()->with('error', 'Tidak ada lampiran untuk diunduh.');
            }

            if ($files->count() === 1) {
                $file = $files->first();
                $path = public_path($file->file_path);
                \Log::info('Single file download.', ['path' => $path]);

                if (!file_exists($path)) {
                    \Log::error('File not found on disk.', ['path' => $path]);
                    return redirect()->back()->with('error', 'File tidak ditemukan di server.');
                }
                return response()->download($path, $file->original_name);
            }

            // Multiple files: create and download a zip file
            \Log::info('Multiple files found. Creating zip archive.', ['count' => $files->count()]);
            $zip = new \ZipArchive();
            $zipFileName = 'surat-keluar-' . $suratKeluar->id . '-' . time() . '.zip';
            
            // Create a temporary directory for the zip file
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $zipPath = $tempDir . '/' . $zipFileName;

            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                \Log::error('Cannot create zip archive.', ['path' => $zipPath]);
                return redirect()->back()->with('error', 'Gagal membuat arsip ZIP.');
            }

            foreach ($files as $file) {
                $filePath = public_path($file->file_path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $file->original_name);
                    \Log::info('Added file to zip.', ['file_path' => $filePath, 'zip_name' => $file->original_name]);
                } else {
                    \Log::warning('File skipped (not found).', ['file_path' => $filePath]);
                }
            }

            $zip->close();
            \Log::info('Zip archive created successfully.', ['path' => $zipPath]);

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Error during file download: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file.');
        }
    }

    /**
     * Preview file surat keluar. Redirects to preview the first file.
     */
    public function preview(SuratKeluar $suratKeluar)
    {
        try {
            \Log::info('Preview request for surat_keluar_id: ' . $suratKeluar->id);
            $suratKeluar->load('files');
            $file = $suratKeluar->files->first();

            if (!$file) {
                \Log::warning('No file found for preview.', ['surat_keluar_id' => $suratKeluar->id]);
                return response('Tidak ada file untuk ditampilkan.', 404);
            }

            // Redirect to the new previewFile route for cleaner implementation
            \Log::info('File found, redirecting to previewFile route.', ['file_id' => $file->id]);
            return redirect()->route('suratkeluar.preview-file', ['suratId' => $suratKeluar->id, 'fileId' => $file->id]);
            
        } catch (\Exception $e) {
            \Log::error('Error creating preview redirect: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response('Terjadi kesalahan saat menampilkan file.', 500);
        }
    }

    /**
     * Download an individual file from a surat keluar.
     */
    public function downloadFile($suratId, $fileId)
    {
        try {
            \Log::info('Individual file download request.', ['surat_id' => $suratId, 'file_id' => $fileId]);
            $suratKeluar = SuratKeluar::findOrFail($suratId);
            $file = $suratKeluar->files()->findOrFail($fileId);
            $path = public_path($file->file_path);

            if (!file_exists($path)) {
                \Log::error('File not found on disk for download.', ['path' => $path]);
                return redirect()->back()->with('error', 'File tidak ditemukan di server.');
            }
            
            \Log::info('Downloading individual file.', ['path' => $path, 'original_name' => $file->original_name]);
            return response()->download($path, $file->original_name);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Model not found for file download.', ['surat_id' => $suratId, 'file_id' => $fileId, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Data surat atau file tidak ditemukan.');
        } catch (\Exception $e) {
            \Log::error('Error downloading individual file: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengunduh file.');
        }
    }

    /**
     * Preview an individual file from a surat keluar.
     */
    public function previewFile($suratId, $fileId)
    {
        try {
            \Log::info('Individual file preview request.', ['surat_id' => $suratId, 'file_id' => $fileId]);
            $suratKeluar = SuratKeluar::findOrFail($suratId);
            $file = $suratKeluar->files()->findOrFail($fileId);
            $path = public_path($file->file_path);

            if (!file_exists($path)) {
                \Log::error('File not found on disk for preview.', ['path' => $path]);
                return response('File tidak ditemukan di server.', 404);
            }

            $mime = mime_content_type($path);
            \Log::info('Serving file for preview.', ['path' => $path, 'mime' => $mime]);

            return response()->file($path, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $file->original_name . '"'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Model not found for file preview.', ['surat_id' => $suratId, 'file_id' => $fileId, 'error' => $e->getMessage()]);
            return response('Data surat atau file tidak ditemukan.', 404);
        } catch (\Exception $e) {
            \Log::error('Error previewing individual file: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response('Terjadi kesalahan saat menampilkan file.', 500);
        }
    }

    /**
     * Get surat keluar data for API
     */
    public function getSuratKeluar(Request $request)
    {
        try {
            $query = SuratKeluar::with(['disposisi.tujuan', 'creator.jabatan', 'perusahaanData', 'files']);

            $user = auth()->user();
            
            // Semua role hanya menampilkan data yang dibuat oleh user yang sedang login
            $query->where('created_by', $user->id);

            // Filter status disposisi
            if ($request->filled('status_sekretaris')) {
                $query->whereHas('disposisi', function ($q) use ($request) {
                    $q->where('status_sekretaris', $request->status_sekretaris);
                });
            }

            if ($request->filled('status_dirut')) {
                $query->whereHas('disposisi', function ($q) use ($request) {
                    $q->where('status_dirut', $request->status_dirut);
                });
            }

            // Filter berdasarkan tanggal
            if ($request->filled('start_date')) {
                $query->whereDate('tanggal_surat', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('tanggal_surat', '<=', $request->end_date);
            }
            
            // Filter berdasarkan jenis surat
            if ($request->filled('jenis_surat')) {
                $query->where('jenis_surat', $request->jenis_surat);
            }
            
            // Filter berdasarkan sifat surat
            if ($request->filled('sifat_surat')) {
                $query->where('sifat_surat', $request->sifat_surat);
            }
            
            // Filter berdasarkan perusahaan
            if ($request->filled('perusahaan')) {
                $query->where('perusahaan', $request->perusahaan);
            }
            
            // Filter pencarian
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nomor_surat', 'like', "%{$searchTerm}%")
                      ->orWhere('perihal', 'like', "%{$searchTerm}%")
                      ->orWhereHas('creator', function ($subq) use ($searchTerm) {
                          $subq->where('name', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('perusahaanData', function ($subq) use ($searchTerm) {
                        $subq->where('nama_perusahaan', 'like', "%{$searchTerm}%");
                    });
                });
            }
            
            // Urutkan data
            $suratKeluar = $query->orderBy('tanggal_surat', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->get();

            return response()->json($suratKeluar);
        } catch (\Exception $e) {
            \Log::error('Error fetching surat keluar: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data surat keluar'], 500);
        }
    }

    public function getLastNumber(Request $request)
    {
        try {
            // Logic untuk nomor surat internal maupun eksternal
            $isEksternalAzra = $request->is_eksternal_azra ?? false;
            $isAsp = $request->is_asp ?? false;
            $tahun = $request->tanggal_surat ? date('Y', strtotime($request->tanggal_surat)) : date('Y');

            if ($isEksternalAzra) {
                // Eksternal AZRA
                $nomorList = SuratKeluar::where('nomor_surat', 'like', '%/RSAZRA/%')
                    ->whereYear('tanggal_surat', $tahun)
                    ->pluck('nomor_surat')
                    ->toArray();
                \Log::info('Tahun yang diambil:', [$tahun]);
                \Log::info('Nomor surat AZRA ditemukan:', $nomorList);
                $maxNumber = 0;
                foreach ($nomorList as $nomor) {
                    if (preg_match('/^(\\d{3})\/RSAZRA\//', $nomor, $matches)) {
                        $num = intval($matches[1]);
                        if ($num > $maxNumber) {
                            $maxNumber = $num;
                        }
                    }
                }
                $lastNumber = str_pad($maxNumber, 3, '0', STR_PAD_LEFT); // hanya angka terakhir
                return response()->json([
                    'success' => true,
                    'last_number' => $lastNumber
                ]);
            }

            if ($isAsp) {
                // ASP
                $nomorList = SuratKeluar::where('nomor_surat', 'like', '%/ASP/%')
                    ->whereYear('tanggal_surat', $tahun)
                    ->pluck('nomor_surat')
                    ->toArray();
                $maxNumber = 0;
                foreach ($nomorList as $nomor) {
                    if (preg_match('/^(\\d{3})\/ASP\//', $nomor, $matches)) {
                        $num = intval($matches[1]);
                        if ($num > $maxNumber) {
                            $maxNumber = $num;
                        }
                    }
                }
                $lastNumber = str_pad($maxNumber, 3, '0', STR_PAD_LEFT); // hanya angka terakhir
                return response()->json([
                    'success' => true,
                    'last_number' => $lastNumber
                ]);
            }

            // Logic untuk nomor surat internal (default)
            $kodeJabatan = $request->kode_jabatan;
            $isAsDirut = $request->is_as_dirut;
            if ($isAsDirut) {
                $kodeJabatan = 'DIRUT';
            }
            $nomorList = SuratKeluar::whereYear('tanggal_surat', $tahun)
                ->where(function ($query) use ($kodeJabatan) {
                    $query->where('nomor_surat', 'like', "%/{$kodeJabatan}/%")
                        ->orWhere('nomor_surat', 'like', "%/{$kodeJabatan}/%\-");
                })
                ->pluck('nomor_surat')
                ->toArray();
            \Log::info('Nomor surat internal ditemukan:', $nomorList);
            $maxNumber = 0;
            foreach ($nomorList as $nomor) {
                if (preg_match('/^(\\d{3})\/' . preg_quote($kodeJabatan, '/') . '\//', $nomor, $matches)) {
                    $num = intval($matches[1]);
                    if ($num > $maxNumber) {
                        $maxNumber = $num;
                    }
                }
            }
            $lastNumber = $maxNumber; // hanya angka terakhir
            return response()->json([
                'success' => true,
                'last_number' => $lastNumber
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getLastNumber: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil nomor surat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function arsip()
    {
        try {
            $suratKeluar = SuratKeluar::with([
                'disposisi.tujuan',
                'creator.jabatan',
                'perusahaanData',
                'files'
            ])
            ->orderBy('tanggal_surat', 'desc')
            ->get();
            
            // Get all active perusahaan for filter dropdown
            $perusahaans = Perusahaan::where('status', 'aktif')
                          ->orderBy('nama_perusahaan')
                          ->get();
            
            // Get all jabatan for filter dropdown
            $jabatanList = \App\Models\Jabatan::where('status', 'aktif')
                          ->orderBy('nama_jabatan')
                          ->get();
            
            return view('pages.arsip', compact('suratKeluar', 'jabatanList', 'perusahaans'));
        } catch (\Exception $e) {
            \Log::error('Error in SuratKeluarController@arsip: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data arsip');
        }
    }

    /**
     * Menampilkan halaman form tambah surat keluar
     */
    public function create()
    {
        try {
            $users = User::with('jabatan')
                ->where('status_akun', 'aktif')
                ->where('id', '!=', auth()->id())
                ->get();

            // Get perusahaan data for dropdown
            $perusahaans = Perusahaan::where('status', 'aktif')
                          ->orderBy('nama_perusahaan')
                          ->get();
                          
            // Get authenticated user's perusahaan preference
            $userPerusahaan = null;
            $user = auth()->user();
            if ($user && $user->jabatan && $user->jabatan->perusahaan_default) {
                $userPerusahaan = Perusahaan::where('kode', $user->jabatan->perusahaan_default)
                                  ->where('status', 'aktif')
                                  ->first();
            }

            return view('pages.surat.suratkeluar', compact('users', 'perusahaans', 'userPerusahaan'));
        } catch (\Exception $e) {
            \Log::error('Error in SuratKeluarController@create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman');
        }
    }

    /**
     * Menampilkan surat yang sudah dihapus (soft delete)
     */
    public function trashed()
    {
        try {
            // Surat yang sudah dihapus hanya bisa dilihat oleh pembuat surat, kecuali untuk admin (role 3)
            return view('pages.surat.trashed');
        } catch (\Exception $e) {
            \Log::error('Error saat menampilkan surat yang sudah dihapus: ' . $e->getMessage());
            return redirect()->route('suratkeluar.index')
                ->with('error', 'Terjadi kesalahan saat memuat data surat terhapus');
        }
    }
    
    /**
     * Mendapatkan data surat yang sudah dihapus untuk API
     */
    public function getTrashedSurat(Request $request)
    {
        try {
            $userId = auth()->id();
            $userRole = auth()->user()->role;
            
            \Log::info('Accessing getTrashedSurat', [
                'user_id' => $userId,
                'user_role' => $userRole,
                'request' => $request->all()
            ]);
            
            // Hanya ambil yang sudah di-soft delete
            $query = SuratKeluar::onlyTrashed();
            
            // Tambahkan eager loading untuk relasi yang diperlukan
            $query->with([
                'disposisi' => function($q) {
                    $q->select('id', 'surat_keluar_id', 'status_sekretaris', 'status_dirut');
                },
                'creator' => function($q) {
                    $q->select('id', 'name', 'jabatan_id')
                      ->with('jabatan:id,nama_jabatan');
                },
                'files'
            ]);

            // Filter berdasarkan role
            if ($userRole != 3) { // Bukan admin
                \Log::info('Applying filter for non-admin role');
                $query->where('created_by', $userId);
            }

            // Dapatkan data surat yang sudah dihapus
            $trashedSurat = $query->orderBy('deleted_at', 'desc')->get();
            
            \Log::info('Total trashed surat found', ['count' => $trashedSurat->count()]);
            
            // Transform data untuk response
            $transformedData = $trashedSurat->map(function ($surat) {
                return [
                    'id' => $surat->id,
                    'nomor_surat' => $surat->nomor_surat,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'jenis_surat' => $surat->jenis_surat,
                    'sifat_surat' => $surat->sifat_surat,
                    'perihal' => $surat->perihal,
                    'perusahaan' => $surat->perusahaan,
                    'created_by' => $surat->created_by,
                    'creator' => [
                        'id' => $surat->creator->id ?? null,
                        'name' => $surat->creator->name ?? 'User tidak ditemukan',
                        'jabatan' => optional($surat->creator->jabatan)->nama_jabatan ?? 'Tidak ada jabatan'
                    ],
                    'deleted_at' => $surat->deleted_at->format('Y-m-d H:i:s'),
                    'files' => $surat->files->map(function($file) {
                        return [
                            'id' => $file->id,
                            'file_path' => $file->file_path,
                            'original_name' => $file->original_name
                        ];
                    })
                ];
            });
            
            return response()->json($transformedData);
            
        } catch (\Exception $e) {
            \Log::error('Error saat mendapatkan data surat yang dihapus: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data surat terhapus'
            ], 500);
        }
    }
    
    /**
     * Mengembalikan surat yang sudah dihapus (restore)
     */
    public function restore($id)
    {
        try {
            $userId = auth()->id();
            $userRole = auth()->user()->role;
            
            // Cari surat di trash
            $surat = SuratKeluar::onlyTrashed()->findOrFail($id);
            
            // Periksa apakah user berhak mengembalikan surat ini
            if ($userRole != 3 && $surat->created_by != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengembalikan surat ini'
                ], 403);
            }
            
            // Kembalikan surat
            $surat->restore();
            
            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil dikembalikan'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error saat mengembalikan surat: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengembalikan surat'
            ], 500);
        }
    }
    
    /**
     * Menghapus surat permanen
     */
    public function forceDelete($id)
    {
        try {
            $userId = auth()->id();
            $userRole = auth()->user()->role;
            
            // Cari surat di trash
            $surat = SuratKeluar::onlyTrashed()->findOrFail($id);
            
            // Periksa apakah user berhak menghapus permanen surat ini
            if ($userRole != 3 && $surat->created_by != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus permanen surat ini'
                ], 403);
            }
            
            // Hapus file fisik jika ada
            if ($surat->file_path && file_exists(public_path($surat->file_path))) {
                unlink(public_path($surat->file_path));
            }
            
            // Hapus file terkait dari table surat_keluar_files
            foreach ($surat->files as $file) {
                if (file_exists(public_path($file->file_path))) {
                    unlink(public_path($file->file_path));
                }
                $file->delete();
            }
            
            // Hapus surat permanen
            $surat->forceDelete();
            
            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil dihapus permanen'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error saat menghapus permanen surat: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus permanen surat'
            ], 500);
        }
    }

    /**
     * Hapus file surat keluar
     */
    public function deleteFile($suratId, $fileId)
    {
        try {
            $suratKeluar = SuratKeluar::findOrFail($suratId);
            $file = $suratKeluar->files()->findOrFail($fileId);
            
            // Hapus file fisik
            if (file_exists(public_path($file->file_path))) {
                unlink(public_path($file->file_path));
            }
            
            // Hapus record file dari database
            $file->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error saat menghapus file surat keluar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get surat keluar berdasarkan kode format nomor surat
     * Param: ?kode=RSAZRA|DIRRS|Dir.Adm.Keu|ASP&search=...&page=1&per_page=10
     * Return: JSON data lengkap + pagination
     */
    public function getByFormat(Request $request)
    {
        $kode = $request->input('kode');
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        if (!$kode) {
            return response()->json(['success' => false, 'message' => 'Kode format nomor surat wajib diisi'], 400);
        }
        try {
            $query = SuratKeluar::with(['creator', 'disposisi.tujuan']);
            // Filter khusus untuk kode RSAZRA (Umum)
            if ($kode === 'RSAZRA') {
                $query->whereRaw("nomor_surat REGEXP '^[0-9]{3}/RSAZRA/(I{1,3}|IV|V?I{0,3}|VI{0,3}|VII|VIII|IX|X|XI|XII)/[0-9]{4}$'");
            } else if ($kode === 'DIRRS') {
                $query->whereRaw("nomor_surat REGEXP '^[0-9]{3}/DIRRS/RSAZRA/(I{1,3}|IV|V?I{0,3}|VI{0,3}|VII|VIII|IX|X|XI|XII)/[0-9]{4}$'");
            } else if ($kode === 'Dir.Adm.Keu') {
                $query->whereRaw("nomor_surat REGEXP '^[0-9]{3}/Dir\\.Adm\\.Keu/RSAZRA/(I{1,3}|IV|V?I{0,3}|VI{0,3}|VII|VIII|IX|X|XI|XII)/[0-9]{4}$'");
            } else if ($kode === 'ASP') {
                $query->whereRaw("nomor_surat REGEXP '^[0-9]{3}/ASP/(I{1,3}|IV|V?I{0,3}|VI{0,3}|VII|VIII|IX|X|XI|XII)/[0-9]{4}$'");
            } else {
                $query->where('nomor_surat', 'like', "%/{$kode}/%");
            }
            $query->orderBy('tanggal_surat', 'desc');
            if ($search) {
                // Escape karakter spesial untuk REGEXP
                $searchSafe = preg_replace('/[^a-zA-Z0-9\s\-\/]/', '', $search);
                // Jika search mengandung karakter spesial yang tidak diizinkan, fallback ke LIKE
                if ($searchSafe !== $search) {
                    $query->where(function($q) use ($search) {
                        $q->where('nomor_surat', 'like', "%{$search}%")
                          ->orWhere('perihal', 'like', "%{$search}%")
                          ->orWhereHas('creator', function($qc) use ($search) {
                              $qc->where('name', 'like', "%{$search}%");
                          });
                    });
                } else {
                    $query->where(function($q) use ($searchSafe) {
                        $q->where('nomor_surat', 'like', "%{$searchSafe}%")
                          ->orWhere('perihal', 'like', "%{$searchSafe}%")
                          ->orWhereHas('creator', function($qc) use ($searchSafe) {
                              $qc->where('name', 'like', "%{$searchSafe}%");
                          });
                    });
                }
            }
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);
            $data = $paginator->getCollection()->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_surat' => $item->nomor_surat,
                    'tanggal_surat' => $item->tanggal_surat,
                    'waktu' => $item->created_at->format('H:i'),
                    'tanggal' => $item->created_at->format('Y-m-d'),
                    'status' => $item->disposisi->status_sekretaris ?? '-',
                    'perihal' => $item->perihal,
                    'pengirim' => $item->creator ? $item->creator->name : '-',
                    'tujuan' => $item->disposisi && $item->disposisi->tujuan ? $item->disposisi->tujuan->pluck('name')->implode(', ') : '-',
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getByFormat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }
}
