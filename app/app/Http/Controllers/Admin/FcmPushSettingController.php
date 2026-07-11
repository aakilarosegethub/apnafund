<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use App\Support\FirebaseFcmConfig;
use Illuminate\Http\Request;

class FcmPushSettingController extends Controller
{
    public function index()
    {
        $pageTitle = 'Push notifications (FCM)';

        $record = SiteData::where('data_key', FirebaseFcmConfig::SITE_DATA_KEY)->first();
        $dataInfo = ($record && is_array($record->data_info))
            ? $record->data_info
            : $this->defaultDataInfo();

        return view('admin.setting.fcm_push', compact('pageTitle', 'dataInfo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'enabled' => 'nullable|boolean',
            'project_id' => 'nullable|string|max:255',
            'private_key_id' => 'nullable|string|max:255',
            'private_key' => 'nullable|string',
            'client_email' => 'nullable|string|max:255',
            'client_id' => 'nullable|string|max:255',
            'client_cert_url' => 'nullable|string|max:500',
            'api_key' => 'nullable|string|max:255',
            'auth_domain' => 'nullable|string|max:255',
            'storage_bucket' => 'nullable|string|max:255',
            'messaging_sender_id' => 'nullable|string|max:255',
            'app_id' => 'nullable|string|max:255',
            'service_account_json' => 'nullable|string',
            'sync_to_env' => 'nullable|boolean',
        ]);

        $record = SiteData::where('data_key', FirebaseFcmConfig::SITE_DATA_KEY)->first();
        if (! $record && ! $request->boolean('enabled')) {
            $toast[] = ['info', 'Nothing saved. Enable the switch to store FCM credentials here, or keep using .env only.'];

            return back()->withToasts($toast);
        }

        if (! $record) {
            $record = new SiteData;
            $record->data_key = FirebaseFcmConfig::SITE_DATA_KEY;
        }

        $prev = is_array($record->data_info) ? $record->data_info : [];

        $dataInfo = [
            'enabled' => $request->boolean('enabled'),
            'project_id' => $request->input('project_id', ''),
            'private_key_id' => $request->input('private_key_id', ''),
            'private_key' => $request->filled('private_key')
                ? (string) $request->input('private_key')
                : ($prev['private_key'] ?? ''),
            'client_email' => $request->input('client_email', ''),
            'client_id' => $request->input('client_id', ''),
            'client_cert_url' => $request->input('client_cert_url', ''),
            'api_key' => $request->input('api_key', ''),
            'auth_domain' => $request->input('auth_domain', ''),
            'storage_bucket' => $request->input('storage_bucket', ''),
            'messaging_sender_id' => $request->input('messaging_sender_id', ''),
            'app_id' => $request->input('app_id', ''),
        ];

        if ($request->filled('service_account_json')) {
            $decoded = json_decode(trim((string) $request->input('service_account_json')), true);
            if (is_array($decoded) && ($decoded['type'] ?? '') === 'service_account') {
                $dataInfo['project_id'] = (string) ($decoded['project_id'] ?? $dataInfo['project_id']);
                $dataInfo['private_key_id'] = (string) ($decoded['private_key_id'] ?? $dataInfo['private_key_id']);
                if (! empty($decoded['private_key'])) {
                    $dataInfo['private_key'] = (string) $decoded['private_key'];
                }
                $dataInfo['client_email'] = (string) ($decoded['client_email'] ?? $dataInfo['client_email']);
                $dataInfo['client_id'] = (string) ($decoded['client_id'] ?? $dataInfo['client_id']);
                $dataInfo['client_cert_url'] = (string) ($decoded['client_x509_cert_url'] ?? $dataInfo['client_cert_url']);
            }
        }

        $record->data_info = $dataInfo;
        $record->save();

        if ($request->boolean('sync_to_env') && $dataInfo['enabled']) {
            $this->syncFirebaseEnvFromData($dataInfo);
            \Illuminate\Support\Facades\Artisan::call('config:clear');
        }

        $toast[] = ['success', 'Push notification settings saved.'];

        return back()->withToasts($toast);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncFirebaseEnvFromData(array $data): void
    {
        $envFile = base_path('.env');
        if (! file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);

        $updates = [
            'FIREBASE_PROJECT_ID' => (string) ($data['project_id'] ?? ''),
            'FIREBASE_PRIVATE_KEY_ID' => (string) ($data['private_key_id'] ?? ''),
            'FIREBASE_PRIVATE_KEY' => $this->formatPrivateKeyForEnv((string) ($data['private_key'] ?? '')),
            'FIREBASE_CLIENT_EMAIL' => (string) ($data['client_email'] ?? ''),
            'FIREBASE_CLIENT_ID' => (string) ($data['client_id'] ?? ''),
            'FIREBASE_AUTH_DOMAIN' => (string) ($data['auth_domain'] ?? ''),
            'FIREBASE_CLIENT_CERT_URL' => (string) ($data['client_cert_url'] ?? ''),
            'FIREBASE_API_KEY' => (string) ($data['api_key'] ?? ''),
            'FIREBASE_STORAGE_BUCKET' => (string) ($data['storage_bucket'] ?? ''),
            'FIREBASE_MESSAGING_SENDER_ID' => (string) ($data['messaging_sender_id'] ?? ''),
            'FIREBASE_APP_ID' => (string) ($data['app_id'] ?? ''),
        ];

        foreach ($updates as $key => $value) {
            $envContent = $this->updateEnvVariable($envContent, $key, $value);
        }

        file_put_contents($envFile, $envContent);
    }

    private function formatPrivateKeyForEnv(string $key): string
    {
        if ($key === '') {
            return '';
        }
        $key = trim($key);
        if ($key !== '' && $key[0] !== '"') {
            $key = '"'.str_replace(["\r\n", "\n"], ['\n', '\n'], $key).'"';
        }

        return $key;
    }

    private function updateEnvVariable(string $content, string $key, string $value): string
    {
        $pattern = "/^{$key}=.*/m";
        $replacement = $key.'='.$value;
        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $replacement, $content, 1);
        }

        return $content."\n{$replacement}";
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultDataInfo(): array
    {
        return [
            'enabled' => false,
            'project_id' => '',
            'private_key_id' => '',
            'private_key' => '',
            'client_email' => '',
            'client_id' => '',
            'client_cert_url' => '',
            'api_key' => '',
            'auth_domain' => '',
            'storage_bucket' => '',
            'messaging_sender_id' => '',
            'app_id' => '',
        ];
    }
}
