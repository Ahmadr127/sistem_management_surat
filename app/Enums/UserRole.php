<?php

namespace App\Enums;

enum UserRole: int
{
    case STAFF = 0;
    case SEKRETARIS = 1;
    case DIREKTUR = 2;
    case ADMIN = 3;

    /**
     * Get role name
     */
    public function getName(): string
    {
        return match($this) {
            self::STAFF => 'Staff',
            self::SEKRETARIS => 'Sekretaris',
            self::DIREKTUR => 'Direktur',
            self::ADMIN => 'Admin',
        };
    }

    /**
     * Get role description
     */
    public function getDescription(): string
    {
        return match($this) {
            self::STAFF => 'Staff biasa yang dapat membuat dan mengelola surat sendiri',
            self::SEKRETARIS => 'Sekretaris yang dapat mereview dan menyetujui surat',
            self::DIREKTUR => 'Direktur yang dapat menyetujui surat final',
            self::ADMIN => 'Administrator sistem dengan akses penuh',
        };
    }

    /**
     * Get role permissions
     */
    public function getPermissions(): array
    {
        return match($this) {
            self::STAFF => [
                'create_surat',
                'edit_own_surat',
                'delete_own_surat',
                'view_surat'
            ],
            self::SEKRETARIS => [
                'create_surat',
                'edit_own_surat',
                'delete_own_surat',
                'view_surat',
                'review_surat',
                'approve_surat_sekretaris',
                'edit_disposisi_sekretaris'
            ],
            self::DIREKTUR => [
                'create_surat',
                'edit_own_surat',
                'delete_own_surat',
                'view_surat',
                'view_all_surat',
                'approve_surat_direktur',
                'edit_disposisi_direktur',
                'assign_disposisi'
            ],
            self::ADMIN => [
                'create_surat',
                'edit_any_surat',
                'delete_any_surat',
                'view_surat',
                'view_all_surat',
                'manage_users',
                'manage_roles',
                'manage_settings',
                'view_reports'
            ],
        };
    }

    /**
     * Check if role has permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getPermissions());
    }

    /**
     * Get all roles as array
     */
    public static function toArray(): array
    {
        return [
            self::STAFF->value => self::STAFF->getName(),
            self::SEKRETARIS->value => self::SEKRETARIS->getName(),
            self::DIREKTUR->value => self::DIREKTUR->getName(),
            self::ADMIN->value => self::ADMIN->getName(),
        ];
    }
}
