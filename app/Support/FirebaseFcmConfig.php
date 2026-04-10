<?php

namespace App\Support;

use App\Models\SiteData;

/**
 * FCM server credentials: admin panel (SiteData) overrides .env when enabled and complete.
 */
class FirebaseFcmConfig
{
    public const SITE_DATA_KEY = 'fcm_push.data';

    public static function serviceAccount(): ?array
    {
        $row = SiteData::where('data_key', self::SITE_DATA_KEY)->first();
        if ($row && is_array($row->data_info)) {
            $d = $row->data_info;
            if (array_key_exists('enabled', $d) && !$d['enabled']) {
                return null;
            }
            if (!empty($d['enabled'])) {
                $built = self::buildCredentialsFromArray($d);
                if ($built !== null) {
                    return $built;
                }
            }
        }

        $c = config('firebase.service_account');
        if (!empty($c['private_key']) && !empty($c['project_id'])) {
            $c['private_key'] = self::normalizePrivateKey((string) $c['private_key']);

            return $c;
        }

        return null;
    }

    public static function projectId(): ?string
    {
        $acc = self::serviceAccount();

        return $acc && !empty($acc['project_id']) ? (string) $acc['project_id'] : null;
    }

    /**
     * @param  array<string, mixed>  $d
     */
    private static function buildCredentialsFromArray(array $d): ?array
    {
        $projectId = trim((string) ($d['project_id'] ?? ''));
        $privateKey = trim((string) ($d['private_key'] ?? ''));
        $clientEmail = trim((string) ($d['client_email'] ?? ''));
        if ($projectId === '' || $privateKey === '' || $clientEmail === '') {
            return null;
        }

        return [
            'type' => 'service_account',
            'project_id' => $projectId,
            'private_key_id' => (string) ($d['private_key_id'] ?? ''),
            'private_key' => self::normalizePrivateKey($privateKey),
            'client_email' => $clientEmail,
            'client_id' => (string) ($d['client_id'] ?? ''),
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => (string) ($d['client_cert_url'] ?? ''),
        ];
    }

    private static function normalizePrivateKey(string $key): string
    {
        return str_replace('\\n', "\n", $key);
    }
}
