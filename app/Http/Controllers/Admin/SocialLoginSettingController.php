<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use Illuminate\Http\Request;

class SocialLoginSettingController extends Controller
{
    /**
     * Show social login settings page
     */
    public function index()
    {
        $pageTitle = 'Social Login Settings';
        
        // Get social login settings
        $socialLoginSettings = SiteData::where('data_key', 'social_login.data')->first();
        
        if (!$socialLoginSettings) {
            // Create default settings
            $defaultSettings = [
                'facebook' => [
                    'status' => false,
                    'client_id' => '',
                    'client_secret' => '',
                    'redirect_uri' => env('APP_URL') . '/user/auth/facebook/callback'
                ],
                'google' => [
                    'status' => false,
                    'client_id' => '',
                    'client_secret' => '',
                    'redirect_uri' => env('APP_URL') . '/user/auth/google/callback'
                ]
            ];
            
            $socialLoginSettings = new SiteData();
            $socialLoginSettings->data_key = 'social_login.data';
            $socialLoginSettings->data_info = $defaultSettings;
            $socialLoginSettings->save();
        }

        return view('admin.setting.social_login', compact('pageTitle', 'socialLoginSettings'));
    }

    /**
     * Update social login settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'facebook_status' => 'boolean',
            'facebook_client_id' => 'nullable|string|max:255',
            'facebook_client_secret' => 'nullable|string|max:255',
            'google_status' => 'boolean',
            'google_client_id' => 'nullable|string|max:255',
            'google_client_secret' => 'nullable|string|max:255',
        ]);

        $socialLoginSettings = SiteData::where('data_key', 'social_login.data')->first();
        
        if (!$socialLoginSettings) {
            $socialLoginSettings = new SiteData();
            $socialLoginSettings->data_key = 'social_login.data';
        }

        $settings = $socialLoginSettings->data_info ?? [];

        // Update Facebook settings
        $settings['facebook'] = [
            'status' => $request->boolean('facebook_status'),
            'client_id' => $request->facebook_client_id ?? '',
            'client_secret' => $request->facebook_client_secret ?? '',
            'redirect_uri' => env('APP_URL') . '/user/auth/facebook/callback'
        ];

        // Update Google settings
        $settings['google'] = [
            'status' => $request->boolean('google_status'),
            'client_id' => $request->google_client_id ?? '',
            'client_secret' => $request->google_client_secret ?? '',
            'redirect_uri' => env('APP_URL') . '/user/auth/google/callback'
        ];

        $socialLoginSettings->data_info = $settings;
        $socialLoginSettings->save();

        // Update .env file
        $this->updateEnvFile($settings);

        $toast[] = ['success', 'Social login settings updated successfully'];
        return back()->withToasts($toast);
    }

    /**
     * Update .env file with social login credentials
     */
    private function updateEnvFile($settings)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);
        
        // Facebook settings
        $envContent = $this->updateEnvVariable($envContent, 'FACEBOOK_CLIENT_ID', $settings['facebook']['client_id']);
        $envContent = $this->updateEnvVariable($envContent, 'FACEBOOK_CLIENT_SECRET', $settings['facebook']['client_secret']);
        $envContent = $this->updateEnvVariable($envContent, 'FACEBOOK_REDIRECT_URI', $settings['facebook']['redirect_uri']);
        
        // Google settings
        $envContent = $this->updateEnvVariable($envContent, 'GOOGLE_CLIENT_ID', $settings['google']['client_id']);
        $envContent = $this->updateEnvVariable($envContent, 'GOOGLE_CLIENT_SECRET', $settings['google']['client_secret']);
        $envContent = $this->updateEnvVariable($envContent, 'GOOGLE_REDIRECT_URI', $settings['google']['redirect_uri']);

        file_put_contents($envFile, $envContent);
    }

    /**
     * Update or add environment variable
     */
    private function updateEnvVariable($content, $key, $value)
    {
        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}={$value}";
        
        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $replacement, $content);
        } else {
            return $content . "\n{$replacement}";
        }
    }

    /**
     * Test social login configuration
     */
    public function testConfiguration(Request $request)
    {
        $provider = $request->input('provider');
        
        try {
            if ($provider === 'facebook') {
                $clientId = config('services.facebook.client_id');
                $clientSecret = config('services.facebook.client_secret');
                
                if (empty($clientId) || empty($clientSecret)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Facebook credentials not configured'
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Facebook configuration is valid'
                ]);
                
            } elseif ($provider === 'google') {
                $clientId = config('services.google.client_id');
                $clientSecret = config('services.google.client_secret');
                
                if (empty($clientId) || empty($clientSecret)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Google credentials not configured'
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Google configuration is valid'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid provider'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration test failed: ' . $e->getMessage()
            ]);
        }
    }
}
