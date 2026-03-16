<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Form;
use App\Models\Plugin;
use App\Models\SiteData;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\File;
use Image;

class SettingController extends Controller
{
    function basic() {
        $pageTitle   = 'Basic Setting';
        $timeRegions = json_decode(file_get_contents(resource_path('views/admin/partials/timeRegion.json')));
        
        // Get allowed countries settings
        $allowedCountriesData = SiteData::where('data_key', 'general.allowed_countries')->first();
        
        // WhatsApp settings
        $whatsappData = SiteData::where('data_key', 'general.whatsapp_settings')->first();
        $whatsappContactMessage = '';
        $whatsappChatbotNumber = '';
        if ($whatsappData && $whatsappData->data_info) {
            $wi = is_array($whatsappData->data_info) ? $whatsappData->data_info : (array)$whatsappData->data_info;
            $whatsappContactMessage = $wi['contact_creator_message'] ?? '';
            $whatsappChatbotNumber = $wi['chatbot_number'] ?? '';
        }
        $selectedCountries = [];
        $useSelectedOnly = false;
        
        if ($allowedCountriesData && $allowedCountriesData->data_info) {
            $dataInfo = is_array($allowedCountriesData->data_info) ? $allowedCountriesData->data_info : (array)$allowedCountriesData->data_info;
            $selectedCountries = $dataInfo['selected_countries'] ?? [];
            $useSelectedOnly = $dataInfo['use_selected_only'] ?? false;
        }
        
        // Get all countries list
        $allCountries = $this->getAllCountriesList();

        // Campaign days limit (max days between start_date and end_date)
        $campaignDaysLimit = 30;
        $campaignDaysLimitData = SiteData::where('data_key', 'general.campaign_days_limit')->first();
        if ($campaignDaysLimitData && isset($campaignDaysLimitData->data_info['campaign_days_limit'])) {
            $campaignDaysLimit = (int) $campaignDaysLimitData->data_info['campaign_days_limit'];
        }

        return view('admin.setting.basic', compact('pageTitle', 'timeRegions', 'selectedCountries', 'useSelectedOnly', 'allCountries', 'whatsappContactMessage', 'whatsappChatbotNumber', 'campaignDaysLimit'));
    }
    
    private function getAllCountriesList() {
        return [
            'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Australia', 'Austria',
            'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan',
            'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon',
            'Canada', 'Cape Verde', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica',
            'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt',
            'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon',
            'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana',
            'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel',
            'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kosovo', 'Kuwait', 'Kyrgyzstan',
            'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Madagascar',
            'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico', 'Micronesia',
            'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal',
            'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman', 'Pakistan',
            'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Qatar',
            'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia',
            'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa',
            'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan',
            'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan',
            'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City',
            'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
        ];
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

        // This function is called in the basicUpdate() method of SettingController
        $setting = bs();
        $setting->site_name      = request('site_name');
        $setting->site_cur       = request('site_cur');
        $setting->cur_sym        = request('cur_sym');
        $setting->per_page_item  = request('per_page_item');
        $setting->fraction_digit = request('fraction_digit');
        $setting->date_format    = request('date_format');
        $setting->first_color    = str_replace('#', '', request('first_color'));
        $setting->second_color   = str_replace('#', '', request('second_color'));

        // Campaign Registration Fee
        if (Schema::hasColumn('settings', 'registration_fee_enabled')) {
            $setting->registration_fee_enabled = request('registration_fee_enabled') ? 1 : 0;
            $setting->registration_fee_min     = max(0, (float) (request('registration_fee_amount') ?? 0));
            $setting->registration_fee_max     = $setting->registration_fee_min;
        }

        $result = $setting->save();

        $timeRegionFile = config_path('timeRegion.php');
        $setTimeRegion  = '<?php $timeRegion = '.request('time_region').' ?>';
        file_put_contents($timeRegionFile, $setTimeRegion);

        // Save allowed countries settings
        $allowedCountriesData = SiteData::where('data_key', 'general.allowed_countries')->first();
        if (!$allowedCountriesData) {
            $allowedCountriesData = new SiteData();
            $allowedCountriesData->data_key = 'general.allowed_countries';
        }
        
        // Get checkbox value (will be '1' if checked, null if unchecked)
        $useSelectedOnly = request()->has('use_selected_countries') && request('use_selected_countries') == '1';
        $selectedCountries = request('selected_countries') ? (array)request('selected_countries') : [];
        
        $allowedCountriesData->data_info = [
            'use_selected_only' => $useSelectedOnly,
            'selected_countries' => $selectedCountries
        ];
        $allowedCountriesData->save();

        // Save WhatsApp settings
        $whatsappData = SiteData::where('data_key', 'general.whatsapp_settings')->first();
        if (!$whatsappData) {
            $whatsappData = new SiteData();
            $whatsappData->data_key = 'general.whatsapp_settings';
        }
        $whatsappData->data_info = [
            'contact_creator_message' => request('whatsapp_contact_creator_message', ''),
            'chatbot_number' => request('whatsapp_chatbot_number', ''),
        ];
        $whatsappData->save();

        // Save campaign days limit (max days between start_date and end_date)
        $campaignDaysLimit = max(1, min(365, (int) (request('campaign_days_limit') ?? 30)));
        $campaignDaysLimitData = SiteData::where('data_key', 'general.campaign_days_limit')->first();
        if (!$campaignDaysLimitData) {
            $campaignDaysLimitData = new SiteData();
            $campaignDaysLimitData->data_key = 'general.campaign_days_limit';
        }
        $campaignDaysLimitData->data_info = ['campaign_days_limit' => $campaignDaysLimit];
        $campaignDaysLimitData->save();

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
        // Debug: Log request data
        \Log::info('Logo Upload Debug:', [
            'has_logo_light' => request()->hasFile('logo_light'),
            'has_logo_dark' => request()->hasFile('logo_dark'),
            'has_favicon' => request()->hasFile('favicon'),
            'request_files' => request()->allFiles(),
        ]);

        $this->validate(request(), [
            'logo_light' => [File::types(['png'])],
            'logo_dark'  => [File::types(['png'])],
            'favicon'    => [File::types(['png'])],
        ]);

        $path = getFilePath('logoFavicon');
        $fullPath = public_path($path);
        
        // Create entire directory path if it doesn't exist
        if (!file_exists($fullPath)) {
            if (!mkdir($fullPath, 0755, true)) {
                \Log::error('Failed to create directory: ' . $fullPath);
                $toast[] = ['error', 'Failed to create directory. Please check permissions.'];
                return back()->withToasts($toast);
            }
            \Log::info('Created directory: ' . $fullPath);
        }
        
        // Ensure directory is writable
        if (!is_writable($fullPath)) {
            if (!chmod($fullPath, 0755)) {
                \Log::error('Failed to set directory permissions: ' . $fullPath);
                $toast[] = ['error', 'Directory is not writable. Please check permissions.'];
                return back()->withToasts($toast);
            }
            \Log::info('Fixed directory permissions: ' . $fullPath);
        }
        
        // Debug: Log path info
        \Log::info('Path Debug:', [
            'relative_path' => $path,
            'full_path' => $fullPath,
            'path_exists' => file_exists($fullPath),
            'path_writable' => is_writable($fullPath),
        ]);

        if (request()->hasFile('logo_light')) {
            try {
                $logoPath = $fullPath . '/logo_light.png';
                // dd($logoPath);
                
                // STEP 1: Remove old file completely if exists
                if (file_exists($logoPath)) {
                    if (!unlink($logoPath)) {
                        \Log::warning('Could not delete old logo_light.png, but continuing...');
                    } else {
                        \Log::info('Deleted old logo_light.png');
                    }
                    clearstatcache(true, $logoPath);
                }
                
                // STEP 2: Save new logo
                $image = Image::make(request('logo_light'));
                $image->save($logoPath, 90);
                
                // STEP 3: Set proper file permissions
                if (!chmod($logoPath, 0644)) {
                    \Log::warning('Could not set permissions for logo_light.png');
                }
                
                // STEP 4: Clear cache
                clearstatcache(true, $logoPath);
                
                // Verify file was saved
                if (!file_exists($logoPath)) {
                    throw new \Exception('File was not saved successfully');
                }
                
                \Log::info('Logo Light Upload Success:', [
                    'file_path' => $logoPath,
                    'file_exists' => file_exists($logoPath),
                    'file_size' => filesize($logoPath),
                    'is_readable' => is_readable($logoPath),
                    'permissions' => substr(sprintf('%o', fileperms($logoPath)), -4),
                ]);
                
                $toast[] = ['success', 'Light logo uploaded successfully'];
            } catch (\Exception $exp) {
                \Log::error('Light Logo Upload Error: ' . $exp->getMessage(), [
                    'trace' => $exp->getTraceAsString(),
                    'full_path' => $fullPath,
                ]);
                $toast[] = ['error', 'Unable to upload light logo: ' . $exp->getMessage()];
                return back()->withToasts($toast);
            }
        }

        if (request()->hasFile('logo_dark')) {
            try {
                $logoPath = $fullPath . '/logo_dark.png';
                
                // STEP 1: Remove old file completely if exists
                if (file_exists($logoPath)) {
                    if (!unlink($logoPath)) {
                        \Log::warning('Could not delete old logo_dark.png, but continuing...');
                    } else {
                        \Log::info('Deleted old logo_dark.png');
                    }
                    clearstatcache(true, $logoPath);
                }
                
                // STEP 2: Save new logo
                $image = Image::make(request('logo_dark'));
                $image->save($logoPath, 90);
                
                // STEP 3: Set proper file permissions
                if (!chmod($logoPath, 0644)) {
                    \Log::warning('Could not set permissions for logo_dark.png');
                }
                
                // STEP 4: Clear cache
                clearstatcache(true, $logoPath);
                
                // Verify file was saved
                if (!file_exists($logoPath)) {
                    throw new \Exception('File was not saved successfully');
                }
                
                \Log::info('Logo Dark Upload Success:', [
                    'file_path' => $logoPath,
                    'file_exists' => file_exists($logoPath),
                    'file_size' => filesize($logoPath),
                    'is_readable' => is_readable($logoPath),
                    'permissions' => substr(sprintf('%o', fileperms($logoPath)), -4),
                ]);
                
                $toast[] = ['success', 'Dark logo uploaded successfully'];
            } catch (\Exception $exp) {
                \Log::error('Dark Logo Upload Error: ' . $exp->getMessage(), [
                    'trace' => $exp->getTraceAsString(),
                    'full_path' => $fullPath,
                ]);
                $toast[] = ['error', 'Unable to upload dark logo: ' . $exp->getMessage()];
                return back()->withToasts($toast);
            }
        }

        if (request()->hasFile('favicon')) {
            try {
                $size = explode('x', getFileSize('favicon'));
                $faviconPath = $fullPath . '/favicon.png';
                
                // STEP 1: Remove old file completely if exists
                if (file_exists($faviconPath)) {
                    if (!unlink($faviconPath)) {
                        \Log::warning('Could not delete old favicon.png, but continuing...');
                    } else {
                        \Log::info('Deleted old favicon.png');
                    }
                    clearstatcache(true, $faviconPath);
                }
                
                // STEP 2: Save new favicon
                $image = Image::make(request('favicon'));
                $image->resize($size[0], $size[1])->save($faviconPath, 90);
                
                // STEP 3: Set proper file permissions
                if (!chmod($faviconPath, 0644)) {
                    \Log::warning('Could not set permissions for favicon.png');
                }
                
                // STEP 4: Clear cache
                clearstatcache(true, $faviconPath);
                
                // Verify file was saved
                if (!file_exists($faviconPath)) {
                    throw new \Exception('File was not saved successfully');
                }
                
                \Log::info('Favicon Upload Success:', [
                    'file_path' => $faviconPath,
                    'file_exists' => file_exists($faviconPath),
                    'file_size' => filesize($faviconPath),
                    'is_readable' => is_readable($faviconPath),
                    'permissions' => substr(sprintf('%o', fileperms($faviconPath)), -4),
                ]);
                
                $toast[] = ['success', 'Favicon uploaded successfully'];
            } catch (\Exception $exp) {
                \Log::error('Favicon Upload Error: ' . $exp->getMessage(), [
                    'trace' => $exp->getTraceAsString(),
                    'full_path' => $fullPath,
                ]);
                $toast[] = ['error', 'Unable to upload the favicon: ' . $exp->getMessage()];
                return back()->withToasts($toast);
            }
        }

        if (empty($toast)) {
            $toast[] = ['info', 'No files were uploaded'];
        } else {
            $toast[] = ['success', 'Logo and favicon update completed'];
        }
        
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

    function home() {
        $pageTitle = 'Home Setting';
        return view('admin.setting.home', compact('pageTitle'));
    }

    function homeUpdate() {
        $setting = bs();
        
        // Hero Section
        $setting->home_hero_title_1 = request('home_hero_title_1');
        $setting->home_hero_title_2 = request('home_hero_title_2');
        $setting->home_hero_subtitle = request('home_hero_subtitle');
        $setting->home_business_button_text = request('home_business_button_text');
        $setting->home_personal_button_text = request('home_personal_button_text');
        
        // Resource Section
        $setting->home_resource_title = request('home_resource_title');
        $setting->home_resource_subtitle = request('home_resource_subtitle');
        $setting->home_resource_description = request('home_resource_description');
        $setting->home_resource_button_text = request('home_resource_button_text');
        
        // Steps Section
        $setting->home_steps_title = request('home_steps_title');
        $setting->home_step_1_title = request('home_step_1_title');
        $setting->home_step_1_description = request('home_step_1_description');
        $setting->home_step_2_title = request('home_step_2_title');
        $setting->home_step_2_description = request('home_step_2_description');
        $setting->home_step_3_title = request('home_step_3_title');
        $setting->home_step_3_description = request('home_step_3_description');
        
        // Success Stories Section
        $setting->home_stories_title = request('home_stories_title');
        $setting->home_stories_subtitle = request('home_stories_subtitle');
        
        // FAQ Section
        $setting->home_faq_title = request('home_faq_title');
        $setting->home_faq_subtitle = request('home_faq_subtitle');
        
        // Community Section
        $setting->home_community_title = request('home_community_title');
        $setting->home_community_description = request('home_community_description');
        $setting->home_community_button_text = request('home_community_button_text');
        
        $setting->save();

        $toast[] = ['success', 'Home setting update success'];
        return back()->withToasts($toast);
    }
}
