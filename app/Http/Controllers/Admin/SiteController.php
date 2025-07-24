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
        $themePaths = array_filter(glob('main/resources/views/themes/*'), 'is_dir');

        foreach ($themePaths as $key => $theme) {
            $arr                   = explode('/', $theme);
            $themeName             = end($arr);
            $themes[$key]['name']  = $themeName;
            $themes[$key]['image'] = asset($theme) . '/' . $themeName . '.jpg';
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
        $pageTitle = $section->name ;

        return view('admin.site.index', compact('section', 'content', 'elements', 'key', 'pageTitle'));
    }

    function content($key) {
        $purifier  = new HTMLPurifier();
        $valInputs = request()->except('_token', 'image_input', 'key', 'status', 'type', 'id');

        foreach ($valInputs as $keyName => $input) {
            if (gettype($input) == 'array') {
                $inputContentValue[$keyName] = $input;
                continue;
            }

            $inputContentValue[$keyName] = htmlspecialchars_decode($purifier->purify($input));
        }

        $type = request('type');

        if (!$type) {
            abort(404);
        }

        $imgJson           = @getPageSections()->$key->$type->images;
        $validationRule    = [];
        $validationMessage = [];

        foreach (request()->except('_token', 'video') as $inputField => $val) {
            if ($inputField == 'has_image' && $imgJson) {

                foreach ($imgJson as $imgValKey => $imgJsonVal) {
                    $validationRule['image_input.'.$imgValKey] = ['nullable' , 'image', File::types(['png', 'jpg', 'jpeg'])];
                    $validationMessage['image_input.'.$imgValKey.'.image'] = keyToTitle($imgValKey).' must be an image';
                    $validationMessage['image_input.'.$imgValKey.'.mimes'] = keyToTitle($imgValKey).' file type not supported';
                }

                continue;
            }elseif($inputField == 'seo_image'){
                $validationRule['image_input'] = ['nullable', 'image', File::types(['png', 'jpg', 'jpeg'])];
                continue;
            }

            $validationRule[$inputField] = 'required';
        }

        request()->validate($validationRule, $validationMessage, ['image_input' => 'image']);

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
            $inputContentValue['image'] = @$content->data_info->image;
            if (request()->hasFile('image_input')) {
                try {
                    $inputContentValue['image'] = fileUploader(request('image_input'), getFilePath('seo'), getFileSize('seo'), @$content->data_info->image);
                } catch (Exception $exp) {
                    $toast[] = ['error', 'Image upload failed'];
                    return back()->withToasts($toast);
                }
            }
        } else {
            if ($imgJson) {
                foreach ($imgJson as $imgKey => $imgValue) {
                    $imgData = @request()->image_input[$imgKey];

                    if (is_file($imgData)) {
                        try {
                            $inputContentValue[$imgKey] = $this->storeImage($imgJson, $type, $key, $imgData, $imgKey,  @$content->data_info->$imgKey);
                        } catch (Exception $exp) {
                            $toast[] = ['error', 'Image upload failed'];
                            return back()->withToasts($toast);
                        }
                    } else if (isset($content->data_info->$imgKey)) {
                        $inputContentValue[$imgKey] = $content->data_info->$imgKey;
                    }
                }
            }
        }

        $content->data_info = $inputContentValue;
        $content->save();

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
                    fileManager()->removeFile($path . '/' . @$siteData->data_info->$imgKey);
                    fileManager()->removeFile($path . '/thumb_' . @$siteData->data_info->$imgKey);
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
            $thumb = @fileManager()->$key()->thumb;
        }

        return fileUploader($image, $path, $size, $oldImage, $thumb);
    }
}
