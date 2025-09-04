<?php

namespace App\Services;

use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Illuminate\Support\Facades\Log;
use Exception;

class YouTubeUploadService
{
    private $client;
    private $youtube;

    public function __construct()
    {
        $this->initializeClient();
    }

    private function initializeClient()
    {
        try {
            $this->client = new Google_Client();
            $this->client->setApplicationName('ApnaFund Campaign Videos');
            $this->client->setScopes([
                Google_Service_YouTube::YOUTUBE_UPLOAD,
                Google_Service_YouTube::YOUTUBE_READONLY
            ]);

            // Set credentials
            $credentialsPath = config('services.youtube.credentials_path');
            if (file_exists($credentialsPath)) {
                $this->client->setAuthConfig($credentialsPath);
            } else {
                throw new Exception('YouTube credentials file not found at: ' . $credentialsPath);
            }

            // Set access token if available
            $accessToken = config('services.youtube.access_token');
            if ($accessToken) {
                $this->client->setAccessToken($accessToken);
            }

            $this->youtube = new Google_Service_YouTube($this->client);
        } catch (Exception $e) {
            Log::error('YouTube client initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload video to YouTube
     *
     * @param string $videoPath Path to the video file
     * @param string $title Video title
     * @param string $description Video description
     * @param array $tags Video tags
     * @param string $privacyStatus Privacy status (private, public, unlisted)
     * @return string YouTube video URL
     */
    public function uploadVideo($videoPath, $title, $description = '', $tags = [], $privacyStatus = 'unlisted')
    {
        try {
            // Check if access token is valid
            if ($this->client->isAccessTokenExpired()) {
                $this->refreshAccessToken();
            }

            // Create video snippet
            $snippet = new Google_Service_YouTube_VideoSnippet();
            $snippet->setTitle($title);
            $snippet->setDescription($description);
            $snippet->setTags($tags);

            // Create video status
            $status = new Google_Service_YouTube_VideoStatus();
            $status->setPrivacyStatus($privacyStatus);

            // Create video object
            $video = new Google_Service_YouTube_Video();
            $video->setSnippet($snippet);
            $video->setStatus($status);

            // Upload video
            $insertResponse = $this->youtube->videos->insert(
                'snippet,status',
                $video,
                [
                    'data' => file_get_contents($videoPath),
                    'mimeType' => 'video/*',
                    'uploadType' => 'media'
                ]
            );

            $videoId = $insertResponse['id'];
            $youtubeUrl = 'https://www.youtube.com/watch?v=' . $videoId;

            Log::info('Video uploaded successfully to YouTube', [
                'video_id' => $videoId,
                'youtube_url' => $youtubeUrl,
                'title' => $title
            ]);

            return $youtubeUrl;

        } catch (Exception $e) {
            Log::error('YouTube upload failed: ' . $e->getMessage(), [
                'video_path' => $videoPath,
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            throw new Exception('YouTube upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Refresh access token
     */
    private function refreshAccessToken()
    {
        try {
            $refreshToken = config('services.youtube.refresh_token');
            if ($refreshToken) {
                $this->client->refreshToken($refreshToken);
                $accessToken = $this->client->getAccessToken();
                
                // Update access token in config (you might want to store this in database)
                config(['services.youtube.access_token' => $accessToken]);
            } else {
                throw new Exception('Refresh token not configured');
            }
        } catch (Exception $e) {
            Log::error('Failed to refresh YouTube access token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get authorization URL for OAuth
     */
    public function getAuthUrl()
    {
        $this->client->setRedirectUri(config('services.youtube.redirect_uri'));
        return $this->client->createAuthUrl();
    }

    /**
     * Handle OAuth callback
     */
    public function handleCallback($code)
    {
        try {
            $this->client->setRedirectUri(config('services.youtube.redirect_uri'));
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (array_key_exists('error', $accessToken)) {
                throw new Exception('Error: ' . $accessToken['error']);
            }

            $this->client->setAccessToken($accessToken);
            
            return $accessToken;
        } catch (Exception $e) {
            Log::error('YouTube OAuth callback failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if YouTube service is properly configured
     */
    public function isConfigured()
    {
        try {
            $credentialsPath = config('services.youtube.credentials_path');
            return file_exists($credentialsPath) && 
                   config('services.youtube.client_id') && 
                   config('services.youtube.client_secret');
        } catch (Exception $e) {
            return false;
        }
    }
}