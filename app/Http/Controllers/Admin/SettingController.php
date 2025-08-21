<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Form;
use App\Models\Plugin;
use App\Models\SiteData;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rules\File;
use Image;

class SettingController extends Controller
{
    function basic() {
        $pageTitle   = 'Basic Setting';
        $timeRegions = json_decode(file_get_contents(resource_path('views/admin/partials/timeRegion.json')));

        return view('admin.setting.basic', compact('pageTitle', 'timeRegions'));
    }

    function basicUpdate() {
        $this->validate(request(), [
            'site_name'      => 'required|string|max:40',
            'site_cur'       => 'required|string|max:40',
            'cur_sym'        => 'required|string|max:40',
            'first_color'    => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'second_color'   => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'per_page_item'  => 'required|in:20,50,100',
            'fraction_digit' => 'required|int|gte:0|max:9',
            'date_format'    => 'required|in:m-d-Y,d-m-Y,Y-m-d',
            'time_region'    => 'required',
        ]);

        $setting                 = bs();
        $setting->site_name      = request('site_name');
        $setting->site_cur       = request('site_cur');
        $setting->cur_sym        = request('cur_sym');
        $setting->per_page_item  = request('per_page_item');
        $setting->fraction_digit = request('fraction_digit');
        $setting->date_format    = request('date_format');
        $setting->first_color    = str_replace('#', '', request('first_color'));
        $setting->second_color   = str_replace('#', '', request('second_color'));
        $setting->save();

        $timeRegionFile = config_path('timeRegion.php');
        $setTimeRegion  = '<?php $timeRegion = '.request('time_region').' ?>';
        file_put_contents($timeRegionFile, $setTimeRegion);

        $toast[] = ['success', 'Basic setting update success'];
        return back()->withToasts($toast);
    }

    function systemUpdate() {
        $setting               = bs();
        $setting->signup       = request('signup')       ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->enforce_ssl  = request('enforce_ssl')  ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->agree_policy = request('agree_policy') ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->strong_pass  = request('strong_pass')  ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->kc           = request('kc')           ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->ec           = request('ec')           ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->ea           = request('ea')           ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->sc           = request('sc')           ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->sa           = request('sa')           ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->language     = request('language')     ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->save();

        $toast[] = ['success', 'System setting update success'];
        return back()->withToasts($toast);
    }

    function logoFaviconUpdate() {
        $this->validate(request(), [
            'logo_light' => [File::types(['png'])],
            'logo_dark'  => [File::types(['png'])],
            'favicon'    => [File::types(['png'])],
        ]);

        $path = getFilePath('logoFavicon');

        if (request()->hasFile('logo_light')) {
            try {
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                Image::make(request('logo_light'))->save($path . '/logo_light.png');
            } catch (\Exception $exp) {
                $toast[] = ['error', 'Unable to upload light logo'];
                return back()->withToasts($toast);
            }
        }

        if (request()->hasFile('logo_dark')) {
            try {
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                Image::make(request('logo_dark'))->save($path . '/logo_dark.png');
            } catch (\Exception $exp) {
                $toast[] = ['error', 'Unable to upload dark logo'];
                return back()->withToasts($toast);
            }
        }

        if (request()->hasFile('favicon')) {
            try {
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $size = explode('x', getFileSize('favicon'));
                Image::make(request('favicon'))->resize($size[0], $size[1])->save($path . '/favicon.png');
            } catch (\Exception $exp) {
                $toast[] = ['error', 'Unable to upload the favicon'];
                return back()->withToasts($toast);
            }
        }

        $toast[] = ['success', 'Logo and favicon update success'];
        return back()->withToasts($toast);
    }

    function cover() {
        $pageTitle = 'Cover Settings';
        $coverContent = SiteData::where('data_key', 'cover.content')->first();
        return view('admin.setting.cover', compact('pageTitle', 'coverContent'));
    }

    function coverUpdate() {
        $this->validate(request(), [
            'cover_image' => ['nullable', 'image', File::types(['png', 'jpg', 'jpeg'])],
            'heading' => 'required|string|max:255',
            'subheading' => 'required|string|max:255',
            'description' => 'required|string',
            'first_button_text' => 'required|string|max:100',
            'first_button_url' => 'required|string|max:255',
            'second_button_text' => 'required|string|max:100',
            'second_button_url' => 'required|string|max:255',
        ]);

        $coverContent = SiteData::where('data_key', 'cover.content')->first();
        
        if (!$coverContent) {
            $coverContent = new SiteData();
            $coverContent->data_key = 'cover.content';
        }

        // Handle image upload
        if (request()->hasFile('cover_image')) {
            try {
                $path = getFilePath('site') . '/cover';
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                
                $image = request('cover_image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                Image::make($image)->resize(1920, 1080)->save($path . '/' . $imageName);
                
                $coverContent->data_info = [
                    'cover_image' => $imageName,
                    'heading' => request('heading'),
                    'subheading' => request('subheading'),
                    'description' => request('description'),
                    'first_button_text' => request('first_button_text'),
                    'first_button_url' => request('first_button_url'),
                    'second_button_text' => request('second_button_text'),
                    'second_button_url' => request('second_button_url'),
                ];
            } catch (\Exception $exp) {
                $toast[] = ['error', 'Unable to upload cover image'];
                return back()->withToasts($toast);
            }
        } else {
            // Update text content only
            $dataInfo = $coverContent->data_info ?? (object)[];
            $dataInfo->heading = request('heading');
            $dataInfo->subheading = request('subheading');
            $dataInfo->description = request('description');
            $dataInfo->first_button_text = request('first_button_text');
            $dataInfo->first_button_url = request('first_button_url');
            $dataInfo->second_button_text = request('second_button_text');
            $dataInfo->second_button_url = request('second_button_url');
            
            $coverContent->data_info = $dataInfo;
        }

        $coverContent->save();

        $toast[] = ['success', 'Cover image and content update success'];
        return back()->withToasts($toast);
    }

    function plugin() {
        $pageTitle = 'Plugin Settings';
        $plugins   = Plugin::orderBy('name')->get();

        return view('admin.setting.plugin', compact('pageTitle', 'plugins'));
    }

    function pluginUpdate($id) {
        $plugin = Plugin::findOrFail($id);
        $validationRule = [];

        foreach ($plugin->shortcode as $key => $val) {
            $validationRule = array_merge($validationRule,[$key => 'required']);
        }

        request()->validate($validationRule);

        $shortCode = json_decode(json_encode($plugin->shortcode), true);

        foreach ($shortCode as $key => $value) {
            $shortCode[$key]['value'] = request($key);
        }

        $plugin->shortcode = $shortCode;
        $plugin->status    = request('status') ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $plugin->save();

        $toast[] = ['success', $plugin->name . ' updated success'];
        return back()->withToasts($toast);
    }

    function pluginStatus($id) {
        return Plugin::changeStatus($id);
    }

    function seo() {
        $pageTitle = 'SEO Setting';
        $seo       = SiteData::where('data_key', 'seo.data')->first();

        if(!$seo) {
            $data_info           = '{"keywords":[],"description":"","social_title":"","social_description":"","image":null}';
            $data_info           = json_decode($data_info, true);
            $siteData            = new SiteData();
            $siteData->data_key  = 'seo.data';
            $siteData->data_info = $data_info;
            $siteData->save();
        }

        return view('admin.site.seo', compact('pageTitle', 'seo'));
    }

    function cookie() {
        $pageTitle = 'Cookie Policy';
        $cookie    = SiteData::where('data_key', 'cookie.data')->first();

        return view('admin.site.cookie', compact('pageTitle', 'cookie'));
    }

    function cookieUpdate() {
        $this->validate(request(), [
            'short_details' => 'required',
            'details'       => 'required',
        ]);

        $cookie = SiteData::where('data_key', 'cookie.data')->first();
        $cookie->data_info = [
            'short_details' => request('short_details'),
            'details'       => request('details'),
            'status'        => request('status') ? ManageStatus::ACTIVE : ManageStatus::INACTIVE,
        ];
        $cookie->save();

        $toast[] = ['success', 'Cookie policy update success'];
        return back()->withToasts($toast);
    }

    function maintenance() {
        $pageTitle   = 'Under Maintenance Mode';
        $maintenance = SiteData::where('data_key', 'maintenance.data')->first();

        return view('admin.site.maintenance', compact('pageTitle', 'maintenance'));
    }

    function maintenanceUpdate() {
        $this->validate(request(), [
            'heading' => 'required',
            'details' => 'required',
        ]);

        $setting = bs();
        $setting->site_maintenance = request('status') ? ManageStatus::ACTIVE : ManageStatus::INACTIVE;
        $setting->save();

        $maintenance = SiteData::where('data_key', 'maintenance.data')->first();
        $maintenance->data_info = [
            'heading' => request('heading'),
            'details' => request('details'),
        ];
        $maintenance->save();

        $toast[] = ['success', 'Maintenance data update success'];
        return back()->withToasts($toast);
    }

    function kyc() {
        $pageTitle   = 'KYC Setting';
        $form        = Form::where('act','kyc')->first();
        $formHeading = 'KYC Form Data';

        return view('admin.setting.kyc',compact('pageTitle', 'form', 'formHeading'));
    }

    function kycUpdate() {
        $formProcessor       = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();

        request()->validate($generatorValidation['rules'], $generatorValidation['messages']);

        $exist    = Form::where('act','kyc')->first();
        $isUpdate = $exist ? true : false;

        $formProcessor->generate('kyc',$isUpdate,'act');

        $toast[] = ['success', 'KYC data update success'];
        return back()->withToasts($toast);
    }

    function cacheClear() {
        Artisan::call('optimize:clear');
        $toast[] = ['success', 'Clearing cache success'];
        return back()->withToasts($toast);
    }
}
