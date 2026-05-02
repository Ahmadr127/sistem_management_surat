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
    /**
     * Public method untuk redirect user ke halaman approval yang sesuai dengan role
     * Ini adalah entry point universal dari sidebar
     */
    public function redirectToApproval()
    {
        // Langsung arahkan ke route approval generic
        return redirect()->route('surat-unit-manager.approval.index');
    }

    /**
     * Generic Index Method for All Approvals
     */
    public function approvalIndex(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Cek permission saja, JANGAN cek role ID
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman persetujuan surat');
            }

            $query = SuratUnitManager::with([
                'unit', 'manager', 'perusahaanData', 'files'
            ]);

            // Context selalu manager (Kepala Unit)
            // Halaman ini didedikasikan untuk approval tingkat unit
            $context = 'manager';

            // Filter surat berdasarkan Unit User yang login
            // User hanya bisa melihat surat dari unitnya sendiri
            if ($user->role != 3) { // Not Super Admin
                $query->byManager($user->id);
            }
            
            // Apply status filter hanya jika dipilih secara eksplisit
            if ($request->has('status') && $request->status !== '') {
                $query->byStatusManager($request->status);
            }

            // Search filter
            if ($request->has('search') && $request->search !== '') {
                $query->search($request->search);
            }

            $suratUnitManager = $query->orderBy('created_at', 'desc')->paginate(10);

            // Use a single generic view
            return view('pages.surat_unit_manager.approval_index', compact('suratUnitManager', 'context'));
        } catch (\Exception $e) {
            Log::error('Error in approvalIndex: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    /**
     * Generic Show Method
     */
    public function approvalShow($id)
    {
        try {
            $surat = SuratUnitManager::with([
                'unit', 'manager', 'perusahaanData', 'files', 'histories.user'
            ])->findOrFail($id);

            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
            }

            $context = 'manager';

            return view('pages.surat_unit_manager.approval_show', compact('surat', 'context'));
        } catch (\Exception $e) {
            Log::error('Error in approvalShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Surat tidak ditemukan');
        }
    }

    /**
     * Generic Process Approval Method
     */
    public function processApproval(Request $request, $id)
    {
        try {
            $surat = SuratUnitManager::findOrFail($id);
            $user = auth()->user();
            
            // Cek permission
            if (!$user->hasPermission('approve_surat_unit')) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
            }

            $context = 'manager';
            
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:approve,reject',
                'catatan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $action = $request->action; // 'approve' or 'reject'
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            $catatan = $request->catatan;

            DB::beginTransaction();

            // Logic Manager Approval
            $surat->status_manager = $status;
            $surat->keterangan_manager = $catatan;
            $surat->waktu_review_manager = now();
            $surat->manager_id = $user->id; // Record who actually approved it
            
            // Alur selesai di Manager — tidak ada tahap selanjutnya
            // (Sekretaris dan Dirut tidak terlibat dalam alur ini)

            $surat->save();

            // Log history
            $surat->histories()->create([
                'user_id' => $user->id,
                'action' => $action,
                'keterangan' => "Persetujuan oleh Manager Unit: " . ($catatan ?? '-'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return redirect()->back()
                             ->with('success', 'Anda Bisa Meneruskan surat untuk diajukan ke Direktur');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in processApproval: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    /**
     * Helper to determine approval context
     * (Deprecated/Unused for now as we force 'manager')
     */
    private function getApprovalContext($user)
    {
        return 'manager';
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
                'approval_action' => 'required|in:approve,reject',
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
                $suratUnitManager->status_manager = $request->approval_action === 'approve' ? 'approved' : 'rejected';
                $suratUnitManager->keterangan_manager = $request->keterangan_manager;
                $suratUnitManager->waktu_review_manager = now();
                $suratUnitManager->save();

                DB::commit();

                $actionText = $request->approval_action === 'approve' ? 'disetujui' : 'ditolak';
                
                return response()->json([
                    'success' => true,
                    'message' => "SUCCESS Anda Bisa Meneruskan surat untuk diajukan ke Direktur"
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
}
