<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
// Do NOT use SoftDeletes
// use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    // Do not use SoftDeletes

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'role_id',
        'organization_unit_id',
        'jabatan_id',
        'manager_id',
        'general_manager_id',
        'status_akun',
        'foto_profile',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
    }

    /**
     * Relasi ke manager (self-referencing)
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Relasi ke staff yang dibawahi (self-referencing)
     */
    public function staff()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /**
     * Relasi ke manager yang dibawahi general manager
     */
    public function managers()
    {
        return $this->hasMany(User::class, 'general_manager_id');
    }

    /**
     * Relasi ke general manager (self-referencing)
     */
    public function generalManager()
    {
        return $this->belongsTo(User::class, 'general_manager_id');
    }

    /**
     * Relasi ke semua staff yang dibawahi (termasuk melalui manager)
     */
    public function allStaff()
    {
        return $this->hasMany(User::class, 'manager_id')
                    ->with('staff'); // Include nested staff
    }

    /**
     * NEW PERMISSION SYSTEM RELATIONSHIPS
     */

    /**
     * Get the role of this user (new permission system)
     */
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the organization unit of this user
     */
    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    /**
     * Check if user has a specific permission
     * Works with both new permission system and legacy role system
     */
    public function hasPermission($permission)
    {
        // If using new permission system (has role_id)
        if ($this->role_id && $this->roleModel) {
            return $this->roleModel->hasPermission($permission);
        }

        // Fallback to legacy role-based permissions
        return $this->hasLegacyPermission($permission);
    }

    /**
     * Legacy permission check based on integer role
     */
    private function hasLegacyPermission($permission)
    {
        // Map permissions to legacy roles
        $permissionMap = [
            'view_dashboard' => [0, 1, 2, 3, 4, 5, 6, 7, 8],
            'manage_users' => [3],
            'manage_roles' => [3],
            'manage_permissions' => [3],
            'manage_organization_types' => [3],
            'manage_organization_units' => [3],
            'manage_perusahaan' => [1, 3, 5],
            'manage_jabatan' => [3],
            'manage_surat' => [1, 3, 4, 6, 7, 8],
            'approve_surat' => [2, 4, 7, 8],
            'view_laporan' => [1, 2, 3, 4, 5, 6, 7, 8],
        ];

        if (isset($permissionMap[$permission])) {
            return in_array($this->role, $permissionMap[$permission]);
        }

        return false;
    }

    /**
     * Get all permissions for this user
     */
    public function getAllPermissions()
    {
        if ($this->role_id && $this->roleModel) {
            return $this->roleModel->permissions;
        }

        return collect();
    }

    // Tambahkan accessor untuk memudahkan pengambilan nama jabatan
    public function getJabatanNameAttribute()
    {
        return $this->jabatan ? $this->jabatan->nama_jabatan : 'Tidak ada jabatan';
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto_profile) {
            return Storage::url($this->foto_profile);
        }
        return asset('images/default-avatar.png');
    }

    /**
     * Check if user is manager
     */
    public function isManager()
    {
        return $this->role === 4;
    }

    /**
     * Check if user is staff
     */
    public function isStaff()
    {
        return $this->role === 0;
    }

    /**
     * Check if user is secretary
     */
    public function isSecretary()
    {
        return $this->role === 1;
    }

    /**
     * Check if user is director
     */
    public function isDirector()
    {
        return $this->role === 2;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 3;
    }

    /**
     * Check if user is sekretaris ASP
     */
    public function isSekretarisAsp()
    {
        return $this->role === 5;
    }

    /**
     * Check if user is general manager
     */
    public function isGeneralManager()
    {
        return $this->role === 6;
    }

    // /**
    //  * Check if user is direktur administrasi keuangan
    //  */
    // public function isDirekturAdmKeuangan()
    // {
    //     return $this->role === 7;
    // }

    /**
     * Check if user is manager keuangan
     */
    public function isManagerKeuangan()
    {
        return $this->role === 7;
    }

    /**
     * Check if user is direktur ASP
     */
    public function isDirekturAsp()
    {
        return $this->role === 8;
    }

    /**
     * Check if manager is connected to general manager
     */
    public function isConnectedToGeneralManager()
    {
        return ($this->role === 4 || $this->role === 7) && $this->general_manager_id !== null;
    }

    /**
     * Check if manager is independent (not connected to general manager)
     */
    public function isIndependentManager()
    {
        return ($this->role === 4 || $this->role === 7) && $this->general_manager_id === null;
    }

    /**
     * Get all managers under this general manager
     */
    public function getConnectedManagers()
    {
        return $this->hasMany(User::class, 'general_manager_id')
                    ->whereIn('role', [4, 7]); // Include both Manager and Manager Keuangan
    }

    /**
     * Get all independent managers (not connected to any general manager)
     */
    public static function getIndependentManagers()
    {
        return self::whereIn('role', [4, 7]) // Include both Manager and Manager Keuangan
                   ->whereNull('general_manager_id')
                   ->where('status_akun', 'aktif');
    }

    /**
     * Get role name
     */
    public function getRoleNameAttribute()
    {
        $roles = [
            0 => 'Staff',
            1 => 'Sekretaris',
            2 => 'Direktur',
            3 => 'Admin',
            4 => 'Manager',
            5 => 'Sekretaris ASP',
            6 => 'General Manager',
            7 => 'Manager Keuangan',
            8 => 'Direktur ASP'
        ];
        return $roles[$this->role] ?? 'Unknown';
    }

    // Tambahkan relasi untuk surat keluar yang ditujukan ke user ini
    public function suratKeluarDituju()
    {
        return $this->hasMany(SuratKeluar::class, 'tujuan');
    }
}
