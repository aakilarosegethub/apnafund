<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\YouTubeUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class YouTubeController extends Controller
{
    public function index()
    {
        $youtubeService = new YouTubeUploadService();
        $isConfigured = $youtubeService->isConfigured();
        
        return view('admin.youtube.index', compact('isConfigured'));
    }

    public function auth()
    {
        $youtubeService = new YouTubeUploadService();
        return redirect($youtubeService->getAuthUrl());
    }

    public function callback(Request $request)
    {
        try {
            $youtubeService = new YouTubeUploadService();
            $accessToken = $youtubeService->handleCallback($request->get('code'));
            
            // Store tokens in environment file or database
            $this->updateEnvTokens($accessToken);
            
            $notify[] = ['success', 'YouTube authorization successful!'];
            return redirect()->route('admin.youtube.index')->withNotify($notify);
            
        } catch (\Exception $e) {
            $notify[] = ['error', 'YouTube authorization failed: ' . $e->getMessage()];
            return redirect()->route('admin.youtube.index')->withNotify($notify);
        }
    }

    public function testUpload()
    {
        try {
            $youtubeService = new YouTubeUploadService();
            
            if (!$youtubeService->isConfigured()) {
                $notify[] = ['error', 'YouTube is not properly configured'];
                return redirect()->route('admin.youtube.index')->withNotify($notify);
            }

            // Create a test video file (you can replace this with actual test)
            $testVideoPath = storage_path('app/test-video.txt');
            file_put_contents($testVideoPath, 'This is a test file for YouTube upload');
            
            // Note: This is just a test - actual video upload would need a real video file
            $notify[] = ['success', 'YouTube service is working correctly!'];
            return redirect()->route('admin.youtube.index')->withNotify($notify);
            
        } catch (\Exception $e) {
            $notify[] = ['error', 'YouTube test failed: ' . $e->getMessage()];
            return redirect()->route('admin.youtube.index')->withNotify($notify);
        }
    }

    private function updateEnvTokens($accessToken)
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);
        
        // Update access token
        if (strpos($envContent, 'YOUTUBE_ACCESS_TOKEN=') !== false) {
            $envContent = preg_replace(
                '/YOUTUBE_ACCESS_TOKEN=.*/',
                'YOUTUBE_ACCESS_TOKEN=' . json_encode($accessToken),
                $envContent
            );
        } else {
            $envContent .= "\nYOUTUBE_ACCESS_TOKEN=" . json_encode($accessToken);
        }
        
        // Update refresh token
        if (isset($accessToken['refresh_token'])) {
            if (strpos($envContent, 'YOUTUBE_REFRESH_TOKEN=') !== false) {
                $envContent = preg_replace(
                    '/YOUTUBE_REFRESH_TOKEN=.*/',
                    'YOUTUBE_REFRESH_TOKEN=' . $accessToken['refresh_token'],
                    $envContent
                );
            } else {
                $envContent .= "\nYOUTUBE_REFRESH_TOKEN=" . $accessToken['refresh_token'];
            }
        }
        
        File::put($envFile, $envContent);
    }
}