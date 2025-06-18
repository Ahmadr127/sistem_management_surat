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
            
            // Automatically set perusahaan to RSAZRA for internal letters
            if ($request->jenis_surat === 'internal') {
                $request->merge(['perusahaan' => 'RSAZRA']);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
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
                        
                        // Set status_sekretaris to 'approved' if creator is a secretary (role 1)
                        if (auth()->user()->role == 1) { // Sekretaris has role 1
                            $disposisi->status_sekretaris = 'approved';
                            \Log::info('SuratKeluarController@store: Secretary detected (role 1 = Sekretaris), setting status_sekretaris to approved', [
                                'user_id' => auth()->id(),
                                'user_role' => auth()->user()->role,
                                'user_name' => auth()->user()->name,
                                'status_sekretaris' => $disposisi->status_sekretaris
                            ]);
                        } else {
                            $disposisi->status_sekretaris = 'pending';
                            \Log::info('SuratKeluarController@store: Non-secretary detected (roles: 0=Staff, 2=Direktur, 3=Super Admin), setting status_sekretaris to pending', [
                                'user_id' => auth()->id(),
                                'user_role' => auth()->user()->role,
                                'user_name' => auth()->user()->name,
                                'status_sekretaris' => $disposisi->status_sekretaris
                            ]);
                        }
                        
                        $disposisi->status_dirut = 'pending';
                        $disposisi->keterangan_pengirim = $request->keterangan_pengirim ?? null;
                        $disposisi->created_by = auth()->id();
            
            $disposisi->save();
            
                        \Log::info('SuratKeluarController@store: Disposisi created', [
                            'disposisi_id' => $disposisi->id,
                            'surat_id' => $suratKeluar->id
                        ]);
                        
                        // Attach users to the disposisi
                        $tujuanIds = (array) $request->tujuan_disposisi;
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
            
            // Automatically set perusahaan to RSAZRA for internal letters
            if ($request->jenis_surat === 'internal') {
                $request->merge(['perusahaan' => 'RSAZRA']);
            }
                          
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
                if (auth()->user()->role == 1) { // Sekretaris
                    $disposisi->status_sekretaris = 'approved';
                } else {
                    $disposisi->status_sekretaris = 'pending';
                }
                $disposisi->status_dirut = 'pending';
                
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
     * Download file surat keluar
     */
    public function download(SuratKeluar $suratKeluar)
    {
        try {
            if (empty($suratKeluar->file_path)) {
                \Log::warning('Path file kosong untuk surat ID: ' . $suratKeluar->id);
                return redirect()
                    ->back()
                    ->with('error', 'Path file tidak ditemukan');
            }

            $filePath = null;
            
            // Check if file exists in public path
            if (file_exists(public_path($suratKeluar->file_path))) {
            $filePath = public_path($suratKeluar->file_path);
                \Log::info('File ditemukan di public_path untuk download: ' . $filePath);
            }
            // Try storage path if the path starts with /storage/
            else if (Str::startsWith($suratKeluar->file_path, '/storage/')) {
                $normalizedPath = str_replace('/storage/', 'public/', $suratKeluar->file_path);
                if (Storage::exists($normalizedPath)) {
                    $filePath = Storage::path($normalizedPath);
                    \Log::info('File ditemukan di storage path untuk download: ' . $filePath);
                }
            }
            
            // If still not found, try with filename only
            if (!$filePath || !file_exists($filePath)) {
                $fileName = basename($suratKeluar->file_path);
                
                // Try in uploads directory
                $uploadsPath = public_path('uploads/surat_keluar/' . $fileName);
                if (file_exists($uploadsPath)) {
                    $filePath = $uploadsPath;
                    \Log::info('File ditemukan di uploads/surat_keluar dengan nama file untuk download: ' . $filePath);
                }
                // Try in storage directory
                else {
                    $storagePath = 'public/surat_keluar/' . $fileName;
                    if (Storage::exists($storagePath)) {
                        $filePath = Storage::path($storagePath);
                        \Log::info('File ditemukan di storage/app/public/surat_keluar dengan nama file untuk download: ' . $filePath);
                    }
                }
            }
            
            // If file not found, return error
            if (!$filePath || !file_exists($filePath)) {
                \Log::error('File download tidak ditemukan untuk surat ID: ' . $suratKeluar->id);
                return redirect()
                    ->back()
                    ->with('error', 'File tidak ditemukan di server');
            }
            
            \Log::info('Mengunduh file: ' . $filePath);
            
            return response()->download($filePath, basename($filePath));
        } catch (\Exception $e) {
            \Log::error('Error saat download file: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengunduh file: ' . $e->getMessage());
        }
    }

    /**
     * Get surat keluar data for API
     */
    public function getSuratKeluar(Request $request)
    {
        try {
            $userId = auth()->id();
            $userRole = auth()->user()->role;
            
            \Log::info('Accessing getSuratKeluar', [
                'user_id' => $userId,
                'user_role' => $userRole,
                'request' => $request->all()
            ]);
            
            // Secara default hanya ambil yang tidak di-soft delete
            $query = SuratKeluar::query();
            
            // Tambahkan eager loading untuk relasi yang diperlukan
            $query->with([
                'disposisi' => function($q) {
                    $q->select('id', 'surat_keluar_id', 'status_sekretaris', 'status_dirut', 'keterangan_pengirim', 'keterangan_sekretaris', 'keterangan_dirut');
                },
                'disposisi.tujuan' => function($q) {
                    $q->select('users.id', 'users.name', 'jabatan_id')
                      ->with('jabatan:id,nama_jabatan');
                },
                'creator' => function($q) {
                    $q->select('id', 'name', 'jabatan_id')
                      ->with('jabatan:id,nama_jabatan');
                },
                'files'
            ]);

            // Filter berdasarkan role
            if ($userRole == 0 || $userRole == 1 || $userRole == 3) { // Staff, Sekretaris, atau Admin
                \Log::info('Applying filter for Staff/Sekretaris/Admin');
                $query->where('created_by', $userId);
            }

            // Terapkan filter pencarian jika ada
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $isPgsql = config('database.default') === 'pgsql';
                \Log::info('Applying search filter', ['term' => $searchTerm, 'pgsql' => $isPgsql]);
                $query->where(function($q) use ($searchTerm, $isPgsql) {
                    if ($isPgsql) {
                        $q->whereRaw('nomor_surat ILIKE ?', ["%{$searchTerm}%"])
                          ->orWhereRaw('perihal ILIKE ?', ["%{$searchTerm}%"])
                          ->orWhereRaw('perusahaan ILIKE ?', ["%{$searchTerm}%"]);
                    } else {
                    $q->where('nomor_surat', 'like', "%{$searchTerm}%")
                      ->orWhere('perihal', 'like', "%{$searchTerm}%")
                          ->orWhere('perusahaan', 'like', "%{$searchTerm}%");
                    }
                    // Relasi perusahaanData
                    $q->orWhereHas('perusahaanData', function($subquery) use ($searchTerm, $isPgsql) {
                        if ($isPgsql) {
                            $subquery->whereRaw('nama_perusahaan ILIKE ?', ["%{$searchTerm}%"]);
                        } else {
                          $subquery->where('nama_perusahaan', 'like', "%{$searchTerm}%");
                        }
                    });
                    // Relasi creator
                    $q->orWhereHas('creator', function($subquery) use ($searchTerm, $isPgsql) {
                        if ($isPgsql) {
                            $subquery->whereRaw('name ILIKE ?', ["%{$searchTerm}%"])
                                     ->orWhereHas('jabatan', function($q2) use ($searchTerm) {
                                         $q2->whereRaw('nama_jabatan ILIKE ?', ["%{$searchTerm}%"]);
                                     });
                        } else {
                          $subquery->where('name', 'like', "%{$searchTerm}%")
                                   ->orWhereHas('jabatan', function($q2) use ($searchTerm) {
                                       $q2->where('nama_jabatan', 'like', "%{$searchTerm}%");
                                   });
                        }
                    });
                    // Relasi disposisi
                    $q->orWhereHas('disposisi', function($subquery) use ($searchTerm, $isPgsql) {
                        if ($isPgsql) {
                            $subquery->whereRaw('status_sekretaris ILIKE ?', ["%{$searchTerm}%"])
                                     ->orWhereRaw('status_dirut ILIKE ?', ["%{$searchTerm}%"])
                                     ->orWhereHas('tujuan', function($q2) use ($searchTerm) {
                                         $q2->whereRaw('name ILIKE ?', ["%{$searchTerm}%"]);
                                     });
                        } else {
                          $subquery->where('status_sekretaris', 'like', "%{$searchTerm}%")
                                   ->orWhere('status_dirut', 'like', "%{$searchTerm}%")
                                   ->orWhereHas('tujuan', function($q2) use ($searchTerm) {
                                       $q2->where('name', 'like', "%{$searchTerm}%");
                                   });
                        }
                      });
                });
            }

            // Filter tanggal
            if ($request->has('start_date') && $request->has('end_date')) {
                \Log::info('Applying date filter', [
                    'start' => $request->start_date,
                    'end' => $request->end_date
                ]);
                
                $query->whereBetween('tanggal_surat', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            // Filter lainnya
            if ($request->has('jenis_surat')) {
                $query->where('jenis_surat', $request->jenis_surat);
            }
            if ($request->has('sifat_surat')) {
                $query->where('sifat_surat', $request->sifat_surat);
            }
            if ($request->has('perusahaan')) {
                $query->where('perusahaan', $request->perusahaan);
            }

            // Get data ordered by newest records first
            $suratKeluar = $query->orderBy('tanggal_surat', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->get();
            
            \Log::info('Total surat found', ['count' => $suratKeluar->count()]);
            
            // Transform data untuk response
            $transformedData = $suratKeluar->map(function ($surat) {
                $data = [
                    'id' => $surat->id,
                    'nomor_surat' => $surat->nomor_surat,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'jenis_surat' => $surat->jenis_surat,
                    'sifat_surat' => $surat->sifat_surat,
                    'perihal' => $surat->perihal,
                    'perusahaan' => $surat->perusahaan,
                    'perusahaanData' => $surat->perusahaanData ? [
                        'kode' => $surat->perusahaanData->kode,
                        'nama_perusahaan' => $surat->perusahaanData->nama_perusahaan
                    ] : null,
                    'file_path' => $surat->file_path,
                    'created_by' => $surat->created_by,
                    'creator' => [
                        'id' => $surat->creator->id,
                        'name' => $surat->creator->name,
                        'jabatan' => optional($surat->creator->jabatan)->nama_jabatan
                    ],
                    'created_at' => $surat->created_at,
                    'disposisi' => null,
                    'files' => $surat->files->map(function($file) {
                        return [
                            'id' => $file->id,
                            'file_path' => $file->file_path,
                            'file_type' => $file->file_type,
                            'original_name' => $file->original_name,
                        ];
                    })
                ];

                if ($surat->disposisi) {
                    $data['disposisi'] = [
                        'id' => $surat->disposisi->id,
                        'status_sekretaris' => $surat->disposisi->status_sekretaris,
                        'status_dirut' => $surat->disposisi->status_dirut,
                        'keterangan_pengirim' => $surat->disposisi->keterangan_pengirim,
                        'keterangan_sekretaris' => $surat->disposisi->keterangan_sekretaris,
                        'keterangan_dirut' => $surat->disposisi->keterangan_dirut,
                        'tujuan' => $surat->disposisi->tujuan->map(function($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                                'jabatan' => optional($user->jabatan)->nama_jabatan
                            ];
                        })
                    ];
                }

                return $data;
            });
            
            return response()->json($transformedData);
            
        } catch (\Exception $e) {
            \Log::error('Error in getSuratKeluar: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data surat keluar',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function getLastNumber(Request $request)
    {
        try {
            // Cek apakah request untuk nomor surat ASP
            if ($request->is_asp) {
                \Log::info('Generating ASP number for date: ' . ($request->tanggal_surat ?? date('Y-m-d')));
                
                // Gunakan tanggal dari request atau default ke tahun sekarang
                $tanggalSurat = $request->tanggal_surat ? date('Y', strtotime($request->tanggal_surat)) : date('Y');
                
                // Ambil nomor urut terakhir untuk surat ASP
                $lastSurat = SuratKeluar::where('jenis_surat', 'eksternal')
                    ->where('perusahaan', 'ASP')
                    ->whereYear('tanggal_surat', $tanggalSurat)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastSurat) {
                    // Extract nomor urut dari nomor surat terakhir
                    $parts = explode('/', $lastSurat->nomor_surat);
                    $lastNumber = (int)$parts[0];
                    \Log::info('Last ASP number found: ' . $lastNumber);
                } else {
                    $lastNumber = 0;
                    \Log::info('No previous ASP number found, starting from 0');
                }

                return response()->json([
                    'success' => true,
                    'last_number' => $lastNumber
                ]);
            }

            // Cek apakah request untuk nomor surat eksternal AZRA
            if ($request->is_eksternal_azra) {
                // Ambil nomor urut terakhir untuk surat eksternal AZRA
                $lastSurat = SuratKeluar::where('jenis_surat', 'eksternal')
                    ->where('perusahaan', 'RSAZRA')
                    ->whereYear('tanggal_surat', date('Y'))
                    ->orderBy('id', 'desc')
                ->first();

            if ($lastSurat) {
                    // Extract nomor urut dari nomor surat terakhir
                $parts = explode('/', $lastSurat->nomor_surat);
                    $lastNumber = (int)$parts[0];
                } else {
                    $lastNumber = 0;
                }

                return response()->json([
                    'success' => true,
                    'last_number' => $lastNumber
                ]);
            }

            // Logic untuk nomor surat internal (tidak diubah)
            $namaJabatan = $request->nama_jabatan;
            $isAsDirut = $request->is_as_dirut;

            // Jika as dirut, gunakan DIRUT sebagai nama jabatan
            if ($isAsDirut) {
                $namaJabatan = 'DIRUT';
            }

            // Ambil nomor urut terakhir untuk jabatan tersebut
            $lastSurat = SuratKeluar::where('jenis_surat', 'internal')
                ->whereYear('tanggal_surat', date('Y'))
                ->where(function ($query) use ($namaJabatan) {
                    $query->where('nomor_surat', 'like', "%/{$namaJabatan}/%")
                        ->orWhere('nomor_surat', 'like', "%/{$namaJabatan}/%");
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($lastSurat) {
                // Extract nomor urut dari nomor surat terakhir
                $parts = explode('/', $lastSurat->nomor_surat);
                $lastNumber = (int)$parts[0];
            } else {
                $lastNumber = 0;
            }

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
     * Preview file surat keluar
     */
    public function preview(SuratKeluar $suratKeluar)
    {
        try {
            \Log::info('Preview file untuk surat ID: ' . $suratKeluar->id . ', file_path: ' . $suratKeluar->file_path);
            
            if (empty($suratKeluar->file_path)) {
                \Log::warning('File path kosong untuk surat ID: ' . $suratKeluar->id);
                return response()->json([
                    'success' => false,
                    'message' => 'File belum diunggah'
                ], 404);
            }

            // Normalize path - handle both uploads/surat_keluar and /storage/surat_keluar paths
            $filePath = null;
            
            // Check if file exists in public path
            if (file_exists(public_path($suratKeluar->file_path))) {
                $filePath = public_path($suratKeluar->file_path);
                \Log::info('File ditemukan di public_path: ' . $filePath);
            }
            // Try storage path if the path starts with /storage/
            else if (Str::startsWith($suratKeluar->file_path, '/storage/')) {
                $normalizedPath = str_replace('/storage/', 'public/', $suratKeluar->file_path);
                if (Storage::exists($normalizedPath)) {
                    $filePath = Storage::path($normalizedPath);
                    \Log::info('File ditemukan di storage path: ' . $filePath);
                }
            }
            
            // If still not found, try with filename only
            if (!$filePath || !file_exists($filePath)) {
                $fileName = basename($suratKeluar->file_path);
                
                // Try in uploads directory
                $uploadsPath = public_path('uploads/surat_keluar/' . $fileName);
                if (file_exists($uploadsPath)) {
                    $filePath = $uploadsPath;
                    \Log::info('File ditemukan di uploads/surat_keluar dengan nama file: ' . $filePath);
                }
                // Try in storage directory
                else {
                    $storagePath = 'public/surat_keluar/' . $fileName;
                    if (Storage::exists($storagePath)) {
                        $filePath = Storage::path($storagePath);
                        \Log::info('File ditemukan di storage/app/public/surat_keluar dengan nama file: ' . $filePath);
                    }
                }
            }
            
            // If file still not found, return 404
            if (!$filePath || !file_exists($filePath)) {
                \Log::error('File tidak ditemukan di semua jalur yang dicoba untuk ID: ' . $suratKeluar->id);
                    return response()->json([
                        'success' => false,
                        'message' => 'File tidak ditemukan di server'
                    ], 404);
            }
            
            // Tentukan content type dari ekstensi file
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $contentType = $this->getContentTypeFromExtension($fileExtension);
            
            \Log::info('Menampilkan file dari path: ' . $filePath . ' dengan content type: ' . $contentType);
            
            // Kembalikan file sebagai response
            return response()->file($filePath, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET',
                'X-Content-Type-Options' => 'nosniff'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error saat preview file: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuka file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan ID Direktur untuk keperluan form
     */
    public function getDirekturId()
    {
        try {
            $direktur = User::where('role', 2)->first();
            
            if (!$direktur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Direktur tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'direktur_id' => $direktur->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error dalam getDirekturId: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil ID Direktur'
            ], 500);
        }
    }

    private function getContentTypeFromExtension($fileExtension)
    {
        $contentType = '';

        // Tentukan content type berdasarkan ekstensi file
        switch(strtolower($fileExtension)) {
            case 'pdf':
                $contentType = 'application/pdf';
                break;
            case 'doc':
                $contentType = 'application/msword';
                break;
            case 'docx':
                $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            default:
                $contentType = 'application/octet-stream';
        }

        return $contentType;
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
}
