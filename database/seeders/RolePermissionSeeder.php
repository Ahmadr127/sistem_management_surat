<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all roles
        $staff = Role::where('name', 'staff')->first();
        $sekretaris = Role::where('name', 'sekretaris')->first();
        $direktur = Role::where('name', 'direktur')->first();
        $superAdmin = Role::where('name', 'super_admin')->first();
        $manager = Role::where('name', 'manager')->first();
        $sekretarisAsp = Role::where('name', 'sekretaris_asp')->first();
        $generalManager = Role::where('name', 'general_manager')->first();
        $managerKeuangan = Role::where('name', 'manager_keuangan')->first();
        $direkturAsp = Role::where('name', 'direktur_asp')->first();

        // Super Admin - Full Access
        if ($superAdmin) {
            $superAdmin->permissions()->sync(Permission::all());
        }

        // Staff/Unit (role 0)
        if ($staff) {
            $staff->permissions()->sync(Permission::whereIn('name', [
                'view_dashboard',
                'manage_surat_masuk',      // Can view surat masuk

                'create_surat_unit',        // Can create surat unit
                'view_own_surat',
                'view_arsip',               // Can view arsip
                'view_laporan',             // Can view laporan
            ])->pluck('id'));
        }

        // Sekretaris (role 1)
        if ($sekretaris) {
            $sekretaris->permissions()->sync(Permission::whereIn('name', [
                'view_dashboard',
                'manage_surat_masuk',       // Full access to surat masuk
                'manage_surat_keluar',      // Full access to surat keluar
                // 'approve_surat_unit',    // REMOVED: Can approve surat unit (sekretaris level)
                'generate_nomor_surat',     // Can generate nomor surat
                'view_laporan',
                'view_arsip',
            ])->pluck('id'));
        }

        // Direktur (role 2)
        if ($direktur) {
            $direktur->permissions()->sync(Permission::whereIn('name', [
                'view_dashboard',
                'manage_surat_masuk',       // Can view surat masuk
                'view_surat',
                'approve_disposisi',
                'view_laporan',
                'view_arsip',
            ])->pluck('id'));
        }

        // Manager (role 4)
        if ($manager) {
            $manager->permissions()->sync(Permission::whereIn('name', [
                'view_dashboard',
                'manage_surat_masuk',       // Can view surat masuk
                'manage_surat_keluar',      // Can manage surat keluar

                'approve_surat_unit',       // Can approve surat unit
                'view_surat',
                'view_laporan',
                'view_arsip',
            ])->pluck('id'));
        }

        // Sekretaris ASP (role 5)
        if ($sekretarisAsp) {
            $sekretarisAsp->permissions()->sync(Permission::whereIn('name', [
                'view_dashboard',
                'manage_surat_masuk',       // Can manage surat masuk
                'manage_surat_keluar',      // Can manage surat keluar
                'approve_surat_unit',       // Can approve surat unit (sekretaris level)
                'view_surat',
                'view_arsip',
                'view_laporan',
            ])->pluck('id'));
        }

        // General Manager (role 6)
        if ($generalManager) {
            $generalManager->permissions()->sync(Permission::whereIn('name', [
                'view_dashboard',
                'manage_surat_masuk',       // Can view surat masuk
                'manage_surat_keluar',      // Can manage surat keluar

                'approve_surat_unit',
                'manage_team',
                'view_surat',
                'view_laporan',
                'view_arsip',
            ])->pluck('id'));
        }

        // Manager Keuangan (role 7)
        if ($managerKeuangan) {
            $managerKeuangan->permissions()->sync(Permission::whereIn('name', [
                'view_dashboard',
                'manage_surat_masuk',       // Can view surat masuk
                'manage_surat_keluar',      // Can manage surat keluar

                'approve_surat_unit',       // Can approve surat unit
                'view_surat',
                'view_laporan',
                'view_arsip',
            ])->pluck('id'));
        }

        // Direktur ASP (role 8)
        if ($direkturAsp) {
            $direkturAsp->permissions()->sync(Permission::whereIn('name', [
                'view_dashboard',
                'manage_surat_masuk',       // Can view surat masuk
                'manage_surat_keluar',      // Can manage surat keluar

                'approve_surat_unit',
                'view_surat',
                'view_laporan',
                'view_arsip',
            ])->pluck('id'));
        }

        $this->command->info('Role permissions assigned successfully!');
    }
}
