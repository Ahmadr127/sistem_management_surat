<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisposisiTujuan extends Model
{
    protected $table = 'disposisi_tujuan';
    protected $fillable = ['disposisi_id', 'user_id', 'dibaca'];
    
    public function disposisi()
    {
        return $this->belongsTo(Disposisi::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 