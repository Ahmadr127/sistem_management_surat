<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;

class FirebaseService
{
    private ?Messaging $messaging = null;

    private ?array $credentials = null;

    public function getMessaging(): Messaging
    {
        if ($this->messaging !== null) {
            return $this->messaging;
        }

        $credentials = $this->getCredentials();

        if (! $credentials) {
            throw new FirebaseException('Firebase credentials not configured');
        }

        try {
            $factory = (new Factory)->withServiceAccount($credentials);
            $this->messaging = $factory->createMessaging();

            return $this->messaging;
        } catch (\Exception $e) {
            Log::error('Failed to initialize Firebase Messaging', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new FirebaseException('Failed to initialize Firebase: '.$e->getMessage());
        }
    }

    public function getCredentials(): ?array
    {
        if ($this->credentials !== null) {
            return $this->credentials;
        }

        $credentialsJson = env('FIREBASE_CREDENTIALS_JSON');
        if ($credentialsJson) {
            $this->credentials = json_decode($credentialsJson, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->credentials;
            }
        }

        $credentialsPath = storage_path('app/firebase-auth.json');
        if (file_exists($credentialsPath)) {
            $this->credentials = json_decode(file_get_contents($credentialsPath), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->credentials;
            }
        }

        return null;
    }

    public function ping(): array
    {
        $messaging = $this->getMessaging();
        $credentials = $this->getCredentials();

        return [
            'status' => 'connected',
            'project_id' => $credentials['project_id'] ?? null,
            'client_email' => $credentials['client_email'] ?? null,
        ];
    }
}
