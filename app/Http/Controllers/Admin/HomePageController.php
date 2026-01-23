<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Image;

class HomePageController extends Controller
{
    public function index()
    {
        $pageTitle = 'Home Page Management';
        
        // Get existing content
        $heroContent = SiteData::where('data_key', 'home.hero')->first();
        $infoBannerContent = SiteData::where('data_key', 'home.info_banner')->first();
        $featuredProjectsContent = SiteData::where('data_key', 'home.featured_projects')->first();
        $trendingCampaignContent = SiteData::where('data_key', 'home.trending_campaign')->first();
        
        return view('admin.homepage.index', compact('pageTitle', 'heroContent', 'infoBannerContent', 'featuredProjectsContent', 'trendingCampaignContent'));
    }

    public function updateHero(Request $request)
    {
        $request->validate([
            'hero_heading_1' => 'required|string|max:255',
            'hero_heading_2' => 'required|string|max:255',
            'hero_heading_3' => 'required|string|max:255',
            'hero_description' => 'required|string',
            'button_text' => 'required|string|max:100',
            'button_url' => 'required|string|max:500',
            'hero_background_image' => ['nullable', 'image', File::types(['png', 'jpg', 'jpeg'])],
        ]);

        $heroContent = SiteData::where('data_key', 'home.hero')->first();
        
        if (!$heroContent) {
            $heroContent = new SiteData();
            $heroContent->data_key = 'home.hero';
        }

        $data = [
            'hero_heading_1' => $request->hero_heading_1,
            'hero_heading_2' => $request->hero_heading_2,
            'hero_heading_3' => $request->hero_heading_3,
            'hero_description' => $request->hero_description,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
        ];

        // Handle background image upload
        if ($request->hasFile('hero_background_image')) {
            try {
                $path = public_path('assets/images/site/home');
                if (!file_exists($path)) {
                    if (!mkdir($path, 0755, true)) {
                        throw new \Exception('Failed to create directory: ' . $path);
                    }
                }
                
                // Check if directory is writable
                if (!is_writable($path)) {
                    throw new \Exception('Directory is not writable: ' . $path);
                }
                
                // Delete old image if exists
                if ($heroContent && $heroContent->data_info) {
                    $oldImage = is_array($heroContent->data_info) 
                        ? ($heroContent->data_info['hero_background_image'] ?? null)
                        : ($heroContent->data_info->hero_background_image ?? null);
                    
                    if ($oldImage) {
                        $oldImagePath = $path . '/' . $oldImage;
                        if (file_exists($oldImagePath)) {
                            @unlink($oldImagePath);
                        }
                    }
                }
                
                $image = $request->file('hero_background_image');
                
                // Validate file
                if (!$image->isValid()) {
                    throw new \Exception('Invalid file uploaded');
                }
                
                $imageName = 'hero_bg_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $fullPath = $path . '/' . $imageName;
                
                // Try to resize with Image library, fallback to direct move if fails
                try {
                    $img = Image::make($image);
                    $img->resize(1920, 1080, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save($fullPath, 90); // Save with 90% quality
                    
                    // Verify file was saved
                    if (!file_exists($fullPath)) {
                        throw new \Exception('File was not saved successfully');
                    }
                } catch (\Exception $imgExp) {
                    // Fallback: just move the file without resize
                    if (!$image->move($path, $imageName)) {
                        throw new \Exception('Failed to move uploaded file');
                    }
                }
                
                $data['hero_background_image'] = $imageName;
            } catch (\Exception $exp) {
                \Log::error('Hero image upload error', [
                    'error' => $exp->getMessage(),
                    'trace' => $exp->getTraceAsString()
                ]);
                $toast[] = ['error', 'Unable to upload hero background image: ' . $exp->getMessage()];
                return back()->withToasts($toast)->withInput();
            }
        } else {
            // Keep existing image if no new image uploaded
            if ($heroContent && $heroContent->data_info) {
                $existingImage = is_array($heroContent->data_info) 
                    ? ($heroContent->data_info['hero_background_image'] ?? null)
                    : ($heroContent->data_info->hero_background_image ?? null);
                
                if ($existingImage) {
                    $data['hero_background_image'] = $existingImage;
                }
            }
        }

        $heroContent->data_info = $data;
        $heroContent->save();

        // Refresh to get updated data
        $heroContent->refresh();

        // Clear any related cache
        cache()->forget('home.hero');

        $toast[] = ['success', 'Hero section updated successfully'];
        return back()->withToasts($toast)->withInput();
    }

    public function updateInfoBanner(Request $request)
    {
        $request->validate([
            'info_item_1_icon' => 'required|string|max:50',
            'info_item_1_text' => 'required|string|max:255',
            'info_item_2_icon' => 'required|string|max:50',
            'info_item_2_text' => 'required|string|max:255',
            'info_item_3_icon' => 'required|string|max:50',
            'info_item_3_text' => 'required|string|max:255',
            'stat_1_value' => 'nullable|numeric',
            'stat_1_label' => 'nullable|string|max:255',
            'stat_2_value' => 'nullable|numeric',
            'stat_2_label' => 'nullable|string|max:255',
            'stat_3_value' => 'nullable|numeric',
            'stat_3_label' => 'nullable|string|max:255',
        ]);

        $infoBannerContent = SiteData::where('data_key', 'home.info_banner')->first();
        
        if (!$infoBannerContent) {
            $infoBannerContent = new SiteData();
            $infoBannerContent->data_key = 'home.info_banner';
        }

        $data = [
            'info_item_1_icon' => $request->info_item_1_icon,
            'info_item_1_text' => $request->info_item_1_text,
            'info_item_2_icon' => $request->info_item_2_icon,
            'info_item_2_text' => $request->info_item_2_text,
            'info_item_3_icon' => $request->info_item_3_icon,
            'info_item_3_text' => $request->info_item_3_text,
        ];

        // Add stats (use existing values if not provided)
        $data['stat_1_value'] = $request->stat_1_value ?? ($infoBannerContent && isset($infoBannerContent->data_info->stat_1_value) ? $infoBannerContent->data_info->stat_1_value : 12000000);
        $data['stat_1_label'] = $request->stat_1_label ?? ($infoBannerContent && isset($infoBannerContent->data_info->stat_1_label) ? $infoBannerContent->data_info->stat_1_label : 'Total Funded');
        $data['stat_2_value'] = $request->stat_2_value ?? ($infoBannerContent && isset($infoBannerContent->data_info->stat_2_value) ? $infoBannerContent->data_info->stat_2_value : 2500);
        $data['stat_2_label'] = $request->stat_2_label ?? ($infoBannerContent && isset($infoBannerContent->data_info->stat_2_label) ? $infoBannerContent->data_info->stat_2_label : 'Successful Projects');
        $data['stat_3_value'] = $request->stat_3_value ?? ($infoBannerContent && isset($infoBannerContent->data_info->stat_3_value) ? $infoBannerContent->data_info->stat_3_value : 50);
        $data['stat_3_label'] = $request->stat_3_label ?? ($infoBannerContent && isset($infoBannerContent->data_info->stat_3_label) ? $infoBannerContent->data_info->stat_3_label : 'Active Backers');

        $infoBannerContent->data_info = $data;
        $infoBannerContent->save();

        $toast[] = ['success', 'Info banner section updated successfully'];
        return back()->withToasts($toast);
    }

    public function updateFeaturedProjects(Request $request)
    {
        $request->validate([
            'section_title' => 'required|string|max:255',
            'view_all_button_text' => 'required|string|max:100',
            'view_all_button_url' => 'required|string|max:255',
        ]);

        $featuredProjectsContent = SiteData::where('data_key', 'home.featured_projects')->first();
        
        if (!$featuredProjectsContent) {
            $featuredProjectsContent = new SiteData();
            $featuredProjectsContent->data_key = 'home.featured_projects';
        }

        $data = [
            'section_title' => $request->section_title,
            'view_all_button_text' => $request->view_all_button_text,
            'view_all_button_url' => $request->view_all_button_url,
        ];

        $featuredProjectsContent->data_info = $data;
        $featuredProjectsContent->save();

        $toast[] = ['success', 'Featured projects section updated successfully'];
        return back()->withToasts($toast);
    }

    public function updateTrendingCampaign(Request $request)
    {
        $request->validate([
            'show_trending' => 'required|in:0,1',
            'trending_campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        $trendingCampaignContent = SiteData::where('data_key', 'home.trending_campaign')->first();
        
        if (!$trendingCampaignContent) {
            $trendingCampaignContent = new SiteData();
            $trendingCampaignContent->data_key = 'home.trending_campaign';
        }

        $data = [
            'show_trending' => (int)$request->show_trending,
            'trending_campaign_id' => $request->trending_campaign_id ? (int)$request->trending_campaign_id : null,
        ];

        $trendingCampaignContent->data_info = $data;
        $trendingCampaignContent->save();

        $toast[] = ['success', 'Trending campaign section updated successfully'];
        return back()->withToasts($toast);
    }
} 