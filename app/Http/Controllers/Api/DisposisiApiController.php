<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisposisiApiController extends Controller
{
    /**
     * Update the receipt note (keterangan_penerima) for a disposal.
     */
    public function updateKeterangan(Request $request, $disposisi_id)
    {
        $request->validate([
            'keterangan_penerima' => 'required|string',
        ]);

        $user = auth()->user();
        
        // Update the pivot table tbl_disposisi_user
        $updated = DB::table('tbl_disposisi_user')
            ->where('disposisi_id', $disposisi_id)
            ->where('user_id', $user->id)
            ->update([
                'keterangan_penerima' => $request->keterangan_penerima,
                'dibaca' => true,
                'updated_at' => now()
            ]);

        if (!$updated) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Disposisi tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Keterangan penerima berhasil diperbarui.'
        ]);
    }

    /**
     * Mark a disposal as read.
     */
    public function markAsRead($disposisi_id)
    {
        $user = auth()->user();
        
        $updated = DB::table('tbl_disposisi_user')
            ->where('disposisi_id', $disposisi_id)
            ->where('user_id', $user->id)
            ->update([
                'dibaca' => true,
                'updated_at' => now()
            ]);

        if (!$updated) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Disposisi tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Disposisi berhasil ditandai sebagai dibaca.'
        ]);
    }
}
