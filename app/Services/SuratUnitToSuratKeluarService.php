<?php

namespace App\Services;

use App\Models\SuratUnitManager;
use App\Models\SuratKeluar;
use App\Models\SuratKeluarFile;
use App\Models\Disposisi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuratUnitToSuratKeluarService
{
    /**
     * Convert an approved SuratUnitManager to SuratKeluar
     *
     * @param int $suratUnitId
     * @param array $data
     * @param \App\Models\User $user
     * @return \App\Models\SuratKeluar
     * @throws \Exception
     */
    public function convert($suratUnitId, array $data, $user)
    {
        DB::beginTransaction();
        try {
            $suratUnit = SuratUnitManager::findOrFail($suratUnitId);

            // Validation: Must be approved by manager and not already converted
            if ($suratUnit->status_manager !== 'approved') {
                throw new \Exception('Surat Unit belum disetujui oleh Manager.');
            }

            if ($suratUnit->surat_keluar_id) {
                throw new \Exception('Surat Unit ini sudah pernah dikonversi menjadi Surat Keluar.');
            }

            // 1. Create SuratKeluar
            $suratKeluar = new SuratKeluar();
            $suratKeluar->nomor_surat = $data['nomor_surat'];
            $suratKeluar->tanggal_surat = $data['tanggal_surat'];
            $suratKeluar->perihal = $data['perihal'];
            $suratKeluar->perusahaan = $data['perusahaan'];
            $suratKeluar->jenis_surat = $data['jenis_surat'];
            $suratKeluar->sifat_surat = $data['sifat_surat'];
            $suratKeluar->created_by = $user->id;
            $suratKeluar->save();

            // 2. Handle files (copy from SuratUnitManager and handle new uploads if any)
            // Copy existing files
            if ($suratUnit->files && $suratUnit->files->count() > 0) {
                foreach ($suratUnit->files as $file) {
                    $suratKeluar->files()->create([
                        'file_path' => $file->file_path, // Reference the same physical file
                        'file_type' => $file->file_type,
                        'original_name' => $file->original_name,
                    ]);
                }
            }

            // Handle new uploaded files
            if (isset($data['file']) && is_array($data['file'])) {
                foreach ($data['file'] as $newFile) {
                    $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $newFile->getClientOriginalExtension();
                    $uploadDir = public_path('uploads/surat_keluar');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $newFile->move($uploadDir, $fileName);
                    
                    $suratKeluar->files()->create([
                        'file_path' => 'uploads/surat_keluar/' . $fileName,
                        'file_type' => $newFile->getClientMimeType(),
                        'original_name' => $newFile->getClientOriginalName(),
                    ]);
                }
            }

            // 3. Create Disposisi
            if (!empty($data['tujuan_disposisi'])) {
                $disposisi = new Disposisi();
                $disposisi->surat_keluar_id = $suratKeluar->id;
                $disposisi->created_by = $data['pengirim_id'] ?? $user->id;
                $disposisi->keterangan_pengirim = $data['keterangan_pengirim'] ?? null;
                
                // Set logic status based on role
                $pengirimRole = $user->role;
                $isAsDirut = isset($data['as_dirut']) && $data['as_dirut'];
                $isAsManagerKeuangan = isset($data['as_manager_keuangan']) && $data['as_manager_keuangan'];
                
                if ($isAsDirut && ($pengirimRole == 1 || $pengirimRole == 5)) {
                    $pengirimRole = 4; // Dirut
                } elseif ($isAsManagerKeuangan && $pengirimRole == 5) {
                    $pengirimRole = 7; // Manager Keuangan
                }

                if ($pengirimRole == 5 || $pengirimRole == 8 || $pengirimRole == 1) { 
                    $disposisi->status_sekretaris = 'approved';
                    $disposisi->status_dirut = 'pending';
                } else {
                    $disposisi->status_sekretaris = 'pending';
                    $disposisi->status_dirut = 'pending';
                }

                $disposisi->save();
                
                // Attach tujuan
                $disposisi->tujuan()->sync($data['tujuan_disposisi']);
                
                // Try sending notification
                if (class_exists(\App\Services\SuratPushNotificationService::class)) {
                    try {
                        $svc = app(\App\Services\SuratPushNotificationService::class);
                        $disposisi->refresh();
                        $excludedIds = [];
                        if ($disposisi->status_sekretaris === 'pending') {
                            $excludedIds = $svc->notifySekretarisPerluReview($disposisi);
                        } elseif ($disposisi->status_sekretaris === 'approved' && $disposisi->status_dirut === 'pending') {
                            $excludedIds = $svc->notifyDirekturSuratMenunggu($disposisi);
                        }
                        $svc->notifyDisposisiTujuan($disposisi, (array) $data['tujuan_disposisi'], $excludedIds);
                    } catch (\Throwable $e) {
                        Log::warning('Push notification failed during conversion', [
                            'message' => $e->getMessage(),
                            'surat_id' => $suratKeluar->id
                        ]);
                    }
                }
            }

            // 4. Update the original SuratUnitManager
            $suratUnit->surat_keluar_id = $suratKeluar->id;
            $suratUnit->save();
            
            // Add history
            if (method_exists($suratUnit, 'histories')) {
                $suratUnit->histories()->create([
                    'user_id' => $user->id,
                    'action' => 'converted_to_surat_keluar',
                    'keterangan' => 'Surat diajukan sebagai Surat Keluar resmi dengan Nomor: ' . $suratKeluar->nomor_surat,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }

            DB::commit();

            return $suratKeluar;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in SuratUnitToSuratKeluarService: ' . $e->getMessage());
            throw $e;
        }
    }
}
