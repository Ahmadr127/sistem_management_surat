<?php

namespace App\Http\Controllers;

use App\Models\SuratUnitManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SuratUnitManagerApprovalController extends Controller
{
    /**
     * Public method untuk redirect user ke halaman approval yang sesuai dengan role
     * Ini adalah entry point universal dari sidebar
     */
    public function redirectToApproval()
    {
        $user = auth()->user();
        
        Log::info('RedirectToApproval called for user: ' . $user->id . ' Role: ' . $user->role);
        Log::info('Has permission approve_surat_unit: ' . ($user->hasPermission('approve_surat_unit') ? 'Yes' : 'No'));

        // Cek permission dulu
        if (!$user->hasPermission('approve_surat_unit')) {
            Log::warning('User does not have permission to approve surat unit');
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman persetujuan surat');
        }
        
        // Redirect berdasarkan role
        $role = $user->role;
        
        if ($role == 4) { // Manager
            Log::info('Redirecting to manager index');
            return redirect()->route('surat-unit-manager.manager.index');
        } elseif ($role == 7) { // Manager Keuangan
            Log::info('Redirecting to manager keuangan index');
            return redirect()->route('surat-unit-manager.manager-keuangan.index');
        } elseif (in_array($role, [1, 5])) { // Sekretaris atau Sekretaris ASP
            Log::info('Redirecting to sekretaris index');
            return redirect()->route('surat-unit-manager.sekretaris.index');
        } elseif (in_array($role, [2, 8])) { // Direktur atau Direktur ASP
            Log::info('Redirecting to dirut index');
            return redirect()->route('surat-unit-manager.dirut.index');
        }
        
        // Default untuk Super Admin atau role lainnya: redirect ke manager page (view all)
        Log::info('Redirecting to default (manager index)');
        return redirect()->route('surat-unit-manager.manager.index');
    }

    /**
     * Menampilkan daftar surat yang perlu disetujui manager
     */
    public function managerIndex(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Cek permission saja, tidak perlu cek role
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }

            $query = SuratUnitManager::with([
                'unit',
                'manager',
                'sekretaris',
                'dirut',
                'perusahaanData',
                'files'
            ]);
            
            // Jika bukan Super Admin (role 3), filter hanya surat yang ditujukan ke user ini
            if ($user->role != 3) {
                $query->byManager($user->id);
            }

            // Filter berdasarkan status
            if ($request->has('status') && $request->status !== '') {
                $query->byStatusManager($request->status);
            }

            // Search filter
            if ($request->has('search')) {
                $query->search($request->search);
            }

            $suratUnitManager = $query->orderBy('created_at', 'desc')->paginate(10);

            return view('pages.surat_unit_manager.manager.index', compact('suratUnitManager'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@managerIndex: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    /**
     * Menampilkan detail surat untuk persetujuan manager
     */
    public function managerShow(SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke surat ini');
            }
            
            // Super Admin bisa lihat semua, selain itu harus manager yang dituju
            if ($user->role != 3 && $suratUnitManager->manager_id !== $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke surat ini');
            }

            // Load files relationship
            $suratUnitManager->load('files');

            return view('pages.surat_unit_manager.manager.show', compact('suratUnitManager'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@managerShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat detail surat');
        }
    }

    /**
     * Proses persetujuan/rejection oleh manager
     */
    public function managerApproval(Request $request, SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui surat ini'
                ], 403);
            }
            
            // Super Admin bisa approve semua, selain itu harus manager yang dituju
            if ($user->role != 3 && $suratUnitManager->manager_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui surat ini'
                ], 403);
            }

            // Check if surat is still pending
            if ($suratUnitManager->status_manager !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat sudah diproses'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'action' => 'required|in:approve,reject',
                'keterangan_manager' => 'nullable|string'
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
                $suratUnitManager->status_manager = $request->action === 'approve' ? 'approved' : 'rejected';
                $suratUnitManager->keterangan_manager = $request->keterangan_manager;
                $suratUnitManager->waktu_review_manager = now();
                $suratUnitManager->save();

                DB::commit();

                $actionText = $request->action === 'approve' ? 'disetujui' : 'ditolak';
                
                return response()->json([
                    'success' => true,
                    'message' => "Surat berhasil {$actionText}",
                    'redirect_url' => route('surat-unit-manager.manager.index')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in managerApproval: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses persetujuan'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@managerApproval: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses persetujuan'
            ], 500);
        }
    }

    /**
     * Menampilkan daftar surat yang perlu disetujui manager keuangan
     */
    public function managerKeuanganIndex(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }

            $query = SuratUnitManager::with([
                'unit',
                'manager',
                'sekretaris',
                'dirut',
                'perusahaanData',
                'files'
            ]);
            
            // Jika bukan Super Admin (role 3), filter hanya surat yang ditujukan ke user ini
            if ($user->role != 3) {
                $query->byManager($user->id);
            }

            // Filter berdasarkan status
            if ($request->has('status') && $request->status !== '') {
                $query->byStatusManager($request->status);
            }

            // Search filter
            if ($request->has('search')) {
                $query->search($request->search);
            }

            $suratUnitManager = $query->orderBy('created_at', 'desc')->get();

            // Gunakan view yang sama dengan manager biasa
            return view('pages.surat_unit_manager.manager.index', compact('suratUnitManager'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@managerKeuanganIndex: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    /**
     * Menampilkan detail surat untuk persetujuan manager keuangan
     */
    public function managerKeuanganShow(SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke surat ini');
            }
            
            // Super Admin bisa lihat semua, selain itu harus manager yang dituju
            if ($user->role != 3 && $suratUnitManager->manager_id !== $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke surat ini');
            }

            // Load files relationship
            $suratUnitManager->load('files');

            // Gunakan view yang sama dengan manager biasa
            return view('pages.surat_unit_manager.manager.show', compact('suratUnitManager'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@managerKeuanganShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat detail surat');
        }
    }

    /**
     * Proses persetujuan/rejection oleh manager keuangan
     */
    public function managerKeuanganApproval(Request $request, SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui surat ini'
                ], 403);
            }
            
            // Super Admin bisa approve semua, selain itu harus manager yang dituju
            if ($user->role != 3 && $suratUnitManager->manager_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui surat ini'
                ], 403);
            }

            // Check if surat is still pending
            if ($suratUnitManager->status_manager !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat sudah diproses'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'action' => 'required|in:approve,reject',
                'keterangan_manager' => 'nullable|string'
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
                $suratUnitManager->status_manager = $request->action === 'approve' ? 'approved' : 'rejected';
                $suratUnitManager->keterangan_manager = $request->keterangan_manager;
                $suratUnitManager->waktu_review_manager = now();
                $suratUnitManager->save();

                DB::commit();

                $actionText = $request->action === 'approve' ? 'disetujui' : 'ditolak';
                
                return response()->json([
                    'success' => true,
                    'message' => "Surat berhasil {$actionText}",
                    'redirect_url' => route('surat-unit-manager.manager-keuangan.index')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in managerKeuanganApproval: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses persetujuan'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@managerKeuanganApproval: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses persetujuan'
            ], 500);
        }
    }

    /**
     * Menampilkan daftar surat yang perlu disetujui sekretaris
     */
    public function sekretarisIndex(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }

            $query = SuratUnitManager::with([
                'unit',
                'manager',
                'sekretaris',
                'dirut',
                'perusahaanData',
                'files'
            ])->where('status_manager', 'approved');

            // Filter berdasarkan status
            if ($request->has('status')) {
                $query->byStatusSekretaris($request->status);
            } else {
                // Default: tampilkan yang pending
                $query->byStatusSekretaris('pending');
            }

            // Search filter
            if ($request->has('search')) {
                $query->search($request->search);
            }

            $suratUnitManager = $query->orderBy('waktu_review_manager', 'desc')->get();

            return view('pages.surat_unit_manager.sekretaris.index', compact('suratUnitManager'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@sekretarisIndex: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    /**
     * Menampilkan detail surat untuk persetujuan sekretaris
     */
    public function sekretarisShow(SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke surat ini');
            }

            // Check if manager has approved
            if ($suratUnitManager->status_manager !== 'approved') {
                return redirect()->back()->with('error', 'Surat belum disetujui manager');
            }

            // Load files relationship
            $suratUnitManager->load('files');

            return view('pages.surat_unit_manager.sekretaris.show', compact('suratUnitManager'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@sekretarisShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat detail surat');
        }
    }

    /**
     * Proses persetujuan/rejection oleh sekretaris
     */
    public function sekretarisApproval(Request $request, SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui surat ini'
                ], 403);
            }

            // Check if manager has approved
            if ($suratUnitManager->status_manager !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat belum disetujui manager'
                ], 422);
            }

            // Check if surat is still pending
            if ($suratUnitManager->status_sekretaris !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat sudah diproses'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'action' => 'required|in:approve,reject',
                'keterangan_sekretaris' => 'nullable|string'
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
                $suratUnitManager->status_sekretaris = $request->action === 'approve' ? 'approved' : 'rejected';
                $suratUnitManager->keterangan_sekretaris = $request->keterangan_sekretaris;
                $suratUnitManager->waktu_review_sekretaris = now();
                $suratUnitManager->sekretaris_id = $user->id;
                $suratUnitManager->save();

                DB::commit();

                $actionText = $request->action === 'approve' ? 'disetujui' : 'ditolak';
                
                return response()->json([
                    'success' => true,
                    'message' => "Surat berhasil {$actionText}",
                    'redirect_url' => route('surat-unit-manager.sekretaris.index')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@sekretarisApproval: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan daftar surat yang perlu disetujui direktur
     */
    public function dirutIndex(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }

            $query = SuratUnitManager::with([
                'unit.jabatan',
                'manager.jabatan',
                'sekretaris.jabatan',
                'dirut.jabatan',
                'perusahaanData',
                'files'
            ])->where('status_sekretaris', 'approved');

            // Filter berdasarkan status
            if ($request->has('status')) {
                $query->byStatusDirut($request->status);
            } else {
                // Default: tampilkan yang pending
                $query->byStatusDirut('pending');
            }

            // Search filter
            if ($request->has('search')) {
                $query->search($request->search);
            }

            $suratUnitManager = $query->orderBy('waktu_review_sekretaris', 'desc')->get();

            return view('pages.surat_unit_manager.dirut.index', compact('suratUnitManager'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@dirutIndex: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    /**
     * Menampilkan detail surat untuk persetujuan direktur
     */
    public function dirutShow(SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke surat ini');
            }

            // Check if sekretaris has approved
            if ($suratUnitManager->status_sekretaris !== 'approved') {
                return redirect()->back()->with('error', 'Surat belum disetujui sekretaris');
            }

            // Load files relationship
            $suratUnitManager->load('files');

            return view('pages.surat_unit_manager.dirut.show', compact('suratUnitManager'));
        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@dirutShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat detail surat');
        }
    }

    /**
     * Proses persetujuan/rejection oleh direktur
     */
    public function dirutApproval(Request $request, SuratUnitManager $suratUnitManager)
    {
        try {
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui surat ini'
                ], 403);
            }

            // Check if sekretaris has approved
            if ($suratUnitManager->status_sekretaris !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat belum disetujui sekretaris'
                ], 422);
            }

            // Check if surat is still pending
            if ($suratUnitManager->status_dirut !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat sudah diproses'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'action' => 'required|in:approve,reject',
                'keterangan_dirut' => 'nullable|string'
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
                $suratUnitManager->status_dirut = $request->action === 'approve' ? 'approved' : 'rejected';
                $suratUnitManager->keterangan_dirut = $request->keterangan_dirut;
                $suratUnitManager->waktu_review_dirut = now();
                $suratUnitManager->dirut_id = $user->id;
                $suratUnitManager->save();

                DB::commit();

                $actionText = $request->action === 'approve' ? 'disetujui' : 'ditolak';
                
                return response()->json([
                    'success' => true,
                    'message' => "Surat berhasil {$actionText}",
                    'redirect_url' => route('surat-unit-manager.dirut.index')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@dirutApproval: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}