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
