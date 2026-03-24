<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    /**
     * Handle SSO Login from Main SSO Token.
     */
    public function loginViaToken(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        $ssoBaseUrl = env('SSO_BASE_URL', 'http://localhost');

        try {
            $response = Http::withToken($request->access_token)
                ->withoutVerifying()
                ->get("{$ssoBaseUrl}/api/user");

            \Illuminate\Support\Facades\Log::debug('SSO API Raw Response:', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid SSO token or SSO server unreachable.',
                    'details' => $response->body()
                ], 401);
            }

            $ssoUser = $response->json();
            \Illuminate\Support\Facades\Log::debug('SSO User Response:', $ssoUser ?? []);
            
            if (!$ssoUser) {
                \Illuminate\Support\Facades\Log::error('SSO Response is empty or invalid JSON');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Empty response from SSO server.',
                ], 500);
            }

            $ssoUserData = $ssoUser['data'] ?? $ssoUser;

            if (empty($ssoUserData['nik'])) {
                \Illuminate\Support\Facades\Log::warning('SSO User Data missing NIK:', $ssoUserData);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'SSO user does not have a NIK. Cannot authenticate.',
                ], 422);
            }

            $user = User::where('nik', $ssoUserData['nik'])->first();

            if ($user) {
                $user->update([
                    'name'     => $ssoUserData['name'],
                    'email'    => $ssoUserData['email']    ?? $user->email,
                    'username' => $ssoUserData['username'] ?? $user->username,
                ]);
            } else {
                $user = User::create([
                    'nik'      => $ssoUserData['nik'],
                    'name'     => $ssoUserData['name'],
                    'email'    => $ssoUserData['email']    ?? ($ssoUserData['username'] . '@rs-azra.co.id'),
                    'username' => $ssoUserData['username'] ?? str_replace(' ', '.', strtolower($ssoUserData['name'])),
                    'password' => bcrypt(Str::random(32)),
                    'role'     => 0,
                    'status_akun' => 'aktif',
                ]);
            }

            // Generate a local Sanctum token
            $token = $user->createToken('Surat API Token')->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'message' => 'SSO Login successful',
                'data'    => [
                    'user' => [
                        'id'       => $user->id,
                        'name'     => $user->name,
                        'email'    => $user->email,
                        'username' => $user->username,
                        'nik'      => $user->nik,
                        'role'     => $user->role,
                    ],
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred during SSO authentication.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
