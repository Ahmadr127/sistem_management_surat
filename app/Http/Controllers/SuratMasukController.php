<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SuratMasukController extends Controller
{
    public function index(Request $request)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    $user = auth()->user();
    $userRole = $user->role;
    
    // Start query with eager loading
    $query = SuratKeluar::with([
        'disposisi.tujuan',
        'creator.organizationUnit',
        'perusahaanData',
        'files'
    ]);
    
    // Role-based filtering using clean scope
    $query->forSuratMasuk($user);
    
    // Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nomor_surat', 'like', "%{$search}%")
              ->orWhere('perihal', 'like', "%{$search}%")
              ->orWhereHas('creator', function($subq) use ($search) {
                  $subq->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('perusahaanData', function($subq) use ($search) {
                  $subq->where('nama_perusahaan', 'like', "%{$search}%");
              });
        });
    }
    
    // Date range filter
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('tanggal_surat', [$request->start_date, $request->end_date]);
    } elseif ($request->filled('start_date')) {
        $query->whereDate('tanggal_surat', '>=', $request->start_date);
    } elseif ($request->filled('end_date')) {
        $query->whereDate('tanggal_surat', '<=', $request->end_date);
    }
    
    // Jenis surat filter
    if ($request->filled('jenis_surat')) {
        $query->where('jenis_surat', $request->jenis_surat);
    }
    
    // Sifat surat filter
    if ($request->filled('sifat_surat')) {
        $query->where('sifat_surat', $request->sifat_surat);
    }
    
    // Status sekretaris filter
    if ($request->filled('status_sekretaris')) {
        $query->whereHas('disposisi', function($q) use ($request) {
            $q->where('status_sekretaris', $request->status_sekretaris);
        });
    }
    
    // Status direktur filter
    if ($request->filled('status_dirut')) {
        $query->whereHas('disposisi', function($q) use ($request) {
            $q->where('status_dirut', $request->status_dirut);
        });
    }
    
    // Order by newest
    $query->orderBy('tanggal_surat', 'desc')
          ->orderBy('created_at', 'desc');
    
    // Paginate
    $suratMasuk = $query->paginate(10);
    
    // Prepare surat options for searchable dropdown
    $suratOptionsQuery = SuratKeluar::with(['perusahaanData']);
    
    // Apply same role-based filter for options using clean scope
    $suratOptionsQuery->forSuratMasuk($user);
    
    $suratOptions = $suratOptionsQuery->orderBy('tanggal_surat', 'desc')
                                      ->get()
                                      ->map(function($surat) {
                                          return [
                                              'id' => $surat->id,
                                              'label' => $surat->nomor_surat . ' - ' . $surat->perihal,
                                              'nomor_surat' => $surat->nomor_surat,
                                              'perihal' => $surat->perihal,
                                              'tanggal_surat' => $surat->tanggal_surat,
                                              'jenis_surat' => $surat->jenis_surat,
                                          ];
                                      });
    
    return view('pages.surat.surat_masuk.index', compact('suratMasuk', 'suratOptions'));
}
    
    public function getSuratMasuk(Request $request)
    {
        try {
            Log::info('SuratMasukController@getSuratMasuk - Request:', $request->all());
            
            $query = SuratKeluar::with([
                'disposisi', 
                'disposisi.tujuan',
                'creator',
                'files',
                'perusahaanData'
            ]);
            
            // Filter untuk role 0 (staff), 4 (manager), 5 (Sekretaris ASP), dll
            if ($request->has('user_id')) {
                $userId = $request->user_id;
                $user = \App\Models\User::find($userId);
                $userOrgUnitId = $user ? $user->organization_unit_id : null;
                
                Log::info('Filtering surat for user_id: ' . $userId . ' OrgUnit: ' . $userOrgUnitId);
                
                // Jika include_created = true, tambahkan surat yang dibuat oleh user
                if ($request->has('include_created') && $request->include_created === 'true') {
                    $query->where(function($q) use ($userId, $userOrgUnitId) {
                        // Surat yang ditujukan kepada user (atau unitnya)
                        $q->whereHas('disposisi.tujuan', function($subq) use ($userId, $userOrgUnitId) {
                            if ($userOrgUnitId) {
                                // Cek apakah ada target disposisi yang memiliki organization_unit_id yang sama
                                $subq->where('users.organization_unit_id', $userOrgUnitId);
                            } else {
                                // Jika user tidak punya org unit, fallback ke specific user id
                                $subq->where('users.id', $userId);
                            }
                        });
                        // Atau surat yang dibuat oleh user
                        $q->orWhere('created_by', $userId);
                    });
                } else {
                    // Hanya surat yang ditujukan kepada user (atau unitnya)
                    $query->whereHas('disposisi.tujuan', function($q) use ($userId, $userOrgUnitId) {
                        if ($userOrgUnitId) {
                            $q->where('users.organization_unit_id', $userOrgUnitId);
                        } else {
                            $q->where('users.id', $userId);
                        }
                    });
                }
            } 
            // Filter untuk role 1 (Sekretaris) - semua data
            else if ($request->has('all') && $request->all === 'true') {
                Log::info('Showing all surat for Sekretaris');
                // Tidak ada filter tambahan, tampilkan semua
            }
            // Filter untuk role 2 (direktur)
            else if ($request->has('status_sekretaris')) {
                $query->whereHas('disposisi', function($q) use ($request) {
                    $q->where('status_sekretaris', $request->status_sekretaris);
                });
            }
            // Filter untuk role 8 (Direktur ASP) - sama seperti direktur
            else if ($request->has('status_sekretaris_asp')) {
                $query->whereHas('disposisi', function($q) use ($request) {
                    $q->where('status_sekretaris', $request->status_sekretaris_asp);
                });
            }
            
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nomor_surat', 'like', "%{$searchTerm}%")
                      ->orWhere('perihal', 'like', "%{$searchTerm}%")
                      ->orWhere('perusahaan', 'like', "%{$searchTerm}%")
                      ->orWhereHas('perusahaanData', function($subquery) use ($searchTerm) {
                          $subquery->where('nama_perusahaan', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('creator', function($subquery) use ($searchTerm) {
                          $subquery->where('name', 'like', "%{$searchTerm}%")
                                   ->orWhereHas('organizationUnit', function($q2) use ($searchTerm) {
                                       $q2->where('name', 'like', "%{$searchTerm}%");
                                   });
                      })
                      ->orWhereHas('disposisi', function($subquery) use ($searchTerm) {
                          $subquery->where('status_sekretaris', 'like', "%{$searchTerm}%")
                                   ->orWhere('status_dirut', 'like', "%{$searchTerm}%")
                                   ->orWhereHas('tujuan', function($q2) use ($searchTerm) {
                                       $q2->where('name', 'like', "%{$searchTerm}%");
                                   });
                      });
                });
            }
            
            $suratMasuk = $query->latest()->get();
            
            Log::info('Found ' . count($suratMasuk) . ' surat for request');
            
            // Transform response agar ada perusahaanData (kode dan nama_perusahaan)
            $transformed = $suratMasuk->map(function($surat) {
                return [
                    'id' => $surat->id,
                    'nomor_surat' => $surat->nomor_surat,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'perihal' => $surat->perihal,
                    'file_path' => $surat->file_path,
                    'jenis_surat' => $surat->jenis_surat,
                    'sifat_surat' => $surat->sifat_surat,
                    'created_by' => $surat->created_by,
                    'perusahaan' => $surat->perusahaan,
                    'perusahaanData' => $surat->perusahaanData ? [
                        'kode' => $surat->perusahaanData->kode,
                        'nama_perusahaan' => $surat->perusahaanData->nama_perusahaan
                    ] : null,
                    'creator' => $surat->creator,
                    'disposisi' => $surat->disposisi,
                    'files' => $surat->files,
                ];
            });
            
            return response()->json($transformed);
        } catch (\Exception $e) {
            Log::error('Error in getSuratMasuk: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data surat masuk',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function markAsRead(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;
            Log::info('Marking surat ' . $id . ' as read for user: ' . $userId);
            
            // Cari disposisi untuk surat ini
            $disposisi = DB::table('tbl_disposisi')
                ->where('surat_keluar_id', $id)
                ->first();
            
            if ($disposisi) {
                // Cek apakah kolom dibaca ada di tabel tbl_disposisi_user
                $hasReadColumn = Schema::hasColumn('tbl_disposisi_user', 'dibaca');
                
                if ($hasReadColumn) {
                    // Update status dibaca
                    DB::table('tbl_disposisi_user')
                        ->where('disposisi_id', $disposisi->id)
                        ->where('user_id', $userId)
                        ->update(['dibaca' => true]);
                }
                
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'Disposisi tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error marking surat as read: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
