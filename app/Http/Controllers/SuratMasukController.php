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
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        // Ambil semua surat masuk yang terkait user
        $suratList = \App\Models\SuratKeluar::with(['disposisi.tujuan'])
            ->whereHas('disposisi.tujuan', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->latest()
            ->get();

        $suratMasuk = [];
        foreach ($suratList as $surat) {
            $userAdalahPenerimaDisposisi = false;
            $disposisiId = null;
            $keteranganPenerima = null;
            if ($surat->disposisi) {
                foreach ($surat->disposisi->tujuan as $tujuan) {
                    if ($tujuan->id == $user->id) {
                        $userAdalahPenerimaDisposisi = true;
                        $disposisiId = $surat->disposisi->id;
                        $keteranganPenerima = $tujuan->pivot->keterangan_penerima ?? null;
                        break;
                    }
                }
            }
            $suratMasuk[] = [
                'surat' => $surat,
                'userAdalahPenerimaDisposisi' => $userAdalahPenerimaDisposisi,
                'disposisiId' => $disposisiId,
                'keteranganPenerima' => $keteranganPenerima,
            ];
        }

        return view('pages.surat.suratmasuk', [
            'suratMasuk' => $suratMasuk,
        ]);
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
            
            // Filter untuk role 0 (staff) atau role 5 (Sekretaris ASP)
            if ($request->has('user_id')) {
                $userId = $request->user_id;
                Log::info('Filtering surat for user_id: ' . $userId);
                
                // Jika include_created = true, tambahkan surat yang dibuat oleh user
                if ($request->has('include_created') && $request->include_created === 'true') {
                    $query->where(function($q) use ($userId) {
                        // Surat yang ditujukan kepada user
                        $q->whereHas('disposisi.tujuan', function($subq) use ($userId) {
                            $subq->where('users.id', $userId);
                        });
                        // Atau surat yang dibuat oleh user
                        $q->orWhere('created_by', $userId);
                    });
                } else {
                    // Hanya surat yang ditujukan kepada user
                    $query->whereHas('disposisi.tujuan', function($q) use ($userId) {
                        $q->where('users.id', $userId);
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
                                   ->orWhereHas('jabatan', function($q2) use ($searchTerm) {
                                       $q2->where('nama_jabatan', 'like', "%{$searchTerm}%");
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
                $user = auth()->user();
                $userAdalahPenerimaDisposisi = false;
                $disposisiId = null;
                $keteranganPenerima = null;
                if ($surat->disposisi) {
                    foreach ($surat->disposisi->tujuan as $tujuan) {
                        if ($tujuan->id == $user->id) {
                            $userAdalahPenerimaDisposisi = true;
                            $disposisiId = $surat->disposisi->id;
                            $keteranganPenerima = $tujuan->pivot->keterangan_penerima ?? null;
                            break;
                        }
                    }
                }
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
                    // Tambahan untuk fitur keterangan penerima
                    'user_adalah_penerima_disposisi' => $userAdalahPenerimaDisposisi,
                    'disposisi_id' => $disposisiId,
                    'keterangan_penerima' => $keteranganPenerima,
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
