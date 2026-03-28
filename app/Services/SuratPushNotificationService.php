<?php

namespace App\Services;

use App\Jobs\SendFcmNotification;
use App\Models\Disposisi;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\UserDeviceToken;
use Illuminate\Support\Collection;
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

    public function notifyUsers(Collection $users, string $title, string $body, array $data = []): void
    {
        if ($users->isEmpty()) {
            return;
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
        $tokens = UserDeviceToken::whereIn('user_id', $userIds)
            ->pluck('device_token')
            ->toArray();

        if (! empty($tokens)) {
            SendFcmNotification::dispatch($tokens, $title, $body, $data);
        }
    }

    public function notifyDisposisiTujuan(Disposisi $disposisi, array $userIds): void
    {
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
}
