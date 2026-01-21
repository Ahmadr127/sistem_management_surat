<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\SuratUnitManager;
use App\Models\User;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with stats based on user permissions
     */
    public function index()
    {
        $user = Auth::user();
        $stats = $this->getStats($user);
        $recentActivities = $this->getRecentActivities($user);
        $quickActions = $this->getQuickActions($user);

        return view('pages.dashboard', compact('stats', 'recentActivities', 'quickActions'));
    }

    /**
     * Get stats based on user permissions
     */
    private function getStats($user)
    {
        $stats = [];

        // 1. Stats Surat Masuk (Card Biru)
        // Logic: Sama persis dengan SuratMasukController@index
        if ($user->hasPermission('manage_surat_masuk')) {
            $suratMasukQuery = SuratKeluar::forSuratMasuk($user);
            
            // Hitung 'belum_dibaca' sebagai 'perlu ditindaklanjuti'
            $pendingCount = 0;
            
            if ($user->role == 2 || $user->role == 8) { // Direktur & Direktur ASP
                // Direktur perlu menindaklanjuti surat yang status_dirut-nya masih pending
                $pendingCount = (clone $suratMasukQuery)->whereHas('disposisi', function($q) {
                    $q->where('status_dirut', 'pending');
                })->count();
            } elseif ($user->role == 1 || $user->role == 5) { // Sekretaris & Sekretaris ASP
                // Sekretaris perlu menindaklanjuti surat yang status_sekretaris-nya masih pending
                $pendingCount = (clone $suratMasukQuery)->whereHas('disposisi', function($q) {
                    $q->where('status_sekretaris', 'pending');
                })->count();
            }

            $stats['surat_masuk'] = [
                'total' => (clone $suratMasukQuery)->count(),
                'belum_dibaca' => $pendingCount,
                'bulan_ini' => (clone $suratMasukQuery)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            ];
        }

        // 2. Stats Surat Keluar (Card Hijau)
        // Logic: Sama persis dengan SuratKeluarController@index (suratKeluarForUser)
        if ($user->hasPermission('manage_surat_keluar')) {
            $suratKeluarQuery = SuratKeluar::suratKeluarForUser($user);
            $stats['surat_keluar'] = [
                'total' => (clone $suratKeluarQuery)->count(),
                'menunggu_persetujuan' => (clone $suratKeluarQuery)->whereHas('disposisi', function($q) {
                    $q->where('status_sekretaris', 'pending')
                      ->orWhere('status_dirut', 'pending');
                })->count(),
                'bulan_ini' => (clone $suratKeluarQuery)->whereMonth('tanggal_surat', now()->month)->whereYear('tanggal_surat', now()->year)->count(),
            ];
        }

        // 3. Stats Persetujuan Surat (Card Merah)
        // Logic: Sama persis dengan SuratUnitManagerApprovalController@approvalIndex
        // Khusus untuk Manager Unit/Keuangan menyetujui surat dari unitnya
        if ($user->hasPermission('approve_surat_unit')) {
            // Gunakan filter byManager (filter by Unit ID) dan status pending manager
            $persetujuanQuery = SuratUnitManager::byManager($user->id)->byStatusManager('pending');
            
            $stats['persetujuan'] = [
                'menunggu_approval' => $persetujuanQuery->count(),
                // Total yang sudah diproses (Approved/Rejected) oleh user ini
                'total_disetujui' => SuratUnitManager::where('manager_id', $user->id)->where('status_manager', 'approved')->count(),
                'total_ditolak' => SuratUnitManager::where('manager_id', $user->id)->where('status_manager', 'rejected')->count(),
            ];
        }

        // 4. Stats Surat Unit Manager (Card Kuning - Jika ada)
        // Logic: Sama persis dengan SuratUnitManagerController@index
        if ($user->hasPermission('create_surat_unit')) {
            $suratUnitQuery = SuratUnitManager::suratUnitForUser($user);
            $stats['surat_unit'] = [
                'total' => (clone $suratUnitQuery)->count(),
                'menunggu_manager' => (clone $suratUnitQuery)->where('status_manager', 'pending')->count(),
                'disetujui' => (clone $suratUnitQuery)->where('status_dirut', 'approved')->count(),
                'bulan_ini' => (clone $suratUnitQuery)->whereMonth('tanggal_surat', now()->month)->whereYear('tanggal_surat', now()->year)->count(),
            ];
        }

        // Stats untuk Generate Nomor
        if ($user->hasPermission('generate_nomor_surat')) {
            $stats['generate_nomor'] = [
                'total_hari_ini' => SuratKeluar::whereDate('created_at', now()->toDateString())->count(),
                'total_bulan_ini' => SuratKeluar::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            ];
        }

        return $stats;
    }

    /**
     * Get surat masuk query based on user permission
     */
    private function getSuratMasukQuery($user)
    {
        return SuratKeluar::forSuratMasuk($user);
    }

    /**
     * Get surat keluar query based on user permission
     */
    private function getSuratKeluarQuery($user)
    {
        return SuratKeluar::suratKeluarForUser($user);
    }

    /**
     * Get surat unit query based on user permission
     */
    private function getSuratUnitQuery($user)
    {
        return SuratUnitManager::suratUnitForUser($user);
    }

    /**
     * Get persetujuan query based on user permission
     */
    private function getPersetujuanQuery($user)
    {
        return SuratUnitManager::forUserApproval($user);
    }

    /**
     * Get total approved by user
     */
    private function getTotalApproved($user)
    {
        return SuratUnitManager::forUserApproved($user)->count();
    }

    /**
     * Get total rejected by user
     */
    /**
     * Get total rejected by user
     */
    private function getTotalRejected($user)
    {
        return SuratUnitManager::forUserRejected($user)->count();
    }

    /**
     * Get recent activities based on user permissions
     */
    private function getRecentActivities($user)
    {
        $activities = collect();

        if ($user->hasPermission('manage_surat_masuk')) {
            $suratMasuk = $this->getSuratMasukQuery($user)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function($surat) {
                    return [
                        'type' => 'surat_masuk',
                        'icon' => 'fa-inbox',
                        'color' => 'blue',
                        'title' => 'Surat Masuk Baru',
                        'description' => $surat->perihal ?? $surat->nomor_surat,
                        'time' => $surat->created_at,
                        'url' => url('/suratmasuk'),
                    ];
                });
            $activities = $activities->merge($suratMasuk);
        }

        if ($user->hasPermission('manage_surat_keluar')) {
            $suratKeluar = $this->getSuratKeluarQuery($user)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function($surat) {
                    return [
                        'type' => 'surat_keluar',
                        'icon' => 'fa-paper-plane',
                        'color' => 'green',
                        'title' => 'Surat Keluar',
                        'description' => $surat->perihal ?? $surat->nomor_surat,
                        'time' => $surat->created_at,
                        'url' => route('suratkeluar.index'),
                    ];
                });
            $activities = $activities->merge($suratKeluar);
        }

        if ($user->hasPermission('approve_surat_unit')) {
            $persetujuan = $this->getPersetujuanQuery($user)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function($surat) {
                    return [
                        'type' => 'persetujuan',
                        'icon' => 'fa-check-circle',
                        'color' => 'yellow',
                        'title' => 'Menunggu Persetujuan',
                        'description' => $surat->perihal ?? $surat->nomor_surat,
                        'time' => $surat->created_at,
                        'url' => route('surat-unit-manager.approval.index'),
                    ];
                });
            $activities = $activities->merge($persetujuan);
        }

        if ($user->hasPermission('create_surat_unit')) {
            $suratUnit = $this->getSuratUnitQuery($user)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function($surat) {
                    return [
                        'type' => 'surat_unit',
                        'icon' => 'fa-file-alt',
                        'color' => 'green',
                        'title' => 'Surat Unit Baru',
                        'description' => $surat->perihal ?? $surat->nomor_surat,
                        'time' => $surat->created_at,
                        'url' => route('surat-unit-manager.show', $surat->id),
                    ];
                });
            $activities = $activities->merge($suratUnit);
        }

        return $activities->sortByDesc('time')->take(5);
    }

    /**
     * Get quick actions based on user permissions
     */
    private function getQuickActions($user)
    {
        $actions = [];

        if ($user->hasPermission('manage_surat_masuk')) {
            $actions[] = [
                'title' => 'Lihat Surat Masuk',
                'description' => 'Cek surat masuk',
                'icon' => 'fa-inbox',
                'color' => 'blue',
                'url' => route('suratmasuk.index'),
            ];
        }

        if ($user->hasPermission('manage_surat_keluar')) {
            $actions[] = [
                'title' => 'Buat Surat Keluar',
                'description' => 'Buat surat keluar baru',
                'icon' => 'fa-paper-plane',
                'color' => 'green',
                'url' => route('suratkeluar.create'),
            ];
        }

        if ($user->hasPermission('generate_nomor_surat')) {
            $actions[] = [
                'title' => 'Generate Nomor',
                'description' => 'Generate nomor surat',
                'icon' => 'fa-hashtag',
                'color' => 'purple',
                'url' => route('nomor.generate'),
            ];
        }

        if ($user->hasPermission('create_surat_unit')) {
            $actions[] = [
                'title' => 'Buat Surat Unit',
                'description' => 'Ajukan surat unit manager',
                'icon' => 'fa-file-alt',
                'color' => 'yellow',
                'url' => route('surat-unit-manager.create'),
            ];
        }

        if ($user->hasPermission('approve_surat_unit')) {
            // Use generic approval route
            $approvalUrl = route('surat-unit-manager.approval.index');
            
            $actions[] = [
                'title' => 'Persetujuan Surat',
                'description' => 'Setujui surat yang pending',
                'icon' => 'fa-check-circle',
                'color' => 'red',
                'url' => $approvalUrl,
            ];
        }

        return $actions;
    }
}
