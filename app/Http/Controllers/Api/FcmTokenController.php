<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDeviceToken;
use App\Services\SuratPushNotificationService;
use App\Support\FcmTokenFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FcmTokenController extends Controller
{
    public function __construct(
        private SuratPushNotificationService $suratPushNotificationService
    ) {}

    /**
     * Satu akun boleh punya banyak baris token (tiap perangkat = FCM token unik).
     * Kunci unik adalah `device_token`, bukan `user_id` — notifikasi mengirim ke semua token user tersebut.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_token' => 'required|string|min:50|max:500',
            'device_type' => ['nullable', Rule::in(['android', 'ios', 'web'])],
        ]);

        try {
            $incomingToken = trim((string) $validated['device_token']);
            if ($incomingToken === 'YOUR_FCM_TOKEN_HERE') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'device_token tidak valid.',
                ], 422);
            }

            $row = UserDeviceToken::updateOrCreate(
                ['device_token' => $incomingToken],
                [
                    'user_id' => auth()->id(),
                    'device_type' => $validated['device_type'] ?? null,
                ]
            );

            $shaPrefix = FcmTokenFormatter::sha256Prefix($incomingToken);
            $tokensRegistered = UserDeviceToken::where('user_id', auth()->id())->count();

            Log::info('SISM FCM token registered', [
                'user_id' => auth()->id(),
                'device_type' => $row->device_type,
                'token_id' => $row->id,
                'tokens_registered_for_user' => $tokensRegistered,
                'incoming_is_placeholder_exact' => false,
                'incoming_token_sha256_prefix' => $shaPrefix,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'FCM Token berhasil didaftarkan',
                'data' => [
                    'token_id' => $row->id,
                    'token' => $incomingToken,
                    'token_preview' => FcmTokenFormatter::preview($incomingToken),
                    'sha256_prefix' => $shaPrefix,
                    'tokens_registered_for_user' => $tokensRegistered,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('SISM FCM register failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendaftarkan FCM token',
            ], 500);
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_token' => 'required|string',
        ]);

        try {
            $token = trim((string) $validated['device_token']);
            UserDeviceToken::where('device_token', $token)
                ->where('user_id', auth()->id())
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'FCM Token berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus FCM token',
            ], 500);
        }
    }

    public function testNotification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string|max:1000',
        ]);

        try {
            $user = auth()->user();
            $deviceRows = UserDeviceToken::where('user_id', $user->id)->get();

            if ($deviceRows->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada device token terdaftar untuk user ini',
                ], 404);
            }

            $title = $validated['title'] ?? 'Test Notifikasi SISM';
            $body = $validated['body'] ?? 'Ini adalah test notification dari SISM';

            $data = [
                'type' => 'test_notification',
                'source' => 'sism',
                'sent_at' => now()->toIso8601String(),
                'user_id' => (string) $user->id,
            ];

            $devicesForResponse = $deviceRows->map(function (UserDeviceToken $row) {
                $t = $row->device_token;

                return [
                    'id' => $row->id,
                    'user_id' => $row->user_id,
                    'device_type' => $row->device_type,
                    'token' => $t,
                    'token_preview' => FcmTokenFormatter::preview($t),
                    'sha256_prefix' => FcmTokenFormatter::sha256Prefix($t),
                    'token_length' => strlen($t),
                ];
            })->values()->all();

            $devicesForLog = array_map(function (array $d) {
                return [
                    'id' => $d['id'],
                    'device_type' => $d['device_type'],
                    'token_preview' => $d['token_preview'],
                    'sha256_prefix' => $d['sha256_prefix'],
                    'token_length' => $d['token_length'],
                ];
            }, $devicesForResponse);

            Log::info('SISM test notification: mengirim FCM', [
                'user_id' => $user->id,
                'devices_count' => count($devicesForLog),
                'devices' => $devicesForLog,
            ]);

            if (config('app.debug')) {
                Log::debug('SISM test notification: token lengkap (APP_DEBUG)', [
                    'user_id' => $user->id,
                    'tokens' => $deviceRows->pluck('device_token')->values()->all(),
                ]);
            }

            $this->suratPushNotificationService->notifyUsersByIds(
                [$user->id],
                $title,
                $body,
                $data
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Test notification berhasil dikirim (cek queue jika async)',
                'data' => [
                    'user_id' => $user->id,
                    'devices_count' => count($devicesForResponse),
                    'devices' => $devicesForResponse,
                    'payload_title' => $title,
                    'payload_body' => $body,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('SISM test notification failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim test notification: '.$e->getMessage(),
            ], 500);
        }
    }
}
