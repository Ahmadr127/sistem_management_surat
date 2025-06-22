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
        $jabatan = [
            [
                'nama_jabatan' => 'STAFF',
                'status' => 'aktif'
            ],
            [
                'nama_jabatan' => 'MANAGER',
                'status' => 'aktif'
            ],
            [
                'nama_jabatan' => 'DIREKTUR UTAMA',
                'status' => 'aktif'
            ],
            [
                'nama_jabatan' => 'SEKRETARIS',
                'status' => 'aktif'
            ],
            [
                'nama_jabatan' => 'IT',
                'status' => 'aktif'
            ]
        ];

        foreach ($jabatan as $jab) {
            Jabatan::firstOrCreate(['nama_jabatan' => $jab['nama_jabatan']], $jab);
        }
    }
}
