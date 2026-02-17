<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'view_dashboard', 'display_name' => 'Lihat Dashboard', 'description' => 'Dapat melihat dashboard'],

            // User Management
            ['name' => 'manage_users', 'display_name' => 'Kelola Users', 'description' => 'Dapat mengelola users'],
            ['name' => 'manage_roles', 'display_name' => 'Kelola Roles', 'description' => 'Dapat mengelola roles'],
            ['name' => 'manage_permissions', 'display_name' => 'Kelola Permissions', 'description' => 'Dapat mengelola permissions'],

            // Organization Management
            ['name' => 'manage_organization_types', 'display_name' => 'Kelola Tipe Organisasi', 'description' => 'Dapat mengelola tipe organisasi'],
            ['name' => 'manage_organization_units', 'display_name' => 'Kelola Unit Organisasi', 'description' => 'Dapat mengelola unit organisasi'],

            // Master Data
            ['name' => 'manage_perusahaan', 'display_name' => 'Kelola Perusahaan', 'description' => 'Dapat mengelola data perusahaan'],
            ['name' => 'manage_jabatan', 'display_name' => 'Kelola Jabatan', 'description' => 'Dapat mengelola data jabatan'],

            // Surat Management
            ['name' => 'manage_surat_masuk', 'display_name' => 'Kelola Surat Masuk', 'description' => 'Dapat mengelola surat masuk'],
            ['name' => 'manage_surat_keluar', 'display_name' => 'Kelola Surat Keluar', 'description' => 'Dapat mengelola surat keluar'],
            ['name' => 'view_surat', 'display_name' => 'Lihat Surat', 'description' => 'Dapat melihat surat'],
            ['name' => 'create_surat_unit', 'display_name' => 'Buat Surat Unit', 'description' => 'Dapat membuat surat unit manager'],
            ['name' => 'view_own_surat', 'display_name' => 'Lihat Surat Sendiri', 'description' => 'Dapat melihat surat sendiri'],

            // Disposisi
            ['name' => 'manage_disposisi', 'display_name' => 'Kelola Disposisi', 'description' => 'Dapat mengelola disposisi'],
            ['name' => 'approve_disposisi', 'display_name' => 'Approve Disposisi', 'description' => 'Dapat menyetujui disposisi'],
            ['name' => 'manage_disposisi_assignments', 'display_name' => 'Kelola Aturan Disposisi', 'description' => 'Dapat mengelola aturan disposisi'],

            // Approval
            ['name' => 'approve_surat_unit', 'display_name' => 'Approve Surat Unit', 'description' => 'Dapat menyetujui surat unit manager'],
            ['name' => 'approve_surat', 'display_name' => 'Approve Surat', 'description' => 'Dapat menyetujui surat'],

            // Nomor Surat
            ['name' => 'generate_nomor_surat', 'display_name' => 'Generate Nomor Surat', 'description' => 'Dapat generate nomor surat'],

            // Laporan
            ['name' => 'view_laporan', 'display_name' => 'Lihat Laporan', 'description' => 'Dapat melihat laporan'],
            ['name' => 'export_laporan', 'display_name' => 'Export Laporan', 'description' => 'Dapat export laporan'],

            // Arsip
            ['name' => 'view_arsip', 'display_name' => 'Lihat Arsip', 'description' => 'Dapat melihat arsip'],

            // Team Management
            ['name' => 'manage_team', 'display_name' => 'Kelola Tim', 'description' => 'Dapat mengelola tim'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
