<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratUnitManagerHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_unit_manager_id',
        'user_id',
        'action',
        'keterangan',
        'ip_address',
        'user_agent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surat()
    {
        return $this->belongsTo(SuratUnitManager::class, 'surat_unit_manager_id');
    }
}
