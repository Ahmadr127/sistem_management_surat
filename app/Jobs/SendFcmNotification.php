<?php

namespace App\Jobs;

use App\Models\UserDeviceToken;
use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class SendFcmNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        protected array|string $tokens,
        protected string $title,
        protected string $body,
        protected array $data = []
    ) {
        if (is_string($this->tokens)) {
            $this->tokens = [$this->tokens];
        }
    }

    public function handle(FirebaseService $firebaseService): void
    {
        $filteredTokens = [];
        foreach ($this->tokens as $token) {
            if (! is_string($token)) {
                continue;
            }
            $token = trim($token);
            if ($token === '' || $token === 'YOUR_FCM_TOKEN_HERE' || strlen($token) < 50) {
                continue;
            }
            $filteredTokens[] = $token;
        }
        $this->tokens = array_values(array_unique($filteredTokens));

        if (empty($this->tokens)) {
            Log::warning('SendFcmNotification: No tokens provided');

            return;
        }

        try {
            $messaging = $firebaseService->getMessaging();
            $batches = array_chunk($this->tokens, 500);
            $invalidTokens = [];

            foreach ($batches as $batchIndex => $batch) {
                try {
                    $notification = Notification::create($this->title, $this->body);
                    $message = CloudMessage::new()
                        ->withNotification($notification)
                        ->withData($this->data);

                    $report = $messaging->sendMulticast($message, $batch);

                    foreach ($report->failures()->getItems() as $failure) {
                        $error = $failure->error();
                        $token = $failure->target()->value();
                        if ($this->isInvalidTokenError($error)) {
                            $invalidTokens[] = $token;
                        }
                    }

                    Log::info('SendFcmNotification: Batch sent', [
                        'batch' => $batchIndex + 1,
                        'success' => $report->successes()->count(),
                        'failure' => $report->failures()->count(),
                    ]);
                } catch (MessagingException $e) {
                    Log::error('SendFcmNotification: Messaging error in batch', [
                        'batch' => $batchIndex + 1,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (! empty($invalidTokens)) {
                UserDeviceToken::whereIn('device_token', $invalidTokens)->delete();
            }
        } catch (FirebaseException $e) {
            Log::error('SendFcmNotification: Firebase error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function isInvalidTokenError($error): bool
    {
        $errorMessage = $error->getMessage();
        $patterns = [
            'registration-token-not-registered',
            'invalid-registration-token',
            'invalid-argument',
            'registration token is invalid',
            'requested entity was not found',
        ];

        foreach ($patterns as $pattern) {
            if (stripos($errorMessage, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
