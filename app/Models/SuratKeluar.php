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
    /**
     * Scope untuk Surat Masuk dengan logic yang clean dan dynamic
     */
    public function scopeForSuratMasuk($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            // Definisi logic filter berdasarkan role
            // Bisa dipindahkan ke config atau database jika ingin lebih dinamis
            $filters = [
                // Sekretaris (Role 1): Melihat semua surat
                1 => fn($q) => $q->whereRaw('1=1'),
                
                // Direktur (Role 2): Surat yang sudah disetujui Sekretaris
                2 => fn($q) => $q->whereHas('disposisi', fn($d) => $d->where('status_sekretaris', 'approved')),
                
                // Direktur ASP (Role 8): Surat yang sudah disetujui Sekretaris ASP (asumsi)
                8 => fn($q) => $q->whereHas('disposisi', fn($d) => $d->where('status_sekretaris_asp', 'approved')),
            ];

            // Default filter untuk Manager (4, 7, 6) dan Staff (0)
            // Menampilkan surat yang didisposisikan ke Unit mereka
            $defaultFilter = fn($q) => $q->whereHas('disposisi.tujuan', function ($t) use ($user) {
                if ($user->organization_unit_id) {
                    $t->where('organization_unit_id', $user->organization_unit_id);
                } else {
                    // Fallback jika user tidak punya unit, cek by ID
                    $t->where('users.id', $user->id);
                }
            });

            // Eksekusi filter yang sesuai
            $filter = $filters[$user->role] ?? $defaultFilter;
            $filter($q);
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
