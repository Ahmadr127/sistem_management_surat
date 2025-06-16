<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluarFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_keluar_id',
        'file_path',
        'file_type',
        'original_name',
    ];

    public function suratKeluar()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_keluar_id');
    }
} 