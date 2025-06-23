<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Perbaiki data lama yang mungkin memiliki timestamp NULL
        Jabatan::whereNull('created_at')->update(['created_at' => now(), 'updated_at' => now()]);
        
        $jabatans = [
            ['nama_jabatan' => 'Direktur Utama', 'kode_jabatan' => 'DIRUT', 'status' => 'aktif'],
            ['nama_jabatan' => 'Sekretaris', 'kode_jabatan' => 'SEKRE', 'status' => 'aktif'],
            ['nama_jabatan' => 'Manager', 'kode_jabatan' => 'MNGR', 'status' => 'aktif'],
            ['nama_jabatan' => 'Staff', 'kode_jabatan' => 'STAFF', 'status' => 'aktif'],
            ['nama_jabatan' => 'IT', 'kode_jabatan' => 'IT', 'status' => 'aktif'],
            ['nama_jabatan' => 'Super Admin', 'kode_jabatan' => 'SADMIN', 'status' => 'aktif'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::updateOrInsert(
                ['nama_jabatan' => $jabatan['nama_jabatan']],
                ['kode_jabatan' => $jabatan['kode_jabatan'], 'status' => $jabatan['status']]
            );
        }
    }
}
