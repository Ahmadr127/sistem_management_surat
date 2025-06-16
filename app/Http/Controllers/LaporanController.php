<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\Disposisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        // Get all active jabatan for the dropdown filter
        $jabatanList = \App\Models\Jabatan::where('status', 'aktif')
            ->orderBy('nama_jabatan')
            ->get();
            
        // Get all active perusahaan for the dropdown filter
        $perusahaans = \App\Models\Perusahaan::where('status', 'aktif')
            ->orderBy('nama_perusahaan')
            ->get();
            
        \Log::info('Loaded active jabatan for laporan filter dropdown:', [
            'count' => $jabatanList->count()
        ]);
            
        return view('pages.laporan', compact('jabatanList', 'perusahaans'));
    }
    
    public function getData(Request $request)
    {
        $user = Auth::user();
        \Log::info('Laporan getData accessed by user:', [
            'user_id' => $user->id,
            'name' => $user->name,
            'role' => $user->role
        ]);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perusahaan = $request->input('perusahaan');
        $status = $request->input('status');
        $periodType = $request->input('period_type', 'custom'); // 'weekly', 'monthly', 'custom'
        $jabatan = $request->input('jabatan'); // Added jabatan parameter
        
        \Log::info('Laporan request parameters:', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'perusahaan' => $perusahaan,
            'status' => $status,
            'period_type' => $periodType,
            'jabatan' => $jabatan
        ]);
        
        // Handle period types
        if ($periodType === 'weekly') {
            // Set to current week
            $startDate = now()->startOfWeek()->format('Y-m-d');
            $endDate = now()->endOfWeek()->format('Y-m-d');
        } elseif ($periodType === 'monthly') {
            // Set to current month
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');
        }
        
        \Log::info('Date range after period type calculation:', [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        try {
            $data = $this->getSuratKeluarReport($startDate, $endDate, $perusahaan, $user, $jabatan, $status);
            
            \Log::info('Laporan data retrieved:', [
                'count' => $data->count(),
                'data_sample' => $data->take(3)->toArray()
            ]);
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('Error in getData:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    protected function getSuratKeluarReport($startDate, $endDate, $perusahaan, $user, $jabatan, $status)
    {
        \Log::info('Running getSuratKeluarReport');
        
        // Count total records first
        $totalSuratKeluar = SuratKeluar::count();
        \Log::info("Total SuratKeluar records in database: {$totalSuratKeluar}");
        
        $query = SuratKeluar::with([
                'disposisi' => function($q) {
                    $q->select('id', 'surat_keluar_id', 'status_sekretaris', 'status_dirut', 'waktu_review_dirut', 'waktu_review_sekretaris', 'created_at', 'updated_at');
                },
                'disposisi.tujuan' => function($q) {
                    $q->select('users.id', 'users.name', 'jabatan_id');
                },
                'creator.jabatan'
            ])
            ->orderBy('tanggal_surat', 'desc');
        
        // Apply date filters
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
            \Log::info("Applied date filter: {$startDate} to {$endDate}");
        }
        
        // Apply perusahaan filter
        if ($perusahaan) {
            $query->where('perusahaan', $perusahaan);
            \Log::info("Applied perusahaan filter: {$perusahaan}");
        }
        
        // Apply jabatan filter
        if ($jabatan) {
            $query->whereHas('creator.jabatan', function($q) use ($jabatan) {
                $q->where('nama_jabatan', $jabatan);
            });
            \Log::info("Applied jabatan filter: {$jabatan}");
        }
        
        // Apply status filter
        if ($status) {
            $query->whereHas('disposisi', function($q) use ($status) {
                $q->where('status_sekretaris', $status)
                  ->orWhere('status_dirut', $status);
            });
            \Log::info("Applied status filter: {$status}");
        }
        
        // Apply role-based access
        if ($user->role == 0) { // Staff
            $query->where('created_by', $user->id);
            \Log::info("Applied staff role filter for user_id: {$user->id}");
        } elseif ($user->role == 1) { // Sekretaris - can see all
            \Log::info("No additional filter for sekretaris role");
        } elseif ($user->role == 2) { // Direktur - can see all
            \Log::info("No additional filter for direktur role");
        }
        
        $result = $query->get();
        \Log::info("SuratKeluar report result count: {$result->count()}");
        
        return $result;
    }
    
    protected function getDisposisiReport($startDate, $endDate, $perusahaan, $status, $reviewBy, $user, $jabatan)
    {
        \Log::info('Running getDisposisiReport');
        
        // Count total records first
        $totalDisposisi = Disposisi::count();
        \Log::info("Total Disposisi records in database: {$totalDisposisi}");
        
        $query = Disposisi::with(['suratKeluar', 'tujuan', 'creator.jabatan'])
            ->orderBy('created_at', 'desc');
        
        // Apply date filters
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
            \Log::info("Applied date filter: {$startDate} to {$endDate}");
        }
        
        // Apply status and reviewer filters
        if ($status) {
            if ($reviewBy === 'sekretaris') {
                $query->where('status_sekretaris', $status);
                \Log::info("Applied sekretaris status filter: {$status}");
            } elseif ($reviewBy === 'dirut') {
                $query->where('status_dirut', $status);
                \Log::info("Applied dirut status filter: {$status}");
            } else {
                $query->where(function($q) use ($status) {
                    $q->where('status_sekretaris', $status)
                      ->orWhere('status_dirut', $status);
                });
                \Log::info("Applied both reviewers status filter: {$status}");
            }
        }
        
        // Apply perusahaan filter
        if ($perusahaan) {
            $query->whereHas('suratKeluar', function($q) use ($perusahaan) {
                $q->where('perusahaan', $perusahaan);
            });
            \Log::info("Applied perusahaan filter: {$perusahaan}");
        }
        
        // Apply jabatan filter
        if ($jabatan) {
            $query->whereHas('creator.jabatan', function($q) use ($jabatan) {
                $q->where('nama_jabatan', $jabatan);
            });
            \Log::info("Applied jabatan filter: {$jabatan}");
        }
        
        // Apply role-based access
        if ($user->role == 0) { // Staff
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('tujuan', function($sq) use ($user) {
                      $sq->where('users.id', $user->id);
                  });
            });
            \Log::info("Applied staff role filter for user_id: {$user->id}");
        } elseif ($user->role == 1) { // Sekretaris
            \Log::info("No additional filter for sekretaris role");
        } elseif ($user->role == 2) { // Direktur
            \Log::info("No additional filter for direktur role");
        }
        
        $result = $query->get();
        \Log::info("Disposisi report result count: {$result->count()}");
        
        return $result;
    }
    
    protected function getSuratMasukReport($startDate, $endDate, $perusahaan, $user, $jabatan)
    {
        \Log::info('Running getSuratMasukReport');
        
        $query = SuratKeluar::with([
                'creator.jabatan',
                'disposisi' => function($q) {
                    $q->select('id', 'surat_keluar_id', 'status_sekretaris', 'status_dirut');
                },
                'disposisi.tujuan' => function($q) {
                    $q->select('users.id', 'users.name', 'jabatan_id');
                }
            ])
            ->whereHas('disposisi', function($q) use ($user) {
                $q->whereHas('tujuan', function($sq) use ($user) {
                    $sq->where('users.id', $user->id);
                });
            })
            ->orderBy('tanggal_surat', 'desc');
        
        // Apply date filters
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
            \Log::info("Applied date filter: {$startDate} to {$endDate}");
        }
        
        // Apply perusahaan filter
        if ($perusahaan) {
            $query->where('perusahaan', $perusahaan);
            \Log::info("Applied perusahaan filter: {$perusahaan}");
        }
        
        // Apply jabatan filter - filter by the creator's jabatan
        if ($jabatan) {
            $query->whereHas('creator.jabatan', function($q) use ($jabatan) {
                $q->where('nama_jabatan', $jabatan);
            });
            \Log::info("Applied jabatan filter: {$jabatan}");
        }
        
        $result = $query->get();
        \Log::info("SuratMasuk report result count: {$result->count()}");
        
        return $result;
    }
    
    public function exportLaporan(Request $request)
    {
        try {
            $user = Auth::user();
            \Log::info('Export laporan accessed by user:', [
                'user_id' => $user->id,
                'name' => $user->name,
                'role' => $user->role
            ]);
            
            $perusahaan = $request->input('perusahaan');
            $status = $request->input('status');
        $format = $request->input('format', 'excel');
            $period_type = $request->input('period_type', 'custom');
            $jabatan = $request->input('jabatan');
            $jenis_surat = $request->input('jenis_surat');
            
            \Log::info('Export parameters:', [
                'perusahaan' => $perusahaan,
                'status' => $status,
                'format' => $format,
                'period_type' => $period_type,
                'jabatan' => $jabatan,
                'jenis_surat' => $jenis_surat
            ]);
            
            // Handle period types
            $start_date = null;
            $end_date = null;
            
            if ($period_type === 'weekly') {
                $start_date = Carbon::now()->startOfWeek()->format('Y-m-d');
                $end_date = Carbon::now()->endOfWeek()->format('Y-m-d');
            } elseif ($period_type === 'monthly') {
                $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date = Carbon::now()->endOfMonth()->format('Y-m-d');
            } else {
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
            }
            
            \Log::info('Date range:', [
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            
            // Start with a basic query
            $query = SuratKeluar::with([
                'disposisi' => function($q) {
                    $q->select('id', 'surat_keluar_id', 'status_sekretaris', 'status_dirut', 'waktu_review_dirut', 'waktu_review_sekretaris', 'created_at', 'updated_at');
                },
                'disposisi.tujuan' => function($q) {
                    $q->select('users.id', 'users.name', 'jabatan_id');
                },
                'creator.jabatan'
            ])
            ->orderBy('tanggal_surat', 'desc');
            
            // Apply filters
            if ($start_date && $end_date) {
                $query->whereBetween('tanggal_surat', [$start_date, $end_date]);
            }
            
            if ($perusahaan) {
                $query->where('perusahaan', $perusahaan);
            }
            
            if ($jabatan) {
                $query->whereHas('creator.jabatan', function($q) use ($jabatan) {
                    $q->where('nama_jabatan', $jabatan);
                });
            }
            
            if ($status) {
                $query->whereHas('disposisi', function($q) use ($status, $request) {
                    if ($request->input('use_dirut_status', false)) {
                        $q->where('status_dirut', $status);
                    } else {
                        $q->where('status_sekretaris', $status)
                          ->orWhere('status_dirut', $status);
                    }
                });
            }
            
            // Apply jenis_surat filter directly in the query
            if ($jenis_surat) {
                $query->where('jenis_surat', $jenis_surat);
            }
            
            // Apply role-based access
            if ($user->role == 0) { // Staff
                $query->where('created_by', $user->id);
                \Log::info("Applied staff role filter for user_id: {$user->id}");
            }
            
            $data = $query->get();
            \Log::info('Data retrieved for export:', [
                'count' => $data->count()
            ]);
        
        if ($format === 'excel') {
                $useWaktuReviewDirut = true; // Always use waktu_review_dirut, no fallback
                $disposisiDateLabel = $request->input('disposisi_date_label', 'Tanggal Disposisi');
                $useDirutStatus = $request->input('use_dirut_status', true);
                
                // Include additional configuration
                $exportConfig = [
                    'data' => $data,
                    'jenis' => 'surat_keluar',
                    'title' => "Laporan Surat Keluar",
                    'startDate' => $start_date,
                    'endDate' => $end_date,
                    'periodType' => $period_type,
                    'useWaktuReviewDirut' => $useWaktuReviewDirut,
                    'disposisiDateLabel' => $disposisiDateLabel,
                    'useDirutStatus' => $useDirutStatus
                ];
                
                \Log::info('Excel export configuration:', $exportConfig);
                
                return Excel::download(
                    new LaporanExport(
                        $data, 
                        'surat_keluar', 
                        "Laporan Surat Keluar", 
                        $start_date, 
                        $end_date, 
                        $period_type, 
                        $useWaktuReviewDirut, 
                        $disposisiDateLabel,
                        $useDirutStatus
                    ),
                    "Laporan Surat Keluar.xlsx"
                );
        } else {
            $useWaktuReviewDirut = true; // Always use waktu_review_dirut, no fallback
            $disposisiDateLabel = $request->input('disposisi_date_label', 'Tanggal Disposisi');
            
            $pdf = PDF::loadView('exports.laporan_pdf', [
                'data' => $data,
                'jenis' => 'surat_keluar',
                'title' => "Laporan Surat Keluar",
                'startDate' => $start_date,
                'endDate' => $end_date,
                'periodType' => $period_type,
                'useWaktuReviewDirut' => $useWaktuReviewDirut,
                'disposisiDateLabel' => $disposisiDateLabel
            ]);
                
            return $pdf->download("Laporan Surat Keluar.pdf");
        }
        } catch (\Exception $e) {
            \Log::error('Error in exportLaporan:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getLaporan(Request $request)
    {
        try {
            $user = Auth::user();
            \Log::info('Laporan accessed by user:', [
                'user_id' => $user->id,
                'name' => $user->name,
                'role' => $user->role
            ]);
            
            $perusahaan = $request->input('perusahaan');
            $status = $request->input('status');
            $period_type = $request->input('period_type', 'custom');
            $jabatan = $request->input('jabatan');
            $jenis_surat = $request->input('jenis_surat');
            
            \Log::info('Filter parameters:', [
                'perusahaan' => $perusahaan,
                'status' => $status,
                'period_type' => $period_type,
                'jabatan' => $jabatan,
                'jenis_surat' => $jenis_surat
            ]);
            
            // Handle period types
            $start_date = null;
            $end_date = null;
            
            if ($period_type === 'weekly') {
                $start_date = Carbon::now()->startOfWeek()->format('Y-m-d');
                $end_date = Carbon::now()->endOfWeek()->format('Y-m-d');
            } elseif ($period_type === 'monthly') {
                $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date = Carbon::now()->endOfMonth()->format('Y-m-d');
            } else {
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');
            }
            
            \Log::info('Date range:', [
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            
            // Start with a basic query
            $query = SuratKeluar::with([
                'disposisi' => function($q) {
                    $q->select('id', 'surat_keluar_id', 'status_sekretaris', 'status_dirut', 'waktu_review_dirut', 'waktu_review_sekretaris', 'created_at', 'updated_at');
                },
                'disposisi.tujuan' => function($q) {
                    $q->select('users.id', 'users.name', 'jabatan_id');
                },
                'creator.jabatan'
            ])
            ->orderBy('tanggal_surat', 'desc');
            
            // Apply filters
            if ($start_date && $end_date) {
                $query->whereBetween('tanggal_surat', [$start_date, $end_date]);
            }
            
            if ($perusahaan) {
                $query->where('perusahaan', $perusahaan);
            }
            
            if ($jabatan) {
                $query->whereHas('creator.jabatan', function($q) use ($jabatan) {
                    $q->where('nama_jabatan', $jabatan);
                });
            }
            
            if ($status) {
                $query->whereHas('disposisi', function($q) use ($status, $request) {
                    if ($request->input('use_dirut_status', false)) {
                        $q->where('status_dirut', $status);
                    } else {
                        $q->where('status_sekretaris', $status)
                          ->orWhere('status_dirut', $status);
                    }
                });
            }
            
            // Apply jenis_surat filter directly in the query
            if ($jenis_surat) {
                $query->where('jenis_surat', $jenis_surat);
            }
            
            // Apply role-based access
            if ($user->role == 0) { // Staff
                $query->where('created_by', $user->id);
                \Log::info("Applied staff role filter for user_id: {$user->id}");
            }
            
            $data = $query->get();
            \Log::info('Data retrieved for report:', [
                'count' => $data->count()
            ]);
            
            return response()->json([
                'data' => $data,
                'from_date' => $start_date,
                'to_date' => $end_date,
                'total' => $data->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getLaporan:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
