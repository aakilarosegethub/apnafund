<?php

namespace App\Constants;

class FileDetails
{
    function fileDetails(): array
    {
        $data['logoFavicon'] = [
            'path' => 'assets/universal/images/logoFavicon',
        ];

        $data['favicon'] = [
            'size' => '128x128',
        ];

        $data['seo'] = [
            'path' => 'assets/universal/images/seo',
            'size' => '1180x600',
        ];

        $data['adminProfile'] = [
            'path' => 'assets/admin/images/profile',
            'size' => '200x200',
        ];

        $data['userProfile'] = [
            'path' => 'assets/user/images/profile',
            'size' => '200x200',
        ];

        $data['plugin'] = [
            'path' => 'assets/admin/images/plugin'
        ];

        $data['verify'] = [
            'path' => 'assets/verify'
        ];

        $data['category'] = [
            'path' => 'assets/universal/images/category',
            'size' => '300x300',
        ];

        $data['campaign'] = [
            'path'  => 'assets/universal/images/campaign',
            'size'  => '855x475',
            'thumb' => '415x230',
        ];

        $data['campaignOriginal'] = [
            'path' => 'assets/universal/images/campaign/original',
        ];

        $data['reward'] = [
            'path'  => 'assets/universal/images/reward',
            'size'  => '400x300',
            'thumb' => '200x150',
        ];

        $data['storyImage'] = [
            'path' => 'assets/universal/images/story',
        ];

        $data['storyVideo'] = [
            'path' => 'assets/universal/videos/story',
        ];

        $data['document'] = [
            'path' => 'assets/universal/documents/campaign',
        ];

        $data['cover'] = [
            'path' => 'assets/images/site/cover',
            'size' => '1920x1080',
        ];

        $data['success_story'] = [
            'path' => 'assets/images/site/success_story',
            'size' => '855x475',
            'thumb' => '415x230',
        ];

        $data['blog'] = [
            'path' => 'assets/universal/images/blog',
            'size' => '855x475',
            'thumb' => '415x230',
        ];

        return $data;
    }
}
