<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CleanUsersNoDisposisiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:clean-no-disposisi
                            {--force : Force deletion without confirmation}
                            {--dry-run : Only list users that would be deleted}
                            {--diagnose : Show WHY each user cannot be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus user yang tidak memiliki relasi disposisi dan aman untuk dihapus dari database';

    /**
     * Daftar semua tabel + kolom yang mereferensikan tabel users (tanpa onDelete cascade).
     * Ini adalah "blocker" — user yang direferensikan di sini TIDAK boleh dihapus.
     *
     * Menggunakan raw DB query (bukan Eloquent whereDoesntHave) agar SoftDeletes tidak
     * menyembunyikan baris yang masih memegang foreign key di database.
     */
    protected array $blockers = [
        ['table' => 'tbl_disposisi',          'column' => 'created_by',  'label' => 'Membuat Disposisi'],
        ['table' => 'tbl_surat_keluar',        'column' => 'created_by',  'label' => 'Membuat Surat Keluar'],
        ['table' => 'tbl_surat_unit_manager',  'column' => 'unit_id',     'label' => 'Unit di Surat Unit'],
        ['table' => 'tbl_surat_unit_manager',  'column' => 'manager_id',  'label' => 'Manager di Surat Unit'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // --diagnose: tampilkan user yang TIDAK bisa dihapus beserta alasannya
        if ($this->option('diagnose')) {
            return $this->runDiagnose();
        }

        // Bangun subquery untuk setiap blocker menggunakan raw DB
        // Hasilnya: ID user yang terlibat di salah satu tabel blocker
        $blockedIds = $this->getBlockedUserIds();

        // ID user yang memiliki role yang terdefinisi di tabel roles (dikecualikan dari penghapusan)
        $roleExcludedIds = $this->getRoleExcludedUserIds();

        // Query: ambil user yang bukan admin, bukan blocked, dan bukan memiliki role aktif
        $query = User::where('username', '!=', 'admin')
                     ->whereNotIn('id', $blockedIds)
                     ->whereNotIn('id', $roleExcludedIds);

        $count = $query->count();

        if ($count === 0) {
            $this->info('Tidak ada user yang dapat dihapus.');
            return 0;
        }

        $users = $query->get(['id', 'name', 'username', 'role']);

        $this->info("Ditemukan {$count} user yang aman untuk dihapus:");
        $this->table(
            ['ID', 'Name', 'Username', 'Role'],
            $users->map(fn($u) => [$u->id, $u->name, $u->username, $u->role])->toArray()
        );

        if ($this->option('dry-run')) {
            $this->comment('Dry run: tidak ada user yang dihapus.');
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("Apakah Anda yakin ingin menghapus {$count} user ini?", false)) {
                $this->comment('Operasi dibatalkan.');
                return 0;
            }
        }

        $this->info('Menghapus user...');

        try {
            DB::beginTransaction();
            $userIds = $users->pluck('id');
            User::whereIn('id', $userIds)->delete();
            DB::commit();
            $this->info("Berhasil menghapus {$count} user.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Gagal menghapus user: ' . $e->getMessage());
            $this->comment('Tip: Jalankan dengan --diagnose untuk melihat user yang tidak bisa dihapus.');
            return 1;
        }

        return 0;
    }

    /**
     * Dapatkan semua ID user yang tidak boleh dihapus karena masih
     * direferensikan di salah satu tabel blocker (termasuk soft-deleted rows).
     */
    protected function getBlockedUserIds(): array
    {
        $blocked = collect();

        foreach ($this->blockers as $blocker) {
            $ids = DB::table($blocker['table'])
                     ->whereNotNull($blocker['column'])
                     ->pluck($blocker['column']);
            $blocked = $blocked->merge($ids);
        }

        return $blocked->unique()->values()->toArray();
    }

    /**
     * Dapatkan semua ID user yang memiliki role yang terdefinisi di tabel roles.
     * Role diambil secara dinamis dari DB (tidak hardcode) — sama dengan yang ada di RolePermissionSeeder.
     * User dengan role ini dianggap sebagai akun sistem yang tidak boleh dihapus massal.
     */
    protected function getRoleExcludedUserIds(): array
    {
        // Ambil semua legacy_role_id yang terdaftar di tabel roles,
        // KECUALI role 'staff' dan 'manager' (karena user dengan role ini boleh dihapus jika tidak ada relasi).
        $definedLegacyRoleIds = DB::table('roles')
                                  ->whereNotNull('legacy_role_id')
                                  ->whereNotIn('name', ['staff', 'manager'])
                                  ->pluck('legacy_role_id')
                                  ->toArray();

        if (empty($definedLegacyRoleIds)) {
            return [];
        }

        // Kembalikan ID user yang role-nya cocok dengan salah satu legacy_role_id yang dilindungi
        return DB::table('users')
                 ->whereIn('role', $definedLegacyRoleIds)
                 ->pluck('id')
                 ->toArray();
    }

    /**
     * Mode diagnose: tampilkan setiap user dan kenapa mereka TIDAK bisa dihapus.
     */
    protected function runDiagnose(): int
    {
        $this->info('=== DIAGNOSA USER YANG TIDAK BISA DIHAPUS ===');
        $this->newLine();

        $blockedIds   = $this->getBlockedUserIds();
        $roleExcluded = $this->getRoleExcludedUserIds();

        // Ambil semua role yang aktif (legacy_role_id => name) untuk label alasan
        $rolesMap = DB::table('roles')
                      ->whereNotNull('legacy_role_id')
                      ->pluck('name', 'legacy_role_id')
                      ->toArray();

        $allUsers = User::where('username', '!=', 'admin')->get(['id', 'name', 'username', 'role']);

        $canDelete    = [];
        $cannotDelete = [];

        foreach ($allUsers as $user) {
            $reasons = [];

            // Cek blocker FK
            foreach ($this->blockers as $blocker) {
                $count = DB::table($blocker['table'])
                           ->where($blocker['column'], $user->id)
                           ->count();
                if ($count > 0) {
                    $reasons[] = "{$blocker['label']} ({$count} record di {$blocker['table']})";
                }
            }

            // Cek role system
            if (in_array($user->id, $roleExcluded)) {
                $roleName = $rolesMap[$user->role] ?? "role={$user->role}";
                $reasons[] = "Memiliki role sistem: {$roleName}";
            }

            if (empty($reasons)) {
                $canDelete[] = [$user->id, $user->name, $user->username, 'Aman'];
            } else {
                $cannotDelete[] = [$user->id, $user->name, $user->username, implode(', ', $reasons)];
            }
        }

        $this->comment('--- USER YANG AMAN DIHAPUS (' . count($canDelete) . ') ---');
        if (!empty($canDelete)) {
            $this->table(['ID', 'Name', 'Username', 'Status'], $canDelete);
        } else {
            $this->line('Tidak ada.');
        }

        $this->newLine();
        $this->comment('--- USER YANG TIDAK BISA DIHAPUS (' . count($cannotDelete) . ') ---');
        if (!empty($cannotDelete)) {
            $this->table(['ID', 'Name', 'Username', 'Alasan'], $cannotDelete);
        } else {
            $this->line('Tidak ada.');
        }

        return 0;
    }
}
