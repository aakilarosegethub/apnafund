<?php

use App\Constants\ManageStatus;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\FileManager;
use App\Lib\GoogleAuthenticator;
use App\Models\Plugin;
use App\Models\Setting;
use App\Models\SiteData;
use App\Notify\Notify;
use Carbon\Carbon;
use Illuminate\Support\Str;

function systemDetails(): array {
    $system['name']          = 'PnixFund';
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
    $setting = cache()->get('setting');

    if (!$setting) {
        $setting = Setting::first();
        cache()->put('setting', $setting);
    }

    if ($fieldName) return @$setting->$fieldName;

    return $setting;
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null): string {
    $fileManager        = new FileManager($file);
    $fileManager->path  = $location;
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
    return fileManager()->$key()->path;
}

function getFileSize($key) {
    return fileManager()->$key()->size;
}

function getThumbSize($key) {
    return fileManager()->$key()->thumb;
}

function getImage($image, $size = null, $avatar = false): string {
    $clean = '';

    if (file_exists($image) && is_file($image)) return asset($image) . $clean;

    if ($avatar) return asset('assets/universal/images/avatar.png');

    if ($size) return route('placeholder.image', $size);

    return asset('assets/universal/images/default.png');
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
    $sections = json_decode(file_get_contents($jsonUrl));

    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }

    return $sections;
}

function getAmount($amount, $length = 2): float|int {
    return round($amount ?? 0, $length);
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

function getRealIP() {
    $ip = $_SERVER["REMOTE_ADDR"];

    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }

    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }

    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }

    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }

    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
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

function gatewayRedirectUrl($type = false): string {
    if (auth()->check() && $type) return 'user.donation.history';

    return 'campaign';
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
    return Str::limit($title, $length);
}

function ordinal($number): string {
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');

    if (($number % 100) >= 11 && ($number % 100) <= 13) return $number . 'th';
    else return $number . $ends[$number % 10];
}

function donationPercentage($goalAmount, $raisedAmount): int {
    return (int) (($raisedAmount / $goalAmount) * 100);
}
