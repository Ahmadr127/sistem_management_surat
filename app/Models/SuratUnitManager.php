<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SuratUnitManager extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_surat_unit_manager';

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

    protected $attributes = [
        'jenis_surat' => 'internal',
        'sifat_surat' => 'normal',
        'perusahaan' => 'RSAZRA',
        'status_manager' => 'pending',
        'status_sekretaris' => 'pending',
        'status_dirut' => 'pending'
    ];

    public function unit()
    {
        return $this->belongsTo(User::class, 'unit_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function sekretaris()
    {
        return $this->belongsTo(User::class, 'sekretaris_id');
    }

    public function dirut()
    {
        return $this->belongsTo(User::class, 'dirut_id');
    }

    public function perusahaanData()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan', 'kode');
    }

    public function files()
    {
        return $this->hasMany(SuratUnitManagerFile::class, 'surat_unit_manager_id');
    }

    public function getNamaPerusahaanAttribute()
    {
        return $this->perusahaanData ? $this->perusahaanData->nama_perusahaan : $this->perusahaan;
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    public function getFormattedNomorSuratAttribute()
    {
        return strtoupper($this->nomor_surat);
    }

    public function isManagerPending()
    {
        return $this->status_manager === 'pending';
    }

    public function isManagerApproved()
    {
        return $this->status_manager === 'approved';
    }

    public function isManagerRejected()
    {
        return $this->status_manager === 'rejected';
    }

    public function isSecretaryPending()
    {
        return $this->status_sekretaris === 'pending';
    }

    public function isSecretaryApproved()
    {
        return $this->status_sekretaris === 'approved';
    }

    public function isDirectorPending()
    {
        return $this->status_dirut === 'pending';
    }

    public function isDirectorApproved()
    {
        return $this->status_dirut === 'approved';
    }

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

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeByManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    public function scopeByStatusManager($query, $status)
    {
        return $query->where('status_manager', $status);
    }

    public function scopeByStatusSekretaris($query, $status)
    {
        return $query->where('status_sekretaris', $status);
    }

    public function scopeByStatusDirut($query, $status)
    {
        return $query->where('status_dirut', $status);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nomor_surat', 'like', "%{$search}%")
              ->orWhere('perihal', 'like', "%{$search}%")
              ->orWhere('isi_surat', 'like', "%{$search}%")
              ->orWhere('perusahaan', 'like', "%{$search}%");
        });
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

    public function scopeForUserApproval($query, $user)
    {
        return $query->where(function($q) use ($user) {
            // 1. Approval sebagai Manager Unit (Data-driven: manager_id)
            if ($user->hasPermission('approve_surat_unit')) {
                $q->orWhere(function($sub) use ($user) {
                    $sub->where('manager_id', $user->id)
                        ->where('status_manager', 'pending');
                });
            }



            // 3. Approval sebagai Sekretaris (Permission-driven: manage_surat_keluar + approve_surat_unit)
            if ($user->hasPermission('approve_surat_unit') && $user->hasPermission('manage_surat_keluar')) {
                $q->orWhere(function($sub) {
                    $sub->where('status_manager', 'approved')
                        ->where('status_sekretaris', 'pending');
                });
            }

            // 4. Approval sebagai Direktur (Permission-driven: approve_disposisi)
            if ($user->hasPermission('approve_disposisi')) {
                $q->orWhere(function($sub) {
                    $sub->where('status_sekretaris', 'approved')
                        ->where('status_dirut', 'pending');
                });
            }
            
            // 5. Super Admin (manage_users)
            if ($user->hasPermission('manage_users')) {
                // Tampilkan semua pending
                $q->orWhere('status_manager', 'pending')

                  ->orWhere(function($sub) {
                      $sub->where('status_manager', 'approved')
                          ->where('status_sekretaris', 'pending');
                  })
                  ->orWhere(function($sub) {
                      $sub->where('status_sekretaris', 'approved')
                          ->where('status_dirut', 'pending');
                  });
            }
        });
    }

    public function scopeForUserApproved($query, $user)
    {
        return $query->where(function($q) use ($user) {
            if ($user->hasPermission('approve_surat_unit')) {
                $q->orWhere(function($sub) use ($user) {
                    $sub->where('manager_id', $user->id)->where('status_manager', 'approved');
                });
            }
            if ($user->hasPermission('approve_surat_unit') && $user->hasPermission('manage_surat_keluar')) {
                $q->orWhere('status_sekretaris', 'approved');
            }
            if ($user->hasPermission('approve_disposisi')) {
                $q->orWhere('status_dirut', 'approved');
            }
            if ($user->hasPermission('manage_users')) {
                $q->orWhereRaw('1=1');
            }
        });
    }

    public function scopeForUserRejected($query, $user)
    {
        return $query->where(function($q) use ($user) {
            if ($user->hasPermission('approve_surat_unit')) {
                $q->orWhere(function($sub) use ($user) {
                    $sub->where('manager_id', $user->id)->where('status_manager', 'rejected');
                });
            }
            if ($user->hasPermission('approve_surat_unit') && $user->hasPermission('manage_surat_keluar')) {
                $q->orWhere('status_sekretaris', 'rejected');
            }
            if ($user->hasPermission('approve_disposisi')) {
                $q->orWhere('status_dirut', 'rejected');
            }
            if ($user->hasPermission('manage_users')) {
                $q->orWhereRaw('1=1');
            }
        });
    }
    
    public function scopeSuratUnitForUser($query, $user)
    {
        return $query->where(function($q) use ($user) {
            // 1. Unit pembuat (unit_id)
            $q->where('unit_id', $user->id);
            
            // 2. Manager unit (manager_id)
            $q->orWhere('manager_id', $user->id);

            // 3. Permission-based View All
            if (($user->hasPermission('approve_surat_unit') && $user->hasPermission('manage_surat_keluar')) ||
                $user->hasPermission('approve_disposisi') ||
                $user->hasPermission('manage_users')) {
                $q->orWhereRaw('1=1');
            }
        });
    }

    protected static function boot()
    {
        parent::boot();

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
