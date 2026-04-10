<?php

namespace App\Services;

use App\Models\UserPushDevice;
use App\Support\FirebaseFcmConfig;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Throwable;

class FcmPushService
{
    private ?Messaging $messaging = null;

    private function messaging(): ?Messaging
    {
        if ($this->messaging !== null) {
            return $this->messaging;
        }

        $credentials = FirebaseFcmConfig::serviceAccount();
        $projectId = FirebaseFcmConfig::projectId();
        if ($credentials === null || $projectId === null || $projectId === '') {
            return null;
        }

        try {
            $factory = (new Factory)
                ->withServiceAccount($credentials)
                ->withProjectId($projectId);
            $this->messaging = $factory->createMessaging();

            return $this->messaging;
        } catch (Throwable $e) {
            Log::warning('FCM: could not init messaging: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * @param  array<string, string>  $data  FCM data payload (values must be strings)
     */
    public function sendToToken(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $messaging = $this->messaging();
        if ($messaging === null || $fcmToken === '') {
            return false;
        }

        $message = CloudMessage::new()
            ->toToken($fcmToken)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        $messaging->send($message);

        return true;
    }

    public function notifyUserDevices(int $userId, string $title, string $body, array $data = []): void
    {
        $messaging = $this->messaging();
        if ($messaging === null) {
            return;
        }

        $devices = UserPushDevice::query()->where('user_id', $userId)->get();
        foreach ($devices as $device) {
            $token = (string) $device->fcm_token;
            if ($token === '') {
                continue;
            }
            try {
                $this->sendToToken($token, $title, $body, $data);
            } catch (NotFound $e) {
                $device->delete();
            } catch (Throwable $e) {
                Log::warning('FCM send failed for user ' . $userId . ': ' . $e->getMessage());
            }
        }
    }
}
