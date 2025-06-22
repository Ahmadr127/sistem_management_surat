<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SuratUnitManager extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'tbl_surat_unit_manager';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'isi_surat',
        'jenis_surat',
        'sifat_surat',
        'perusahaan',
        'file_path',
        'keterangan_unit',
        'keterangan_manager',
        'keterangan_sekretaris',
        'keterangan_dirut',
        'status_manager',
        'status_sekretaris',
        'status_dirut',
        'waktu_review_manager',
        'waktu_review_sekretaris',
        'waktu_review_dirut',
        'unit_id',
        'manager_id',
        'sekretaris_id',
        'dirut_id'
    ];

    /**
     * Atribut yang harus dikonversi menjadi tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_surat' => 'date',
        'waktu_review_manager' => 'datetime',
        'waktu_review_sekretaris' => 'datetime',
        'waktu_review_dirut' => 'datetime',
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
        'sifat_surat' => 'normal',
        'perusahaan' => 'RSAZRA',
        'status_manager' => 'pending',
        'status_sekretaris' => 'pending',
        'status_dirut' => 'pending'
    ];

    /**
     * Relasi dengan user unit (staff yang membuat surat)
     */
    public function unit()
    {
        return $this->belongsTo(User::class, 'unit_id');
    }

    /**
     * Relasi dengan manager
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Relasi dengan sekretaris
     */
    public function sekretaris()
    {
        return $this->belongsTo(User::class, 'sekretaris_id');
    }

    /**
     * Relasi dengan direktur
     */
    public function dirut()
    {
        return $this->belongsTo(User::class, 'dirut_id');
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
     * Accessor untuk mendapatkan URL file
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
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
     * Check if manager approval is pending
     */
    public function isManagerPending()
    {
        return $this->status_manager === 'pending';
    }

    /**
     * Check if manager has approved
     */
    public function isManagerApproved()
    {
        return $this->status_manager === 'approved';
    }

    /**
     * Check if manager has rejected
     */
    public function isManagerRejected()
    {
        return $this->status_manager === 'rejected';
    }

    /**
     * Check if secretary approval is pending
     */
    public function isSecretaryPending()
    {
        return $this->status_sekretaris === 'pending';
    }

    /**
     * Check if secretary has approved
     */
    public function isSecretaryApproved()
    {
        return $this->status_sekretaris === 'approved';
    }

    /**
     * Check if director approval is pending
     */
    public function isDirectorPending()
    {
        return $this->status_dirut === 'pending';
    }

    /**
     * Check if director has approved
     */
    public function isDirectorApproved()
    {
        return $this->status_dirut === 'approved';
    }

    /**
     * Get current approval status
     */
    public function getCurrentStatusAttribute()
    {
        if ($this->isManagerPending()) {
            return 'Menunggu Persetujuan Manager';
        } elseif ($this->isManagerRejected()) {
            return 'Ditolak Manager';
        } elseif ($this->isSecretaryPending()) {
            return 'Menunggu Persetujuan Sekretaris';
        } elseif ($this->isDirectorPending()) {
            return 'Menunggu Persetujuan Direktur';
        } elseif ($this->isDirectorApproved()) {
            return 'Disetujui';
        } else {
            return 'Ditolak';
        }
    }

    /**
     * Get current status color for UI
     */
    public function getStatusColorAttribute()
    {
        if ($this->isManagerPending() || $this->isSecretaryPending() || $this->isDirectorPending()) {
            return 'warning';
        } elseif ($this->isManagerRejected() || $this->status_sekretaris === 'rejected' || $this->status_dirut === 'rejected') {
            return 'danger';
        } elseif ($this->isDirectorApproved()) {
            return 'success';
        } else {
            return 'info';
        }
    }

    /**
     * Scope query untuk filter berdasarkan unit (staff)
     */
    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    /**
     * Scope query untuk filter berdasarkan manager
     */
    public function scopeByManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    /**
     * Scope query untuk filter berdasarkan status manager
     */
    public function scopeByStatusManager($query, $status)
    {
        return $query->where('status_manager', $status);
    }

    /**
     * Scope query untuk filter berdasarkan status sekretaris
     */
    public function scopeByStatusSekretaris($query, $status)
    {
        return $query->where('status_sekretaris', $status);
    }

    /**
     * Scope query untuk filter berdasarkan status direktur
     */
    public function scopeByStatusDirut($query, $status)
    {
        return $query->where('status_dirut', $status);
    }

    /**
     * Scope query untuk pencarian
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nomor_surat', 'like', "%{$search}%")
              ->orWhere('perihal', 'like', "%{$search}%")
              ->orWhere('isi_surat', 'like', "%{$search}%")
              ->orWhere('perusahaan', 'like', "%{$search}%");
        });
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
     * Boot method untuk model
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set manager_id based on unit's manager when creating
        static::creating(function ($surat) {
            if (!$surat->manager_id && $surat->unit_id) {
                $unit = User::find($surat->unit_id);
                if ($unit && $unit->manager_id) {
                    $surat->manager_id = $unit->manager_id;
                }
            }
        });
    }
} 