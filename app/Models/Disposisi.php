<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'tbl_disposisi';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'surat_keluar_id',
        'status_sekretaris',
        'status_dirut',
        'keterangan_pengirim',
        'keterangan_sekretaris',
        'keterangan_dirut',
        'waktu_review_sekretaris',
        'waktu_review_dirut',
        'created_by'
    ];

    /**
     * Atribut yang harus dikonversi menjadi tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'waktu_review_sekretaris' => 'datetime',
        'waktu_review_dirut' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi dengan surat keluar
     */
    public function suratKeluar()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_keluar_id');
    }

    /**
     * Relasi dengan user (tujuan disposisi) - many-to-many
     */
    public function tujuan()
    {
        return $this->belongsToMany(User::class, 'tbl_disposisi_user', 'disposisi_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Relasi dengan user pembuat disposisi
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Boot method untuk model
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set created_by when creating new record
        static::creating(function ($disposisi) {
            if (!$disposisi->created_by && auth()->check()) {
                $disposisi->created_by = auth()->id();
            }
        });
    }
}
