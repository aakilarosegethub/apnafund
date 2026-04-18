<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Firebase Auth/Firestore integration for OTP and chat features (requires valid `config/firebase.php` credentials).
 */
class FirebaseService
{
    protected $auth;
    protected $firestore;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        
        try {
            $factory = (new Factory)
                ->withServiceAccount(config('firebase.service_account'))
                ->withProjectId($this->projectId);

            $this->auth = $factory->createAuth();
            try {
                $this->firestore = $factory->createFirestore();
            } catch (Exception $e) {
                Log::warning('Firebase Firestore init failed (Auth OK): ' . $e->getMessage());
                $this->firestore = null; // Chat token works without Firestore; unread count will return 0
            }
        } catch (Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            throw new Exception('Firebase service initialization failed');
        }
    }

    /**
     * Send OTP to phone number
     */
    public function sendOTP(string $phoneNumber): array
    {
        try {
            // Format phone number to E.164 format
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            // Create custom token for phone authentication
            $customToken = $this->auth->createCustomToken('phone-auth-' . time(), [
                'phone_number' => $formattedPhone
            ]);

            return [
                'success' => true,
                'message' => 'OTP sent successfully',
                'verification_id' => $customToken->toString(),
                'phone_number' => $formattedPhone
            ];

        } catch (AuthException $e) {
            Log::error('Firebase Auth Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            Log::error('Firebase Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while sending OTP'
            ];
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOTP(string $verificationId, string $otpCode, string $phoneNumber): array
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            // Verify the OTP using Firebase Auth
            $userRecord = $this->auth->getUserByPhoneNumber($formattedPhone);
            
            if (!$userRecord) {
                return [
                    'success' => false,
                    'message' => 'Invalid phone number or verification ID'
                ];
            }

            // Verify the OTP code
            $verified = $this->auth->verifyIdToken($verificationId);
            
            if ($verified) {
                return [
                    'success' => true,
                    'message' => 'OTP verified successfully',
                    'user_id' => $userRecord->uid,
                    'phone_number' => $formattedPhone
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid OTP code'
            ];

        } catch (AuthException $e) {
            Log::error('Firebase Auth Verification Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'OTP verification failed: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            Log::error('Firebase Verification Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while verifying OTP'
            ];
        }
    }

    /**
     * Create or get user by phone number
     */
    public function createOrGetUser(string $phoneNumber, array $userData = []): array
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            // Check if user exists
            try {
                $userRecord = $this->auth->getUserByPhoneNumber($formattedPhone);
                return [
                    'success' => true,
                    'user_id' => $userRecord->uid,
                    'phone_number' => $formattedPhone,
                    'is_new_user' => false
                ];
            } catch (AuthException $e) {
                // User doesn't exist, create new user
                $userProperties = [
                    'phoneNumber' => $formattedPhone,
                    'displayName' => $userData['name'] ?? 'User',
                    'email' => $userData['email'] ?? null,
                ];

                $userRecord = $this->auth->createUser($userProperties);
                
                return [
                    'success' => true,
                    'user_id' => $userRecord->uid,
                    'phone_number' => $formattedPhone,
                    'is_new_user' => true
                ];
            }

        } catch (Exception $e) {
            Log::error('Firebase User Creation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create or get user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to E.164 format
     */
    public function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add country code if not present
        if (!str_starts_with($phoneNumber, '1') && strlen($phoneNumber) === 10) {
            $phoneNumber = '1' . $phoneNumber;
        }
        
        return '+' . $phoneNumber;
    }

    /**
     * Validate phone number format
     */
    public function validatePhoneNumber(string $phoneNumber): bool
    {
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);
        
        // Basic E.164 format validation
        return preg_match('/^\+[1-9]\d{1,14}$/', $formattedPhone) === 1;
    }

    /**
     * Get user by phone number
     */
    public function getUserByPhone(string $phoneNumber): ?array
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            $userRecord = $this->auth->getUserByPhoneNumber($formattedPhone);
            
            return [
                'uid' => $userRecord->uid,
                'phone_number' => $userRecord->phoneNumber,
                'email' => $userRecord->email,
                'display_name' => $userRecord->displayName,
                'created_at' => $userRecord->metadata->createdAt,
                'last_sign_in' => $userRecord->metadata->lastSignInAt,
            ];
        } catch (Exception $e) {
            Log::error('Firebase Get User Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create custom token for Laravel user (for Firestore chat auth).
     * UID in token = Laravel user id so Firestore rules can allow access.
     */
    public function createCustomTokenForUser(string $uid, array $claims = [], int $ttl = 3600): array
    {
        try {
            $token = $this->auth->createCustomToken($uid, $claims, $ttl);
            return [
                'success' => true,
                'token' => $token->toString(),
            ];
        } catch (Exception $e) {
            Log::error('Firebase Custom Token Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get count of conversations with unread messages for the given user (for nav badge).
     */
    public function getChatUnreadCount(string $userId): int
    {
        if (!$this->firestore) {
            return 0;
        }
        try {
            $prefix = config('firebase.firestore.collection_prefix', 'apnacrowdfunding');
            $chat = config('firebase.firestore.chat', []);
            $fParticipants = $chat['participants_field'] ?? 'participants';
            $fLastSenderId = $chat['last_sender_id_field'] ?? 'last_sender_id';

            $coll = $this->firestore->database()->collection($prefix . '_conversations');
            $query = $coll->where($fParticipants, 'array-contains', $userId);
            $docs = $query->documents();
            $count = 0;
            foreach ($docs as $doc) {
                if (!$doc->exists()) {
                    continue;
                }
                $d = $doc->data();
                $lastSender = $d[$fLastSenderId] ?? $d['last_sender_id'] ?? null;
                $readBy = $d['read_by'] ?? [];
                if ($lastSender && $lastSender !== $userId && empty($readBy[$userId])) {
                    $count++;
                }
            }
            return $count;
        } catch (Exception $e) {
            Log::debug('Firebase getChatUnreadCount: ' . $e->getMessage());
            return 0;
        }
    }
}
