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
     * Menampilkan daftar surat yang perlu disetujui manager
     */
    public function managerIndex(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Hanya manager yang bisa akses
            if ($user->role !== 4) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }

            $query = SuratUnitManager::with([
                'unit.jabatan',
                'manager.jabatan',
                'sekretaris.jabatan',
                'dirut.jabatan',
                'perusahaanData',
                'files'
            ])->byManager($user->id);

            // Filter berdasarkan status
            if ($request->has('status')) {
                $query->byStatusManager($request->status);
            } else {
                // Default: tampilkan yang pending
                $query->byStatusManager('pending');
            }

            // Search filter
            if ($request->has('search')) {
                $query->search($request->search);
            }

            $suratUnitManager = $query->orderBy('created_at', 'desc')->get();

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
            
            // Check access permission
            if ($user->role !== 4 || $suratUnitManager->manager_id !== $user->id) {
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
            
            // Check access permission
            if ($user->role !== 4 || $suratUnitManager->manager_id !== $user->id) {
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
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error in SuratUnitManagerApprovalController@managerApproval: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
            
            // Hanya sekretaris yang bisa akses
            if ($user->role !== 1) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }

            $query = SuratUnitManager::with([
                'unit.jabatan',
                'manager.jabatan',
                'sekretaris.jabatan',
                'dirut.jabatan',
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
            
            // Check access permission
            if ($user->role !== 1) {
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
            
            // Check access permission
            if ($user->role !== 1) {
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
            
            // Hanya direktur yang bisa akses
            if ($user->role !== 2) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini');
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
            
            // Check access permission
            if ($user->role !== 2) {
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
            
            // Check access permission
            if ($user->role !== 2) {
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