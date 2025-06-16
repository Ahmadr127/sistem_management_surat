<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SuratKeluar extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'tbl_surat_keluar';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'file_path',
        'jenis_surat',
        'sifat_surat',
        'created_by',
        'perusahaan'
    ];

    /**
     * Atribut yang harus dikonversi menjadi tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_surat' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'jenis_surat' => 'string',
        'sifat_surat' => 'string'
    ];

    /**
     * Nilai default untuk atribut.
     *
     * @var array
     */
    protected $attributes = [
        'jenis_surat' => 'internal',
        'sifat_surat' => 'normal'
    ];

    /**
     * Relasi dengan user yang membuat surat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi dengan disposisi
     */
    public function disposisi()
    {
        return $this->hasOne(Disposisi::class, 'surat_keluar_id');
    }

    /**
     * Relasi dengan perusahaan
     */
    public function perusahaanData()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan', 'kode');
    }

    /**
     * Accessor untuk mendapatkan nama perusahaan
     */
    public function getNamaPerusahaanAttribute()
    {
        return $this->perusahaanData ? $this->perusahaanData->nama_perusahaan : $this->perusahaan;
    }

    /**
     * Accessor untuk mendapatkan semua tujuan (user) dari disposisi surat ini
     */
    public function getTujuanAttribute()
    {
        $disposisiCollection = $this->disposisi()->with('tujuan')->get();
        $users = collect();
        
        foreach ($disposisiCollection as $disposisi) {
            $users = $users->merge($disposisi->tujuan);
        }
        
        return $users->unique('id');
    }

    /**
     * Accessor untuk mendapatkan URL file
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            $url = Storage::url($this->file_path);
            \Log::info('File URL generated: ' . $url);
            return $url;
        }
        \Log::warning('No file path found for surat keluar ID: ' . $this->id);
        return null;
    }

    /**
     * Accessor untuk mendapatkan format nomor surat yang rapi
     */
    public function getFormattedNomorSuratAttribute()
    {
        return strtoupper($this->nomor_surat);
    }

    /**
     * Scope query untuk filter berdasarkan perusahaan
     */
    public function scopeByPerusahaan($query, $perusahaan)
    {
        // If numeric or less than 20 chars, assume it's a code, otherwise a name
        if (is_numeric($perusahaan) || strlen($perusahaan) <= 20) {
        return $query->where('perusahaan', $perusahaan);
        } else {
            // Otherwise assume it's a name and join with perusahaan table
            return $query->whereHas('perusahaanData', function($q) use ($perusahaan) {
                $q->where('nama_perusahaan', 'like', "%{$perusahaan}%");
            });
        }
    }

    /**
     * Scope query untuk filter berdasarkan tanggal
     */
    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_surat', [$start, $end]);
    }

    /**
     * Scope query untuk filter berdasarkan jenis surat
     */
    public function scopeByJenisSurat($query, $jenis)
    {
        return $query->where('jenis_surat', $jenis);
    }

    /**
     * Scope query untuk filter berdasarkan sifat surat
     */
    public function scopeBySifatSurat($query, $sifat)
    {
        return $query->where('sifat_surat', $sifat);
    }
    
    /**
     * Scope query untuk filter berdasarkan status disposisi 
     */
    public function scopeByStatusSekretaris($query, $status)
    {
        return $query->whereHas('disposisi', function($q) use ($status) {
            $q->where('status_sekretaris', $status);
        });
    }
    
    /**
     * Scope query untuk filter berdasarkan status direktur
     */
    public function scopeByStatusDirut($query, $status)
    {
        return $query->whereHas('disposisi', function($q) use ($status) {
            $q->where('status_dirut', $status);
        });
    }

    /**
     * Scope query untuk pencarian
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nomor_surat', 'like', "%{$search}%")
              ->orWhere('perihal', 'like', "%{$search}%")
              ->orWhere('perusahaan', 'like', "%{$search}%")
              ->orWhereHas('perusahaanData', function($subquery) use ($search) {
                  $subquery->where('nama_perusahaan', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Boot method untuk model
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set created_by when creating new record
        static::creating(function ($suratKeluar) {
            if (!$suratKeluar->created_by && auth()->check()) {
                $suratKeluar->created_by = auth()->id();
            }
        });
    }
    
    /**
     * Method helper untuk cek apakah surat sudah memiliki disposisi
     */
    public function hasDisposisi()
    {
        return $this->disposisi()->exists();
    }
    
    /**
     * Method helper untuk mendapatkan status terkini surat
     */
    public function getLatestStatus()
    {
        if (!$this->hasDisposisi()) {
            return [
                'sekretaris' => 'belum_disposisi',
                'dirut' => 'belum_disposisi'
            ];
        }
        
        $latestDisposisi = $this->disposisi()->latest()->first();
        
        return [
            'sekretaris' => $latestDisposisi->status_sekretaris,
            'dirut' => $latestDisposisi->status_dirut
        ];
    }

    /**
     * Relasi dengan file-file surat
     */
    public function files()
    {
        return $this->hasMany(SuratKeluarFile::class, 'surat_keluar_id');
    }
}
