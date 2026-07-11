<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use Illuminate\Http\Request;

class FirebaseOTPSettingController extends Controller
{
    /**
     * Show Firebase OTP settings page
     */
    public function index()
    {
        $pageTitle = 'Firebase OTP Settings';

        $firebaseSettings = SiteData::where('data_key', 'otp_firebase.data')->first();

        if (! $firebaseSettings) {
            $defaultSettings = [
                'status' => false,
                'otp_provider' => 'twilio', // twilio, firebase, msg91
                'project_id' => '',
                'private_key_id' => '',
                'private_key' => '',
                'client_email' => '',
                'client_id' => '',
                'auth_domain' => '',
                'client_cert_url' => '',
                'api_key' => '',
                'storage_bucket' => '',
                'messaging_sender_id' => '',
                'app_id' => '',
                'collection_prefix' => 'apnafund',
            ];

            $firebaseSettings = new SiteData;
            $firebaseSettings->data_key = 'otp_firebase.data';
            $firebaseSettings->data_info = $defaultSettings;
            $firebaseSettings->save();
        }

        $dataInfo = is_array($firebaseSettings->data_info) ? $firebaseSettings->data_info : (array) $firebaseSettings->data_info;

        return view('admin.setting.firebase_otp', compact('pageTitle', 'firebaseSettings', 'dataInfo'));
    }

    /**
     * Update Firebase OTP settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'otp_provider' => 'required|in:twilio,firebase,msg91',
            'project_id' => 'nullable|string|max:255',
            'private_key_id' => 'nullable|string|max:255',
            'private_key' => 'nullable|string',
            'client_email' => 'nullable|string|max:255',
            'client_id' => 'nullable|string|max:255',
            'auth_domain' => 'nullable|string|max:255',
            'client_cert_url' => 'nullable|string|max:500',
            'api_key' => 'nullable|string|max:255',
            'storage_bucket' => 'nullable|string|max:255',
            'messaging_sender_id' => 'nullable|string|max:255',
            'app_id' => 'nullable|string|max:255',
            'collection_prefix' => 'nullable|string|max:100',
        ]);

        $firebaseSettings = SiteData::where('data_key', 'otp_firebase.data')->first();

        if (! $firebaseSettings) {
            $firebaseSettings = new SiteData;
            $firebaseSettings->data_key = 'otp_firebase.data';
        }

        $dataInfo = [
            'status' => $request->otp_provider === 'firebase',
            'otp_provider' => $request->otp_provider,
            'project_id' => $request->project_id ?? '',
            'private_key_id' => $request->private_key_id ?? '',
            'private_key' => $request->private_key ?? '',
            'client_email' => $request->client_email ?? '',
            'client_id' => $request->client_id ?? '',
            'auth_domain' => $request->auth_domain ?? '',
            'client_cert_url' => $request->client_cert_url ?? '',
            'api_key' => $request->api_key ?? '',
            'storage_bucket' => $request->storage_bucket ?? '',
            'messaging_sender_id' => $request->messaging_sender_id ?? '',
            'app_id' => $request->app_id ?? '',
            'collection_prefix' => $request->collection_prefix ?? 'apnafund',
        ];

        $firebaseSettings->data_info = $dataInfo;
        $firebaseSettings->save();

        // Update .env when Firebase is selected
        if ($request->otp_provider === 'firebase') {
            $this->updateEnvFile($dataInfo);
            \Illuminate\Support\Facades\Artisan::call('config:clear');
        }

        $toast[] = ['success', 'OTP settings updated successfully. Use Firebase for OTP when selected.'];

        return back()->withToasts($toast);
    }

    /**
     * Update .env file with Firebase credentials
     */
    private function updateEnvFile(array $data): void
    {
        $envFile = base_path('.env');
        if (! file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);

        $updates = [
            'FIREBASE_PROJECT_ID' => $data['project_id'],
            'FIREBASE_PRIVATE_KEY_ID' => $data['private_key_id'],
            'FIREBASE_PRIVATE_KEY' => $this->formatPrivateKeyForEnv($data['private_key']),
            'FIREBASE_CLIENT_EMAIL' => $data['client_email'],
            'FIREBASE_CLIENT_ID' => $data['client_id'],
            'FIREBASE_AUTH_DOMAIN' => $data['auth_domain'],
            'FIREBASE_CLIENT_CERT_URL' => $data['client_cert_url'],
            'FIREBASE_COLLECTION_PREFIX' => $data['collection_prefix'],
            'FIREBASE_API_KEY' => $data['api_key'],
            'FIREBASE_STORAGE_BUCKET' => $data['storage_bucket'],
            'FIREBASE_MESSAGING_SENDER_ID' => $data['messaging_sender_id'],
            'FIREBASE_APP_ID' => $data['app_id'],
        ];

        foreach ($updates as $key => $value) {
            $envContent = $this->updateEnvVariable($envContent, $key, $value);
        }

        file_put_contents($envFile, $envContent);
    }

    private function formatPrivateKeyForEnv(?string $key): string
    {
        if (empty($key)) {
            return '';
        }
        $key = trim($key);
        if (strpos($key, '"') !== 0) {
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
}
