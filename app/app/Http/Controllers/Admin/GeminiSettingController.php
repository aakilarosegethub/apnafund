<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class GeminiSettingController extends Controller
{
    private const DATA_KEY = 'gemini_settings.data';

    private const DEFAULT_SYSTEM = 'Tu Lahore Property Guide ka AI assistant ho. Tumhare do main kaam hain.';

    private const DEFAULT_CONVERSATION = 'Ek ek question poocho. Roman Urdu aur English dono use kar sakte ho.';

    public function index()
    {
        $pageTitle = 'Gemini AI Settings';
        $data = $this->getSettings();
        $apiKeyMasked = $this->maskApiKey($data['api_key'] ?? null);

        return view('admin.setting.gemini', compact('pageTitle', 'data', 'apiKeyMasked'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string|max:100',
            'system_instructions' => 'nullable|string|max:10000',
            'conversation_instructions' => 'nullable|string|max:5000',
            'model' => 'nullable|string|in:gemini-1.5-flash,gemini-1.5-pro',
        ]);

        $apiKey = $request->input('api_key');
        if ($apiKey && ! $this->isValidApiKeyFormat($apiKey)) {
            $toast[] = ['error', 'Invalid API key format. Key should start with AIzaSy.'];

            return back()->withToasts($toast);
        }

        $data = $this->getSettings();

        if ($apiKey !== null && $apiKey !== '') {
            $data['api_key'] = Crypt::encryptString($apiKey);
        }

        $data['system_instructions'] = $request->input('system_instructions', '');
        $data['conversation_instructions'] = $request->input('conversation_instructions', '');
        $data['model'] = $request->input('model', 'gemini-1.5-flash');
        $data['updated_at'] = now()->toIso8601String();

        SiteData::updateOrCreate(
            ['data_key' => self::DATA_KEY],
            ['data_info' => $data]
        );

        $toast[] = ['success', 'Gemini settings saved successfully.'];

        return back()->withToasts($toast);
    }

    public function test(Request $request)
    {
        $apiKey = $request->input('api_key');
        if (! $apiKey) {
            $data = $this->getSettings();
            try {
                $apiKey = $data['api_key'] ? Crypt::decryptString($data['api_key']) : null;
            } catch (\Exception $e) {
                $apiKey = null;
            }
        }

        if (! $apiKey || ! $this->isValidApiKeyFormat($apiKey)) {
            $toast[] = ['error', 'Invalid or missing API key.'];

            return back()->withToasts($toast);
        }

        $model = $this->getSettings()['model'] ?? 'gemini-1.5-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = Http::withHeaders([
            'x-goog-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [['parts' => [['text' => 'Hello']]]],
        ]);

        if ($response->successful()) {
            $toast[] = ['success', 'Connection successful! Gemini API is working.'];
        } else {
            $body = $response->json();
            $error = $body['error']['message'] ?? $response->body();
            $toast[] = ['error', 'Connection failed: '.$error];
        }

        return back()->withToasts($toast);
    }

    public function reset()
    {
        $data = $this->getSettings();
        $data['system_instructions'] = self::DEFAULT_SYSTEM;
        $data['conversation_instructions'] = self::DEFAULT_CONVERSATION;
        $data['updated_at'] = now()->toIso8601String();

        SiteData::updateOrCreate(
            ['data_key' => self::DATA_KEY],
            ['data_info' => $data]
        );

        $toast[] = ['success', 'Instructions reset to default.'];

        return back()->withToasts($toast);
    }

    private function getSettings(): array
    {
        $row = SiteData::where('data_key', self::DATA_KEY)->first();
        $data = $row ? (is_array($row->data_info) ? $row->data_info : (array) $row->data_info) : [];

        return array_merge([
            'api_key' => null,
            'system_instructions' => self::DEFAULT_SYSTEM,
            'conversation_instructions' => self::DEFAULT_CONVERSATION,
            'model' => 'gemini-1.5-flash',
            'updated_at' => null,
        ], $data);
    }

    private function maskApiKey(?string $encrypted): string
    {
        if (! $encrypted) {
            return '';
        }
        try {
            $key = Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            return '';
        }

        return strlen($key) >= 8 ? substr($key, 0, 4).'***'.substr($key, -4) : '***';
    }

    private function isValidApiKeyFormat(?string $key): bool
    {
        return $key && preg_match('/^AIzaSy[a-zA-Z0-9_-]{35}$/', $key);
    }
}
