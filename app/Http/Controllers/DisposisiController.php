<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Disposisi;
use Illuminate\Support\Facades\Auth;
use App\Mail\DisposisiMail;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DisposisiController extends Controller
{
    public function create()
    {
        $users = User::with('jabatan')
                     ->where('status_akun', 'aktif')
            ->where('id', '!=', auth()->id())
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'jabatan' => $user->jabatanName,
                    'email' => $user->email
                ];
            })
            ->values()
            ->toArray();

        return view('pages.disposisi', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            // Log request untuk debugging
            \Log::info('DisposisiController@store - Request data:', [
                'all_request' => $request->all(),
                'has_keterangan' => $request->has('keterangan_pengirim'),
                'keterangan_value' => $request->input('keterangan_pengirim')
            ]);
            
            $validated = $request->validate([
                'surat_keluar_id' => 'required|exists:tbl_surat_keluar,id',
                'tujuan' => 'required|array',
                'tujuan.*' => 'exists:users,id',
                'keterangan_pengirim' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Buat disposisi
            $disposisi = Disposisi::create([
                'surat_keluar_id' => $request->surat_keluar_id,
                'keterangan_pengirim' => $request->input('keterangan_pengirim'),
                'status_sekretaris' => auth()->user()->role == 1 || auth()->user()->role == 5 ? 'approved' : 'pending',
                'status_dirut' => 'pending',
                'created_by' => auth()->id()
            ]);

            // Log the role and status
            \Log::info('DisposisiController@store - Set status based on role:', [
                'user_role' => auth()->user()->role,
                'status_sekretaris' => auth()->user()->role == 1 || auth()->user()->role == 5 ? 'approved' : 'pending',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'disposisi_data' => $disposisi->toArray()
            ]);

            // Attach tujuan disposisi
            $disposisi->tujuan()->attach($request->tujuan);

            DB::commit();
            
            \Log::info('DisposisiController@store - Disposisi berhasil dibuat dengan ID: ' . $disposisi->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Disposisi berhasil dibuat',
                'data' => $disposisi
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('DisposisiController@store - Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitForm(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'kd_surat_keluar' => 'required|exists:tbl_surat_keluar,id',
                'jenis_disposisi' => 'required',
                'status_penyelesaian' => 'required',
                'tingkat_kepentingan' => 'required',
                'diteruskan_kepada' => 'required|exists:users,id'
            ]);

            // Simpan disposisi
            $disposisi = new Disposisi();
            $disposisi->kd_surat_keluar = $request->kd_surat_keluar;
            $disposisi->jenis_disposisi = $request->jenis_disposisi;
            $disposisi->status_penyelesaian = $request->status_penyelesaian;
            $disposisi->tingkat_kepentingan = $request->tingkat_kepentingan;
            $disposisi->instruksi = $request->instruksi;
            $disposisi->diteruskan_kepada = $request->diteruskan_kepada;
            $disposisi->catatan = $request->catatan;

            if ($disposisi->save()) {
                // Ambil data user yang menerima disposisi
                $user = User::find($request->diteruskan_kepada);
                
                // Kirim email notifikasi
                try {
                    $mailer = new DisposisiMail($disposisi, $user->email, $user->name);
                    $emailSent = $mailer->send();
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Disposisi berhasil disimpan' . 
                                   ($emailSent ? ' dan email notifikasi telah dikirim' : ''),
                        'data' => $disposisi
                    ]);
                } catch (\Exception $e) {
                    // Jika pengiriman email gagal, tetap return success tapi dengan pesan berbeda
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Disposisi berhasil disimpan tetapi gagal mengirim email notifikasi',
                        'data' => $disposisi
                    ]);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan disposisi'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $user = Auth::user();
            $query = Disposisi::with(['suratKeluar', 'tujuan', 'creator']);

            // Filter berdasarkan role
            if ($user->role == 1) { // staff
                $query->whereHas('tujuan', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->role == 0) { // admin
                $query->where('created_by', $user->id)
                    ->orWhereHas('tujuan', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            }
            // Super admin bisa melihat semua disposisi

            $disposisi = $query->latest()->get();

            return view('pages.disposisi.index', compact('disposisi'));
        } catch (\Exception $e) {
            \Log::error('DisposisiController@index - Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            // Get disposisi by ID instead of surat_id
            $disposisi = Disposisi::with(['tujuan', 'suratKeluar', 'creator'])
                ->findOrFail($id);
            
            // Log the found data for debugging
            \Log::info('DisposisiController@show - Found disposisi:', [
                'id' => $disposisi->id,
                'surat_keluar_id' => $disposisi->surat_keluar_id,
                'status_sekretaris' => $disposisi->status_sekretaris,
                'status_dirut' => $disposisi->status_dirut,
                'tujuan_count' => $disposisi->tujuan->count()
            ]);
            
            return response()->json([
                'success' => true,
                'disposisi' => $disposisi
            ]);
        } catch (\Exception $e) {
            \Log::error('DisposisiController@show - Error:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Disposisi tidak ditemukan atau terjadi kesalahan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update status surat based on role
     */
    public function updateStatus(Request $request)
    {
        try {
            \Log::info('Memulai update status disposisi', [
                'surat_id' => $request->input('surat_id'),
                'status' => $request->input('status'),
                'catatan' => $request->input('catatan'),
                'user' => auth()->user()->name,
                'role' => auth()->user()->role
            ]);
            
            // Memvalidasi request
            $validator = Validator::make($request->all(), [
                'surat_id' => 'required|exists:surat_keluar,id',
                'status' => 'required|in:diterima,ditolak',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                \Log::warning('Validasi gagal pada updateStatus', [
                    'errors' => $validator->errors()->toArray()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $user = auth()->user();
            $suratKeluar = SuratKeluar::findOrFail($request->surat_id);
            
            // Transaction untuk memastikan konsistensi data
            DB::beginTransaction();
            
            try {
                if ($user->role == 1) { // Sekretaris
                    \Log::info('Update status sebagai Sekretaris');
                    $suratKeluar->status_sekretaris = $request->status;
                    $suratKeluar->catatan_sekretaris = $request->catatan;
                    $suratKeluar->save();
                    
                    if ($request->status == 'ditolak') {
                        \Log::info('Surat ditolak oleh Sekretaris, menghapus disposisi jika ada');
                        // Jika ditolak oleh Sekretaris, hapus disposisi yang ada (jika ada)
                        if ($suratKeluar->disposisi) {
                            $suratKeluar->disposisi->delete();
                        }
                    }
                } 
                else if ($user->role == 5) { // Sekretaris ASP
                    \Log::info('Update status sebagai Sekretaris ASP');
                    $suratKeluar->status_sekretaris = $request->status;
                    $suratKeluar->catatan_sekretaris = $request->catatan;
                    $suratKeluar->save();
                    
                    if ($request->status == 'ditolak') {
                        \Log::info('Surat ditolak oleh Sekretaris ASP, menghapus disposisi jika ada');
                        // Jika ditolak oleh Sekretaris ASP, hapus disposisi yang ada (jika ada)
                        if ($suratKeluar->disposisi) {
                            $suratKeluar->disposisi->delete();
                        }
                    }
                }
                else if ($user->role == 2) { // Direktur
                    \Log::info('Update status sebagai Direktur');
                    $suratKeluar->status_direktur = $request->status;
                    $suratKeluar->catatan_direktur = $request->catatan;
                    $suratKeluar->save();
                }
                
                DB::commit();
                
                \Log::info('Status berhasil diupdate', [
                    'surat_id' => $suratKeluar->id,
                    'nomor_surat' => $suratKeluar->nomor_surat,
                    'status_sekretaris' => $suratKeluar->status_sekretaris,
                    'status_direktur' => $suratKeluar->status_direktur
            ]);

            return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diupdate',
                    'data' => [
                        'id' => $suratKeluar->id,
                        'status_sekretaris' => $suratKeluar->status_sekretaris,
                        'status_direktur' => $suratKeluar->status_direktur
                    ]
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error dalam transaction updateStatus: ' . $e->getMessage(), [
                    'surat_id' => $request->surat_id,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Error dalam updateStatus: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data disposisi berdasarkan ID surat keluar
     */
    public function getDisposisiBySurat($suratId)
    {
        try {
            \Log::info('Mengambil data disposisi untuk surat ID: ' . $suratId);
            
            // Find the SuratKeluar first
            $suratKeluar = \App\Models\SuratKeluar::findOrFail($suratId);
            
            // Get the disposisi with all related data
            $disposisi = Disposisi::where('surat_keluar_id', $suratId)
                ->with(['tujuan' => function($query) {
                    $query->select('users.id', 'name', 'jabatan_id', 'email')
                          ->with('jabatan:id,nama_jabatan');
                }])
                ->first();
            
            if (!$disposisi) {
                \Log::warning('Disposisi tidak ditemukan untuk surat ID: ' . $suratId . ', membuat disposisi default');
                
                // Create a temporary disposisi object with default values
                $disposisi = new Disposisi();
                $disposisi->surat_keluar_id = $suratId;
                $disposisi->status_sekretaris = 'pending';
                $disposisi->status_dirut = 'pending';
                $disposisi->keterangan_pengirim = '';
                $disposisi->created_by = auth()->id();
                
                // Return the default disposisi with a flag indicating it's new
                return response()->json([
                    'success' => true,
                    'disposisi' => $disposisi,
                    'is_new' => true,
                    'message' => 'Belum ada disposisi untuk surat ini'
                ]);
            }
            
            // Log the found data for debugging
            \Log::info('Data disposisi ditemukan:', [
                'id' => $disposisi->id,
                'surat_id' => $disposisi->surat_keluar_id,
                'keterangan_pengirim' => $disposisi->keterangan_pengirim,
                'tujuan_count' => $disposisi->tujuan->count()
            ]);
            
            return response()->json([
                'success' => true,
                'disposisi' => $disposisi,
                'is_new' => false
            ]);
        } catch (\Exception $e) {
            \Log::error('Error dalam getDisposisiBySurat: ' . $e->getMessage(), [
                'suratId' => $suratId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tujuan disposisi based on disposisi ID
     */
    public function getTujuanDisposisi($id)
    {
        try {
            \Log::info('Mengambil data tujuan disposisi untuk ID: ' . $id);
            
            $disposisi = Disposisi::findOrFail($id);
            
            // Get users who are the target of the disposisi
            $tujuan = $disposisi->tujuan()
                ->select('users.id', 'users.name', 'users.jabatan_id', 'users.email')
                ->with('jabatan:id,nama_jabatan')
                ->paginate(10);
            
            \Log::info('Data tujuan disposisi ditemukan: ' . $tujuan->count());
            
            return response()->json([
                'success' => true,
                'tujuan' => $tujuan
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error dalam getTujuanDisposisi: ' . $e->getMessage(), [
                'disposisiId' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update data disposisi
     */
    public function update(Request $request, $id)
    {
        try {
            \Log::info('Update Disposisi Request', [
                'id' => $id,
                'data' => $request->all(),
                'has_keterangan_pengirim' => $request->has('keterangan_pengirim'),
                'keterangan_pengirim_value' => $request->input('keterangan_pengirim')
            ]);
            
            $disposisi = Disposisi::findOrFail($id);
            
            DB::beginTransaction();
            
            try {
                // Update keterangan pengirim jika ada
                if ($request->has('keterangan_pengirim')) {
                    $disposisi->keterangan_pengirim = $request->input('keterangan_pengirim');
                    \Log::info('Keterangan pengirim diperbarui', [
                        'old' => $disposisi->getOriginal('keterangan_pengirim'),
                        'new' => $request->input('keterangan_pengirim')
                    ]);
                }
                
                // Update berdasarkan role
                if (auth()->user()->role == 1) { // Sekretaris
                    $disposisi->status_sekretaris = $request->status_sekretaris;
                    $disposisi->keterangan_sekretaris = $request->keterangan_sekretaris;
                    
                    if ($request->filled('waktu_review_sekretaris')) {
                        $disposisi->waktu_review_sekretaris = $request->waktu_review_sekretaris;
                    } else {
                        $disposisi->waktu_review_sekretaris = now();
                    }
                } 
                else if (auth()->user()->role == 5) { // Sekretaris ASP
                    $disposisi->status_sekretaris = $request->status_sekretaris;
                    $disposisi->keterangan_sekretaris = $request->keterangan_sekretaris;
                    
                    if ($request->filled('waktu_review_sekretaris')) {
                        $disposisi->waktu_review_sekretaris = $request->waktu_review_sekretaris;
                    } else {
                        $disposisi->waktu_review_sekretaris = now();
                    }
                }
                else if (auth()->user()->role == 2) { // Direktur
                    $disposisi->status_dirut = $request->status_dirut;
                    $disposisi->keterangan_dirut = $request->keterangan_dirut;
                    
                    if ($request->filled('waktu_review_dirut')) {
                        $disposisi->waktu_review_dirut = $request->waktu_review_dirut;
                    } else {
                        $disposisi->waktu_review_dirut = now();
                    }
                    
                    // Update tujuan disposisi untuk Direktur
                    if ($request->has('tujuan_disposisi')) {
                        \Log::info('Updating tujuan disposisi', [
                            'disposisi_id' => $id,
                            'tujuan_disposisi' => $request->tujuan_disposisi
                        ]);
                        
                        // Hapus tujuan yang ada
                        $disposisi->tujuan()->detach();
                        
                        // Tambahkan tujuan baru
                        $disposisi->tujuan()->attach($request->tujuan_disposisi);
                        
                        \Log::info('Tujuan disposisi updated successfully');
                    }
                }
                
                $disposisi->save();
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Disposisi berhasil diperbarui',
                    'disposisi' => $disposisi
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error updating disposisi: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tujuan disposisi along with available users for selection
     */
    public function getTujuanDisposisiWithUsers($id)
    {
        try {
            \Log::info('getTujuanDisposisiWithUsers - Getting tujuan and available users for disposisi ID: ' . $id);
            
            $disposisi = Disposisi::findOrFail($id);
            
            // Get current selected users for this disposisi
            $selectedUsers = $disposisi->tujuan()
                ->select('users.id', 'users.name', 'users.jabatan_id', 'users.email', 'users.role')
                ->with('jabatan:id,nama_jabatan')
                ->get();
                
            // Get all available users including both staff (role 0) and admin (role 3)
            $availableUsers = User::where('status_akun', 'aktif')
                ->whereIn('role', [1, 4, 5]) // Hanya Sekretaris (1), Manager (4), dan Sekretaris ASP (5)
                ->where('id', '!=', auth()->id()) // Exclude current user
                ->select('id', 'name', 'jabatan_id', 'email', 'role')
                ->with('jabatan:id,nama_jabatan')
                ->orderBy('name')
                ->get();
                
            // Set up pagination info
            $page = request()->input('page', 1);
            $pageSize = request()->input('page_size', 10);
            $totalUsers = $availableUsers->count();
            $totalPages = ceil($totalUsers / $pageSize);
            
            // Paginate manually
            $paginatedUsers = $availableUsers->forPage($page, $pageSize);
            
            \Log::info('getTujuanDisposisiWithUsers - Found data', [
                'disposisi_id' => $id,
                'selected_users_count' => $selectedUsers->count(),
                'available_users_count' => $availableUsers->count(),
                'current_page' => $page,
                'total_pages' => $totalPages
            ]);
            
            return response()->json([
                'success' => true,
                'tujuan' => $selectedUsers,
                'available_users' => $paginatedUsers->values(),
                'pagination' => [
                    'current_page' => (int)$page,
                    'page_size' => (int)$pageSize,
                    'total_pages' => $totalPages,
                    'total_items' => $totalUsers
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error dalam getTujuanDisposisiWithUsers: ' . $e->getMessage(), [
                'disposisiId' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
