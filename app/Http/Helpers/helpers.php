<?php

/**
 * Globally autoloaded helpers (`composer.json` → `autoload.files`).
 *
 * Cross-cutting utilities: system metadata, admin RBAC helpers, settings (`bs()`), uploads,
 * currency/IP detection, notifications, and legacy compatibility helpers used across web and API.
 */

use App\Constants\ManageStatus;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\FileManager;
use App\Lib\GoogleAuthenticator;
use App\Models\Plugin;
use App\Models\CampaignDocumentField;
use App\Models\Setting;
use App\Models\SiteData;
use App\Notify\Notify;
use Carbon\Carbon;
use Illuminate\Support\Str;

function systemDetails(): array {
    $system['name']          = 'ApnaCrowdFunding';
    $system['version']       = '1.1';
    $system['build_version'] = '0.0.1';

    return $system;
}

function verificationCode($length): int {
    if ($length <= 0) return 0;

    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';

    return random_int($min, $max);
}

function admin_has_widget(string $widgetKey): bool
{
    $admin = auth()->guard('admin')->user();
    if (!$admin) {
        return false;
    }
    if ($admin->isSuperAdmin()) {
        return true;
    }
    if ($admin->role_id && $admin->rbacRole) {
        $widgets = $admin->rbacRole->dashboard_widgets ?? [];
        return is_array($widgets) && in_array($widgetKey, $widgets);
    }
    return false;
}

function admin_can(string|array $permission): bool
{
    $admin = auth()->guard('admin')->user();
    if (!$admin) {
        return false;
    }
    $helper = app(\App\Support\PermissionHelper::class);
    $keys = is_array($permission) ? $permission : [$permission];
    foreach ($keys as $key) {
        if ($helper->hasPermission($admin, $key)) {
            return true;
        }
    }
    return false;
}

function navigationActive($routeName, $type = null, $param = null) {
    if ($type == 1) $class = 'active';
    else $class = 'active show';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $name) if (request()->routeIs($name)) return $class;
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);

            if (strtolower(@$routeParam[0]) == strtolower($param)) return $class;
            else return;
        }

        return $class;
    }
}

function bs($fieldName = null) {
    try {
        cache()->forget('setting');
        // Cache clear karne ke liye aap command line se yeh command chalaen:
        // php artisan cache:clear
        $setting = null;

        if (!$setting) {
            $setting = Setting::first();
            if ($setting) {
                cache()->put('setting', $setting);
            }
        }

        if ($fieldName) return @$setting->$fieldName;

        return $setting;
    } catch (\Exception $e) {
        // Database connection failed, return null
        \Log::error('Database connection failed in bs() function: ' . $e->getMessage());
        return null;
    }
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null): string {
    $fileManager        = new FileManager($file);
    // Convert relative path to full public path if needed
    $publicPath = public_path();
    
    // If location is already an absolute path, use it as is
    if (strpos($location, $publicPath) === 0 || (strpos($location, '/') === 0 && file_exists($location))) {
        $fileManager->path = $location;
    } 
    // If location starts with / but doesn't exist, try public_path
    elseif (strpos($location, '/') === 0) {
        $fileManager->path = public_path(ltrim($location, '/'));
    }
    // Otherwise, treat as relative path from public directory
    else {
        $fileManager->path = public_path($location);
    }
    
    // Ensure the directory exists and is writable
    $fileManager->makeDirectory();
    
    $fileManager->size  = $size;
    $fileManager->old   = $old;
    $fileManager->thumb = $thumb;
    $fileManager->upload();

    return $fileManager->filename;
}

function fileManager(): FileManager {
    return new FileManager();
}

function getFilePath($key) {
    $fileInfo = new \App\Constants\FileDetails;
    $filePaths = $fileInfo->fileDetails();
    
    if (array_key_exists($key, $filePaths)) {
        return $filePaths[$key]['path'];
    }
    
    return '';
}

function getFileSize($key) {
    $fileInfo = new \App\Constants\FileDetails;
    $filePaths = $fileInfo->fileDetails();
    
    if (array_key_exists($key, $filePaths) && isset($filePaths[$key]['size'])) {
        return $filePaths[$key]['size'];
    }
    
    return null;
}

/**
 * Save an Intervention image instance as WebP.
 * Falls back to cwebp when GD WebP support is missing (XAMPP PHP).
 */
function saveImageAsWebp($image, $destPath, $quality = 90) {
    // Try Intervention encode first (works for GD or Imagick drivers)
    try {
        $image->encode('webp', $quality)->save($destPath);
        return;
    } catch (\Exception $e) {
        // Continue to cwebp fallback
    }

    $cwebp = '/usr/local/bin/cwebp';
    if (!is_executable($cwebp)) {
        throw new \Exception('WebP format is not supported by the server. Please enable WebP support.');
    }

    $tmpBase = tempnam(sys_get_temp_dir(), 'webp_');
    $tmpPng = $tmpBase . '.png';
    @rename($tmpBase, $tmpPng);

    $image->encode('png')->save($tmpPng);

    $command = escapeshellarg($cwebp)
        . ' -quiet -q ' . intval($quality)
        . ' ' . escapeshellarg($tmpPng)
        . ' -o ' . escapeshellarg($destPath);

    exec($command, $output, $exitCode);
    @unlink($tmpPng);

    if ($exitCode !== 0 || !file_exists($destPath)) {
        throw new \Exception('WebP conversion failed. Please ensure cwebp is available.');
    }
}

/**
 * Save an uploaded image (or file path) as WebP with optional resize.
 * Uses Intervention first; falls back to cwebp if the driver can't read the source.
 */
function saveUploadedImageAsWebp($source, $destPath, $quality = 90, $maxWidth = null, $maxHeight = null) {
    try {
        $image = \Intervention\Image\Facades\Image::make($source);
        if ($maxWidth && $maxHeight) {
            $image->fit($maxWidth, $maxHeight, function ($constraint) {
                $constraint->upsize();
            });
        }
        saveImageAsWebp($image, $destPath, $quality);
        return;
    } catch (\Exception $e) {
        // Continue to cwebp fallback
    }

    $sourcePath = is_string($source) ? $source : $source->getRealPath();
    saveWebpFromSource($sourcePath, $destPath, $quality, $maxWidth, $maxHeight);
}

/**
 * Convert a source file to WebP via cwebp with optional resize.
 */
function saveWebpFromSource($sourcePath, $destPath, $quality = 90, $maxWidth = null, $maxHeight = null) {
    $cwebp = '/usr/local/bin/cwebp';
    if (!is_executable($cwebp)) {
        throw new \Exception('WebP format is not supported by the server. Please enable WebP support.');
    }

    $resizeArgs = '';
    if ($maxWidth && $maxHeight) {
        $size = @getimagesize($sourcePath);
        if ($size && $size[0] > 0 && $size[1] > 0) {
            $ratio = min($maxWidth / $size[0], $maxHeight / $size[1]);
            $newW = max(1, (int) floor($size[0] * $ratio));
            $newH = max(1, (int) floor($size[1] * $ratio));
            $resizeArgs = ' -resize ' . $newW . ' ' . $newH;
        }
    }

    $command = escapeshellarg($cwebp)
        . ' -quiet -q ' . intval($quality)
        . $resizeArgs
        . ' ' . escapeshellarg($sourcePath)
        . ' -o ' . escapeshellarg($destPath);

    exec($command, $output, $exitCode);
    if ($exitCode !== 0 || !file_exists($destPath)) {
        throw new \Exception('WebP conversion failed. Please ensure cwebp is available.');
    }
}

function getPageSEO($pageKey) {
    $seoData = SiteData::where('data_key', $pageKey . '.seo')->first();
    
    if ($seoData && $seoData->data_info) {
        return [
            'meta_title' => @$seoData->data_info->meta_title,
            'meta_description' => @$seoData->data_info->meta_description,
            'meta_keywords' => @$seoData->data_info->meta_keywords,
        ];
    }
    
    return [
        'meta_title' => '',
        'meta_description' => '',
        'meta_keywords' => '',
    ];
}

/**
 * Validate phone number based on country
 */
function validatePhoneByCountry($phone, $country) {
    // Clean the phone number (remove all non-digits)
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    
    // Get country data
    $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')), true);
    
    // Find country code by country name
    $countryCode = null;
    foreach ($countryData as $code => $data) {
        if ($data['country'] === $country) {
            $countryCode = $code;
            break;
        }
    }
    
    if (!$countryCode) {
        return false; // Invalid country
    }
    
    // Get dial code for the country
    $dialCode = $countryData[$countryCode]['dial_code'];
    
    // Remove country code if it's at the beginning
    if (strpos($cleanPhone, $dialCode) === 0) {
        $cleanPhone = substr($cleanPhone, strlen($dialCode));
    }
    
    // Also handle +92 format (remove the + and 92)
    if (strpos($cleanPhone, '92') === 0 && strlen($cleanPhone) > 10) {
        $cleanPhone = substr($cleanPhone, 2);
    }
    
    // Country-specific validation rules
    switch ($countryCode) {
        case 'PK': // Pakistan
            // Pakistani mobile numbers: 03XXXXXXXXX (11 digits) or 3XXXXXXXXX (10 digits)
            if (strlen($cleanPhone) === 11 && substr($cleanPhone, 0, 2) === '03') {
                return true;
            }
            if (strlen($cleanPhone) === 10 && substr($cleanPhone, 0, 1) === '3') {
                return true;
            }
            // Also accept numbers starting with 92 (country code)
            if (strlen($cleanPhone) === 12 && substr($cleanPhone, 0, 2) === '92') {
                return true;
            }
            // Accept any 10-11 digit number for Pakistan (more flexible)
            if (strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 11) {
                return true;
            }
            break;
            
        case 'US': // United States
            // US numbers: 10 digits (area code + number)
            if (strlen($cleanPhone) === 10) {
                return true;
            }
            // Also accept 11 digits starting with 1
            if (strlen($cleanPhone) === 11 && substr($cleanPhone, 0, 1) === '1') {
                return true;
            }
            break;
            
        case 'GB': // United Kingdom
            // UK numbers: 10-11 digits (excluding country code)
            if (strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 11) {
                return true;
            }
            // Also accept numbers starting with 44 (country code)
            if (strlen($cleanPhone) === 12 && substr($cleanPhone, 0, 2) === '44') {
                return true;
            }
            break;
            
        case 'IN': // India
            // Indian mobile numbers: 10 digits
            if (strlen($cleanPhone) === 10) {
                return true;
            }
            // Also accept numbers starting with 91 (country code)
            if (strlen($cleanPhone) === 12 && substr($cleanPhone, 0, 2) === '91') {
                return true;
            }
            break;
            
        default:
            // General validation: 7-15 digits
            if (strlen($cleanPhone) >= 7 && strlen($cleanPhone) <= 15) {
                return true;
            }
            break;
    }
    
    return false;
}

/**
 * Format phone number for storage
 */
function formatPhoneForStorage($phone, $country) {
    // Clean the phone number
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    
    // Get country data
    $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')), true);
    
    // Find country code by country name
    $countryCode = null;
    foreach ($countryData as $code => $data) {
        if ($data['country'] === $country) {
            $countryCode = $code;
            break;
        }
    }
    
    if (!$countryCode) {
        return $phone; // Return original if country not found
    }
    
    // Get dial code for the country
    $dialCode = $countryData[$countryCode]['dial_code'];
    
    // Remove country code if it's at the beginning
    if (strpos($cleanPhone, $dialCode) === 0) {
        $cleanPhone = substr($cleanPhone, strlen($dialCode));
    }
    
    // Return formatted number with country code
    return '+' . $dialCode . $cleanPhone;
}

function getThumbSize($key) {
    $fileInfo = new \App\Constants\FileDetails;
    $filePaths = $fileInfo->fileDetails();
    
    if (array_key_exists($key, $filePaths) && isset($filePaths[$key]['thumb'])) {
        return $filePaths[$key]['thumb'];
    }
    
    return null;
}

function custom_asset($path) {
    // When running on localhost, always use current URL so local files load correctly
    $isLocal = in_array(request()->getHost(), ['localhost', '127.0.0.1', '0.0.0.0']);
    $assetsUrl = $isLocal ? url('/') : (env('ASSETS_URL') ?: url('/'));
    
    // Remove leading slash from path if present
    $path = ltrim($path, '/');
    
    // Ensure assets URL doesn't end with slash
    $assetsUrl = rtrim($assetsUrl, '/');
    
    return $assetsUrl . '/' . $path;
}

function getImage($image, $size = null, $avatar = false): string {
    $clean = '';

    // Multiple path checks for better compatibility
    $paths = [
        public_path($image),
        base_path('public/' . $image),
        base_path($image),
        $image
    ];

    foreach ($paths as $path) {
        if (file_exists($path) && is_file($path)) {
            return custom_asset($image) . $clean;
        }
    }

    // If file not found, try direct asset URL (for live servers)
    $assetUrl = custom_asset($image);
    if ($assetUrl && $assetUrl !== custom_asset('assets/universal/images/default.png')) {
        return $assetUrl . $clean;
    }

    if ($avatar) return custom_asset('assets/universal/images/avatar.png');

    if ($size) return route('placeholder.image', $size);

    return custom_asset('assets/universal/images/default.png');
}

function getImageAlt($content, $imageKey, $default = 'image') {
    if (!$content || !$content->data_info) {
        return $default;
    }
    
    $altKey = $imageKey . '_alt';
    return @$content->data_info->$altKey ?: $default;
}

function isImage($string): bool {
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension     = pathinfo($string, PATHINFO_EXTENSION);

    if (in_array($fileExtension, $allowedExtensions)) return true;
    else return false;
}

function isHtml($string): bool {
    if (preg_match('/<.*?>/', $string)) return true;
    else return false;
}

function getPaginate($paginate = 0) {
    return $paginate ? $paginate : bs('per_page_item');
}

function paginateLinks($data) {
    return $data->appends(request()->all())->links();
}

function keyToTitle($text): string {
    return ucwords(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

/**
 * User-facing transaction remark: keep DB keys (donation_given) but show "Contribution" wording.
 */
function transactionRemarkDisplay(?string $remark): string
{
    if ($remark === null || $remark === '') {
        return '';
    }

    return match ($remark) {
        'donation_given' => __('Contribution Given'),
        'donation_received' => __('Contribution Received'),
        default => __(keyToTitle($remark)),
    };
}

/**
 * Replace whole-word "Donation" with "Contribution" in stored transaction details (legacy rows).
 */
function contributionLabelDisplay(?string $text): string
{
    if ($text === null || $text === '') {
        return '';
    }

    return (string) preg_replace('/\bDonation\b/i', 'Contribution', $text);
}

/**
 * Whether a deposit's details JSON already contains an uploaded payment proof file.
 */
function depositHasPaymentProofUpload($deposit): bool
{
    if ($deposit === null) {
        return false;
    }

    $details = $deposit->details ?? null;
    if (is_string($details)) {
        $decoded = json_decode($details, true);
        $details = is_array($decoded) ? $decoded : [];
    } elseif (is_object($details)) {
        $details = (array) $details;
    }

    if (! is_array($details)) {
        return false;
    }

    foreach ($details as $entry) {
        $row = is_object($entry) ? (array) $entry : (array) $entry;
        $name = strtolower(trim((string) ($row['name'] ?? '')));
        $type = strtolower(trim((string) ($row['type'] ?? '')));
        $value = trim((string) ($row['value'] ?? ''));
        if (($name === 'payment proof' || $type === 'file') && $value !== '') {
            return true;
        }
    }

    return false;
}

function titleToKey($text): string {
    return strtolower(str_replace(' ', '_', $text));
}

function activeTheme($asset = false): string {
    $theme = bs('active_theme');

    if ($asset) return 'assets/themes/' . $theme . '/';

    return 'themes.' . $theme . '.';
}

function getPageSections($arr = false) {
    $jsonUrl  = resource_path('views/') . str_replace('.', '/', activeTheme()) . 'site.json';
    
    // Check if file exists
    if (!file_exists($jsonUrl)) {
        \Log::error('site.json not found', ['path' => $jsonUrl, 'active_theme' => bs('active_theme')]);
        return $arr ? [] : (object)[];
    }
    
    $jsonContent = file_get_contents($jsonUrl);
    $sections = json_decode($jsonContent);

    // Check for JSON errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        \Log::error('JSON decode error in site.json', [
            'error' => json_last_error_msg(),
            'path' => $jsonUrl,
            'active_theme' => bs('active_theme')
        ]);
        return $arr ? [] : (object)[];
    }

    if ($arr) {
        $sections = json_decode($jsonContent, true);
        ksort($sections);
    }

    return $sections;
}

function getAmount($amount, $length = 2): float|int {
    $num = $amount ?? 0;
    if ($num === '' || !is_numeric($num)) {
        $num = 0;
    }
    return round((float) $num, (int) $length);
}

function removeElement($array, $value): array {
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null): void {
    $setting          = bs();
    $globalShortCodes = [
        'site_name'       => $setting->site_name,
        'site_currency'   => $setting->site_cur,
        'currency_symbol' => $setting->cur_sym,
    ];

    if (gettype($user) == 'array') $user = (object) $user;

    $shortCodes           = array_merge($shortCodes ?? [], $globalShortCodes);
    $notify               = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes   = $shortCodes;
    $notify->user         = $user;
    $notify->userColumn   = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

/**
 * Email recipients for system → admin notifications.
 * Uses settings.site_email when valid; otherwise every admin with a valid email.
 *
 * @return array<int, object{email: string, fullname: string, username: string}>
 */
function adminMailNotifyRecipients(): array
{
    $setting = bs();
    $email = isset($setting->site_email) ? trim((string) $setting->site_email) : '';
    if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [(object) [
            'email'    => strtolower($email),
            'fullname' => ($setting->site_name ?? 'Site') . ' Admin',
            'username' => 'admin',
        ]];
    }

    $recipients = [];
    foreach (\App\Models\Admin::query()->orderBy('id')->get() as $admin) {
        if (empty($admin->email) || !filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        $recipients[] = (object) [
            'email'    => $admin->email,
            'fullname' => $admin->name ?? $admin->username ?? 'Admin',
            'username' => $admin->username ?? 'admin',
        ];
    }

    return $recipients;
}

/**
 * Send the same notification email to each adminMailNotifyRecipients() address.
 */
function notifySiteAdmins(string $templateName, array $shortCodes, ?array $sendVia = null): void
{
    foreach (adminMailNotifyRecipients() as $recipient) {
        notify($recipient, $templateName, $shortCodes, $sendVia);
    }
}

function showDateTime($date, $format = null): string {
    $lang = session()->get('lang');
    Carbon::setlocale($lang);

    return $format ? Carbon::parse($date)->translatedFormat($format) : Carbon::parse($date)->translatedFormat(bs('date_format') . ' h:i A');
}

function getIpInfo(): array {
    return ClientInfo::ipInfo();
}

function osBrowser(): array {
    return ClientInfo::osBrowser();
}

/**
 * Request ka client IP (visitor), proxy/load balancer ke baad wala — sirf REMOTE_ADDR (server internal) nahi.
 * Pehle CF / X-Forwarded-For ka pehla hop / X-Real-IP, phir Laravel trusted client IP, phir REMOTE_ADDR.
 */
function getRealIP(): string
{
    $headerKeys = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_TRUE_CLIENT_IP',
        'HTTP_X_REAL_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'HTTP_CLIENT_IP',
    ];

    foreach ($headerKeys as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }
        $raw   = (string) $_SERVER[$key];
        $first = trim(explode(',', $raw)[0]);
        if ($first !== '' && filter_var($first, FILTER_VALIDATE_IP)) {
            return $first === '::1' ? '127.0.0.1' : $first;
        }
    }

    if (function_exists('request') && request()) {
        $clientIp = request()->getClientIp();
        if ($clientIp && filter_var($clientIp, FILTER_VALIDATE_IP)) {
            return $clientIp === '::1' ? '127.0.0.1' : $clientIp;
        }
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    return $ip === '::1' ? '127.0.0.1' : $ip;
}

function loadReCaptcha(): ?string {
    return Captcha::reCaptcha();
}

function verifyCaptcha(): bool {
    return Captcha::verify();
}

function loadExtension($key) {
    $plugin = Plugin::where('act', $key)->active()->first();

    return $plugin ? $plugin->generateScript() : '';
}

function urlPath($routeName, $routeParam = null): array|string {
    if ($routeParam == null) $url = route($routeName);
    else $url = route($routeName, $routeParam);

    $basePath = route('home');

    return str_replace($basePath, '', $url);
}

/**
 * Get campaign days limit from admin general settings.
 * Max allowed days between start_date and end_date.
 * Default 30 if not set.
 */
function getCampaignDaysLimit(): int
{
    $data = SiteData::where('data_key', 'general.campaign_days_limit')->first();
    if (!$data || !isset($data->data_info['campaign_days_limit'])) {
        return 30;
    }
    return max(1, min(365, (int) $data->data_info['campaign_days_limit']));
}

/**
 * Get admin-configurable required campaign documents list.
 * Stored in SiteData key: general.campaign_required_documents.
 * Returns non-empty lines; falls back to sensible defaults.
 */
function getCampaignRequiredDocuments(): array
{
    $requirements = getCampaignDocumentRequirements(true);
    return array_map(function ($item) {
        return $item['label'];
    }, $requirements);
}

/**
 * Campaign document requirements config.
 * If no config exists, returns defaults for CNIC front/back and supporting PDF.
 *
 * @param bool $onlyActive
 * @return array<int, array{id:string,field_key:string,label:string,is_required:bool,is_active:bool}>
 */
function getCampaignDocumentRequirements(bool $onlyActive = true, ?string $countryName = null): array
{
    $defaults = [
        [
            'id' => 'default-cnic-front',
            'field_key' => 'cnic_front_image',
            'label' => 'CNIC Front Copy',
            'is_required' => true,
            'is_active' => true,
            'is_global' => true,
            'countries' => [],
        ],
        [
            'id' => 'default-cnic-back',
            'field_key' => 'cnic_back_image',
            'label' => 'CNIC Back Copy',
            'is_required' => true,
            'is_active' => true,
            'is_global' => true,
            'countries' => [],
        ],
        [
            'id' => 'default-supporting-doc',
            'field_key' => 'document',
            'label' => 'Business Registration / Supporting Document',
            'is_required' => false,
            'is_active' => true,
            'is_global' => true,
            'countries' => [],
        ],
    ];

    if (!\Illuminate\Support\Facades\Schema::hasTable('campaign_document_fields')) {
        return $defaults;
    }
    $rows = CampaignDocumentField::query()
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    if ($rows->isEmpty()) return $defaults;

    $normalized = $rows->map(function ($row) {
        return [
            'id' => (string) $row->id,
            'field_key' => (string) $row->field_key,
            'label' => (string) $row->label,
            'is_required' => (bool) $row->is_required,
            'is_active' => (bool) $row->is_active,
            'is_global' => (bool) ($row->is_global ?? true),
            'countries' => array_values(array_filter((array) ($row->countries ?? []))),
        ];
    })->all();

    if (empty($normalized)) {
        return $defaults;
    }

    if ($onlyActive) {
        $normalized = array_values(array_filter($normalized, function ($item) {
            return !empty($item['is_active']);
        }));
    }

    if ($countryName !== null && trim($countryName) !== '') {
        $targetCountry = strtolower(trim($countryName));
        $normalized = array_values(array_filter($normalized, function ($item) use ($targetCountry) {
            if (!empty($item['is_global'])) {
                return true;
            }
            $countries = array_map(function ($c) {
                return strtolower(trim((string) $c));
            }, (array) ($item['countries'] ?? []));

            return in_array($targetCountry, $countries, true);
        }));
    }

    return !empty($normalized) ? $normalized : $defaults;
}

function getSiteData($dataKeys, $singleQuery = false, $limit = null, $orderById = false) {
    if ($singleQuery) {
        $siteData = SiteData::where('data_key', $dataKeys)->first();
    } else {
        $article = SiteData::query();

        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });

        if ($orderById) {
            $siteData = $article->where('data_key', $dataKeys)->orderBy('id')->get();
        } else {
            $siteData = $article->where('data_key', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }

    return $siteData;
}

function slug($string): string {
    return Str::slug($string);
}

function showMobileNumber($number): array|string {
    $length = strlen($number);

    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email): array|string {
    $endPosition = strpos($email, '@') - 1;

    return substr_replace($email, '***', 1, $endPosition);
}

function verifyG2fa($user, $code, $secret = null): bool {
    $authenticator = new GoogleAuthenticator();

    if (!$secret) $secret = $user->tsc;

    $oneCode  = $authenticator->getCode($secret);
    $userCode = $code;

    if ($oneCode == $userCode) {
        $user->tc = ManageStatus::YES;
        $user->save();

        return true;
    } else {
        return false;
    }
}

function getTrx($length = 12): string {
    $characters       = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString     = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

/**
 * Detect if request is from Flutter mobile app webview.
 * Mobile app → redirect to /user/deposit/error (Flutter detects URL).
 * Desktop → redirect to /campaigns with toast notification.
 *
 * Use X-Flutter-Webview: 1 header in Flutter WebView for reliable detection.
 * UA fallback: Android "; wv)" or iOS "iPhone" + in-app patterns.
 */
function isFlutterWebview(): bool {
    if (request()->header('X-Flutter-Webview') === '1') return true;
    $ua = request()->userAgent() ?? '';
    // Android WebView (specific "; wv)" pattern, not generic "wv")
    if (str_contains($ua, '; wv)')) return true;
    // Flutter WebView or in-app browser
    if (str_contains($ua, 'Flutter') || preg_match('/WebView\/[\d.]+/', $ua)) return true;
    return false;
}

function gatewayRedirectUrl($type = false): string {
    if (auth()->check() && $type) return 'user.deposit.success';
    // Error case: mobile app → dedicated page for Flutter to detect; desktop → campaigns + toast
    if (!$type) return isFlutterWebview() ? 'user.deposit.error' : 'campaign';

    return 'campaign';
}

/**
 * Full redirect URL with payment_status param for Flutter webview detection.
 * Flutter app can detect ?payment_status=success or ?payment_status=error to hide webview and show popup.
 * Error case: dedicated user.deposit.error page (mobile-friendly).
 * Uses url() to avoid "Route [user.deposit.error] not defined" when route cache is stale.
 */
function gatewayRedirectUrlFull(bool $success = false, ?string $message = null): string {
    $params = $success ? ['payment_status' => 'success'] : ['payment_status' => 'error'];
    if (!$success && $message !== null && trim($message) !== '') {
        $params['message'] = trim($message);
    }
    $path = match (gatewayRedirectUrl($success)) {
        'user.deposit.error'   => '/user/deposit/error',
        'user.deposit.success' => '/user/deposit/success',
        default               => '/campaigns',
    };
    return url($path . '?' . http_build_query($params));
}

function showAmount($amount, $decimal = 0, $separate = true, $exceptZeros = false): string {
    $decimal   = $decimal ?? bs('fraction_digit');
    $separator = '';

    if ($separate) $separator = ',';

    $printAmount = number_format($amount, $decimal, '.', $separator);

    if ($exceptZeros) {
        $exp = explode('.', $printAmount);

        if ($exp[1] * 1 == 0) $printAmount = $exp[0];
        else $printAmount = rtrim($printAmount, '0');
    }

    return $printAmount;
}

function cryptoQR($wallet): string {
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}

function diffForHumans($date): string {
    $lang = session()->get('lang');
    Carbon::setlocale($lang);

    return Carbon::parse($date)->diffForHumans();
}

function appendQuery($key, $value): string {
    return request()->fullUrlWithQuery([$key => $value]);
}

function strLimit($title = null, $length = 10): string {
    if ($title === null) {
        return '';
    }
    return Str::limit($title, $length);
}

function ordinal($number): string {
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');

    if (($number % 100) >= 11 && ($number % 100) <= 13) return $number . 'th';
    else return $number . $ends[$number % 10];
}

function contributionPercentage($goalAmount, $raisedAmount): int {
    return (int) (($raisedAmount / $goalAmount) * 100);
}

// New helper functions for replacing hardcoded values

function getSiteLogo($type = 'light'): string {
    $setting = bs();
    $logoPath = getFilePath('logoFavicon');
    
    if ($type === 'dark') {
        return getImage($logoPath . '/logo_dark.png');
    }
    
    return getImage($logoPath . '/logo_light.png');
}

function getSiteFavicon(): string {
    $faviconPath = getFilePath('logoFavicon');
    return getImage($faviconPath . '/favicon.png');
}

function getDashboardTitle(): string {
    return __('Dashboard');
}

function getBusinessDashboardTitle(): string {
    return __('Business Dashboard');
}

function getDefaultCurrency(): string {
    $setting = bs();
    return $setting->cur_sym ?? '$';
}

function getDefaultCurrencyCode(): string {
    $setting = bs();
    return $setting->site_cur ?? 'USD';
}

/**
 * System/platform currency code from admin settings.
 * Alias helper for clearer naming in views/controllers.
 */
function getSystemCurrency(): string
{
    return getPlatformCurrency();
}

/**
 * Full country name list (same as Admin Basic → allowed countries fallback).
 */
function getAdminDefaultAllCountryNames(): array
{
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
        'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe',
    ];
}

/**
 * Countries allowed in Admin → Basic Settings (same rules as project location / WebsiteController).
 */
function getSiteAllowedCountryNames(): array
{
    $siteData = \App\Models\SiteData::where('data_key', 'general.allowed_countries')->first();

    if ($siteData && $siteData->data_info) {
        $dataInfo = $siteData->data_info;
        if (!is_array($dataInfo)) {
            $dataInfo = is_object($dataInfo) ? (array) $dataInfo : json_decode($dataInfo, true);
        }

        $selectedCountries = $dataInfo['selected_countries'] ?? [];
        $useSelectedOnly = false;
        if (isset($dataInfo['use_selected_only'])) {
            $value = $dataInfo['use_selected_only'];
            $useSelectedOnly = ($value === true || $value === '1' || $value === 1 || $value === 'true');
        }

        if ($useSelectedOnly) {
            if (!empty($selectedCountries) && is_array($selectedCountries)) {
                $selectedCountries = array_filter($selectedCountries);
                if (!empty($selectedCountries)) {
                    sort($selectedCountries);

                    return array_values($selectedCountries);
                }
            }

            return [];
        }

        if (!empty($selectedCountries) && is_array($selectedCountries)) {
            $selectedCountries = array_filter($selectedCountries);
            if (!empty($selectedCountries)) {
                sort($selectedCountries);

                return array_values($selectedCountries);
            }
        }
    }

    return getAdminDefaultAllCountryNames();
}

/**
 * Resolve allowed country name from mobile/API country_id (1-based index in getAdminDefaultAllCountryNames()).
 * Same semantics as AllowedLocationCountriesController; returns null if id is missing or not allowed.
 */
function resolveAllowedCountryNameFromCountryId($countryId): ?string
{
    if ($countryId === null || $countryId === '') {
        return null;
    }
    $id = (int) $countryId;
    if ($id < 1) {
        return null;
    }
    $all = getAdminDefaultAllCountryNames();
    if ($id > count($all)) {
        return null;
    }
    $countryName = $all[$id - 1];
    $allowed = getSiteAllowedCountryNames();
    if (! in_array($countryName, $allowed, true)) {
        return null;
    }

    return $countryName;
}

/**
 * Map country_id (1-based index in getAdminDefaultAllCountryNames()) to country name for payment/checkout.
 * Unlike resolveAllowedCountryNameFromCountryId(), does not filter by site allowed-location list — donor country
 * must match the id the app sent so deposits.country stores the correct label.
 */
function resolveCountryNameFromAdminCountryId($countryId): ?string
{
    if ($countryId === null || $countryId === '') {
        return null;
    }
    $id = (int) $countryId;
    if ($id < 1) {
        return null;
    }
    $all = getAdminDefaultAllCountryNames();
    if ($id > count($all)) {
        return null;
    }

    return $all[$id - 1];
}

/**
 * Currency ISO code for a full country name (Admin list / project location).
 */
function getCurrencyCodeForCountryName(string $countryName): string
{
    $code = app(\App\Services\CurrencyService::class)->resolveCurrencyCodeFromCountry($countryName);

    return $code ? strtoupper($code) : 'USD';
}

/**
 * Default country name for a currency code (matches typical gateway `countries` JSON labels).
 * Used when no admin “allowed country” maps to that currency.
 */
function getCanonicalCountryNameForCurrencyCode(string $currencyCode): ?string
{
    $code = strtoupper(trim($currencyCode));
    $map  = [
        'USD' => 'United States',
        'PKR' => 'Pakistan',
        'GBP' => 'United Kingdom',
        'EUR' => 'Germany',
        'INR' => 'India',
        'BDT' => 'Bangladesh',
        'AED' => 'United Arab Emirates',
        'SAR' => 'Saudi Arabia',
        'CAD' => 'Canada',
        'AUD' => 'Australia',
        'NZD' => 'New Zealand',
        'SEK' => 'Sweden',
        'NOK' => 'Norway',
        'DKK' => 'Denmark',
        'CHF' => 'Switzerland',
        'JPY' => 'Japan',
        'CNY' => 'China',
        'HKD' => 'Hong Kong',
        'SGD' => 'Singapore',
        'MYR' => 'Malaysia',
        'IDR' => 'Indonesia',
        'THB' => 'Thailand',
        'PHP' => 'Philippines',
        'ZAR' => 'South Africa',
        'NGN' => 'Nigeria',
        'KES' => 'Kenya',
        'EGP' => 'Egypt',
        'BRL' => 'Brazil',
        'MXN' => 'Mexico',
        'TRY' => 'Turkey',
        'RUB' => 'Russia',
        'QAR' => 'Qatar',
        'KWD' => 'Kuwait',
        'OMR' => 'Oman',
        'BHD' => 'Bahrain',
    ];

    return $map[$code] ?? null;
}

/**
 * Display/local currency → canonical country for gateway `countries` matching (PKR → Pakistan, USD → United States).
 * Phir sirf woh gateways jahan yeh country allow ho; agar map na mile to session/IP fallback country.
 */
function resolveCountryForGatewayCurrencyList(?string $gatewayContextCountry = null): ?string
{
    $localCode = strtoupper(trim(getLocalCurrencyCode()));
    $canonical = $localCode !== '' ? getCanonicalCountryNameForCurrencyCode($localCode) : null;
    if ($canonical !== null) {
        return $canonical;
    }

    return $gatewayContextCountry;
}

/**
 * Country label used for gateway availability (forCountry): follows visitor display currency first,
 * so e.g. Pakistan + USD (footer) shows gateways allowed for United States / USD, not only Pakistan.
 *
 * Strict country→currency only: getCurrencyCodeForCountryName unknown pe USD default karta hai — is se
 * galat country (jo map mein nahi) pehle match ho kar PayPal jaisi restricted gateways exclude ho jate the.
 */
function resolveCountryForGatewayFiltering(): ?string
{
    $localCode = strtoupper(getLocalCurrencyCode());

    $fromDetected = session('user_detected_country');
    if (is_string($fromDetected) && $fromDetected !== '') {
        $r = resolveStrictCurrencyCodeForCountryName($fromDetected);
        if ($r !== null && $r === $localCode) {
            return $fromDetected;
        }
    }

    $allowed = getSiteAllowedCountryNames();
    $matched = [];
    foreach ($allowed as $countryName) {
        $r = resolveStrictCurrencyCodeForCountryName($countryName);
        if ($r !== null && $r === $localCode) {
            $matched[] = $countryName;
        }
    }
    if ($matched !== []) {
        if (is_string($fromDetected) && $fromDetected !== ''
            && in_array($fromDetected, $matched, true)) {
            return $fromDetected;
        }

        return $matched[0];
    }

    $canonical = getCanonicalCountryNameForCurrencyCode($localCode);
    if ($canonical !== null) {
        return $canonical;
    }

    $sessionCountry = session('user_country');
    if (is_string($sessionCountry) && $sessionCountry !== '') {
        $r = resolveStrictCurrencyCodeForCountryName($sessionCountry);
        if ($r !== null && $r === $localCode) {
            return $sessionCountry;
        }
    }

    $ipCountry = getUserCountryByIP();
    if (is_string($ipCountry) && $ipCountry !== '') {
        $r = resolveStrictCurrencyCodeForCountryName($ipCountry);
        if ($r !== null && $r === $localCode) {
            return $ipCountry;
        }
    }

    if (is_string($sessionCountry) && $sessionCountry !== '') {
        return $sessionCountry;
    }

    return is_string($ipCountry) && $ipCountry !== '' ? $ipCountry : null;
}

/**
 * Koi active gateway hai jiske `countries` mein yeh country allow ho (forGatewayRegion) aur kam az kam ek active currency row ho.
 * $restrictToCurrencyCode agar set ho to sirf us currency wali rows ginni hain; contribute list ke liye null rakho.
 */
function countryHasActiveGatewayForRegion(?string $gatewayContextCountry, ?string $restrictToCurrencyCode = null): bool
{
    $cc = $restrictToCurrencyCode !== null && trim((string) $restrictToCurrencyCode) !== ''
        ? strtoupper(trim($restrictToCurrencyCode))
        : '';

    $q = \App\Models\Gateway::query()
        ->active()
        ->whereHas('currencies', function ($currencies) use ($cc) {
            $currencies->where('status', ManageStatus::ACTIVE);
            if ($cc !== '') {
                $currencies->whereRaw('UPPER(TRIM(currency)) = ?', [$cc]);
            }
        });

    if (is_string($gatewayContextCountry) && $gatewayContextCountry !== '') {
        $q->forGatewayRegion($gatewayContextCountry, $cc !== '' ? $cc : '');
    }

    return $q->exists();
}

/**
 * @deprecated Use countryHasActiveGatewayForRegion($country, null).
 */
function localCurrencyHasGatewayForRegion(?string $gatewayContextCountry, string $localCurrencyCode): bool
{
    return countryHasActiveGatewayForRegion($gatewayContextCountry, null);
}

/**
 * Align footer / getLocalCurrencyCode() session with a country name (e.g. from deposits.country after opening payment link).
 * Sets the same keys as WebsiteController::updateUserCurrency so DetectCurrencyByIP does not overwrite until user changes footer.
 */
function syncVisitorCurrencySessionFromCountryName(?string $countryName): void
{
    $country = is_string($countryName) ? trim($countryName) : '';
    if ($country === '') {
        return;
    }

    $currencyCode = strtoupper((string) getCurrencyCodeForCountryName($country));
    $symbol = \App\Services\CurrencyService::getSymbolForCode($currencyCode);

    session()->put('user_detected_currency', $currencyCode);
    session()->put('user_detected_symbol', $symbol);
    session()->put('user_detected_country', $country);
    session()->put('user_country', $country);
    session()->put('user_currency_manual', true);
}

/**
 * Visitor ki display/local currency (ISO 4217): TCUR (.env) > session > IP > site default.
 */
function getLocalCurrencyCode(): string
{
    $tcur = config('app.currency');
    if ($tcur !== null && trim((string) $tcur) !== '') {
        return strtoupper(trim((string) $tcur));
    }
    

    if (session('user_detected_currency')) {
        return strtoupper(trim((string) session('user_detected_currency')));
    }

    $ipData = getOrFetchIpCurrencyData();
    
    if (!empty($ipData['currency_code'])) {
        return strtoupper(trim((string) $ipData['currency_code']));
    }

    return getDefaultCurrencyCode();
}

/**
 * Visitor/local currency code based on TCUR/IP fallback logic.
 * Alias helper for concise usage in templates.
 */
function getLocalCurrency(): string
{
    return getLocalCurrencyCode();
}

/**
 * TCUR (.env) / config('app.currency') set ho to display currency env se lock (session/IP override nahi).
 */
function isLocalCurrencyLockedByEnv(): bool
{
    $tcur = config('app.currency');

    return $tcur !== null && trim((string) $tcur) !== '';
}

/**
 * Country label → ISO currency jab map resolve ho; unknown label ke liye null.
 * (getCurrencyCodeForCountryName unknown pe USD default karta hai — footer matching ke liye yeh use karein.)
 */
function resolveStrictCurrencyCodeForCountryName(string $countryName): ?string
{
    $code = app(\App\Services\CurrencyService::class)->resolveCurrencyCodeFromCountry($countryName);

    return $code ? strtoupper(trim((string) $code)) : null;
}

/**
 * Footer country dropdown selection: same currency priority as getLocalCurrencyCode() (TCUR sab se pehle).
 * Env lock par session country ignore agar uski currency TCUR se mismatch ho; canonical country (e.g. USD → United States) allowed list mein ho to select.
 */
function resolveFooterCountryForLocalCurrency(array $allowedCountryNames): ?string
{
    $allowedCountryNames = array_values(array_filter($allowedCountryNames, static function ($c) {
        return is_string($c) && $c !== '';
    }));
    if ($allowedCountryNames === []) {
        return null;
    }

    $localCode     = strtoupper(getLocalCurrencyCode());
    $sessionCountry = session('user_detected_country');
    $locked        = isLocalCurrencyLockedByEnv();

    $matched = [];
    foreach ($allowedCountryNames as $c) {
        $resolved = resolveStrictCurrencyCodeForCountryName($c);
        if ($resolved !== null && $resolved === $localCode) {
            $matched[] = $c;
        }
    }

    if ($matched !== []) {
        if (is_string($sessionCountry) && $sessionCountry !== ''
            && in_array($sessionCountry, $matched, true)) {
            return $sessionCountry;
        }

        return $matched[0];
    }

    if ($locked) {
        $canonical = getCanonicalCountryNameForCurrencyCode($localCode);
        if ($canonical !== null && in_array($canonical, $allowedCountryNames, true)) {
            return $canonical;
        }
    }

    if (is_string($sessionCountry) && $sessionCountry !== ''
        && in_array($sessionCountry, $allowedCountryNames, true)) {
        if (!$locked) {
            return $sessionCountry;
        }
        $sessCode = resolveStrictCurrencyCodeForCountryName($sessionCountry);
        if ($sessCode !== null && $sessCode === $localCode) {
            return $sessionCountry;
        }
    }

    return $allowedCountryNames[0];
}

/**
 * Visitor ki local currency symbol — TCUR > session > IP > site default.
 */
function getLocalCurrencySymbol(): string
{
    $tcur = config('app.currency');
    if ($tcur !== null && trim((string) $tcur) !== '') {
        return \App\Services\CurrencyService::getSymbolForCode(trim((string) $tcur));
    }

    if (session('user_detected_symbol')) {
        return (string) session('user_detected_symbol');
    }

    if (session('user_detected_currency')) {
        return \App\Services\CurrencyService::getSymbolForCode((string) session('user_detected_currency'));
    }
    // yhn ip nkl k do customer ki 
    // Get the user's IP address (taking care of proxies, cloudflare, localhost etc.)
    

    $ipData = getOrFetchIpCurrencyData();
    if (!empty($ipData['currency_symbol'])) {
        return (string) $ipData['currency_symbol'];
    }

    return getDefaultCurrency();
}

/**
 * Amount pehle se visitor ki local currency mein ho (e.g. usdToLocal ke baad).
 * Output: local currency symbol + formatted number (TCUR / IP symbol, `getLocalCurrencySymbol`).
 */
function showCurrency($amount, int $decimal = 0, bool $separate = true, bool $exceptZeros = false): string
{
    return getLocalCurrencySymbol() . showAmount((float) ($amount ?? 0), $decimal, $separate, $exceptZeros);
}

/**
 * Format USD amount for display: convert to user's currency (TCUR/IP) and show with symbol.
 * DB stores all prices in USD; use this helper for frontend display.
 */
/**
 * Platform currency = Admin-set currency in which ALL amounts are stored in DB.
 * Returns raw DB value (not overridden by TCUR/session).
 */
function getPlatformCurrency(): string
{
    $s = bs();
    if (!$s) return 'USD';
    $raw = $s->getRawOriginal('site_cur') ?? ($s->attributes['site_cur'] ?? null);
    return $raw ? strtoupper(trim($raw)) : 'USD';
}

/**
 * Symbol for platform currency (amounts in DB). Not affected by visitor TCUR/session overrides on Setting.
 */
function getPlatformCurrencySymbol(): string
{
    return \App\Services\CurrencyService::getSymbolForCode(getPlatformCurrency());
}

/**
 * Format amount for display: DB stores in platform currency; convert to visitor's currency (IP) and show with symbol.
 */
function formatPlatformForDisplay($amount, int $decimal = 0): string
{
    $amount = (float) ($amount ?? 0);
    $setting = bs();
    $displayCode = $setting->site_cur ?? 'USD';
    $sym = $setting->cur_sym ?? '$';

    $platform = getPlatformCurrency();
    if (strtoupper($displayCode ?? '') === $platform) {
        return $sym . showAmount($amount, $decimal);
    }

    try {
        $cs = app(\App\Services\CurrencyService::class);
        $converted = $cs->convertFromPlatform((float) $amount, $displayCode);
        return $sym . showAmount($converted, $decimal);
    } catch (\Throwable $e) {
        \Log::warning('formatPlatformForDisplay failed', ['error' => $e->getMessage(), 'amount' => $amount]);
        return $sym . showAmount($amount, $decimal);
    }
}

/**
 * @deprecated Use formatPlatformForDisplay. Kept for backward compatibility.
 */
function formatUsdForDisplay($usdAmount, int $decimal = 0): string
{
    return formatPlatformForDisplay($usdAmount, $decimal);
}

/**
 * Convert USD amount to local/site currency. Returns numeric value.
 *
 * @param float $usdAmount Amount in USD
 * @param string|null $targetCurrency Target currency code (e.g. PKR, INR). If null, uses site currency from settings.
 * @return float Converted amount in target currency
 */
function usdToLocal(float $usdAmount, ?string $targetCurrency = null): float
{
    $target = $targetCurrency ?? getLocalCurrencyCode();
    $target = strtoupper(trim($target ?: 'USD'));
    if ($target === 'USD') {
        return $usdAmount;
    }
    try {
        $cs = app(\App\Services\CurrencyService::class);
        return $cs->convertUsdTo($usdAmount, $target);
    } catch (\Throwable $e) {
        \Log::warning('usdToLocal failed', ['error' => $e->getMessage(), 'amount' => $usdAmount]);
        return $usdAmount;
    }
}

function getNotificationCount(): int {
    // This can be customized based on actual notification logic
    return auth()->check() ? 3 : 0;
}

function getDefaultUserAvatar(): string {
    return asset('assets/universal/images/avatar.png');
}

function getCustomCode($type) {
    $codeData = SiteData::where('data_key', 'custom_code.' . $type)->first();
    if ($codeData && $codeData->data_info) {
        $dataInfo = is_array($codeData->data_info) ? $codeData->data_info : (array)$codeData->data_info;
        if (isset($dataInfo['code']) && !empty($dataInfo['code'])) {
            return $dataInfo['code'];
        }
    }
    return '';
}

function getDefaultCampaignImage(): string {
    return asset('assets/universal/images/default.png');
}

function getThemeColors(): array {
    $setting = bs();
    return [
        'primary' => $setting->first_color ?? '#05ce78',
        'secondary' => $setting->second_color ?? '#04b367',
        'gradient' => 'linear-gradient(135deg, ' . ($setting->first_color ?? '#05ce78') . ' 0%, ' . ($setting->second_color ?? '#04b367') . ' 100%)'
    ];
}

function getDashboardStats(): array {
    // This can be customized based on actual data
    return [
        'active_gigs' => 12,
        'total_raised' => 45230,
        'total_donors' => 1247,
        'success_rate' => 89
    ];
}

function getRecentActivities(): array {
    // This can be customized based on actual data
    return [
        [
            'type' => 'donation',
            'icon' => 'fas fa-sparkles',
            'title' => __('New contribution received'),
            'description' => '$50 for "Local Food Bank Support"',
            'color' => 'text-success'
        ],
        [
            'type' => 'campaign',
            'icon' => 'fas fa-rocket',
            'title' => __('Gig published'),
            'description' => '"Community Garden Project" is now live',
            'color' => 'text-primary'
        ]
    ];
}

function getGigCategories(): array {
    return [
        'education' => __('Education'),
        'healthcare' => __('Healthcare'),
        'environment' => __('Environment'),
        'community' => __('Community'),
        'arts' => __('Arts & Culture'),
        'technology' => __('Technology'),
        'other' => __('Other')
    ];
}

function getRewardTypes(): array {
    return [
        'digital' => __('Digital Reward'),
        'physical' => __('Physical Reward'),
        'experience' => __('Experience'),
        'recognition' => __('Recognition')
    ];
}

function getRewardColorThemes(): array {
    return [
        'gradient-red' => __('Red Gradient'),
        'gradient-blue' => __('Blue Gradient'),
        'gradient-green' => __('Green Gradient'),
        'gradient-purple' => __('Purple Gradient'),
        'gradient-orange' => __('Orange Gradient')
    ];
}

function getFileUploadLimits(): array {
    return [
        'image' => [
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
            'max_files' => 5
        ],
        'reward_image' => [
            'max_size' => 2 * 1024 * 1024, // 2MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
            'max_files' => 1
        ]
    ];
}

function getDashboardNavigation(): array {
    return [
        [
            'id' => 'overview',
            'title' => __('Overview'),
            'icon' => 'fas fa-tachometer-alt',
            'route' => 'user.dashboard'
        ],
        [
            'id' => 'rewards',
            'title' => __('Rewards'),
            'icon' => 'ti ti-gift',
            'route' => 'user.rewards'
        ],
        [
            'id' => 'create',
            'title' => __('Create Campaign'),
            'icon' => 'fas fa-rocket',
            'route' => 'start.project'
        ],
        [
            'id' => 'manage',
            'title' => __('Manage Campaigns'),
            'icon' => 'fas fa-briefcase',
            'route' => 'user.campaign.index'
        ],
        [
            'id' => 'inbox',
            'title' => __('Inbox'),
            'icon' => 'fas fa-inbox',
            'route' => 'user.inbox.index'
        ],
        [
            'id' => 'payments',
            'title' => __('Payments'),
            'icon' => 'ti ti-credit-card',
            'route' => 'user.payments'
        ],
        [
            'id' => 'analytics',
            'title' => __('Analytics'),
            'icon' => 'fas fa-chart-pie',
            'route' => 'user.transactions'
        ],
        [
            'id' => 'settings',
            'title' => __('Settings'),
            'icon' => 'fas fa-sliders-h',
            'route' => 'user.profile'
        ]
    ];
}

function getNotificationTypes(): array {
    return [
        'campaign_created' => [
            'icon' => 'fas fa-info-circle',
            'title' => __('New campaign created')
        ],
        'donation_received' => [
            'icon' => 'fas fa-donation',
            'title' => __('Contribution received')
        ],
        'new_follower' => [
            'icon' => 'fas fa-user-plus',
            'title' => __('New follower')
        ]
    ];
}

function getUserMenuItems(): array {
    return [
        [
            'route' => 'user.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'title' => __('Dashboard')
        ],
        [
            'route' => 'user.inbox.index',
            'icon' => 'fas fa-inbox',
            'title' => __('Inbox')
        ],
        [
            'route' => 'user.profile',
            'icon' => 'fas fa-user',
            'title' => __('Profile Settings')
        ],
        [
            'route' => 'user.campaign.index',
            'icon' => 'fas fa-campaign',
            'title' => __('My Campaigns')
        ],
        [
            'route' => 'user.donation.history',
            'icon' => 'fas fa-heart',
            'title' => __('My Contributions')
        ],
        [
            'route' => 'user.change.password',
            'icon' => 'fas fa-key',
            'title' => __('Change Password')
        ],
        [
            'route' => 'user.twofactor.form',
            'icon' => 'fas fa-shield-alt',
            'title' => __('2FA Settings')
        ]
    ];
}

function formatBytes($bytes, $precision = 2): string {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Get geo data (country name + code) from IP. Used for IP currency cache.
 *
 * @param string|null $ip IP address (uses request IP if null)
 * @return array{country: string, country_code: string}|null
 */
function getIpGeoData(?string $ip = null): ?array
{
    $ip = $ip ?: getRealIP();

    // Resolve localhost to public IP
    if (in_array($ip, ['127.0.0.1', '::1', 'localhost'], true)) {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $candidate = trim(explode(',', (string) $_SERVER[$header])[0]);
                if ($candidate !== '' && !in_array($candidate, ['127.0.0.1', '::1', 'localhost'], true)) {
                    $ip = $candidate;
                    break;
                }
            }
        }
    }
    if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
        try {
            $publicIP = @file_get_contents('https://api.ipify.org');
            if ($publicIP && filter_var(trim($publicIP), FILTER_VALIDATE_IP)) {
                $ip = trim($publicIP);
            }
        } catch (\Throwable $e) {
            return null;
        }
    }
    if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
        return null;
    }

    $ctx = stream_context_create(['http' => ['timeout' => 5, 'user_agent' => 'Mozilla/5.0']]);

    // ip-api.com: country (name), countryCode
    try {
        $res = @file_get_contents("http://ip-api.com/json/{$ip}", false, $ctx);
        $data = $res ? json_decode($res, true) : null;
        if ($data && ($data['status'] ?? '') === 'success' && !empty($data['country'])) {
            return [
                'country' => $data['country'],
                'country_code' => $data['countryCode'] ?? '',
            ];
        }
    } catch (\Throwable $e) {
    }

    // ipapi.co: country_name, country_code
    try {
        $res = @file_get_contents("https://ipapi.co/{$ip}/json/", false, $ctx);
        $data = $res ? json_decode($res, true) : null;
        if ($data && !empty($data['country_name'])) {
            return [
                'country' => $data['country_name'],
                'country_code' => $data['country_code'] ?? '',
            ];
        }
    } catch (\Throwable $e) {
    }

    // ipinfo.io: country (2-letter code)
    try {
        $res = @file_get_contents("https://ipinfo.io/{$ip}/json", false, $ctx);
        $data = $res ? json_decode($res, true) : null;
        if ($data && !empty($data['country'])) {
            $code = strtoupper($data['country']);
            $names = [
                'US' => 'United States', 'PK' => 'Pakistan', 'IN' => 'India', 'GB' => 'United Kingdom',
                'CA' => 'Canada', 'AU' => 'Australia', 'DE' => 'Germany', 'FR' => 'France',
            ];
            return [
                'country' => $names[$code] ?? $code,
                'country_code' => $code,
            ];
        }
    } catch (\Throwable $e) {
    }

    \Log::channel('single')->info('IpCurrencyDebug: getIpGeoData all APIs failed', ['ip' => $ip ?? '']);
    return null;
}

/**
 * Get or fetch IP currency data from DB. Cached 1 hour. Returns ['currency_code','currency_symbol','country_name','country_code'] or null.
 */
function getOrFetchIpCurrencyData(?string $ip = null): ?array
{
    if ($ip === null || $ip === '') {
        $ip = getRealIP();
    }

    try {
        $hasCacheTable = \Illuminate\Support\Facades\Schema::hasTable('ip_currency_cache');
        if (!$hasCacheTable) {
            \Log::channel('single')->info('IpCurrencyDebug: ip_currency_cache table missing — geo only, no DB cache');
        }

        if ($hasCacheTable) {
            $row = \Illuminate\Support\Facades\DB::table('ip_currency_cache')->where('ip', $ip)->first();
            $now = now();
            $fiveMinutesAgo = $now->copy()->subMinutes(5);
            if ($row && $row->refreshed_at && strtotime($row->refreshed_at) >= $fiveMinutesAgo->timestamp) {
                return [
                    'currency_code' => $row->currency_code ?? 'USD',
                    'currency_symbol' => $row->currency_symbol ?? '$',
                    'country_name' => $row->country_name ?? '',
                    'country_code' => $row->country_code ?? '',
                ];
            }
        }

        // Localhost: getIpGeoData() ipify + external geo APIs use karta hai — yahan early return mat karo.
        $geo = getIpGeoData($ip);
        if (!$geo) {
            \Log::channel('single')->info('IpCurrencyDebug: getIpGeoData returned null', ['ip' => $ip]);
            return null;
        }

        $currencyService = app(\App\Services\CurrencyService::class);
        $currencyCode = $currencyService->resolveCurrencyCodeFromCountry($geo['country_code'] ?: $geo['country']) ?: 'USD';
        $symbol = \App\Services\CurrencyService::getSymbolForCode($currencyCode);
        $payload = [
            'currency_code' => $currencyCode,
            'currency_symbol' => $symbol,
            'country_name' => $geo['country'] ?? '',
            'country_code' => $geo['country_code'] ?? '',
        ];

        if ($hasCacheTable) {
            $now = now();
            \Illuminate\Support\Facades\DB::table('ip_currency_cache')->updateOrInsert(
                ['ip' => $ip],
                [
                    'country_code' => $geo['country_code'] ?? '',
                    'country_name' => $geo['country'] ?? '',
                    'currency_code' => $currencyCode,
                    'currency_symbol' => $symbol,
                    'refreshed_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        return $payload;
    } catch (\Throwable $e) {
        \Log::channel('single')->warning('IpCurrencyDebug: getOrFetchIpCurrencyData failed', ['ip' => $ip ?? '', 'error' => $e->getMessage()]);
        return null;
    }
}

/**
 * Get user's country based on IP address
 */
function getUserCountryByIP() {
    try {
        $ip = getRealIP();

        // If still localhost, try external service to get public IP
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            try {
                $publicIP = file_get_contents('https://api.ipify.org');
                if ($publicIP && filter_var($publicIP, FILTER_VALIDATE_IP)) {
                    $ip = $publicIP;
                }
            } catch (Exception $e) {
                // Continue with local IP
            }
        }
        
        // Skip if still localhost
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return null;
        }
        
        // Try multiple IP geolocation services for better reliability
        
        // Service 1: ip-api.com (free, no API key needed)
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);
            
            $response = file_get_contents("http://ip-api.com/json/{$ip}", false, $context);
            $data = json_decode($response, true);
            
            if ($data && $data['status'] === 'success' && !empty($data['country'])) {
                \Log::info('IP Detection Success - ip-api.com', ['ip' => $ip, 'country' => $data['country']]);
                return $data['country'];
            }
        } catch (Exception $e) {
            \Log::warning('IP Detection Failed - ip-api.com', ['ip' => $ip, 'error' => $e->getMessage()]);
        }
        
        // Service 2: ipapi.co (fallback)
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);
            
            $response = file_get_contents("https://ipapi.co/{$ip}/json/", false, $context);
            $data = json_decode($response, true);
            
            if ($data && isset($data['country_name']) && !empty($data['country_name'])) {
                \Log::info('IP Detection Success - ipapi.co', ['ip' => $ip, 'country' => $data['country_name']]);
                return $data['country_name'];
            }
        } catch (Exception $e) {
            \Log::warning('IP Detection Failed - ipapi.co', ['ip' => $ip, 'error' => $e->getMessage()]);
        }
        
        // Service 3: ipinfo.io (fallback)
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);
            
            $response = file_get_contents("https://ipinfo.io/{$ip}/json", false, $context);
            $data = json_decode($response, true);
            
            if ($data && isset($data['country']) && !empty($data['country'])) {
                \Log::info('IP Detection Success - ipinfo.io', ['ip' => $ip, 'country' => $data['country']]);
                return $data['country'];
            }
        } catch (Exception $e) {
            \Log::warning('IP Detection Failed - ipinfo.io', ['ip' => $ip, 'error' => $e->getMessage()]);
        }
        
        \Log::warning('IP Detection Failed - All services', ['ip' => $ip]);
        return null;
    } catch (Exception $e) {
        \Log::error('IP Detection Exception', ['error' => $e->getMessage()]);
        return null;
    }
}

/**
 * Get user's country with fallback options
 */
function detectUserCountry() {
    // 1. Check if user is logged in and has country set
    if (auth()->check() && auth()->user()->country_name) {
        return auth()->user()->country_name;
    }
    
    // 2. Check if country is in session
    if (session()->has('user_country')) {
        return session()->get('user_country');
    }
    
    // 3. Check if country is in request
    if (request()->has('country')) {
        $country = request('country');
        session()->put('user_country', $country);
        return $country;
    }
    
    // 4. Try to detect by IP
    $ipCountry = getUserCountryByIP();
    if ($ipCountry) {
        session()->put('user_country', $ipCountry);
        return $ipCountry;
    }
    
    return null;
}

/**
 * Get country code from country name
 */
function getCountryCode($countryName) {
    $countryMap = [
        'Pakistan' => 'PK',
        'United States' => 'US',
        'United Kingdom' => 'GB',
        'India' => 'IN',
        'Canada' => 'CA',
        'Australia' => 'AU',
        'Germany' => 'DE',
        'France' => 'FR',
        'China' => 'CN',
        'Japan' => 'JP',
        'Brazil' => 'BR',
        'Mexico' => 'MX',
        'Spain' => 'ES',
        'Italy' => 'IT',
        'Netherlands' => 'NL',
        'Sweden' => 'SE',
        'Norway' => 'NO',
        'Denmark' => 'DK',
        'Finland' => 'FI',
        'Switzerland' => 'CH',
        'Austria' => 'AT',
        'Belgium' => 'BE',
        'Ireland' => 'IE',
        'New Zealand' => 'NZ',
        'South Africa' => 'ZA',
        'Egypt' => 'EG',
        'Nigeria' => 'NG',
        'Kenya' => 'KE',
        'Ghana' => 'GH',
        'Morocco' => 'MA',
        'Tunisia' => 'TN',
        'Algeria' => 'DZ',
        'Libya' => 'LY',
        'Sudan' => 'SD',
        'Ethiopia' => 'ET',
        'Uganda' => 'UG',
        'Tanzania' => 'TZ',
        'Zambia' => 'ZM',
        'Zimbabwe' => 'ZW',
        'Botswana' => 'BW',
        'Namibia' => 'NA',
        'Mozambique' => 'MZ',
        'Angola' => 'AO',
        'Congo' => 'CG',
        'Cameroon' => 'CM',
        'Ivory Coast' => 'CI',
        'Senegal' => 'SN',
        'Mali' => 'ML',
        'Burkina Faso' => 'BF',
        'Niger' => 'NE',
        'Chad' => 'TD',
        'Central African Republic' => 'CF',
        'Gabon' => 'GA',
        'Equatorial Guinea' => 'GQ',
        'Sao Tome and Principe' => 'ST',
        'Cape Verde' => 'CV',
        'Guinea-Bissau' => 'GW',
        'Guinea' => 'GN',
        'Sierra Leone' => 'SL',
        'Liberia' => 'LR',
        'Togo' => 'TG',
        'Benin' => 'BJ',
        'Mauritania' => 'MR',
        'Western Sahara' => 'EH',
        'Djibouti' => 'DJ',
        'Somalia' => 'SO',
        'Eritrea' => 'ER',
        'Comoros' => 'KM',
        'Madagascar' => 'MG',
        'Mauritius' => 'MU',
        'Seychelles' => 'SC',
        'Malawi' => 'MW',
        'Lesotho' => 'LS',
        'Eswatini' => 'SZ'
    ];
    
    return $countryMap[$countryName] ?? null;
}

/**
 * Format phone number based on country
 */
function formatPhoneNumber($phone, $countryCode) {
    // Remove all non-digit characters
    $cleanPhone = preg_replace('/\D/', '', $phone);
    
    // Country-wise formatting
    switch($countryCode) {
        case 'PK': // Pakistan
            if (strlen($cleanPhone) === 11 && substr($cleanPhone, 0, 2) === '03') {
                return '+92 ' . substr($cleanPhone, 1, 3) . ' ' . substr($cleanPhone, 4, 3) . ' ' . substr($cleanPhone, 7);
            } elseif (strlen($cleanPhone) === 10 && substr($cleanPhone, 0, 1) === '3') {
                return '+92 ' . substr($cleanPhone, 0, 3) . ' ' . substr($cleanPhone, 3, 3) . ' ' . substr($cleanPhone, 6);
            } elseif (strlen($cleanPhone) === 12 && substr($cleanPhone, 0, 2) === '92') {
                return '+' . substr($cleanPhone, 0, 2) . ' ' . substr($cleanPhone, 2, 3) . ' ' . substr($cleanPhone, 5, 3) . ' ' . substr($cleanPhone, 8);
            }
            break;
            
        case 'US': // United States
            if (strlen($cleanPhone) === 10) {
                return '(' . substr($cleanPhone, 0, 3) . ') ' . substr($cleanPhone, 3, 3) . '-' . substr($cleanPhone, 6);
            } elseif (strlen($cleanPhone) === 11 && substr($cleanPhone, 0, 1) === '1') {
                return '+1 (' . substr($cleanPhone, 1, 3) . ') ' . substr($cleanPhone, 4, 3) . '-' . substr($cleanPhone, 7);
            }
            break;
            
        case 'GB': // United Kingdom
            if (strlen($cleanPhone) === 11 && substr($cleanPhone, 0, 1) === '0') {
                return '+44 ' . substr($cleanPhone, 1, 4) . ' ' . substr($cleanPhone, 5, 3) . ' ' . substr($cleanPhone, 8);
            } elseif (strlen($cleanPhone) === 12 && substr($cleanPhone, 0, 2) === '44') {
                return '+' . substr($cleanPhone, 0, 2) . ' ' . substr($cleanPhone, 2, 4) . ' ' . substr($cleanPhone, 6, 3) . ' ' . substr($cleanPhone, 9);
            }
            break;
            
        case 'IN': // India
            if (strlen($cleanPhone) === 10) {
                return '+91 ' . substr($cleanPhone, 0, 5) . ' ' . substr($cleanPhone, 5);
            } elseif (strlen($cleanPhone) === 12 && substr($cleanPhone, 0, 2) === '91') {
                return '+' . substr($cleanPhone, 0, 2) . ' ' . substr($cleanPhone, 2, 5) . ' ' . substr($cleanPhone, 7);
            }
            break;
            
        default:
            // Generic international format
            if (strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 15) {
                return '+' . $cleanPhone;
            }
    }
    
    return $phone;
}

/**
 * Get phone placeholder based on country
 */
function getPhonePlaceholder($countryCode) {
    $placeholders = [
        'PK' => 'e.g., 0300 1234567 or 300 1234567',
        'US' => 'e.g., (555) 123-4567',
        'GB' => 'e.g., 07700 900000',
        'IN' => 'e.g., 98765 43210',
        'CA' => 'e.g., (555) 123-4567',
        'AU' => 'e.g., 0412 345 678',
        'DE' => 'e.g., 0171 1234567',
        'FR' => 'e.g., 06 12 34 56 78'
    ];
    
    return $placeholders[$countryCode] ?? 'e.g., +1234567890';
}

/**
 * Calculate donation percentage based on goal amount and raised amount
 */
function donationPercentage($goalAmount, $raisedAmount) {
    if (!$goalAmount || $goalAmount <= 0) {
        return 0;
    }
    
    $percentage = ($raisedAmount / $goalAmount) * 100;
    
    // Cap at 100% to avoid showing more than 100%
    return min(round($percentage, 2), 100);
}

/**
 * Get ApnaCrowdfunding as italic linked text
 * Returns the brand name as an italic link throughout the project
 */
function apnaCrowdfundingLink($url = '#', $class = 'italic-text') {
    return '<em>ApnaCrowdfunding</em>';
}