<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaans';

    protected $fillable = [
        'kode', 
        'nama_perusahaan', 
        'alamat', 
        'telepon', 
        'email', 
        'status'
    ];

    /**
     * Get all surat keluar for this perusahaan
     */
    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class, 'perusahaan', 'kode');
    }
    
    /**
     * Scope a query to only include active perusahaans.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }
}
