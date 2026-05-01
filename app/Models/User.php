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

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    // Do not use SoftDeletes

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nik',
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
        // 1. Check if user has explicit role_id assigned (New System)
        if ($this->role_id && $this->roleModel) {
            return $this->roleModel->hasPermission($permission);
        }

        // 2. Fallback: Find role based on legacy integer 'role' column
        // We use the Role model that matches the legacy_role_id
        $legacyRole = Role::where('legacy_role_id', $this->role)->first();
        if ($legacyRole) {
            return $legacyRole->hasPermission($permission);
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

        $legacyRole = Role::where('legacy_role_id', $this->role)->first();
        if ($legacyRole) {
            return $legacyRole->permissions;
        }

        return collect();
    }

    /**
     * Get role display name (Helper)
     */
    public function getRoleDisplayNameAttribute()
    {
        // 1. Try new role system
        if ($this->roleModel) {
            return $this->roleModel->display_name;
        }
        
        // 2. Fallback to legacy role mapping via database
        $legacyRole = Role::where('legacy_role_id', $this->role)->first();
        return $legacyRole ? $legacyRole->display_name : 'User';
    }

    /**
     * Generate Jabatan Name dynamically from Role + Organization
     * Replaces the need for tbl_jabatan
     */
    public function getJabatanNameAttribute()
    {
        // 1. Get Base Role Name (dynamically from database)
        $roleName = $this->role_display_name;
        
        // 2. Get Organization Name
        $orgName = '';
        if ($this->organizationUnit) {
            $orgName = $this->organizationUnit->name;
            
            // Cleanup common prefixes to make it sound natural
            $prefixes = ['Departemen ', 'Unit ', 'Direktorat ', 'PT '];
            $orgName = str_replace($prefixes, '', $orgName);
        }

        // 3. Combine Logic
        
        // Handle cases where the role name already implies the position fully (e.g. Direktur Utama)
        $fullyQualifiedRoles = ['Direktur Utama', 'General Manager', 'Direktur ASP', 'Sekretaris Perusahaan'];
        if (in_array($roleName, $fullyQualifiedRoles)) {
            return $roleName;
        }
        
        // General Cases: "Manager IT", "Staff HR", "Admin IT"
        if ($orgName && $roleName !== 'User') {
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

    /**
     * Relasi dengan disposisi yang dibuat oleh user ini
     */
    public function disposisiDibuat()
    {
        return $this->hasMany(Disposisi::class, 'created_by');
    }

    /**
     * Relasi dengan disposisi yang ditujukan ke user ini
     */
    public function disposisiDiterima()
    {
        return $this->belongsToMany(Disposisi::class, 'tbl_disposisi_user', 'user_id', 'disposisi_id');
    }

    /**
     * BLOCKER RELATIONSHIPS (No cascade in DB)
     */

    public function suratKeluarDibuat()
    {
        return $this->hasMany(SuratKeluar::class, 'created_by');
    }

    public function suratUnitDibuat()
    {
        return $this->hasMany(SuratUnitManager::class, 'unit_id');
    }

    public function suratUnitDimanajeri()
    {
        return $this->hasMany(SuratUnitManager::class, 'manager_id');
    }
}
