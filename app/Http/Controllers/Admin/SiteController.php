<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteData;
use Exception;
use HTMLPurifier;
use Illuminate\Validation\Rules\File;

class SiteController extends Controller
{
    function themes() {
        $pageTitle  = 'Themes';
        $themePaths = array_filter(
    glob(resource_path('views/themes/*')),
    'is_dir'
);
        $themes = [];

        foreach ($themePaths as $key => $theme) {
            $arr                   = explode('/', $theme);
            $themeName             = end($arr);
            $themes[$key]['name']  = $themeName;
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

    function makeActive() {
        $setting = bs();
        $setting->active_theme = request('name');
        $setting->save();

        $toast[] = ['success', strtoupper(request('name')).' theme activation success'];
        return back()->withToasts($toast);
    }

    function sections($key) {
        $section = @getPageSections()->$key;

        if (!$section) {
            abort(404);
        }

        $content   = SiteData::where('data_key', $key . '.content')->orderBy('id','desc')->first();
        $elements  = SiteData::where('data_key', $key . '.element')->orderBy('id','desc')->get();
        $seoContent = SiteData::where('data_key', $key . '.seo')->first();
        $pageTitle = $section->name ;

        return view('admin.site.index', compact('section', 'content', 'elements', 'seoContent', 'key', 'pageTitle'));
    }

    function content($key) {
        $purifier  = new HTMLPurifier();
        $type = request('type');

        if (!$type) {
            abort(404);
        }

        $imgJson           = @getPageSections()->$key->$type->images;
        $validationRule    = [];
        $validationMessage = [];
        $excludeFromValidation = ['_token', 'video', 'key', 'status', 'type', 'id', 'image_url', 'seo_meta_title', 'seo_meta_description', 'seo_meta_keywords'];
        
        // Add image URL and alt fields to exclusion list
        if ($imgJson) {
            foreach ($imgJson as $imgKey => $imgValue) {
                $excludeFromValidation[] = $imgKey . '_url';
                $excludeFromValidation[] = $imgKey . '_alt';
            }
        }

        foreach (request()->except($excludeFromValidation) as $inputField => $val) {
            if ($inputField == 'has_image' && $imgJson) {
                foreach ($imgJson as $imgValKey => $imgJsonVal) {
                    $validationRule['image_input.'.$imgValKey] = ['nullable' , 'image', File::types(['png', 'jpg', 'jpeg'])];
                    $validationMessage['image_input.'.$imgValKey.'.image'] = keyToTitle($imgValKey).' must be an image';
                    $validationMessage['image_input.'.$imgValKey.'.mimes'] = keyToTitle($imgValKey).' file type not supported';
                }
                continue;
            } elseif($inputField == 'seo_image'){
                $validationRule['image_input'] = ['nullable', 'image', File::types(['png', 'jpg', 'jpeg'])];
                continue;
            } elseif($inputField == 'image_input') {
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
            } else {
                $validationRule[$inputField] = 'required';
            }
        }

        request()->validate($validationRule, $validationMessage, ['image_input' => 'image']);

        // Initialize inputContentValue array
        $inputContentValue = [];
        
        // Get all inputs except excluded fields
        $valInputs = request()->except(array_merge($excludeFromValidation, ['image_input']));

        foreach ($valInputs as $keyName => $input) {
            // Skip empty values but allow 0 and false
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
            $content = SiteData::where('data_key', $key . '.' . request('type'))->first();

            if (!$content || request('type') == 'element') {
                $content = new SiteData();
                $content->data_key = $key . '.' . request('type');
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
            else if ($imageUrl && $imageUrl !== '') {
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
                        if ($oldImage && !filter_var($oldImage, FILTER_VALIDATE_URL)) {
                            $oldImagePath = public_path(getFilePath('seo') . '/' . $oldImage);
                            if (file_exists($oldImagePath)) {
                                @unlink($oldImagePath);
                            }
                        }
                    } else {
                        // External URL, save as is
                        $inputContentValue['image'] = $imageUrl;
                        
                        // Delete old image file if it exists and is a local file
                        if ($oldImage && !filter_var($oldImage, FILTER_VALIDATE_URL)) {
                            $oldImagePath = public_path(getFilePath('seo') . '/' . $oldImage);
                            if (file_exists($oldImagePath)) {
                                @unlink($oldImagePath);
                            }
                        }
                    }
                } else {
                    // Not a valid URL, treat as relative path
                    $inputContentValue['image'] = ltrim($imageUrl, '/');
                    
                    // Delete old image file if it exists and is a local file
                    if ($oldImage && !filter_var($oldImage, FILTER_VALIDATE_URL)) {
                        $oldImagePath = public_path(getFilePath('seo') . '/' . $oldImage);
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
                    if (request()->hasFile('image_input.' . $imgKey)) {
                        $imgData = request()->file('image_input.' . $imgKey);
                        if (!$imgData || !$imgData->isValid()) {
                            dd([
                                'status' => 'error',
                                'message' => 'Image selected but invalid',
                                'imgKey' => $imgKey,
                                'error' => $imgData ? $imgData->getError() : 'File object is null'
                            ]);
                        }
                    } 
                    // Alternative: check if image_input is an array
                    elseif (request()->has('image_input')) {
                        $allFiles = request()->file('image_input');
                        if (is_array($allFiles) && isset($allFiles[$imgKey]) && $allFiles[$imgKey]) {
                            $imgData = $allFiles[$imgKey];
                            if ($imgData->isValid()) {
                                dd([
                                    'status' => 'selected',
                                    'message' => 'Image is selected and valid (via array)',
                                    'imgKey' => $imgKey,
                                    'filename' => $imgData->getClientOriginalName(),
                                    'size' => $imgData->getSize(),
                                    'mimeType' => $imgData->getMimeType()
                                ]);
                            } else {
                                dd([
                                    'status' => 'error',
                                    'message' => 'Image selected but invalid (via array)',
                                    'imgKey' => $imgKey,
                                    'error' => $imgData->getError()
                                ]);
                            }
                        }
                    }
                    
                    // If no file selected
                    if (!$imgData) {
                        dd([
                            'status' => 'error',
                            'message' => 'Image not selected',
                            'imgKey' => $imgKey,
                            'debug' => [
                                'hasFile_dot' => request()->hasFile('image_input.' . $imgKey),
                                'hasFile_array' => request()->hasFile('image_input'),
                                'allFiles' => array_keys(request()->allFiles()),
                                'image_input_type' => gettype(request()->file('image_input'))
                            ]
                        ]);
                    }
                    
                    $imgUrl = @request()->input($imgKey . '_url');
                    $oldImage = @$content->data_info[$imgKey] ?? null;

                    // Priority 1: If new file is uploaded, replace old image
                    if ($imgData && $imgData->isValid()) {
                        try {
                            // Simple core PHP style upload
                            $uploadPath = public_path('assets/images/site/' . $key);
                            
                            // Create directory if not exists
                            if (!file_exists($uploadPath)) {
                                @mkdir($uploadPath, 0777, true);
                                @chmod($uploadPath, 0777);
                            }
                            
                            // Generate new unique filename
                            $fileExtension = $imgData->getClientOriginalExtension();
                            $newFileName = uniqid() . time() . '.' . $fileExtension;
                            $fullPath = $uploadPath . '/' . $newFileName;
                            
                            // Delete old image first if exists
                            if ($oldImage && !filter_var($oldImage, FILTER_VALIDATE_URL)) {
                                $oldImagePath = $uploadPath . '/' . $oldImage;
                                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                                    @chmod($oldImagePath, 0777);
                                    @unlink($oldImagePath);
                                }
                                
                                // Delete thumbnail if exists
                                $oldThumbPath = $uploadPath . '/thumb_' . $oldImage;
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
                                    $image->resize((int)$sizeArray[0], (int)$sizeArray[1], function ($constraint) {
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
                                    $thumbImage->resize((int)$thumbArray[0], (int)$thumbArray[1], function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                    $thumbPath = $uploadPath . '/thumb_' . $newFileName;
                                    $thumbImage->save($thumbPath, 90);
                                    @chmod($thumbPath, 0777);
                                }
                            }
                            
                            // Save new image name
                            $inputContentValue[$imgKey] = $newFileName;
                            
                            // Generate and display URL
                            $imageUrl = asset('assets/images/site/' . $key . '/' . $newFileName);
                            $relativePath = 'assets/images/site/' . $key . '/' . $newFileName;
                            
                            // Debug: Show upload success with URL
                            \Log::info('Image uploaded successfully', [
                                'imgKey' => $imgKey,
                                'filename' => $newFileName,
                                'fullUrl' => $imageUrl,
                                'relativePath' => $relativePath,
                                'uploadPath' => $uploadPath
                            ]);
                            
                        } catch (Exception $exp) {
                            $toast[] = ['error', 'Image upload failed: ' . $exp->getMessage()];
                            \Log::error('Image upload error: ' . $exp->getMessage(), [
                                'key' => $key,
                                'imgKey' => $imgKey,
                                'oldImage' => $oldImage,
                                'trace' => $exp->getTraceAsString()
                            ]);
                            return back()->withToasts($toast)->withInput();
                        }
                    } 
                    // Priority 2: If URL is provided, use URL (replace old image if it's a local file)
                    else if ($imgUrl && $imgUrl !== '') {
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
                                if ($oldImage && !filter_var($oldImage, FILTER_VALIDATE_URL)) {
                                    $oldImagePath = public_path('assets/images/site/' . $key . '/' . $oldImage);
                                    if (file_exists($oldImagePath)) {
                                        @unlink($oldImagePath);
                                        // Also delete thumbnail if exists
                                        $oldThumbPath = public_path('assets/images/site/' . $key . '/thumb_' . $oldImage);
                                        if (file_exists($oldThumbPath)) {
                                            @unlink($oldThumbPath);
                                        }
                                    }
                                }
                            } else {
                                // External URL, save as is
                                $inputContentValue[$imgKey] = $imgUrl;
                                
                                // Delete old image file if it exists and is a local file
                                if ($oldImage && !filter_var($oldImage, FILTER_VALIDATE_URL)) {
                                    $oldImagePath = public_path('assets/images/site/' . $key . '/' . $oldImage);
                                    if (file_exists($oldImagePath)) {
                                        @unlink($oldImagePath);
                                        // Also delete thumbnail if exists
                                        $oldThumbPath = public_path('assets/images/site/' . $key . '/thumb_' . $oldImage);
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
                            if ($oldImage && !filter_var($oldImage, FILTER_VALIDATE_URL)) {
                                $oldImagePath = public_path('assets/images/site/' . $key . '/' . $oldImage);
                                if (file_exists($oldImagePath)) {
                                    @unlink($oldImagePath);
                                    // Also delete thumbnail if exists
                                    $oldThumbPath = public_path('assets/images/site/' . $key . '/thumb_' . $oldImage);
                                    if (file_exists($oldThumbPath)) {
                                        @unlink($oldThumbPath);
                                    }
                                }
                            }
                        }
                    } 
                    // Priority 3: Keep existing value if no new input
                    else if (isset($content->data_info[$imgKey])) {
                        $existingValue = $content->data_info[$imgKey];
                            $inputContentValue[$imgKey] = $existingValue;
                    }
                    
                    // Handle alt text
                    if (request()->has($imgKey . '_alt')) {
                        $inputContentValue[$imgKey . '_alt'] = request($imgKey . '_alt');
                    }
                }
            }
        }

        // Merge with existing data to preserve fields not in the form
        $existingData = is_array($content->data_info) ? $content->data_info : [];
        $content->data_info = array_merge($existingData, $inputContentValue);
        
        // Ensure data_info is properly set
        if (empty($content->data_info)) {
            $content->data_info = $inputContentValue;
        }
        
        try {
            if (!$content->save()) {
                $toast[] = ['error', 'Failed to save content'];
                return back()->withToasts($toast)->withInput();
            }
            
        } catch (\Exception $e) {
            $toast[] = ['error', 'Error saving content: ' . $e->getMessage()];
            \Log::error('SiteController content save error: ' . $e->getMessage(), [
                'key' => $key,
                'type' => $type,
                'data' => $inputContentValue
            ]);
            return back()->withToasts($toast)->withInput();
        }

        // Handle SEO data if provided
        if (request('seo_meta_title') || request('seo_meta_description') || request('seo_meta_keywords')) {
            $seoContent = SiteData::where('data_key', $key . '.seo')->first();
            
            if (!$seoContent) {
                $seoContent = new SiteData();
                $seoContent->data_key = $key . '.seo';
            }

            $seoData = [
                'meta_title' => request('seo_meta_title') ?? '',
                'meta_description' => request('seo_meta_description') ?? '',
                'meta_keywords' => request('seo_meta_keywords') ?? '',
            ];

            $existingSeoData = $seoContent->data_info ?? [];
            $seoContent->data_info = array_merge($existingSeoData, $seoData);
            $seoContent->save();
        }

        $toast[] = ['success', 'Content update success'];
        return back()->withToasts($toast);
    }

    function element($key, $id = null) {
        $section = @getPageSections()->$key;

        if (!$section) {
            return abort(404);
        }

        unset($section->element->modal);

        $pageTitle = $section->name . ' Items';

        if ($id) {
            $data = SiteData::findOrFail($id);
            return view('admin.site.element', compact('section', 'key', 'pageTitle', 'data'));
        }

        return view('admin.site.element', compact('section', 'key', 'pageTitle'));
    }

    function remove($id)
    {
        $siteData = SiteData::findOrFail($id);
        $key      = explode('.', @$siteData->data_key)[0];
        $type     = explode('.', @$siteData->data_key)[1];

        if (@$type == 'element' || @$type == 'content') {
            $path    = 'assets/images/site/' . $key;
            $imgJson = @getPageSections()->$key->$type->images;

            if ($imgJson) {
                foreach ($imgJson as $imgKey => $imgValue) {
                    fileManager()->removeFile($path . '/' . (@$siteData->data_info[$imgKey] ?? ''));
                    fileManager()->removeFile($path . '/thumb_' . (@$siteData->data_info[$imgKey] ?? ''));
                }
            }

        }

        $siteData->delete();

        $toast[] = ['success', 'Content removed successfully'];
        return back()->withToasts($toast);
    }

    protected function storeImage($imgJson, $type, $key, $image, $imgKey, $oldImage = null)
    {
        $path = 'assets/images/site/' . $key;

        if ($type == 'element' || $type == 'content') {

            $size  = @$imgJson->$imgKey->size;
            $thumb = @$imgJson->$imgKey->thumb;
        } else {
            $path  = getFilePath($key);
            $size  = getFileSize($key);
            $thumb = @getThumbSize($key);
        }

        return fileUploader($image, $path, $size, $oldImage, $thumb);
    }
}
