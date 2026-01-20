<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_surat_keluar';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'perusahaan',
        'jenis_surat',
        'sifat_surat',
        'created_by'
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    // Relasi
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function disposisi()
    {
        return $this->hasOne(Disposisi::class, 'surat_keluar_id');
    }

    public function perusahaanData()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan', 'kode');
    }

    public function files()
    {
        return $this->hasMany(SuratKeluarFile::class, 'surat_keluar_id');
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nomor_surat', 'like', "%{$search}%")
              ->orWhere('perihal', 'like', "%{$search}%");
        });
    }

    public function scopeByPerusahaan($query, $kode)
    {
        return $query->where('perusahaan', $kode);
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_surat', [$start, $end]);
    }

    public function scopeByJenisSurat($query, $jenis)
    {
        return $query->where('jenis_surat', $jenis);
    }

    public function scopeBySifatSurat($query, $sifat)
    {
        return $query->where('sifat_surat', $sifat);
    }

    /**
     * Scope untuk Surat Masuk (Surat Keluar yang didisposisikan ke user)
     */
    public function scopeSuratMasukForUser($query, $user)
    {
        return $query->where(function($q) use ($user) {
            // Kondisi 1: Ditujukan ke user
            $q->whereHas('disposisi.tujuan', function($sub) use ($user) {
                $sub->where('users.id', $user->id);
            });

            // Kondisi 2: Dibuat oleh user (jika sudah ada disposisi approved?)
            $q->orWhere(function($sub) use ($user) {
                $sub->where('created_by', $user->id)
                    ->whereHas('disposisi', function($disp) {
                        $disp->where('status_dirut', 'approved');
                    });
            });

            // Kondisi 3: Permission-based View All
            // Sekretaris (generate_nomor_surat)
            if ($user->hasPermission('generate_nomor_surat')) {
                $q->orWhereRaw('1=1'); // View All
            }
            
            // Direktur (approve_disposisi)
            if ($user->hasPermission('approve_disposisi')) {
                 $q->orWhereHas('disposisi', function($sub) {
                     $sub->where('status_sekretaris', 'approved');
                 });
            }
            
            // Admin (manage_users)
            if ($user->hasPermission('manage_users')) {
                $q->orWhereRaw('1=1');
            }
        });
    }

    /**
     * Scope untuk Surat Keluar (Management)
     */
    public function scopeSuratKeluarForUser($query, $user)
    {
        return $query->where(function($q) use ($user) {
            $q->where('created_by', $user->id);

            // Sekretaris (generate_nomor_surat) & Direktur (approve_disposisi) & Admin (manage_users) -> View All
            if ($user->hasPermission('generate_nomor_surat') || 
                $user->hasPermission('approve_disposisi') || 
                $user->hasPermission('manage_users')) {
                $q->orWhereRaw('1=1');
            }
        });
    }
}
