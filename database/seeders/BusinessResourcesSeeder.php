<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteData;

class BusinessResourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create business resources content
        $businessContent = [
            'title' => 'Learn Crowd Funding',
            'subtitle' => 'This is a comprehensive guidelines for your crowdfunding campaign success',
            'description' => 'Master the art of crowdfunding with our comprehensive resources, expert tips, and real success stories from entrepreneurs who achieved their funding goals.',
        ];

        // Check if business resources content already exists
        if (!SiteData::where('data_key', 'business_resources.content')->exists()) {
            SiteData::create([
                'data_key' => 'business_resources.content',
                'data_info' => $businessContent,
            ]);
        }

        // Create sample success stories if they don't exist
        $successStories = [
            [
                'title' => 'From Idea to $50K: A Tech Startup Success Story',
                'details' => 'Learn how a small tech startup raised $50,000 in just 30 days by following proven crowdfunding strategies and building a strong community around their product.',
                'image' => 'success-story-1.jpg',
            ],
            [
                'title' => 'Artistic Vision Meets Business Success',
                'details' => 'Discover how an independent artist turned their creative vision into a profitable business through strategic crowdfunding and community engagement.',
                'image' => 'success-story-2.jpg',
            ],
            [
                'title' => 'Social Impact: Changing Lives Through Crowdfunding',
                'details' => 'Explore how a social enterprise used crowdfunding to launch a community project that positively impacted hundreds of lives.',
                'image' => 'success-story-3.jpg',
            ],
            [
                'title' => 'The Power of Storytelling in Campaign Success',
                'details' => 'Learn the art of compelling storytelling that helped a small business owner exceed their funding goal by 300% and build lasting customer relationships.',
                'image' => 'success-story-4.jpg',
            ],
        ];

        foreach ($successStories as $index => $story) {
            if (!SiteData::where('data_key', 'success_story.element')->where('data_info->title', $story['title'])->exists()) {
                SiteData::create([
                    'data_key' => 'success_story.element',
                    'data_info' => $story,
                ]);
            }
        }

        echo "Business resources data seeded successfully!\n";
    }
}
