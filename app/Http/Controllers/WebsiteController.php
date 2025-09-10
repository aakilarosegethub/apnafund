<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Deposit;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Language;
use App\Models\SiteData;
use App\Constants\ManageStatus;
use App\Models\GatewayCurrency;
use App\Models\AdminNotification;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class WebsiteController extends Controller
{
    public function __construct()
    {

        parent::__construct();
        $this->activeTheme = 'themes.apnafund.';
        // You can add any initialization code here if needed
    }
    function home() {
        $pageTitle               = 'Home';
        
        // Get dynamic home page content
        $heroContent = SiteData::where('data_key', 'home.hero')->first();
        $infoBannerContent = SiteData::where('data_key', 'home.info_banner')->first();
        $featuredProjectsContent = SiteData::where('data_key', 'home.featured_projects')->first();
        
        // Get featured campaigns (approved and featured, regardless of date status)
        $featuredCampaigns = Campaign::commonQuery()->approve()->featured()->latest()->limit(5)->get();

        return view($this->activeTheme .'page.home', compact('pageTitle', 'heroContent', 'infoBannerContent', 'featuredProjectsContent', 'featuredCampaigns'));
    }

    function homeNew() {
        $pageTitle               = 'Home';
        $coverContent            = SiteData::where('data_key', 'cover.content')->first();
        $bannerElements          = getSiteData('banner.element', false, null, true);
        $basicCampaignQuery      = Campaign::campaignCheck()->approve();
        $featuredCampaignContent = getSiteData('featured_campaign.content', true);
        $featuredCampaigns       = array();
        $campaignCategoryContent = getSiteData('campaign_category.content', true);
        $campaignCategories      = Category::active()->get();
        $recentCampaignContent   = getSiteData('recent_campaign.content', true);
        $recentCampaigns         = (clone $basicCampaignQuery)->latest()->limit(9)->get();
        $counterElements         = getSiteData('counter.element', false, null, true);
        $upcomingContent         = getSiteData('upcoming.content', true);
        $upcomingCampaigns       = Campaign::upcomingCheck()->approve()->orderby('start_date')->limit(6)->get();
        $subscribeContent        = getSiteData('subscribe.content', true);
        $successContent          = getSiteData('success_story.content', true);
        $successElements         = getSiteData('success_story.element', false, 3, true);

        return view('themes.primary.page.apnafund-new', compact('pageTitle', 'coverContent', 'bannerElements', 'featuredCampaignContent', 'counterElements', 'campaignCategoryContent', 'campaignCategories', 'recentCampaignContent', 'recentCampaigns', 'featuredCampaigns', 'upcomingContent', 'upcomingCampaigns', 'subscribeContent', 'successContent', 'successElements'));
    }

    function volunteers() {
        $pageTitle         = 'Volunteers';
        $volunteerElements = SiteData::where('data_key', 'volunteer.element')->paginate(getPaginate());
        $pageSEO           = getPageSEO('volunteer');

        return view($this->activeTheme . 'page.volunteer', compact('pageTitle', 'volunteerElements', 'pageSEO'));
    }

    function aboutUs() {
        $pageTitle          = 'About Us';
        $clientContent      = getSiteData('client_review.content', true);
        $clientElements     = getSiteData('client_review.element', false, null, true);
        $pageSEO            = getPageSEO('about_us');

        return view($this->activeTheme . 'page.about', compact('pageTitle', 'clientContent', 'clientElements', 'pageSEO'));
    }

    function faq() {
        $pageTitle   = 'FAQ';
        $faqContent  = getSiteData('faq.content', true);
        $faqElements = getSiteData('faq.element', false, null, true);
        $pageSEO     = getPageSEO('faq');

        return view($this->activeTheme . 'page.faq', compact('pageTitle', 'faqContent', 'faqElements', 'pageSEO'));
    }

    function campaigns() {
        $pageTitle  = 'Campaigns';
        
        // Get categories with campaign counts
        $categories = Category::active()
            ->select('name', 'slug')
            ->withCount(['campaigns' => function($query) {
                $query->commonQuery()->approve();
            }])
            ->get();
            
        $campaigns  = Campaign::when(request()->filled('category'), function ($query) {
                                    $categorySlug = request('category');
                                    $category     = Category::where('slug', $categorySlug)->active()->first();

                                    if ($category) $query->where('category_id', $category->id);
                                })->when(request()->filled('name'), function ($query) {
                                    $query->where('name', 'like', '%' . request('name') . '%');
                                })->when(request()->filled('date_range'), function ($query) {
                                    $dateArray = explode(' - ', request('date_range'));
                                    $startDate = Carbon::parse($dateArray[0])->format('Y-m-d');
                                    $endDate   = Carbon::parse($dateArray[1])->format('Y-m-d');

                                    $query->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate);
                                })->commonQuery()
                                ->approve()
                                ->latest()
                                ->paginate(getPaginate(10));

        return view($this->activeTheme . 'page.campaign', compact('pageTitle', 'categories', 'campaigns'));
    }

    function campaignShow($slug) {

        $pageTitle        = 'Campaign Details';
        $campaignData     = Campaign::with('rewards')->where('slug', $slug)->approve()->firstOrFail();
        $comments         = Comment::with('user')->where('campaign_id', $campaignData->id)->approve()->latest()->limit(6)->get();

        $commentCount     = Comment::where('campaign_id', $campaignData->id)->approve()->count();
        $authUser         = auth()->user();
        $relatedCampaigns = Campaign::where('category_id', $campaignData->category_id)->whereNot('slug', $campaignData->slug)->approve()->latest()->limit(4)->get();

        $seoContents['keywords']           = $campaignData->meta_keywords ?? [];
        $seoContents['social_title']       = $campaignData->name;
        $seoContents['description']        = strLimit($campaignData->description, 150);
        $seoContents['social_description'] = strLimit($campaignData->description, 150);
        $imageSize                         = getFileSize('campaign');
        $seoContents['image']              = getImage(getFilePath('campaign') . '/' . $campaignData->image, $imageSize);
        $seoContents['image_size']         = $imageSize;


        $countries         = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $gatewayCurrencies = GatewayCurrency::whereHas('method', fn ($gateway) => $gateway->active())
                                            ->with('method')
                                            ->orderby('method_code')
                                            ->get();

        $donations         = Deposit::with('user')
                                    ->where('campaign_id', $campaignData->id)
                                    ->done()
                                    ->latest()
                                    ->limit(5)
                                    ->get();
                                    

        return view($this->activeTheme . 'page.campaignShow', compact('pageTitle', 'campaignData', 'relatedCampaigns', 'seoContents', 'authUser', 'comments', 'commentCount', 'countries', 'gatewayCurrencies', 'donations'));
    }

    function campaignDonate($slug) {
        $pageTitle        = 'Make a Contribution';
        $campaignData     = Campaign::where('slug', $slug)->approve()->firstOrFail();
        $authUser         = auth()->user();

        // Check if campaign is expired
        if ($campaignData->isExpired()) {
            $toast[] = ['error', 'This campaign has expired'];
            return redirect()->route('campaign.show', $slug)->withToasts($toast);
        }

        $seoContents['keywords']           = $campaignData->meta_keywords ?? [];
        $seoContents['social_title']       = $campaignData->name;
        $seoContents['description']        = strLimit($campaignData->description, 150);
        $seoContents['social_description'] = strLimit($campaignData->description, 150);
        $imageSize                         = getFileSize('campaign');
        $seoContents['image']              = getImage(getFilePath('campaign') . '/' . $campaignData->image, $imageSize);
        $seoContents['image_size']         = $imageSize;

        $countries         = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        
        // Get user's country - prioritize IP detection for better UX
        
        $userCountry = null;

        if(!session()->get('user_country'))
        {
        // 1. First try IP-based detection
        $ipCountry = getUserCountryByIP();
        if ($ipCountry) {
            $userCountry = $ipCountry;
        }
        
        // 2. If no IP detection, check logged-in user's country
        if (!$userCountry && $authUser && $authUser->country_name) {
            $userCountry = $authUser->country_name;
        }
        
        // 3. If still no country, check session
        if (!$userCountry && session()->has('user_country')) {
            $userCountry = session()->get('user_country');
            
        }
        session()->put('user_country', $userCountry);
    }
    else{
        $userCountry = session()->get('user_country');
    }
        // dd(session());
        echo $userCountry;
        
        // 4. If still no country, check request parameter
        if (!$userCountry && request()->has('country')) {
            $requestCountry = request('country');
            if (!empty($requestCountry)) {
                $userCountry = $requestCountry;
                session()->put('user_country', $userCountry);
            }
        }
        
        // Filter gateways based on detected country
        $gatewayCurrencies = GatewayCurrency::whereHas('method', function ($gateway) use ($userCountry) {
                                            $gateway->active();
                                            if ($userCountry) {
                                                $gateway->forCountry($userCountry);
                                            }
                                        })
                                        ->with('method')
                                        ->orderby('method_code')
                                        ->get();



        return view($this->activeTheme . 'page.campaignDonate', compact('pageTitle', 'campaignData', 'seoContents', 'authUser', 'countries', 'gatewayCurrencies'));
    }

    function storeCampaignComment($slug) {
        // Debug logging
        \Log::info('Comment submission attempt', [
            'slug' => $slug,
            'request_data' => request()->all(),
            'user_id' => auth()->id(),
            'ip' => request()->ip()
        ]);
        
        $this->validate(request(), [
            'name'    => 'required|string|max:40',
            'email'   => 'required|string|max:40',
            'comment' => 'required|string',
            'rating'  => 'nullable|integer|min:1|max:5',
            'title'   => 'nullable|string|max:255',
        ]);

        // Check whether user active or not
        if (auth()->check() && !auth()->user()->status) {
            $toast[] = ['error', 'The user is banned'];

            return back()->withToasts($toast);
        }

        $campaign = Campaign::where('slug', $slug)->first();

        // Check whether campaign found or not
        if (!$campaign) {
            $toast[] = ['error', 'Campaign not found'];

            return back()->withToasts($toast);
        }

        // Check whether campaign category active or not
        if (!$campaign->category->status) {
            $toast[] = ['error', 'Campaign category is not active'];

            return back()->withToasts($toast);
        }

        // Check for approved & running campaign
        if ($campaign->status == ManageStatus::CAMPAIGN_PENDING ||
            $campaign->status == ManageStatus::CAMPAIGN_REJECTED ||
            !$campaign->isRunning() || 
            $campaign->isExpired()
        ) {
            $toast[] = ['error', 'Campaign is unavailable right now'];

            return back()->withToasts($toast);
        }

        // Check whether user commenting on his/her own campaign
        if (auth()->check() && $campaign->user_id == auth()->id()) {
            $toast[] = ['error', 'You can\'t comment on your own campaign'];

            return back()->withToasts($toast);
        }

        $comment = new Comment();

        if (auth()->check()) {
            $comment->user_id = auth()->id();
            $comment->name    = auth()->user()->fullname;
            $comment->email   = auth()->user()->email;
        } else {
            $comment->user_id = null;
            $comment->name    = request('name');
            $comment->email   = request('email');
        }

        $comment->campaign_id = $campaign->id;
        $comment->comment     = request('comment');
        $comment->rating      = request('rating') ?: null;
        $comment->title       = request('title');
        $comment->save();
        
        \Log::info('Comment saved successfully', [
            'comment_id' => $comment->id,
            'campaign_id' => $campaign->id,
            'user_id' => $comment->user_id,
            'name' => $comment->name,
            'email' => $comment->email
        ]);

        // Create admin notification
        $adminNotification = new AdminNotification();

        if (auth()->check()) {
            $adminNotification->user_id = auth()->id();
            $adminNotification->title   = auth()->user()->fullname . ' has commented on a campaign.';
        } else {
            $adminNotification->user_id = 0;
            $adminNotification->title   = request('name') . ' has commented on a campaign.';
        }

        $adminNotification->click_url = urlPath('admin.comments.index');
        $adminNotification->save();

        $toast[] = ['success', 'Your review has been submitted successfully! Please wait for admin approval.'];

        return back()->withToasts($toast);
    }

    function fetchCampaignComment($slug) {
        $validator = Validator::make(request()->all(), [
            'skip' => 'required|integer|gt:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $campaign = Campaign::where('slug', $slug)->first();

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found'
            ], 404);
        }

        $commentsCount = Comment::where('campaign_id', $campaign->id)->approve()->count();
        $skip          = (int) request('skip');
        $comments      = Comment::with('user')
                                ->where('campaign_id', $campaign->id)
                                ->skip($skip)
                                ->approve()
                                ->latest()
                                ->limit(5)
                                ->get();

        $remainingComments = $commentsCount - ($skip + $comments->count());

        if (count($comments)) {
            $view = view($this->activeTheme . 'partials.basicComment', compact('comments'))->render();

            return response()->json([
                'html'               => $view,
                'remaining_comments' => $remainingComments,
            ]);
        } else {
            return response()->json([
                'message' => 'No more comments found'
            ], 404);
        }
    }

    function campaignDonations($slug) {
        $pageTitle = 'All Donations';
        $campaign = Campaign::where('slug', $slug)->approve()->firstOrFail();
        
        $donations = Deposit::with('user')
                            ->where('campaign_id', $campaign->id)
                            ->done()
                            ->latest()
                            ->paginate(20);

        return view($this->activeTheme . 'page.campaignDonations', compact('pageTitle', 'campaign', 'donations'));
    }

    function campaignTopDonations($slug) {
        $pageTitle = 'Top Donations';
        $campaign = Campaign::where('slug', $slug)->approve()->firstOrFail();
        
        $donations = Deposit::with('user')
                            ->where('campaign_id', $campaign->id)
                            ->done()
                            ->orderBy('amount', 'desc')
                            ->paginate(20);

        return view($this->activeTheme . 'page.campaignTopDonations', compact('pageTitle', 'campaign', 'donations'));
    }

    function upcomingCampaigns() {
        $pageTitle         = 'Upcoming Campaigns';
        $upcomingCampaigns = Campaign::when(request()->filled('category'), function ($query) {
                                $categorySlug = request('category');
                                $category     = Category::where('slug', $categorySlug)->active()->first();

                                if ($category) $query->where('category_id', $category->id);
                            })->when(request()->filled('name'), function ($query) {
                                    $query->where('name', 'like', '%' . request('name') . '%');
                            })->upcomingCheck()
                            ->approve()
                            ->orderby('start_date')
                            ->paginate(getPaginate(10));

        $categories = Category::active()->select('name', 'slug')->get();

        return view($this->activeTheme . 'page.upcomingCampaign', compact('pageTitle', 'upcomingCampaigns', 'categories'));
    }

    function upcomingCampaignShow($slug) {
        $pageTitle    = 'Upcoming Campaign Details';
        $campaignData = Campaign::where('slug', $slug)->upcomingCheck()->approve()->firstOrFail();

        $seoContents['keywords']           = $campaignData->meta_keywords ?? [];
        $seoContents['social_title']       = $campaignData->name;
        $seoContents['description']        = strLimit($campaignData->description, 150);
        $seoContents['social_description'] = strLimit($campaignData->description, 150);
        $imageSize                         = getFileSize('campaign');
        $seoContents['image']              = getImage(getFilePath('campaign') . '/' . $campaignData->image, $imageSize);
        $seoContents['image_size']         = $imageSize;

        $moreUpcomingCampaigns = Campaign::upcomingCheck()
                                ->whereNot('slug', $campaignData->slug)
                                ->approve()
                                ->orderby('start_date')
                                ->limit(6)
                                ->get();

        return view($this->activeTheme . 'page.upcomingCampaignShow', compact('pageTitle', 'campaignData', 'seoContents', 'moreUpcomingCampaigns'));
    }

    function stories() {
        $pageTitle       = 'Success Stories';
        $successElements = SiteData::where('data_key', 'success_story.element')->paginate(getPaginate());
        $pageSEO         = getPageSEO('success_story');

        return view($this->activeTheme . 'page.stories', compact('pageTitle', 'successElements', 'pageSEO'));
    }

    function storyShow($id) {
        $pageTitle = 'Story Details';
        $storyData = SiteData::findOrFail($id);

        $seoContents['keywords']           = $storyData->data_info->meta_keywords ?? [];
        $seoContents['social_title']       = $storyData->data_info->title;
        $seoContents['social_description'] = strLimit($storyData->data_info->details, 150);
        $seoContents['description']        = strLimit($storyData->data_info->details, 150);
        $imageSize                         = '855x475';
        $seoContents['image']              = getImage('assets/images/site/success_story/' . $storyData->data_info->image, $imageSize);
        $seoContents['image_size']         = $imageSize;

        $moreStories = SiteData::where('data_key', 'success_story.element')->whereNot('id', $id)->limit(3)->get();

        return view($this->activeTheme . 'page.storyShow', compact('pageTitle', 'storyData', 'seoContents', 'moreStories'));
    }

    function businessResources() {
        $pageTitle = 'Business Resources';
        
        // Get dynamic content from database
        $businessContent = getSiteData('business_resources.content', true);
        $successContent = getSiteData('success_story.content', true);
        $successElements = getSiteData('success_story.element', false, 4, true);
        
        // Get featured campaigns for inspiration
        $featuredCampaigns = Campaign::commonQuery()->approve()->featured()->latest()->limit(2)->get();
        
        // Get categories for tips
        $categories = Category::active()->get();
        
        $pageSEO = getPageSEO('business_resources');
        
        return view($this->activeTheme . 'page.businessResources', compact(
            'pageTitle', 
            'businessContent', 
            'successContent', 
            'successElements', 
            'featuredCampaigns',
            'categories',
            'pageSEO'
        ));
    }

    function contact() {
        $pageTitle       = 'Contact';
        $user            = auth()->user();
        $contactContent  = getSiteData('contact_us.content', true);
        $contactElements = getSiteData('contact_us.element', false, null, true);
        $pageSEO         = getPageSEO('contact_us');

        return view($this->activeTheme . 'page.contact', compact('pageTitle', 'user', 'contactContent', 'contactElements', 'pageSEO'));
    }

    function contactStore() {
        $this->validate(request(), [
            'name'    => 'required|string|max:40',
            'email'   => 'required|string|max:40',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $user         = auth()->user();
        $email        = $user ? $user->email : request('email');
        $contactCheck = Contact::where('email', $email)->where('status', ManageStatus::NO)->first();

        if ($contactCheck) {
            $toast[] = ['warning', 'There is an existing contact on our record, kindly wait for the admin\'s response'];

            return back()->withToasts($toast);
        }

        $contact          = new Contact();
        $contact->name    = $user ? $user->fullname : request('name');
        $contact->email   = $email;
        $contact->subject = request('subject');
        $contact->message = request('message');
        $contact->save();

        $toast[] = ['success', 'We have received your message, kindly wait for the admin\'s response'];

        return back()->withToasts($toast);
    }

    function changeLanguage($lang = null) {
        $language = Language::where('code', $lang)->first();

        if (!$language) $lang = 'en';

        session()->put('lang', $lang);

        return back();
    }

    function cookieAccept() {
        Cookie::queue('gdpr_cookie', bs('site_name'), 43200);
    }

    function cookiePolicy() {
        $pageTitle = 'Cookie Policy';
        $cookie    = SiteData::where('data_key', 'cookie.data')->first();

        return view($this->activeTheme . 'page.cookie',compact('pageTitle', 'cookie'));
    }

    function maintenance() {
        if (bs('site_maintenance') == ManageStatus::INACTIVE) return to_route('home');

        $maintenance = SiteData::where('data_key', 'maintenance.data')->first();
        $pageTitle   = $maintenance->data_info->heading;

        return view($this->activeTheme . 'page.maintenance', compact('pageTitle', 'maintenance'));
    }

    function policyPages($slug, $id) {
        $policy    = SiteData::where('id', $id)->where('data_key', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_info->title;

        return view($this->activeTheme . 'page.policy', compact('policy', 'pageTitle'));
    }

    function reportFundraiser() {
        $pageTitle = 'Report a Fundraiser';
        $reportContent = SiteData::where('data_key', 'report_fundraiser.content')->first();
        
        // Check if report page is enabled
        if (!$reportContent || $reportContent->data_info->status != ManageStatus::ACTIVE) {
            abort(404, 'Report Fundraiser page is not available');
        }

        return view($this->activeTheme . 'page.report-fundraiser', compact('pageTitle', 'reportContent'));
    }

    function subscriberStore() {
        $validate = Validator::make(request()->all(),[
            'email' => 'required|email|unique:subscribers',
        ]);

        if($validate->fails()){
            return response()->json(['error' => $validate->errors()]);
        }

        $subscriber = new Subscriber();
        $subscriber->email = request('email');
        $subscriber->save();

        return response()->json(['success' => 'Subscription successful']);
    }

    function placeholderImage($size = null) {
        $imgWidth  = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font/RobotoMono-Regular.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);

        if ($fontSize <= 9) $fontSize = 9;

        if ($imgHeight < 100 && $fontSize > 30) $fontSize = 30;

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);

        imagefill($image, 0, 0, $bgFill);

        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;

        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    function updateUserCountry() {
        $country = request('country');
        
        if ($country) {
            session()->put('user_country', $country);
            
            // If user is logged in, update their profile too
            if (auth()->check()) {
                $user = auth()->user();
                $user->country_name = $country;
                $user->save();
            }
            
            return response()->json(['success' => true, 'message' => 'Country updated successfully']);
        } else {
            // Clear country selection (All Countries)
            session()->forget('user_country');
            
            // If user is logged in, clear their country too
            if (auth()->check()) {
                $user = auth()->user();
                $user->country_name = null;
                $user->save();
            }
            
            return response()->json(['success' => true, 'message' => 'All Countries selected']);
        }
    }

    public function help()
    {
        // Redirect to external support URL
        return redirect('https://apnacrowdfunding.com/support/');
    }

    public function sitemap()
    {
        $pageTitle = 'Sitemap';
        return view($this->activeTheme . 'page.sitemap', compact('pageTitle'));
    }

    private function getFallbackHelpData()
    {
        return [
            "status" => "success",
            "data" => [
                [
                    "id" => 2,
                    "name" => "Getting Started",
                    "slug" => "apnafund-basics",
                    "description" => "",
                    "count" => 5,
                    "posts" => [
                        [
                            "id" => 7,
                            "title" => "How to sign up",
                            "slug" => "how-to-sign-up",
                            "excerpt" => "Signing up is the first step to join our crowdfunding platform. Create your account and start your journey.",
                            "date" => "2025-08-29 10:34:10",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ],
                        [
                            "id" => 11,
                            "title" => "How to start a campaign",
                            "slug" => "how-to-start-a-campaign",
                            "excerpt" => "Anyone can start a campaign after creating an account. Learn the basics of launching your first project.",
                            "date" => "2025-08-29 10:38:08",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ],
                        [
                            "id" => 13,
                            "title" => "How to edit your profile",
                            "slug" => "how-to-edit-your-profile",
                            "excerpt" => "Update your profile information, add a photo, and customize your public presence on the platform.",
                            "date" => "2025-08-29 10:39:58",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ]
                    ]
                ],
                [
                    "id" => 5,
                    "name" => "Backer Questions",
                    "slug" => "backer-questions",
                    "description" => "",
                    "count" => 3,
                    "posts" => [
                        [
                            "id" => 15,
                            "title" => "How do I back a project?",
                            "slug" => "how-do-i-back-a-project",
                            "excerpt" => "Find projects you want to support and learn how to make pledges safely and securely.",
                            "date" => "2025-08-29 10:44:30",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ],
                        [
                            "id" => 17,
                            "title" => "How do refunds work?",
                            "slug" => "how-do-refunds-work",
                            "excerpt" => "Understand our refund policy and how to get your money back if needed.",
                            "date" => "2025-08-29 10:51:50",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ],
                        [
                            "id" => 19,
                            "title" => "Can I change my pledge?",
                            "slug" => "can-i-change-my-pledge",
                            "excerpt" => "Learn how to modify or cancel your existing pledges before the campaign ends.",
                            "date" => "2025-08-29 10:54:19",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ]
                    ]
                ],
                [
                    "id" => 4,
                    "name" => "Creator Questions",
                    "slug" => "creators-questions",
                    "description" => "",
                    "count" => 2,
                    "posts" => [
                        [
                            "id" => 21,
                            "title" => "How do I launch my campaign?",
                            "slug" => "how-do-i-launch-my-campaign",
                            "excerpt" => "Step-by-step guide to preparing and launching your crowdfunding campaign successfully.",
                            "date" => "2025-08-29 10:56:21",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ],
                        [
                            "id" => 25,
                            "title" => "How to communicate with backers?",
                            "slug" => "how-to-communicate-with-backers",
                            "excerpt" => "Best practices for keeping your supporters engaged and informed throughout your campaign.",
                            "date" => "2025-08-29 10:58:38",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ]
                    ]
                ],
                [
                    "id" => 3,
                    "name" => "Payments & Billing",
                    "slug" => "payments-billing",
                    "description" => "",
                    "count" => 3,
                    "posts" => [
                        [
                            "id" => 27,
                            "title" => "How payments are processed",
                            "slug" => "how-payments-are-processed",
                            "excerpt" => "Learn about our secure payment processing and when charges occur.",
                            "date" => "2025-08-29 10:59:44",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ],
                        [
                            "id" => 29,
                            "title" => "Failed payment solutions",
                            "slug" => "failed-payment-solutions",
                            "excerpt" => "What to do when your payment fails and how to resolve common issues.",
                            "date" => "2025-08-29 11:01:07",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ],
                        [
                            "id" => 23,
                            "title" => "What are the fees?",
                            "slug" => "what-are-the-fees",
                            "excerpt" => "Understand our transparent fee structure for both creators and backers.",
                            "date" => "2025-08-29 10:57:36",
                            "author" => "admin",
                            "featured_image" => false,
                            "permalink" => "#"
                        ]
                    ]
                ]
            ]
        ];
    }
}
