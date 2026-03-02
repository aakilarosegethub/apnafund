<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class GeminiSettingsController extends Controller
{
    private const DATA_KEY = 'gemini_settings.data';
    private const DEFAULT_SYSTEM = 'Tu Lahore Property Guide ka AI assistant ho. Tumhare do main kaam hain.';
    private const DEFAULT_CONVERSATION = 'Ek ek question poocho. Roman Urdu aur English dono use kar sakte ho.';

    /**
     * Get Gemini settings (API key masked in response)
     */
    public function index()
    {
        $data = $this->getSettings();

        return response()->json([
            'apiKey' => null, // Never expose full key in API
            'apiKeyMasked' => $this->maskApiKey($data['api_key'] ?? ''),
            'systemInstructions' => $data['system_instructions'] ?? '',
            'conversationInstructions' => $data['conversation_instructions'] ?? '',
            'model' => $data['model'] ?? 'gemini-1.5-flash',
            'updatedAt' => $data['updated_at'] ?? null,
        ]);
    }

    /**
     * Save/Update Gemini settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'apiKey' => 'nullable|string|max:100',
            'systemInstructions' => 'nullable|string|max:10000',
            'conversationInstructions' => 'nullable|string|max:5000',
            'model' => 'nullable|string|in:gemini-1.5-flash,gemini-1.5-pro',
        ]);

        $apiKey = $request->input('apiKey');
        if ($apiKey !== null && $apiKey !== '' && !$this->isValidApiKeyFormat($apiKey)) {
            return response()->json(['error' => 'Invalid API key format'], 400);
        }

        $data = $this->getSettings();

        if ($apiKey !== null && $apiKey !== '') {
            $data['api_key'] = Crypt::encryptString($apiKey);
        }

        if ($request->has('systemInstructions')) {
            $data['system_instructions'] = $request->input('systemInstructions', '');
        }
        if ($request->has('conversationInstructions')) {
            $data['conversation_instructions'] = $request->input('conversationInstructions', '');
        }
        if ($request->has('model')) {
            $data['model'] = $request->input('model', 'gemini-1.5-flash');
        }

        $data['updated_at'] = now()->toIso8601String();

        $this->saveSettings($data);

        return response()->json([
            'success' => true,
            'message' => 'Settings saved',
            'updatedAt' => $data['updated_at'],
        ]);
    }

    /**
     * Test Gemini connection
     */
    public function test(Request $request)
    {
        $apiKey = $request->input('apiKey');
        if (!$apiKey) {
            $data = $this->getSettings();
            try {
                $apiKey = $data['api_key'] ? Crypt::decryptString($data['api_key']) : null;
            } catch (\Exception $e) {
                $apiKey = null;
            }
        }

        if (!$apiKey || !$this->isValidApiKeyFormat($apiKey)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
            ], 400);
        }

        $model = $this->getSettings()['model'] ?? 'gemini-1.5-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = Http::withHeaders([
            'x-goog-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                ['parts' => [['text' => 'Hello']]],
            ],
        ]);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'Connection successful',
                'model' => $model,
            ]);
        }

        $body = $response->json();
        $error = $body['error']['message'] ?? $response->body();

        return response()->json([
            'success' => false,
            'error' => $error,
        ], 400);
    }

    /**
     * Reset to default instructions
     */
    public function reset()
    {
        $data = $this->getSettings();
        $data['system_instructions'] = self::DEFAULT_SYSTEM;
        $data['conversation_instructions'] = self::DEFAULT_CONVERSATION;
        $data['updated_at'] = now()->toIso8601String();
        $this->saveSettings($data);

        return response()->json([
            'success' => true,
            'message' => 'Reset to default instructions',
            'systemInstructions' => $data['system_instructions'],
            'conversationInstructions' => $data['conversation_instructions'],
        ]);
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

    private function saveSettings(array $data): void
    {
        SiteData::updateOrCreate(
            ['data_key' => self::DATA_KEY],
            ['data_info' => $data]
        );
    }

    private function maskApiKey(?string $encrypted): string
    {
        if (!$encrypted) {
            return '';
        }
        try {
            $key = Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            return '';
        }
        if (strlen($key) < 8) {
            return '***';
        }
        return substr($key, 0, 4) . '***' . substr($key, -4);
    }

    private function isValidApiKeyFormat(?string $key): bool
    {
        return $key && preg_match('/^AIzaSy[a-zA-Z0-9_-]{35}$/', $key);
    }
}
