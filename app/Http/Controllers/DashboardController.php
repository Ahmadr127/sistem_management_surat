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
            $query = SuratKeluar::with(['disposisi', 'disposisi.tujuan', 'creator', 'files', 'perusahaanData']);
            
            if ($user->role == 0 || $user->role == 3) { // Staff atau Admin
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user (jika disetujui direktur)
                $query->where(function($q) use ($user) {
                    // Surat yang ditujukan kepada user
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    // Atau surat yang dibuat oleh user dan disetujui direktur
                    $q->orWhere(function($subq) use ($user) {
                        $subq->where('created_by', $user->id)
                             ->whereHas('disposisi', function($subsubq) {
                                 $subsubq->where('status_dirut', 'approved');
                             });
                    });
                });
            } else if ($user->role == 1) { // Sekretaris
                // Semua data surat
                Log::info('Showing all surat for Sekretaris');
            } else if ($user->role == 2) { // Direktur
                // Surat dengan status sekretaris approved
                $query->whereHas('disposisi', function($q) {
                    $q->where('status_sekretaris', 'approved');
                });
            } else if ($user->role == 4) { // Manager
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user
                $query->where(function($q) use ($user) {
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    $q->orWhere('created_by', $user->id);
                });
            } else if ($user->role == 5) { // Sekretaris ASP
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user
                $query->where(function($q) use ($user) {
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    $q->orWhere('created_by', $user->id);
                });
            } else if ($user->role == 6) { // General Manager
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user
                $query->where(function($q) use ($user) {
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    $q->orWhere('created_by', $user->id);
                });
            } else if ($user->role == 7) { // Manager Keuangan
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user
                $query->where(function($q) use ($user) {
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    $q->orWhere('created_by', $user->id);
                });
            } else if ($user->role == 8) { // Direktur ASP
                // Surat dengan status sekretaris approved
                $query->whereHas('disposisi', function($q) {
                    $q->where('status_sekretaris_asp', 'approved');
                });
            }
            
            $totalSurat = $query->count();
            Log::info('Total Surat calculated:', ['count' => $totalSurat, 'role' => $user->role]);

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

            // Hitung disposisi selesai
            $disposisiSelesai = Disposisi::where(function($q) use ($user) {
                $q->whereHas('tujuan', function($q2) use ($user) {
                    $q2->where('users.id', $user->id);
                })
                ->orWhere('created_by', $user->id);
            })
            ->where(function($q) {
                $q->where('status_sekretaris', 'approved')
                  ->where('status_dirut', 'approved');
            })
            ->count();
            
            Log::info('Disposisi Selesai calculated:', ['count' => $disposisiSelesai]);

            $response = [
                'totalSurat' => $totalSurat,
                'totalDisposisi' => $totalDisposisi,
                'disposisiBelumSelesai' => $disposisiBelumSelesai,
                'disposisiSelesai' => $disposisiSelesai
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
            Log::info('Getting recent activities for user:', [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role
            ]);
            
            $query = SuratKeluar::with([
                'disposisi', 
                'disposisi.tujuan',
                'creator',
                'files',
                'perusahaanData'
            ]);
            
            // Filter berdasarkan role sesuai logic di SuratMasukController
            if ($user->role == 0 || $user->role == 3) { // Staff atau Admin
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user (jika disetujui direktur)
                $query->where(function($q) use ($user) {
                    // Surat yang ditujukan kepada user
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    // Atau surat yang dibuat oleh user dan disetujui direktur
                    $q->orWhere(function($subq) use ($user) {
                        $subq->where('created_by', $user->id)
                             ->whereHas('disposisi', function($subsubq) {
                                 $subsubq->where('status_dirut', 'approved');
                             });
                    });
                });
            } else if ($user->role == 1) { // Sekretaris
                // Semua data surat
                Log::info('Showing all surat for Sekretaris');
            } else if ($user->role == 2) { // Direktur
                // Surat dengan status sekretaris approved
                $query->whereHas('disposisi', function($q) {
                    $q->where('status_sekretaris', 'approved');
                });
            } else if ($user->role == 4) { // Manager
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user
                $query->where(function($q) use ($user) {
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    $q->orWhere('created_by', $user->id);
                });
            } else if ($user->role == 5) { // Sekretaris ASP
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user
                $query->where(function($q) use ($user) {
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    $q->orWhere('created_by', $user->id);
                });
            } else if ($user->role == 6) { // General Manager
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user
                $query->where(function($q) use ($user) {
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    $q->orWhere('created_by', $user->id);
                });
            } else if ($user->role == 7) { // Manager Keuangan
                // Surat yang ditujukan kepada user dan surat yang dibuat oleh user
                $query->where(function($q) use ($user) {
                    $q->whereHas('disposisi.tujuan', function($subq) use ($user) {
                        $subq->where('users.id', $user->id);
                    });
                    $q->orWhere('created_by', $user->id);
                });
            } else if ($user->role == 8) { // Direktur ASP
                // Surat dengan status sekretaris approved
                $query->whereHas('disposisi', function($q) {
                    $q->where('status_sekretaris_asp', 'approved');
                });
            }
            
            $activities = $query->latest('tanggal_surat')
                ->take(5)
                ->get()
                ->map(function ($surat) {
                    return [
                        'id' => $surat->id,
                        'nomor_surat' => $surat->nomor_surat ?? 'N/A',
                        'perihal' => $surat->perihal ?? 'N/A',
                        'created_at' => $surat->created_at,
                        'tanggal_surat' => $surat->tanggal_surat,
                        'status' => $surat->disposisi && 
                                  $surat->disposisi->status_sekretaris === 'approved' && 
                                  $surat->disposisi->status_dirut === 'approved' 
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
