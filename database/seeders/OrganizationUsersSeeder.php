<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationUnit;
use App\Models\OrganizationType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationUsersSeeder extends Seeder
{
    /**
     * Map position/role string to numeric role
     * 0: Staff
     * 1: Sekretaris
     * 2: Direktur
     * 3: Super Admin
     * 4: Manager
     * 5: Sekretaris ASP
     * 7: Manager Keuangan
     * 8: Direktur ASP
     */
    private function mapRole($position)
    {
        $pos = strtoupper($position);
        
        if (str_contains($pos, 'MANAGER KEUANGAN')) return 7;
        if (str_contains($pos, 'MANAGER')) return 4;
        if (str_contains($pos, 'SEKRETARIS ASP')) return 5;
        if (str_contains($pos, 'DIREKTUR ASP')) return 8;
        if (str_contains($pos, 'DIREKTUR RS')) return 2;
        if (str_contains($pos, 'SEKRETARIS')) return 1;
        if (str_contains($pos, 'ADMINISTRATOR')) return 3;
        
        return 0; // Default to Staff
    }

    public function run(): void
    {
        $this->command->info('📋 Creating Organization Types & Units...');

        // 1. Ensure Organization Types exist
        $holdingType = OrganizationType::firstOrCreate(['name' => 'holding']);
        $directorateType = OrganizationType::firstOrCreate(['name' => 'directorate']);
        $departmentType = OrganizationType::firstOrCreate(['name' => 'department']);

        // 2. Create Units
        $unitsData = [
            'MUTU'  => ['name' => 'MUTU', 'type' => $departmentType],
            'PENMED'=> ['name' => 'PENUNJANG MEDIK', 'type' => $departmentType],
            'SDM'   => ['name' => 'SDM', 'type' => $departmentType],
            'DIR'   => ['name' => 'DIREKTUR', 'type' => $directorateType],
            'PTASP' => ['name' => 'PT. ASP', 'type' => $holdingType],
            'PELMED'=> ['name' => 'PELAYANAN MEDIK', 'type' => $departmentType],
            'KEU'   => ['name' => 'KEUANGAN', 'type' => $departmentType],
            'IT'    => ['name' => 'IT', 'type' => $departmentType],
            'AKPAJ' => ['name' => 'AKUNTANSI & PAJAK', 'type' => $departmentType],
            'LEGAL' => ['name' => 'LEGAL', 'type' => $departmentType],
            'DIVKEP'=> ['name' => 'DIVISI KEPERAWATAN', 'type' => $departmentType],
            'SEKR'  => ['name' => 'SEKRETARIAT', 'type' => $directorateType],
            'UMUM'  => ['name' => 'UMUM', 'type' => $departmentType],
            'MARK'  => ['name' => 'MARKETING', 'type' => $departmentType],
        ];

        $units = [];
        foreach ($unitsData as $code => $data) {
            $units[$code] = OrganizationUnit::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $data['name'],
                    'type_id' => $data['type']->id,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('👤 Creating Users...');

        $usersData = [
            ['nik' => '20141969', 'name' => 'DIENI ANANDA PUTRI, DR., MARS', 'username' => 'dieni.ananda', 'email' => 'dieni.ananda@azra.com', 'unit' => 'MUTU', 'pos' => 'MANAGER MUTU'],
            ['nik' => '20061105', 'name' => 'GARCINIA SATIVA FIZRIA SETIADI, Dr, MKM', 'username' => 'garcinia.sativa', 'email' => 'garcinia.sativa@azra.com', 'unit' => 'PENMED', 'pos' => 'MANAGER PENUNJANG MEDIK'],
            ['nik' => '20253017', 'name' => 'INDRA THALIB, B.SN., MM', 'username' => 'indra.thalib', 'email' => 'indra.thalib@azra.com', 'unit' => 'SDM', 'pos' => 'MANAGER SDM'],
            ['nik' => '20253030', 'name' => 'IRMA RISMAYANTI, dr, MM', 'username' => 'irma.rismayanti', 'email' => 'irma.rismayanti@azra.com', 'unit' => 'DIR', 'pos' => 'DIREKTUR RS'],
            ['nik' => '19950015', 'name' => 'LAILA AZRA, DRA.', 'username' => 'laila.azra', 'email' => 'laila.azra@azra.com', 'unit' => 'PTASP', 'pos' => 'KOMISARIS PT. ASP'],
            ['nik' => '20253062', 'name' => 'LILI MARLIANI, DR., MARS', 'username' => 'lili.marliani', 'email' => 'lili.marliani@azra.com', 'unit' => 'PELMED', 'pos' => 'MANAGER PELAYANAN MEDIK'],
            ['nik' => '20212767', 'name' => 'METRI JULIANTI, SE', 'username' => 'metri.julianti', 'email' => 'metri.julianti@azra.com', 'unit' => 'KEU', 'pos' => 'MANAGER KEUANGAN'],
            ['nik' => '20071107', 'name' => 'M. RANGGA ADITYA', 'username' => 'm.rangga', 'email' => 'm.rangga@azra.com', 'unit' => 'PTASP', 'pos' => 'Direktur ASP'],
            ['nik' => '20242964', 'name' => 'MUHAMAD MIFTAHUDIN, M. KOM', 'username' => 'muhamad.miftahudin', 'email' => 'muhamad.miftahudin@azra.com', 'unit' => 'IT', 'pos' => 'MANAGER IT'],
            ['nik' => '20242967', 'name' => 'RIA FAJARROHMI, SE', 'username' => 'ria.fajarrohmi', 'email' => 'ria.fajarrohmi@azra.com', 'unit' => 'AKPAJ', 'pos' => 'SUPERVISOR AKUNTING & PAJAK'],
            ['nik' => '20111600', 'name' => 'RIYADI MAULANA, SH., MH., CLA., CCD', 'username' => 'riyadi.maulana', 'email' => 'riyadi.maulana@azra.com', 'unit' => 'LEGAL', 'pos' => 'MANAGER LEGAL'],
            ['nik' => '19940189', 'name' => 'SENI MAULIDA FITALOKA, S.Kep,Ns, M.Kep', 'username' => 'seni.maulida', 'email' => 'seni.maulida@azra.com', 'unit' => 'DIVKEP', 'pos' => 'MANAGER KEPERAWATAN'],
            ['nik' => '20020462', 'name' => 'SITI KHOIRIAH', 'username' => 'siti.khoiriah', 'email' => 'siti.khoiriah@azra.com', 'unit' => 'PTASP', 'pos' => 'Sekretaris ASP'],
            ['nik' => '20253070', 'name' => 'THORIO FARIED ISHAQ, S.I. KOM', 'username' => 'thorio.faried', 'email' => 'thorio.faried@azra.com', 'unit' => 'UMUM', 'pos' => 'MANAGER UMUM'],
            ['nik' => '20253008', 'name' => 'TUMPAS BANGKIT PRAYUDA, SE', 'username' => 'tumpas.bangkit', 'email' => 'tumpas.bangkit@azra.com', 'unit' => 'MARK', 'pos' => 'MANAGER MARKETING'],
            ['nik' => '20242988', 'name' => 'VERONIKA RINI HANDAYANI, A. MD', 'username' => 'veronika.rini', 'email' => 'veronika.rini@azra.com', 'unit' => 'PTASP', 'pos' => 'Sekretaris ASP'],
            ['nik' => '99999002', 'name' => 'Admin System', 'username' => 'admin', 'email' => 'admin@azra.com', 'unit' => 'IT', 'pos' => 'System Administrator'],
        ];

        foreach ($usersData as $data) {
            $role = $this->mapRole($data['pos']);
            $unitId = $units[$data['unit']]->id ?? null;

            // Find user by NIK or Username to avoid unique constraint errors
            $user = User::where('nik', $data['nik'])
                        ->orWhere('username', $data['username'])
                        ->first();

            if ($user) {
                $user->update([
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'role' => $role,
                    'organization_unit_id' => $unitId,
                    'status_akun' => 'aktif',
                ]);
            } else {
                $user = User::create([
                    'nik' => $data['nik'],
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => Hash::make('rsazra'),
                    'role' => $role,
                    'organization_unit_id' => $unitId,
                    'status_akun' => 'aktif',
                ]);
            }

            // If it's a manager role (2, 4, 7, 8), set them as head of unit
            if (in_array($role, [2, 4, 7, 8]) && $unitId) {
                $units[$data['unit']]->update(['head_id' => $user->id]);
            }

            $this->command->info("  ✓ User '{$user->name}' created/updated.");
        }

        $this->command->newLine();
        $this->command->info('✅ Organization users seeded successfully!');
    }
}

