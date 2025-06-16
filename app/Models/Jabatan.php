<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'tbl_jabatan';
    
    protected $fillable = [
        'nama_jabatan',
        'status'
    ];

    // Accessor untuk format status
    public function getStatusFormattedAttribute()
    {
        return $this->status === 'aktif' ? 
            '<span class="px-2 py-1 text-xs font-medium text-green-600 bg-green-100 rounded-full">Aktif</span>' : 
            '<span class="px-2 py-1 text-xs font-medium text-red-600 bg-red-100 rounded-full">Nonaktif</span>';
    }

    // Relasi ke users
    public function users()
    {
        return $this->hasMany(User::class, 'jabatan_id', 'id');
    }

    // Scope untuk jabatan aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
