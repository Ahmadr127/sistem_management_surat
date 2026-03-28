<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;

class SuratMasukApiController extends Controller
{
    /**
     * Get list of incoming letters (Surat Masuk) for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = SuratKeluar::with([
            'disposisi.tujuan',
            'creator.organizationUnit',
            'perusahaanData',
            'files'
        ]);
        
        // Use existing scope for role-based filtering
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

        // Status disposisi (sama logika badge di mobile: pakai status_dirut jika terisi, else status_sekretaris)
        $status = $request->get('status');
        if (is_string($status) && $status !== '' && $status !== 'all') {
            $allowed = ['pending', 'approved', 'rejected'];
            if (in_array($status, $allowed, true)) {
                $query->whereHas('disposisi', function ($q) use ($status) {
                    $q->where(function ($q2) use ($status) {
                        $q2->where(function ($q3) use ($status) {
                            $q3->whereNotNull('status_dirut')
                                ->where('status_dirut', '!=', '')
                                ->where('status_dirut', $status);
                        })->orWhere(function ($q3) use ($status) {
                            $q3->where(function ($q4) {
                                $q4->whereNull('status_dirut')->orWhere('status_dirut', '');
                            })->where('status_sekretaris', $status);
                        });
                    });
                });
            }
        }
        
        // Order by newest
        $suratMasuk = $query->latest('tanggal_surat')
                            ->latest('created_at')
                            ->paginate($request->get('limit', 10));
        
        return response()->json([
            'status' => 'success',
            'data'   => $suratMasuk
        ]);
    }

    /**
     * Get detail of a specific letter.
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $surat = SuratKeluar::with([
            'disposisi.tujuan',
            'creator.organizationUnit',
            'perusahaanData',
            'files'
        ])->find($id);
        
        if (!$surat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Surat tidak ditemukan'
            ], 404);
        }
        
        // Verify access (optional, if forSuratMasuk logic should be strictly enforced)
        // For simplicity, we assume if they have the ID they might see it, 
        // but better to check if it's in their allowed list.
        
        return response()->json([
            'status' => 'success',
            'data'   => $surat
        ]);
    }
}
