<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Hapus middleware di constructor karena sudah diterapkan di routes
    }

    public function index()
    {
        return view('pages.dashboard');
    }

    public function getStats()
    {
        try {
            $user = Auth::user();
            
            // Debug info
            Log::info('User Role: ' . $user->role);
            
            // Count SuratKeluar
            $totalSurat = SuratKeluar::count();
            Log::info('Total Surat: ' . $totalSurat);

            // Base query for Disposisi
            $disposisiQuery = Disposisi::query();

            // Filter based on user role
            if ($user->role === 'admin') {
                $disposisiQuery->where(function($q) use ($user) {
                    $q->where('asal_surat', $user->id)
                      ->orWhere('diteruskan_kepada', $user->id);
                });
            } elseif ($user->role === 'staff') {
                $disposisiQuery->where('diteruskan_kepada', $user->id);
            }

            // Get counts
            $totalDisposisi = (clone $disposisiQuery)->count();
            
            // Disposisi belum selesai: masih dalam status pending atau review
            $disposisiBelumSelesai = (clone $disposisiQuery)
                ->where(function($query) {
                    $query->where('status_sekretaris', 'pending')
                          ->orWhere('status_sekretaris', 'review')
                          ->orWhere('status_dirut', 'pending')
                          ->orWhere('status_dirut', 'review');
                })
                ->count();

            // Disposisi selesai: sudah approved atau rejected
            $disposisiSelesai = (clone $disposisiQuery)
                ->where(function($query) {
                    $query->where('status_sekretaris', 'approved')
                          ->orWhere('status_sekretaris', 'rejected')
                          ->orWhere('status_dirut', 'approved')
                          ->orWhere('status_dirut', 'rejected');
                })
                ->count();

            // Debug info
            Log::info('Stats:', [
                'totalSurat' => $totalSurat,
                'totalDisposisi' => $totalDisposisi,
                'belumSelesai' => $disposisiBelumSelesai,
                'selesai' => $disposisiSelesai
            ]);

            return response()->json([
                'totalSurat' => $totalSurat,
                'totalDisposisi' => $totalDisposisi,
                'disposisiBelumSelesai' => $disposisiBelumSelesai,
                'disposisiSelesai' => $disposisiSelesai
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getStats: ' . $e->getMessage());
            return response()->json([
                'totalSurat' => 0,
                'totalDisposisi' => 0,
                'disposisiBelumSelesai' => 0,
                'disposisiSelesai' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getRecentActivities()
    {
        try {
            $user = Auth::user();
            
            $query = Disposisi::with(['suratKeluar', 'pengirim', 'userPenerima']);

            if ($user->role === 'admin') {
                $query->where(function($q) use ($user) {
                    $q->where('asal_surat', $user->id)
                      ->orWhere('diteruskan_kepada', $user->id);
                });
            } elseif ($user->role === 'staff') {
                $query->where('diteruskan_kepada', $user->id);
            }

            $activities = $query->latest()
                ->take(5)
                ->get()
                ->map(function ($disposisi) {
                    return [
                        'id' => $disposisi->id,
                        'nomor_surat' => optional($disposisi->suratKeluar)->nomor_surat ?? 'N/A',
                        'perihal' => optional($disposisi->suratKeluar)->perihal ?? 'N/A',
                        'created_at' => $disposisi->created_at,
                        'status_penyelesaian' => $disposisi->status_penyelesaian
                    ];
                });

            Log::info('Recent Activities Count: ' . $activities->count());
            return response()->json($activities);

        } catch (\Exception $e) {
            Log::error('Error in getRecentActivities: ' . $e->getMessage());
            return response()->json([]);
        }
    }
}
