<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use Exception;
use HTMLPurifier;
use Illuminate\Validation\Rules\File;

class SiteController extends Controller
{
    public function themes()
    {
        $pageTitle = 'Themes';
        $themePaths = array_filter(
            glob(resource_path('views/themes/*')),
            'is_dir'
        );
        $themes = [];

        foreach ($themePaths as $key => $theme) {
            $arr = explode('/', $theme);
            $themeName = end($arr);
            $themes[$key]['name'] = $themeName;
            // Check if theme preview image exists in public directory
            $publicImagePath = public_path('assets/images/themes/'.$themeName.'.jpg');
            if (file_exists($publicImagePath)) {
                $themes[$key]['image'] = asset('assets/images/themes/'.$themeName.'.jpg');
            } else {
                // Use default placeholder image
                $themes[$key]['image'] = asset('assets/admin/images/light.png');
            }
        }

        return view('admin.site.themes', compact('pageTitle', 'themes'));
    }

    public function makeActive()
    {
        $setting = bs();
        $setting->active_theme = request('name');
        $setting->save();

        $toast[] = ['success', strtoupper(request('name')).' theme activation success'];

        return back()->withToasts($toast);
    }

    public function sections($key)
    {
        try {
            $sections = getPageSections();

            // Convert to array for easier checking
            $sectionsArray = (array) $sections;
            $section = $sectionsArray[$key] ?? null;

            // If not found as object property, try accessing as object
            if (! $section && is_object($sections)) {
                $section = $sections->$key ?? null;
            }

            if (! $section) {
                // Log for debugging
                $availableSections = array_keys($sectionsArray);
                \Log::error('Section not found in site.json', [
                    'key' => $key,
                    'available_sections' => $availableSections,
                    'active_theme' => bs('active_theme'),
                    'json_path' => resource_path('views/').str_replace('.', '/', activeTheme()).'site.json',
                ]);

                $errorMessage = "Section '{$key}' not found in site.json.";
                $errorMessage .= ' Available sections: '.implode(', ', $availableSections);
                $errorMessage .= ' | Active theme: '.bs('active_theme');

                abort(404, $errorMessage);
            }
        } catch (\Exception $e) {
            \Log::error('Error loading sections', [
                'key' => $key,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'Error loading section: '.$e->getMessage());
        }

        $content = SiteData::where('data_key', $key.'.content')->orderBy('id', 'desc')->first();

        // Get elements with filtering and sorting
        $elementsQuery = SiteData::where('data_key', $key.'.element');

        // Filter by section_type if provided
        $sectionTypeFilter = request('section_type');
        if ($sectionTypeFilter) {
            // Use JSON path extraction for MySQL compatibility
            $elementsQuery->whereRaw("JSON_EXTRACT(data_info, '$.section_type') = ?", [$sectionTypeFilter]);
        }

        // Get all elements first, then filter and sort in PHP
        $allElements = $elementsQuery->get();

        // Filter by section_type in PHP if needed (more reliable)
        if ($sectionTypeFilter) {
            $allElements = $allElements->filter(function ($item) use ($sectionTypeFilter) {
                return isset($item->data_info['section_type']) && $item->data_info['section_type'] == $sectionTypeFilter;
            });
        }

        // Order by sort_order (from data_info JSON), then by id
        $elements = $allElements->sortBy(function ($item) {
            return $item->data_info['sort_order'] ?? 999999;
        })->values();

        $seoContent = SiteData::where('data_key', $key.'.seo')->first();
        $pageTitle = $section->name;

        return view('admin.site.index', compact('section', 'content', 'elements', 'seoContent', 'key', 'pageTitle', 'sectionTypeFilter'));
    }

    public function content($key)
    {
        $purifier = new HTMLPurifier;
        $type = request('type');

        if (! $type) {
            abort(404);
        }

        // Special handling for 'seo' key which is not in site.json
        if ($key == 'seo' && $type == 'data') {
            return $this->saveSeoData($purifier);
        }

        $imgJson = @getPageSections()->$key->$type->images;
        $validationRule = [];
        $validationMessage = [];
        $excludeFromValidation = [
            '_token', 'video', 'key', 'status', 'type', 'id', 'image_url', 'sort_order',
            // SEO helper fields shown on same page; they are saved separately below.
            'seo_meta_title', 'seo_meta_description', 'seo_meta_keywords',
            'seo_meta_author', 'seo_meta_robots', 'seo_canonical_url',
            'seo_meta_viewport', 'seo_meta_charset',
        ];

        // For page_seo, schema_markup, footer_menu, and dynamic_pages, don't exclude slug from validation
        if ($key != 'page_seo' && $key != 'schema_markup' && $key != 'footer_menu' && $key != 'dynamic_pages') {
            $excludeFromValidation = array_merge($excludeFromValidation, ['meta_title', 'meta_description', 'meta_keywords', 'slug']);
        }

        // Add image URL and alt fields to exclusion list
        if ($imgJson) {
            foreach ($imgJson as $imgKey => $imgValue) {
                $excludeFromValidation[] = $imgKey.'_url';
                $excludeFromValidation[] = $imgKey.'_alt';
            }
        }

        foreach (request()->except($excludeFromValidation) as $inputField => $val) {
            if ($inputField == 'has_image' && $imgJson) {
                foreach ($imgJson as $imgValKey => $imgJsonVal) {
                    $validationRule['image_input.'.$imgValKey] = ['nullable', 'image', File::types(['png', 'jpg', 'jpeg'])];
                    $validationMessage['image_input.'.$imgValKey.'.image'] = keyToTitle($imgValKey).' must be an image';
                    $validationMessage['image_input.'.$imgValKey.'.mimes'] = keyToTitle($imgValKey).' file type not supported';
                }

                continue;
            } elseif ($inputField == 'seo_image') {
                $validationRule['image_input'] = ['nullable', 'image', File::types(['png', 'jpg', 'jpeg'])];

                continue;
            } elseif ($inputField == 'image_input') {
                // Skip image_input in validation rules, already handled above
                continue;
            }

            // For dynamic_pages, only title and slug are required
            if ($key == 'dynamic_pages' && $type == 'element') {
                if ($inputField == 'title' || $inputField == 'slug') {
                    $validationRule[$inputField] = 'required';
                } else {
                    $validationRule[$inputField] = 'nullable';
                }
            }
            // For page_seo, all fields are optional (no validation)
            elseif ($key == 'page_seo' && $type == 'element') {
                $validationRule[$inputField] = 'nullable';
            }
            // For schema_markup, slug is required, other fields are optional
            elseif ($key == 'schema_markup' && $type == 'element') {
                if ($inputField == 'slug') {
                    $validationRule[$inputField] = 'required';
                } else {
                    $validationRule[$inputField] = 'nullable';
                }
            }
            // For footer section, footer_text is optional
            elseif ($key == 'footer' && $type == 'content' && $inputField == 'footer_text') {
                $validationRule[$inputField] = 'nullable';
            }
            // For contact_us section, latitude and longitude are optional
            elseif ($key == 'contact_us' && $type == 'content' && in_array($inputField, ['latitude', 'longitude'])) {
                $validationRule[$inputField] = 'nullable';
            }
            // For success_story, slug and meta fields are optional
            elseif ($key == 'success_story' && $type == 'element' && in_array($inputField, ['slug', 'meta_title', 'meta_description', 'meta_keywords'])) {
                $validationRule[$inputField] = 'nullable';
            }
            // For footer_menu, all fields are optional (no validation)
            elseif ($key == 'footer_menu' && $type == 'element') {
                $validationRule[$inputField] = 'nullable';
            } else {
                $validationRule[$inputField] = 'required';
            }
        }

        request()->validate($validationRule, $validationMessage, ['image_input' => 'image']);

        // Initialize inputContentValue array
        $inputContentValue = [];

        // Get all inputs except excluded fields
        $valInputs = request()->except(array_merge($excludeFromValidation, ['image_input']));

        // Handle sort_order separately (allow 0)
        if (request()->has('sort_order')) {
            $sortOrder = request('sort_order');
            $inputContentValue['sort_order'] = is_numeric($sortOrder) ? (int) $sortOrder : 0;
        }

        foreach ($valInputs as $keyName => $input) {
            // Skip sort_order as it's already handled above
            if ($keyName == 'sort_order') {
                continue;
            }

            // For footer section, allow empty footer_text to be saved
            if ($key == 'footer' && $type == 'content' && $keyName == 'footer_text') {
                // Allow empty string for footer_text
                $inputContentValue[$keyName] = $input === null ? '' : htmlspecialchars_decode($purifier->purify($input));

                continue;
            }

            // For contact_us section, always persist latitude/longitude (including empty values).
            if ($key == 'contact_us' && $type == 'content' && in_array($keyName, ['latitude', 'longitude'], true)) {
                $inputContentValue[$keyName] = $input === null ? '' : trim((string) $input);

                continue;
            }

            // Handle slug generation for dynamic_pages
            if ($key == 'dynamic_pages' && $type == 'element' && $keyName == 'slug') {
                if (empty($input) && isset($valInputs['title']) && ! empty($valInputs['title'])) {
                    // Auto-generate slug from title if not provided
                    $input = slug($valInputs['title']);
                } elseif (! empty($input)) {
                    // Clean and format the provided slug
                    $input = slug($input);
                }

                // Ensure slug is unique
                if (! empty($input)) {
                    $existingSlug = SiteData::where('data_key', $key.'.element')
                        ->where('id', '!=', request('id'))
                        ->whereRaw("JSON_EXTRACT(data_info, '$.slug') = ?", [$input])
                        ->first();

                    if ($existingSlug) {
                        $counter = 1;
                        $baseSlug = $input;
                        do {
                            $input = $baseSlug.'-'.$counter;
                            $existingSlug = SiteData::where('data_key', $key.'.element')
                                ->where('id', '!=', request('id'))
                                ->whereRaw("JSON_EXTRACT(data_info, '$.slug') = ?", [$input])
                                ->first();
                            $counter++;
                        } while ($existingSlug && $counter < 1000);
                    }
                }

                // Always save slug for dynamic_pages (required field)
                $inputContentValue[$keyName] = ! empty($input) ? $input : '';

                continue;
            }

            // Handle slug for dynamic_pages - ensure it's unique and properly formatted
            if ($key == 'dynamic_pages' && $type == 'element' && $keyName == 'slug') {
                if (! empty($input)) {
                    // Clean and format the provided slug
                    $input = slug($input);

                    // Ensure slug is unique
                    $existingSlug = SiteData::where('data_key', $key.'.element')
                        ->where('id', '!=', request('id'))
                        ->whereRaw("JSON_EXTRACT(data_info, '$.slug') = ?", [$input])
                        ->first();

                    if ($existingSlug) {
                        $counter = 1;
                        $baseSlug = $input;
                        do {
                            $input = $baseSlug.'-'.$counter;
                            $existingSlug = SiteData::where('data_key', $key.'.element')
                                ->where('id', '!=', request('id'))
                                ->whereRaw("JSON_EXTRACT(data_info, '$.slug') = ?", [$input])
                                ->first();
                            $counter++;
                        } while ($existingSlug && $counter < 1000);
                    }

                    $inputContentValue[$keyName] = $input;
                } else {
                    // Slug is required for dynamic_pages, but if empty, set empty string
                    $inputContentValue[$keyName] = '';
                }

                continue;
            }

            // Handle slug generation for success_story
            if ($key == 'success_story' && $type == 'element' && $keyName == 'slug') {
                if (empty($input) && isset($valInputs['title']) && ! empty($valInputs['title'])) {
                    // Auto-generate slug from title if not provided
                    $input = slug($valInputs['title']);
                } elseif (! empty($input)) {
                    // Clean and format the provided slug
                    $input = slug($input);
                }

                // Ensure slug is unique
                if (! empty($input)) {
                    $existingSlug = SiteData::where('data_key', $key.'.element')
                        ->where('id', '!=', request('id'))
                        ->whereRaw("JSON_EXTRACT(data_info, '$.slug') = ?", [$input])
                        ->first();

                    if ($existingSlug) {
                        $counter = 1;
                        $baseSlug = $input;
                        do {
                            $input = $baseSlug.'-'.$counter;
                            $existingSlug = SiteData::where('data_key', $key.'.element')
                                ->where('id', '!=', request('id'))
                                ->whereRaw("JSON_EXTRACT(data_info, '$.slug') = ?", [$input])
                                ->first();
                            $counter++;
                        } while ($existingSlug && $counter < 1000);
                    }
                }

                if (! empty($input)) {
                    $inputContentValue[$keyName] = $input;
                }

                continue;
            }

            // Handle slug for page_seo - preserve forward slashes (e.g., "page/about")
            if ($key == 'page_seo' && $type == 'element' && $keyName == 'slug') {
                if (! empty($input)) {
                    // For page_seo, don't use slug() function as it removes forward slashes
                    // Just trim and clean the input, but preserve forward slashes
                    $input = trim($input);
                    // Remove only leading/trailing slashes, but keep internal ones
                    $input = trim($input, '/');

                    // No uniqueness check - allow any value
                    $inputContentValue[$keyName] = $input;
                } else {
                    // Slug is optional for page_seo, set empty string if empty
                    $inputContentValue[$keyName] = '';
                }

                continue;
            }

            // Handle slug for schema_markup - preserve forward slashes (e.g., "page/about")
            if ($key == 'schema_markup' && $type == 'element' && $keyName == 'slug') {
                if (! empty($input)) {
                    // For schema_markup, don't use slug() function as it removes forward slashes
                    // Just trim and clean the input, but preserve forward slashes
                    $input = trim($input);
                    // Remove only leading/trailing slashes, but keep internal ones
                    $input = trim($input, '/');

                    // Ensure slug is unique (check with the exact input)
                    $existingSlug = SiteData::where('data_key', $key.'.element')
                        ->where('id', '!=', request('id'))
                        ->whereRaw("JSON_EXTRACT(data_info, '$.slug') = ?", [$input])
                        ->first();

                    if ($existingSlug) {
                        $counter = 1;
                        $baseSlug = $input;
                        do {
                            $input = $baseSlug.'-'.$counter;
                            $existingSlug = SiteData::where('data_key', $key.'.element')
                                ->where('id', '!=', request('id'))
                                ->whereRaw("JSON_EXTRACT(data_info, '$.slug') = ?", [$input])
                                ->first();
                            $counter++;
                        } while ($existingSlug && $counter < 1000);
                    }

                    $inputContentValue[$keyName] = $input;
                } else {
                    // Slug is required, but if empty, set empty string for schema_markup
                    $inputContentValue[$keyName] = '';
                }

                continue;
            }

            // Handle slug for footer_menu - No validations, save as-is
            if ($key == 'footer_menu' && $type == 'element' && $keyName == 'slug') {
                // Save slug exactly as entered - no validation, no normalization, no uniqueness check
                // User can enter anything: /path, /path/to/page, full URL, or simple slug
                $inputContentValue[$keyName] = $input ?? '';

                continue;
            }

            // For page_seo, process all fields (including empty ones)
            if ($key == 'page_seo' && $type == 'element') {
                if (gettype($input) == 'array') {
                    $inputContentValue[$keyName] = $input;

                    continue;
                }

                // Allow empty strings for optional fields
                if ($input === null) {
                    $input = '';
                }
                $purified = htmlspecialchars_decode($purifier->purify($input));
                $inputContentValue[$keyName] = $purified;

                continue;
            }

            // For schema_markup, process all fields (including empty ones)
            if ($key == 'schema_markup' && $type == 'element') {
                if (gettype($input) == 'array') {
                    $inputContentValue[$keyName] = $input;

                    continue;
                }

                // For schema_json, don't purify (keep JSON as is)
                if ($keyName == 'schema_json') {
                    if ($input === null) {
                        $input = '';
                    }
                    // Don't purify JSON, keep it as raw
                    $inputContentValue[$keyName] = $input;
                } else {
                    // Allow empty strings for optional fields
                    if ($input === null) {
                        $input = '';
                    }
                    $purified = htmlspecialchars_decode($purifier->purify($input));
                    $inputContentValue[$keyName] = $purified;
                }

                continue;
            }

            // For footer_menu, process all fields (slug is already handled above, skip it here)
            if ($key == 'footer_menu' && $type == 'element') {
                // Skip slug as it's already handled above
                if ($keyName == 'slug') {
                    continue;
                }

                if (gettype($input) == 'array') {
                    $inputContentValue[$keyName] = $input;

                    continue;
                }

                // Process all fields including empty ones for footer_menu
                if ($input === null) {
                    $input = '';
                }
                $purified = htmlspecialchars_decode($purifier->purify($input));
                $inputContentValue[$keyName] = $purified;

                continue;
            }

            // Skip empty values but allow 0 and false (for other sections)
            if ($input === null || $input === '') {
                continue;
            }

            if (gettype($input) == 'array') {
                $inputContentValue[$keyName] = $input;

                continue;
            }

            // Process all non-empty inputs
            $purified = htmlspecialchars_decode($purifier->purify($input));
            $inputContentValue[$keyName] = $purified;
        }

        if (request('id')) {
            $content = SiteData::findOrFail(request('id'));
        } else {
            $content = SiteData::where('data_key', $key.'.'.request('type'))->first();

            if (! $content || request('type') == 'element') {
                $content = new SiteData;
                $content->data_key = $key.'.'.request('type');
                $content->save();
            }
        }

        if ($type == 'data') {
            $inputContentValue['image'] = @$content->data_info['image'] ?? null;
            $imageUrl = request('image_url');

            // Priority 1: If new file is uploaded, replace old image
            if (request()->hasFile('image_input')) {
                try {
                    $oldImage = @$content->data_info['image'] ?? null;
                    $inputContentValue['image'] = fileUploader(request('image_input'), getFilePath('seo'), getFileSize('seo'), $oldImage);
                } catch (Exception $exp) {
                    $toast[] = ['error', 'Image upload failed'];

                    return back()->withToasts($toast);
                }
            }
            // Priority 2: If URL is provided, use URL (replace old image if it's a local file)
            elseif ($imageUrl && $imageUrl !== '') {
                $oldImage = @$content->data_info['image'] ?? null;

                // If it's a full URL, check if it's from current domain
                if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $currentUrl = url('/');
                    // If URL is from current domain, extract relative path
                    if (strpos($imageUrl, $currentUrl) === 0) {
                        // Extract path from full URL
                        $parsedUrl = parse_url($imageUrl);
                        $path = isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') : '';
                        // Remove public/ if present
                        $path = str_replace('public/', '', $path);
                        $inputContentValue['image'] = $path;

                        // Delete old image file if it exists and is a local file (not a URL)
                        if ($oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL)) {
                            $oldImagePath = public_path(getFilePath('seo').'/'.$oldImage);
                            if (file_exists($oldImagePath)) {
                                @unlink($oldImagePath);
                            }
                        }
                    } else {
                        // External URL, save as is
                        $inputContentValue['image'] = $imageUrl;

                        // Delete old image file if it exists and is a local file
                        if ($oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL)) {
                            $oldImagePath = public_path(getFilePath('seo').'/'.$oldImage);
                            if (file_exists($oldImagePath)) {
                                @unlink($oldImagePath);
                            }
                        }
                    }
                } else {
                    // Not a valid URL, treat as relative path
                    $inputContentValue['image'] = ltrim($imageUrl, '/');

                    // Delete old image file if it exists and is a local file
                    if ($oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL)) {
                        $oldImagePath = public_path(getFilePath('seo').'/'.$oldImage);
                        if (file_exists($oldImagePath)) {
                            @unlink($oldImagePath);
                        }
                    }
                }
            }
        } else {

            if ($imgJson) {

                foreach ($imgJson as $imgKey => $imgValue) {
                    $imgData = null;

                    // Check if file is uploaded - form field name is image_input[imgKey]
                    // Laravel converts array notation to dot notation
                    if (request()->hasFile('image_input.'.$imgKey)) {
                        $imgData = request()->file('image_input.'.$imgKey);
                        if (! $imgData || ! $imgData->isValid()) {
                            // Skip invalid image, continue with other content
                            continue;
                        }
                    }
                    // Alternative: check if image_input is an array
                    elseif (request()->has('image_input')) {
                        $allFiles = request()->file('image_input');
                        if (is_array($allFiles) && isset($allFiles[$imgKey]) && $allFiles[$imgKey]) {
                            $imgData = $allFiles[$imgKey];
                            if (! $imgData->isValid()) {
                                // Skip invalid image, continue with other content
                                continue;
                            }
                        }
                    }

                    $imgUrl = @request()->input($imgKey.'_url');
                    $oldImage = @$content->data_info[$imgKey] ?? null;

                    // Priority 1: If new file is uploaded, replace old image
                    if ($imgData && $imgData->isValid()) {
                        try {
                            // Simple core PHP style upload
                            $uploadPath = public_path('assets/images/site/'.$key);

                            // Create directory if not exists
                            if (! file_exists($uploadPath)) {
                                @mkdir($uploadPath, 0777, true);
                                @chmod($uploadPath, 0777);
                            }

                            // Generate new unique filename
                            $fileExtension = $imgData->getClientOriginalExtension();
                            $newFileName = uniqid().time().'.'.$fileExtension;
                            $fullPath = $uploadPath.'/'.$newFileName;

                            // Delete old image first if exists
                            if ($oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL)) {
                                $oldImagePath = $uploadPath.'/'.$oldImage;
                                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                                    @chmod($oldImagePath, 0777);
                                    @unlink($oldImagePath);
                                }

                                // Delete thumbnail if exists
                                $oldThumbPath = $uploadPath.'/thumb_'.$oldImage;
                                if (file_exists($oldThumbPath) && is_file($oldThumbPath)) {
                                    @chmod($oldThumbPath, 0777);
                                    @unlink($oldThumbPath);
                                }
                            }

                            // Get image size from config
                            $size = @$imgJson->$imgKey->size ?? null;
                            $thumb = @$imgJson->$imgKey->thumb ?? null;

                            // Resize and save image using Intervention Image
                            $image = \Intervention\Image\Facades\Image::make($imgData);

                            // Resize if size is specified
                            if ($size) {
                                $sizeArray = explode('x', strtolower($size));
                                if (count($sizeArray) == 2) {
                                    $image->resize((int) $sizeArray[0], (int) $sizeArray[1], function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                }
                            }

                            // Save main image
                            $image->save($fullPath, 90); // 90% quality
                            @chmod($fullPath, 0777);

                            // Create thumbnail if specified
                            if ($thumb) {
                                $thumbArray = explode('x', strtolower($thumb));
                                if (count($thumbArray) == 2) {
                                    $thumbImage = \Intervention\Image\Facades\Image::make($imgData);
                                    $thumbImage->resize((int) $thumbArray[0], (int) $thumbArray[1], function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                    $thumbPath = $uploadPath.'/thumb_'.$newFileName;
                                    $thumbImage->save($thumbPath, 90);
                                    @chmod($thumbPath, 0777);
                                }
                            }

                            // Save new image name
                            $inputContentValue[$imgKey] = $newFileName;

                            // Generate and display URL
                            $imageUrl = asset('assets/images/site/'.$key.'/'.$newFileName);
                            $relativePath = 'assets/images/site/'.$key.'/'.$newFileName;

                            // Debug: Show upload success with URL
                            \Log::info('Image uploaded successfully', [
                                'imgKey' => $imgKey,
                                'filename' => $newFileName,
                                'fullUrl' => $imageUrl,
                                'relativePath' => $relativePath,
                                'uploadPath' => $uploadPath,
                            ]);

                        } catch (Exception $exp) {
                            $toast[] = ['error', 'Image upload failed: '.$exp->getMessage()];
                            \Log::error('Image upload error: '.$exp->getMessage(), [
                                'key' => $key,
                                'imgKey' => $imgKey,
                                'oldImage' => $oldImage,
                                'trace' => $exp->getTraceAsString(),
                            ]);

                            return back()->withToasts($toast)->withInput();
                        }
                    }
                    // Priority 2: If URL is provided, use URL (replace old image if it's a local file)
                    elseif ($imgUrl && $imgUrl !== '') {
                        // If it's a full URL, check if it's from current domain
                        if (filter_var($imgUrl, FILTER_VALIDATE_URL)) {
                            $currentUrl = url('/');
                            // If URL is from current domain, extract relative path
                            if (strpos($imgUrl, $currentUrl) === 0) {
                                // Extract path from full URL
                                $parsedUrl = parse_url($imgUrl);
                                $path = isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') : '';
                                // Remove public/ if present
                                $path = str_replace('public/', '', $path);
                                $inputContentValue[$imgKey] = $path;

                                // Delete old image file if it exists and is a local file (not a URL)
                                if ($oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL)) {
                                    $oldImagePath = public_path('assets/images/site/'.$key.'/'.$oldImage);
                                    if (file_exists($oldImagePath)) {
                                        @unlink($oldImagePath);
                                        // Also delete thumbnail if exists
                                        $oldThumbPath = public_path('assets/images/site/'.$key.'/thumb_'.$oldImage);
                                        if (file_exists($oldThumbPath)) {
                                            @unlink($oldThumbPath);
                                        }
                                    }
                                }
                            } else {
                                // External URL, save as is
                                $inputContentValue[$imgKey] = $imgUrl;

                                // Delete old image file if it exists and is a local file
                                if ($oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL)) {
                                    $oldImagePath = public_path('assets/images/site/'.$key.'/'.$oldImage);
                                    if (file_exists($oldImagePath)) {
                                        @unlink($oldImagePath);
                                        // Also delete thumbnail if exists
                                        $oldThumbPath = public_path('assets/images/site/'.$key.'/thumb_'.$oldImage);
                                        if (file_exists($oldThumbPath)) {
                                            @unlink($oldThumbPath);
                                        }
                                    }
                                }
                            }
                        } else {
                            // Not a valid URL, treat as relative path
                            $inputContentValue[$imgKey] = ltrim($imgUrl, '/');

                            // Delete old image file if it exists and is a local file
                            if ($oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL)) {
                                $oldImagePath = public_path('assets/images/site/'.$key.'/'.$oldImage);
                                if (file_exists($oldImagePath)) {
                                    @unlink($oldImagePath);
                                    // Also delete thumbnail if exists
                                    $oldThumbPath = public_path('assets/images/site/'.$key.'/thumb_'.$oldImage);
                                    if (file_exists($oldThumbPath)) {
                                        @unlink($oldThumbPath);
                                    }
                                }
                            }
                        }
                    }
                    // Priority 3: Keep existing value if no new input (no file uploaded and no URL provided)
                    else {
                        // If no new image uploaded and no URL provided, keep existing image
                        if (isset($content->data_info[$imgKey])) {
                            $inputContentValue[$imgKey] = $content->data_info[$imgKey];
                        }
                        // If no existing image, don't set anything (field will remain empty)
                    }

                    // Handle alt text
                    if (request()->has($imgKey.'_alt')) {
                        $inputContentValue[$imgKey.'_alt'] = request($imgKey.'_alt');
                    }
                }
            }
        }

        // Merge with existing data to preserve fields not in the form
        $existingData = is_array($content->data_info) ? $content->data_info : [];

        // For page_seo, schema_markup, footer_menu, and dynamic_pages, replace all data (don't merge, replace completely)
        if (($key == 'page_seo' || $key == 'schema_markup' || $key == 'footer_menu' || $key == 'dynamic_pages') && $type == 'element') {
            $content->data_info = $inputContentValue;
        } else {
            $content->data_info = array_merge($existingData, $inputContentValue);
        }

        // Ensure data_info is properly set
        if (empty($content->data_info)) {
            $content->data_info = $inputContentValue;
        }

        try {
            if (! $content->save()) {
                $toast[] = ['error', 'Failed to save content'];

                return back()->withToasts($toast)->withInput();
            }

        } catch (\Exception $e) {
            $toast[] = ['error', 'Error saving content: '.$e->getMessage()];
            \Log::error('SiteController content save error: '.$e->getMessage(), [
                'key' => $key,
                'type' => $type,
                'data' => $inputContentValue,
            ]);

            return back()->withToasts($toast)->withInput();
        }

        // Handle SEO data if provided
        if (request('seo_meta_title') || request('seo_meta_description') || request('seo_meta_keywords')) {
            $seoContent = SiteData::where('data_key', $key.'.seo')->first();

            if (! $seoContent) {
                $seoContent = new SiteData;
                $seoContent->data_key = $key.'.seo';
            }

            $seoData = [
                'meta_title' => request('seo_meta_title') ?? '',
                'meta_description' => request('seo_meta_description') ?? '',
                'meta_keywords' => request('seo_meta_keywords') ?? '',
                'meta_author' => request('seo_meta_author') ?? '',
                'meta_robots' => request('seo_meta_robots') ?? 'index, follow',
                'canonical_url' => request('seo_canonical_url') ?? '',
                'meta_viewport' => request('seo_meta_viewport') ?? 'width=device-width, initial-scale=1',
                'meta_charset' => request('seo_meta_charset') ?? 'UTF-8',
            ];

            $existingSeoData = $seoContent->data_info ?? [];
            $seoContent->data_info = array_merge($existingSeoData, $seoData);
            $seoContent->save();
        }

        $toast[] = ['success', 'Content update success'];

        return back()->withToasts($toast);
    }

    public function element($key, $id = null)
    {
        $section = @getPageSections()->$key;

        if (! $section) {
            return abort(404);
        }

        unset($section->element->modal);

        $pageTitle = $section->name.' Items';

        if ($id) {
            $data = SiteData::findOrFail($id);

            return view('admin.site.element', compact('section', 'key', 'pageTitle', 'data'));
        }

        return view('admin.site.element', compact('section', 'key', 'pageTitle'));
    }

    public function remove($id)
    {
        $siteData = SiteData::findOrFail($id);
        $key = explode('.', @$siteData->data_key)[0];
        $type = explode('.', @$siteData->data_key)[1];

        if (@$type == 'element' || @$type == 'content') {
            $path = 'assets/images/site/'.$key;
            $imgJson = @getPageSections()->$key->$type->images;

            if ($imgJson) {
                foreach ($imgJson as $imgKey => $imgValue) {
                    fileManager()->removeFile($path.'/'.(@$siteData->data_info[$imgKey] ?? ''));
                    fileManager()->removeFile($path.'/thumb_'.(@$siteData->data_info[$imgKey] ?? ''));
                }
            }

        }

        $siteData->delete();

        $toast[] = ['success', 'Content removed successfully'];

        return back()->withToasts($toast);
    }

    protected function storeImage($imgJson, $type, $key, $image, $imgKey, $oldImage = null)
    {
        $path = 'assets/images/site/'.$key;

        if ($type == 'element' || $type == 'content') {

            $size = @$imgJson->$imgKey->size;
            $thumb = @$imgJson->$imgKey->thumb;
        } else {
            $path = getFilePath($key);
            $size = getFileSize($key);
            $thumb = @getThumbSize($key);
        }

        return fileUploader($image, $path, $size, $oldImage, $thumb);
    }

    protected function saveSeoData($purifier)
    {
        // Validate SEO data
        request()->validate([
            'keywords' => 'required|array',
            'description' => 'required',
            'social_title' => 'required',
            'social_description' => 'required',
            'image_input' => 'nullable|image|mimes:png,jpg,jpeg',
            'image_alt' => 'nullable|string',
        ]);

        // Get or create SEO data
        $seo = SiteData::where('data_key', 'seo.data')->first();

        if (! $seo) {
            $seo = new SiteData;
            $seo->data_key = 'seo.data';
            $seo->data_info = [];
        }

        // Prepare data
        $dataInfo = is_array($seo->data_info) ? $seo->data_info : (array) $seo->data_info;

        // Handle keywords
        $dataInfo['keywords'] = request('keywords', []);

        // Handle text fields
        $dataInfo['description'] = htmlspecialchars_decode($purifier->purify(request('description')));
        $dataInfo['social_title'] = htmlspecialchars_decode($purifier->purify(request('social_title')));
        $dataInfo['social_description'] = htmlspecialchars_decode($purifier->purify(request('social_description')));

        // Handle image
        if (request()->hasFile('image_input')) {
            try {
                $oldImage = $dataInfo['image'] ?? null;
                $dataInfo['image'] = fileUploader(
                    request('image_input'),
                    getFilePath('seo'),
                    getFileSize('seo'),
                    $oldImage
                );
            } catch (Exception $exp) {
                $toast[] = ['error', 'Image upload failed'];

                return back()->withToasts($toast)->withInput();
            }
        }
        // If no new image, keep existing image value

        // Handle image alt text
        if (request()->has('image_alt')) {
            $dataInfo['image_alt'] = request('image_alt');
        }

        // Handle meta tags fields
        if (request()->has('meta_title')) {
            $dataInfo['meta_title'] = htmlspecialchars_decode($purifier->purify(request('meta_title')));
        }
        if (request()->has('meta_description')) {
            $dataInfo['meta_description'] = htmlspecialchars_decode($purifier->purify(request('meta_description')));
        }
        if (request()->has('meta_keywords')) {
            $dataInfo['meta_keywords'] = htmlspecialchars_decode($purifier->purify(request('meta_keywords')));
        }
        if (request()->has('meta_author')) {
            $dataInfo['meta_author'] = htmlspecialchars_decode($purifier->purify(request('meta_author')));
        }
        if (request()->has('meta_robots')) {
            $dataInfo['meta_robots'] = request('meta_robots');
        }
        if (request()->has('canonical_url')) {
            $dataInfo['canonical_url'] = htmlspecialchars_decode($purifier->purify(request('canonical_url')));
        }
        if (request()->has('meta_viewport')) {
            $dataInfo['meta_viewport'] = htmlspecialchars_decode($purifier->purify(request('meta_viewport')));
        }
        if (request()->has('meta_charset')) {
            $dataInfo['meta_charset'] = htmlspecialchars_decode($purifier->purify(request('meta_charset')));
        }

        // Save SEO data
        $seo->data_info = $dataInfo;
        $seo->save();

        $toast[] = ['success', 'SEO settings updated successfully'];

        return back()->withToasts($toast);
    }
}
