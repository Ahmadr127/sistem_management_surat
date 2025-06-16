<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposisiComment extends Model
{
    use HasFactory;

    // Nama tabel yang terkait dengan model ini
    protected $table = 'tbl_disposisi_comments';

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'disposisi_id',
        'user_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relasi ke model Disposisi
     */
    public function disposisi()
    {
        return $this->belongsTo(Disposisi::class);
    }

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
