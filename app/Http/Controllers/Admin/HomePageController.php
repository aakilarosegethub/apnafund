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
        
        return view('admin.homepage.index', compact('pageTitle', 'heroContent', 'infoBannerContent', 'featuredProjectsContent'));
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
                $path = 'assets/images/site/home';
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                
                $image = $request->file('hero_background_image');
                $imageName = 'hero_bg_' . time() . '.' . $image->getClientOriginalExtension();
                
                Image::make($image)->resize(1920, 1080)->save($path . '/' . $imageName);
                
                $data['hero_background_image'] = $imageName;
            } catch (\Exception $exp) {
                $toast[] = ['error', 'Unable to upload hero background image'];
                return back()->withToasts($toast);
            }
        } else {
            // Keep existing image if no new image uploaded
            if ($heroContent && isset($heroContent->data_info->hero_background_image)) {
                $data['hero_background_image'] = $heroContent->data_info->hero_background_image;
            }
        }

        $heroContent->data_info = $data;
        $heroContent->save();

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
} 