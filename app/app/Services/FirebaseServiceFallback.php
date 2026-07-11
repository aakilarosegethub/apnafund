<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Fallback when Firebase/Firestore cannot initialize (e.g. gRPC not installed).
 * Returns safe defaults so the app doesn't crash.
 */
class FirebaseServiceFallback extends FirebaseService
{
    public function __construct()
    {
        // Skip parent constructor - Firebase/Firestore not available
    }

    public function createCustomTokenForUser(string $uid, array $claims = [], int $ttl = 3600): array
    {
        Log::warning('FirebaseServiceFallback: createCustomTokenForUser called - Firebase not available');

        return [
            'success' => false,
            'message' => 'Chat requires PHP gRPC extension. Run: pecl install grpc && composer require google/cloud-firestore',
        ];
    }

    public function getChatUnreadCount(string $userId): int
    {
        return 0;
    }

    public function sendOTP(string $phoneNumber): array
    {
        return [
            'success' => false,
            'message' => 'Firebase not available. Install gRPC: pecl install grpc',
        ];
    }

    public function verifyOTP(string $verificationId, string $otpCode, string $phoneNumber): array
    {
        return [
            'success' => false,
            'message' => 'Firebase not available.',
        ];
    }

    public function createOrGetUser(string $phoneNumber, array $userData = []): array
    {
        return ['success' => false, 'message' => 'Firebase not available.'];
    }

    public function getUserByPhone(string $phoneNumber): ?array
    {
        return null;
    }
}
