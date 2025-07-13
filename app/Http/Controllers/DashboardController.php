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
            Log::info('Getting stats for user:', [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role
            ]);
            
            // Hitung total surat masuk sesuai logic di SuratMasukController
            if ($user->role == 0 || $user->role == 3) { // Staff atau Admin
                $totalSurat = SuratKeluar::whereHas('disposisi.tujuan', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->count();
            } else {
                $totalSurat = SuratKeluar::count();
            }
            
            Log::info('Total Surat calculated:', ['count' => $totalSurat]);

            // Hitung total disposisi - sama untuk semua role
            $totalDisposisi = Disposisi::whereHas('tujuan', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->orWhere('created_by', $user->id)
            ->count();
            
            Log::info('Total Disposisi calculated:', ['count' => $totalDisposisi]);

            // Hitung disposisi belum selesai - sama untuk semua role
            $disposisiBelumSelesai = Disposisi::where(function($q) use ($user) {
                $q->whereHas('tujuan', function($q2) use ($user) {
                    $q2->where('users.id', $user->id);
                })
                ->orWhere('created_by', $user->id);
            })
            ->where(function($q) {
                $q->where('status_sekretaris', '!=', 'approved')
                  ->orWhere('status_dirut', '!=', 'approved');
            })
            ->count();
            
            Log::info('Disposisi Belum Selesai calculated:', ['count' => $disposisiBelumSelesai]);

            // Hitung disposisi selesai: khusus direktur tanpa filter tujuan
            if ($user->role == 2) { // direktur
                $disposisiSelesai = Disposisi::where(function($q) {
                    $q->whereRaw('LOWER(status_dirut) = ?', ['approved'])
                      ->orWhereRaw('LOWER(status_dirut) = ?', ['rejected']);
                })->get(['id', 'status_sekretaris', 'status_dirut']);
                Log::info('Disposisi selesai by dirut (all):', $disposisiSelesai->toArray());
            } else if ($user->role == 8) { // direktur ASP
                $disposisiSelesai = Disposisi::where(function($q) {
                    $q->whereRaw('LOWER(status_dirut) = ?', ['approved'])
                      ->orWhereRaw('LOWER(status_dirut) = ?', ['rejected']);
                })->get(['id', 'status_sekretaris', 'status_dirut']);
                Log::info('Disposisi selesai by dirut ASP (all):', $disposisiSelesai->toArray());
            } else {
                $disposisiUser = Disposisi::whereHas('tujuan', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->get(['id', 'status_sekretaris', 'status_dirut']);
                $disposisiSelesai = $disposisiUser->filter(function($item) {
                    return (
                        strtolower($item->status_dirut) === 'approved' ||
                        strtolower($item->status_dirut) === 'rejected'
                    );
                });
                Log::info('Disposisi selesai by user (dirut only):', $disposisiSelesai->toArray());
            }

            // Logging semua status disposisi
            $allDisposisi = Disposisi::all(['id', 'status_sekretaris', 'status_dirut']);
            Log::info('Semua status disposisi:', $allDisposisi->toArray());

            $response = [
                'totalSurat' => $totalSurat,
                'totalDisposisi' => $totalDisposisi,
                'disposisiBelumSelesai' => $disposisiBelumSelesai,
                'disposisiSelesai' => $disposisiSelesai->count()
            ];

            Log::info('Final response:', $response);
            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error in getStats:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan saat mengambil data statistik',
                'totalSurat' => 0,
                'totalDisposisi' => 0,
                'disposisiBelumSelesai' => 0,
                'disposisiSelesai' => 0
            ], 500);
        }
    }

    public function getRecentActivities()
    {
        try {
            $user = Auth::user();
            $activities = Disposisi::with(['suratKeluar', 'tujuan'])
                ->where(function($q) use ($user) {
                    $q->whereHas('tujuan', function($q2) use ($user) {
                        $q2->where('users.id', $user->id);
                    })
                    ->orWhere('created_by', $user->id);
                })
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($disposisi) {
                    return [
                        'id' => $disposisi->id,
                        'nomor_surat' => optional($disposisi->suratKeluar)->nomor_surat ?? 'N/A',
                        'perihal' => optional($disposisi->suratKeluar)->perihal ?? 'N/A',
                        'created_at' => $disposisi->created_at,
                        'status' => $disposisi->status_sekretaris === 'approved' && $disposisi->status_dirut === 'approved' 
                            ? 'selesai' 
                            : 'belum selesai'
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
