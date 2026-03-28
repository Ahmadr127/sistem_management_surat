<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDeviceToken;
use App\Services\SuratPushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FcmTokenController extends Controller
{
    public function __construct(
        private SuratPushNotificationService $suratPushNotificationService
    ) {}

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

            UserDeviceToken::updateOrCreate(
                ['device_token' => $incomingToken],
                [
                    'user_id' => auth()->id(),
                    'device_type' => $validated['device_type'] ?? null,
                ]
            );

            Log::info('SISM FCM token registered', ['user_id' => auth()->id()]);

            return response()->json([
                'status' => 'success',
                'message' => 'FCM Token berhasil didaftarkan',
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
            $tokens = UserDeviceToken::where('user_id', $user->id)->get();

            if ($tokens->isEmpty()) {
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

            $this->suratPushNotificationService->notifyUsersByIds(
                [$user->id],
                $title,
                $body,
                $data
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Test notification berhasil dikirim',
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
