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

    /**
     * Get role display name (Helper)
     */
    public function getRoleDisplayNameAttribute()
    {
        // If using new permission system
        if ($this->roleModel) {
            return $this->roleModel->display_name;
        }
        
        // Fallback for legacy roles
        $roles = [
            0 => 'Staff',
            1 => 'Sekretaris',
            2 => 'Direktur Utama',
            3 => 'Admin',
            4 => 'Manager',
            5 => 'Sekretaris ASP',
            6 => 'General Manager',
            7 => 'Manager Keuangan',
            8 => 'Direktur ASP'
        ];
        return $roles[$this->role] ?? 'User';
    }

    /**
     * Generate Jabatan Name dynamically from Role + Organization
     * Replaces the need for tbl_jabatan
     */
    public function getJabatanNameAttribute()
    {
        // 1. Get Base Role Name
        $roleName = $this->role_display_name;
        
        // 2. Get Organization Name
        $orgName = '';
        if ($this->organizationUnit) {
            $orgName = $this->organizationUnit->name;
            
            // Cleanup common prefixes to make it sound natural
            // "Departemen IT" -> "IT"
            // "Unit Support" -> "Support"
            $prefixes = ['Departemen ', 'Unit ', 'Direktorat ', 'PT '];
            $orgName = str_replace($prefixes, '', $orgName);
        }

        // 3. Combine Logic
        
        // Special Cases
        if ($this->role === 2) return 'Direktur Utama'; // Always Dirut
        if ($this->role === 1) return 'Sekretaris Perusahaan';
        if ($this->role === 6) return 'General Manager';
        if ($this->role === 8) return 'Direktur ASP';
        
        // General Cases: "Manager IT", "Staff HR", "Admin IT"
        if ($orgName) {
            return "$roleName $orgName";
        }
        
        return $roleName;
    }

    /**
     * Get role name (Legacy accessor, kept for compatibility)
     */
    public function getRoleNameAttribute()
    {
        return $this->role_display_name;
    }

    // Tambahkan relasi untuk surat keluar yang ditujukan ke user ini
    public function suratKeluarDituju()
    {
        return $this->hasMany(SuratKeluar::class, 'tujuan');
    }
}
