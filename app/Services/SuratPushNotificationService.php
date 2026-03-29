<?php

namespace App\Services;

use App\Jobs\SendFcmNotification;
use App\Models\Disposisi;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Support\FcmTokenFormatter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SuratPushNotificationService
{
    /**
     * @param  array<int>  $userIds
     */
    public function notifyUsersByIds(array $userIds, string $title, string $body, array $data = []): void
    {
        $users = User::whereIn('id', $userIds)->get();
        $this->notifyUsers($users, $title, $body, $data);
    }

    public function notifyUsers(Collection $users, string $title, string $body, array $data = []): array
    {
        if ($users->isEmpty()) {
            return [];
        }

        foreach ($users as $user) {
            PushNotification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);
        }

        $userIds = $users->pluck('id')->toArray();
        $tokenRows = UserDeviceToken::whereIn('user_id', $userIds)->get();
        $tokens = $tokenRows->pluck('device_token')->toArray();

        if (! empty($tokens)) {
            $devicesMeta = $tokenRows->map(function (UserDeviceToken $row) {
                $t = $row->device_token;

                return [
                    'token_id' => $row->id,
                    'user_id' => $row->user_id,
                    'device_type' => $row->device_type,
                    'token_preview' => FcmTokenFormatter::preview($t),
                    'sha256_prefix' => FcmTokenFormatter::sha256Prefix($t),
                ];
            })->values()->all();

            Log::info('SuratPushNotification: dispatch SendFcmNotification', [
                'user_ids' => $userIds,
                'title' => $title,
                'tokens_count' => count($tokens),
                'devices' => $devicesMeta,
            ]);

            SendFcmNotification::dispatch($tokens, $title, $body, $data);
        } else {
            Log::info('SuratPushNotification: tidak ada device token untuk user_ids', [
                'user_ids' => $userIds,
                'title' => $title,
            ]);
        }

        return $userIds;
    }

    public function notifyDisposisiTujuan(Disposisi $disposisi, array $userIds, array $excludeUserIds = []): void
    {
        $userIds = array_diff($userIds, $excludeUserIds);
        if (empty($userIds)) {
            return;
        }

        $disposisi->load('suratKeluar');
        $surat = $disposisi->suratKeluar;
        $perihal = $surat?->perihal ?? 'Surat masuk';
        $title = 'Disposisi surat';
        $body = 'Anda ditugaskan pada: '.$perihal;

        $data = [
            'type' => 'surat_masuk',
            'source' => 'sism',
            'surat_id' => (string) ($disposisi->surat_keluar_id ?? ''),
            'disposisi_id' => (string) $disposisi->id,
        ];

        $this->notifyUsersByIds($userIds, $title, $body, $data);
    }

    /**
     * Disposisi baru: status sekretaris masih pending — kabari Sekretaris (role 1 & 5).
     */
    public function notifySekretarisPerluReview(Disposisi $disposisi): array
    {
        $users = $this->activeUsersByRoles([1, 5]);
        if ($users->isEmpty()) {
            return [];
        }

        $disposisi->loadMissing('suratKeluar');
        $surat = $disposisi->suratKeluar;
        $perihal = $surat?->perihal ?? 'Surat masuk';
        $title = 'Review disposisi';
        $body = 'Disposisi menunggu persetujuan Anda: '.$perihal;

        $data = [
            'type' => 'surat_perlu_review_sekretaris',
            'source' => 'sism',
            'surat_id' => (string) ($disposisi->surat_keluar_id ?? ''),
            'disposisi_id' => (string) $disposisi->id,
        ];

        return $this->notifyUsers($users, $title, $body, $data);
    }

    /**
     * Sekretaris sudah menyetujui — kabari Direktur (role 2 & 8) bahwa surat menunggu keputusan.
     */
    public function notifyDirekturSuratMenunggu(Disposisi $disposisi): array
    {
        $users = $this->activeUsersByRoles([2, 8]);
        if ($users->isEmpty()) {
            return [];
        }

        $disposisi->loadMissing('suratKeluar');
        $surat = $disposisi->suratKeluar;
        $perihal = $surat?->perihal ?? 'Surat masuk';
        $title = 'Surat menunggu direktur';
        $body = 'Sekretaris telah menyetujui. Perlu keputusan Anda: '.$perihal;

        $data = [
            'type' => 'surat_menunggu_direktur',
            'source' => 'sism',
            'surat_id' => (string) ($disposisi->surat_keluar_id ?? ''),
            'disposisi_id' => (string) $disposisi->id,
        ];

        return $this->notifyUsers($users, $title, $body, $data);
    }

    /**
     * @param  array<int>  $roles  Legacy role integers
     */
    protected function activeUsersByRoles(array $roles): Collection
    {
        return User::query()
            ->whereIn('role', $roles)
            ->where('status_akun', 'aktif')
            ->get();
    }
}
