<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SuratUnitManagerFile extends Model
{
    use HasFactory;

    protected $table = 'surat_unit_manager_files';

    protected $fillable = [
        'surat_unit_manager_id',
        'file_path',
        'original_name',
        'file_type',
        'file_size'
    ];

    /**
     * Relasi dengan surat unit manager
     */
    public function suratUnitManager()
    {
        return $this->belongsTo(SuratUnitManager::class, 'surat_unit_manager_id');
    }

    /**
     * Accessor untuk mendapatkan URL file
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return asset($this->file_path);
        }
        return null;
    }

    /**
     * Accessor untuk mendapatkan ukuran file yang diformat
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Accessor untuk mendapatkan icon berdasarkan tipe file
     */
    public function getFileIconAttribute()
    {
        $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return 'ri-file-pdf-line text-red-500';
            case 'doc':
            case 'docx':
                return 'ri-file-word-line text-blue-500';
            case 'xls':
            case 'xlsx':
                return 'ri-file-excel-line text-green-500';
            case 'ppt':
            case 'pptx':
                return 'ri-file-ppt-line text-orange-500';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'ri-image-line text-purple-500';
            case 'zip':
            case 'rar':
                return 'ri-file-zip-line text-yellow-500';
            default:
                return 'ri-file-line text-gray-500';
        }
    }

    /**
     * Check if file is image
     */
    public function isImage()
    {
        $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
    }

    /**
     * Check if file is PDF
     */
    public function isPdf()
    {
        $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
        return $extension === 'pdf';
    }

    /**
     * Delete file from storage when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            if ($file->file_path && file_exists(public_path($file->file_path))) {
                unlink(public_path($file->file_path));
            }
        });
    }
} 