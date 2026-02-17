<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposisiAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_role',
        'source_user_id',
        'target_role',
    ];

    /**
     * Map role ID to role name or get User name
     */
    public static function getSourceName($sourceRole, $sourceUserId = null)
    {
        if ($sourceUserId) {
            $user = User::find($sourceUserId);
            return $user ? $user->name . ' (User)' : 'Unknown User';
        }
        
        if (!is_null($sourceRole)) {
            $role = Role::where('legacy_role_id', $sourceRole)->first();
            return $role ? $role->display_name : 'Unknown Role';
        }

        return '-';
    }

    public static function getRoleName($roleId)
    {
        $role = Role::where('legacy_role_id', $roleId)->first();
        return $role ? $role->display_name : 'Unknown Role';
    }
    
    /**
     * Get all available roles as array
     */
    public static function getRoles()
    {
        return Role::pluck('display_name', 'legacy_role_id')->toArray();
    }

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }
}
