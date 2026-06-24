<?php

namespace App\Http\Controllers\Api;

use App\Constants\ManageStatus;
use App\Models\CampaignFaq;
use App\Models\CampaignUpdate;
use App\Models\Comment;
use App\Services\CampaignStoryHtmlService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Mobile/legacy API: campaigns as “funds” (list, detail, search, category, create/update flows).
 *
 * Routes (see `routes/web.php`, prefix `/api`): e.g. `fundlist.php`, `fundraise.php`, `fundidwise.php`,
 * `search_fund.php`, `catwisefund.php`. Most mutating routes require **Bearer token** (`auth:sanctum`).
 *
 * Response shape uses legacy keys: `ResponseCode`, `Result`, `ResponseMsg`, plus payload keys like `fundlist`.
 */
class FundController extends BaseApiController
{
    /**
     * **POST/GET** `fundlist.php` — Authenticated user's campaigns filtered by legacy `status` (Pending|Cancelled|Completed).
     *
     * @param  \Illuminate\Http\Request  $request  Body/query: `status` (optional)
     * @return \Illuminate\Http\JsonResponse 401 if no token; 200 with `fundlist` array
     */
    public function fundList(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        // Get user ID from authenticated user (token required)
        $uid = $this->getUserId($request);
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthenticated! Please provide a valid token."
            ], 401);
        }

        $status = $data['status'] ?? 'Pending';

        // Map old status values to campaigns table status
        // campaigns table: status = 2 (pending), status = 1 (approved), status = 0 (rejected/cancelled)
        // For 'Completed', we check if end_date has passed or raised_amount >= goal_amount
        if ($status == 'Pending') {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND status = 2");
        } else if ($status == 'Cancelled') {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND status = 0");
        } else {
            // Completed: either status is approved and (end_date passed or raised_amount >= goal_amount)
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND status = 1 AND (end_date < CURDATE() OR raised_amount >= goal_amount)");
        }

        // Check if query was successful
        if (!$sel) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "fund list Successfully!!!",
                "fundlist" => []
            ]);
        }

        $c = [];
        while ($row = $sel->fetch_assoc()) {
            if (!$row) break;

            // Get charity info if exists (check if tbl_charity table exists)
            $charity_name = "";
            $charity_tinno = "";
            $charity_img = "";
            
            // Use helper function to format campaign data (exclude_story for list - detail in fundById)
            $pol = $this->formatCampaignData($row, [
                'charity_name' => $charity_name,
                'charity_tinno' => $charity_tinno,
                'charity_img' => $charity_img,
                'patient_photo' => [],
                'fund_for' => '',
                'exclude_story' => true,
            ]);
            
            // Override specific fields for fundList
            $pol['lats'] = '';
            $pol['longs'] = '';
            $pol['patient_title'] = '';
            $pol['patient_diagnosis'] = '';
            $pol['fund_plan'] = '';
            $pol['medical_certificate'] = [];
            $pol['reject_comment'] = '';
            
            $c[] = $pol;
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "fund list Successfully!!!",
            "fundlist" => $c
        ]);
    }


    /**
     * Get Fund by ID or Slug
     * Accepts fund_id (int) or slug (string) to fetch campaign detail
     */
    public function fundById(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        // Get user ID from authenticated user (token only)
        $uid = null;
        if (auth()->check()) {
            $uid = auth()->user()->id;
        }

        $fund_id = $data['fund_id'] ?? ($data['campaign_id'] ?? null);
        $slug = isset($data['slug']) ? trim((string) $data['slug']) : null;

        if (empty($fund_id) && empty($slug)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong! Provide fund_id or slug."
            ], 401);
        }

        $status = $data['status'] ?? 'Home';

        // If status is not 'Home', require authentication
        if ($status != 'Home' && empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }

        // Build WHERE clause: by slug or by id
        if (!empty($slug)) {
            $slugEscaped = $this->h->real_string($slug);
            $slugIsNumeric = ctype_digit($slugEscaped);
            if ($status == 'Home') {
                if ($slugIsNumeric) {
                    $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE slug='" . $slugEscaped . "' OR id=" . (int) $slugEscaped);
                } else {
                    $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE slug='" . $slugEscaped . "'");
                }
            } else {
                if ($slugIsNumeric) {
                    $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND (slug='" . $slugEscaped . "' OR id=" . (int) $slugEscaped . ")");
                } else {
                    $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND slug='" . $slugEscaped . "'");
                }
            }
        } else {
            $fund_id = (int) round((float) $fund_id);
            if ($status == 'Home') {
                $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE id=" . $fund_id . "");
            } else {
                $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND id=" . $fund_id . "");
            }
        }

        // Check if query was successful
        if (!$sel) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "fund Data Get Successfully!!!",
                "fund" => null,
            ]);
        }

        $c = [];
        while ($row = $sel->fetch_assoc()) {
            if (!$row) break;

            // Get charity info if exists (check if tbl_charity table exists)
            $charity_name = "";
            $charity_tinno = "";
            $charity_img = "";

            // Get total donaters count
            $funded = $this->h->queryfire("SELECT COUNT(DISTINCT user_id) as total_donaters FROM deposits WHERE campaign_id=" . (int)$row['id'] . " AND status = 1");
            $donatersResult = $funded ? $funded->fetch_assoc() : null;
            $total_donaters = $donatersResult ? ($donatersResult['total_donaters'] ?? 0) : 0;

            // Use helper function to format campaign data (gallery handling is done in helper)
            $pol = $this->formatCampaignData($row, [
                'charity_name' => $charity_name,
                'charity_tinno' => $charity_tinno,
                'charity_img' => $charity_img,
                'patient_photo' => ['images/default.png'],
                'fund_for' => '', // Not in campaigns table
                'status' => $row['status'] ?? 0,
                'total_donaters' => $total_donaters,
                'donaterlist' => $this->getDonaterList($row['id'], true)
            ]);

            // Override specific fields for fundById
            $pol['lats'] = '';
            $pol['longs'] = '';
            $pol['patient_title'] = '';
            $pol['patient_diagnosis'] = '';
            $pol['fund_plan'] = '';
            $pol['medical_certificate'] = [];
            $pol['reject_comment'] = '';
            $pol['remain_amt'] = sprintf("%.2f", $pol['remain_amt']);
            unset($pol['fund_photos']);

            $pol['author'] = $this->getAuthorData($row['user_id'] ?? null);

            // Add slug to campaign detail response
            $pol['slug'] = $row['slug'] ?? '';

            $c[] = $pol;
        }

        if ($c === []) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "fund Data Get Successfully!!!",
                "fund" => null,
            ]);
        }

        // Single campaign object: id/slug query returns one row; extras nested here (not separate root keys).
        $fund = $c[0];
        $campaignId = (int) ($fund['id'] ?? $fund_id ?? 0);
        $fund['updates'] = $campaignId > 0 ? $this->getCampaignUpdatesList($campaignId) : [];
        $fund['faqs'] = $campaignId > 0 ? $this->getCampaignFaqList($campaignId) : [];
        $fund['comments'] = $campaignId > 0 ? $this->getCampaignCommentsList($campaignId, 20) : [];
        $fund['comments_total'] = $campaignId > 0
            ? (int) Comment::where('campaign_id', $campaignId)
                ->where('status', ManageStatus::CAMPAIGN_COMMENT_APPROVED)
                ->whereNull('update_id')
                ->count()
            : 0;

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "fund Data Get Successfully!!!",
            "fund" => $fund,
        ]);
    }

    private function getCampaignFaqList(int $campaignId): array
    {
        if ($campaignId < 1) {
            return [];
        }

        return CampaignFaq::where('campaign_id', $campaignId)
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->map(function (CampaignFaq $faq) {
                return [
                    'id' => (int) $faq->id,
                    'campaign_id' => (int) $faq->campaign_id,
                    'question' => (string) ($faq->question ?? ''),
                    'answer' => (string) ($faq->answer ?? ''),
                    'order' => (int) ($faq->order ?? 0),
                ];
            })->values()->all();
    }

    private function getCampaignCommentsList(int $campaignId, int $limit = 20): array
    {
        if ($campaignId < 1) {
            return [];
        }

        $rows = Comment::with('user')
            ->where('campaign_id', $campaignId)
            ->where('status', ManageStatus::CAMPAIGN_COMMENT_APPROVED)
            ->whereNull('update_id')
            ->latest()
            ->limit(max(1, $limit))
            ->get();

        return $rows->map(fn (Comment $comment) => $this->formatCommentForApi($comment))->values()->all();
    }

    /**
     * Single comment shape for API (campaign wall or update thread).
     */
    private function formatCommentForApi(Comment $comment): array
    {
        $displayName = $comment->user ? ($comment->user->fullname ?? $comment->user->username) : $comment->name;

        return [
            'id' => (int) $comment->id,
            'campaign_id' => (int) $comment->campaign_id,
            'update_id' => $comment->update_id !== null ? (int) $comment->update_id : null,
            'name' => (string) ($displayName ?? ''),
            'email' => (string) ($comment->email ?? ''),
            'title' => (string) ($comment->title ?? ''),
            'comment' => (string) ($comment->comment ?? ''),
            'rating' => $comment->rating,
            'created_at' => optional($comment->created_at)->toDateTimeString(),
            'is_guest' => empty($comment->user_id),
            'user' => $comment->user ? [
                'id' => (int) $comment->user->id,
                'name' => (string) ($comment->user->fullname ?? $comment->user->username ?? ''),
                'username' => (string) ($comment->user->username ?? ''),
            ] : null,
        ];
    }

    private function getCampaignUpdatesList(int $campaignId): array
    {
        if ($campaignId < 1) {
            return [];
        }

        $updates = [];

        $tableUpdates = CampaignUpdate::where('campaign_id', $campaignId)
            ->where('is_published', true)
            ->latest()
            ->get();

        foreach ($tableUpdates as $u) {
            $updates[] = [
                'id' => (int) $u->id,
                'title' => (string) ($u->title ?? ''),
                'update_desc' => (string) ($u->content ?? ''),
                'slug' => (string) ($u->slug ?? ''),
                'photo' => !empty($u->image) ? [$u->image] : [],
                'main_img' => (string) ($u->image ?? ''),
                'update_date' => optional($u->created_at)->toDateTimeString(),
                'source' => 'campaign_updates',
            ];
        }

        $camp = DB::table('campaigns')->where('id', $campaignId)->first();
        if ($camp && !empty($camp->updates)) {
            $legacy = is_string($camp->updates) ? json_decode($camp->updates, true) : (array) $camp->updates;
            if (is_array($legacy)) {
                foreach (array_reverse($legacy) as $u) {
                    $photoArr = $u['photo'] ?? (isset($u['main_img']) ? [$u['main_img']] : []);
                    $updates[] = [
                        'id' => (int) ($u['id'] ?? 0),
                        'title' => (string) ($u['title'] ?? ''),
                        'update_desc' => (string) ($u['update_desc'] ?? $u['content'] ?? ''),
                        'slug' => (string) ($u['slug'] ?? ''),
                        'photo' => is_array($photoArr) ? $photoArr : [$photoArr],
                        'main_img' => (string) ($u['main_img'] ?? (is_array($photoArr) ? ($photoArr[0] ?? '') : $photoArr)),
                        'update_date' => (string) ($u['update_date'] ?? ''),
                        'source' => 'campaigns_json',
                    ];
                }
            }
        }

        $dbUpdateIds = $tableUpdates->pluck('id')->map(fn ($id) => (int) $id)->all();
        $commentsByUpdate = [];
        if ($dbUpdateIds !== []) {
            $allUpdateComments = Comment::with('user')
                ->where('campaign_id', $campaignId)
                ->whereIn('update_id', $dbUpdateIds)
                ->where('status', ManageStatus::CAMPAIGN_COMMENT_APPROVED)
                ->orderByDesc('id')
                ->get();

            foreach ($allUpdateComments as $comment) {
                $oid = (int) $comment->update_id;
                $commentsByUpdate[$oid][] = $this->formatCommentForApi($comment);
            }
        }

        foreach ($updates as $i => $upd) {
            $src = $upd['source'] ?? '';
            $sid = (int) ($upd['id'] ?? 0);
            if ($src === 'campaign_updates' && $sid > 0) {
                $list = $commentsByUpdate[$sid] ?? [];
                $updates[$i]['comments'] = $list;
                $updates[$i]['comments_count'] = count($list);
            } else {
                $updates[$i]['comments'] = [];
                $updates[$i]['comments_count'] = 0;
            }
        }

        return $updates;
    }

    /**
     * Create Fund Raise (Campaign) – accepts web params: name, category_id, description, goal_amount, etc.
     * Also supports old params: cat_id, title, fund_for, fund_amt, fund_story.
     */
    public function fundRaise(Request $request): JsonResponse
    {
        // Web params: category_id, name, description, goal_amount | Old: cat_id, title, fund_for, fund_amt, fund_story
        $cat_id = $request->input('category_id') ?: $request->input('cat_id');
        $name = $request->input('name') ?: $request->input('title');
        $goal_amount = $request->input('goal_amount') ?: $request->input('fund_amt');

        if (empty($cat_id) || empty($name) || empty($goal_amount)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "category_id (or cat_id), name (or title), goal_amount (or fund_amt) required."
            ], 401);
        }

        $cat_id = (int) round((float) (strip_tags($this->h->real_string($cat_id))));
        $name = strip_tags($this->h->real_string($name));
        $description = CampaignStoryHtmlService::replaceDataUrlImagesWithStoredFiles(
            trim((string) ($request->input('description') ?: $request->input('fund_story', '')))
        );
        $short_description = normalizeCampaignShortDescription($request->input('short_description', ''));
        $shortDescMin = getCampaignShortDescriptionMinLength();
        $shortDescMax = getCampaignShortDescriptionMaxLength();
        $shortDescLen = campaignShortDescriptionLength($short_description);

        if ($shortDescLen < $shortDescMin) {
            $rawShort = $request->input('short_description', '');
            $responseMsg = isCampaignShortDescriptionWhitespaceOnly($rawShort)
                ? 'Short description cannot contain only spaces.'
                : ($shortDescLen === 0
                    ? 'Please enter a short description for your project.'
                    : "Short description must be at least {$shortDescMin} characters long.");

            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => $responseMsg,
            ], 401);
        }

        if ($shortDescLen > $shortDescMax) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Short description cannot exceed {$shortDescMax} characters.",
            ], 401);
        }

        $short_description = CampaignStoryHtmlService::replaceDataUrlImagesWithStoredFiles($short_description);
        $location = strip_tags($this->h->real_string($request->input('location') ?: $request->input('full_address', '')));
        $goal_amount = trim(strip_tags($this->h->real_string($goal_amount)), " \t\n\r\0\x0B\"'");
        $originalGoalAmount = (float) $goal_amount;
        if ($originalGoalAmount <= 0) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "goal_amount must be greater than zero."
            ], 401);
        }

        $countryId = $request->input('country_id');
        $countryName = null;
        $originalCurrency = getPlatformCurrency();
        $platformGoalAmount = $originalGoalAmount;
        if ($countryId !== null && $countryId !== '') {
            $countryId = (int) trim((string) $countryId, " \t\n\r\0\x0B\"'");
            $countryName = resolveAllowedCountryNameFromCountryId($countryId);
            if (!$countryName) {
                return response()->json([
                    "ResponseCode" => "400",
                    "Result" => "false",
                    "ResponseMsg" => "Invalid country_id or country is not allowed for project location."
                ], 400);
            }

            $originalCurrency = getCurrencyCodeForCountryName($countryName);
            $platformGoalAmount = app(\App\Services\CurrencyService::class)
                ->convertToPlatform($originalGoalAmount, $originalCurrency);
        }

        $platformGoalAmount = round($platformGoalAmount, 2);
        $exchangeRateUsed = $originalGoalAmount > 0 ? round($platformGoalAmount / $originalGoalAmount, 8) : 1;
        if ($countryName && $location === '') {
            $location = $countryName;
        }

        // Parse dates: multipart form - try allInput, input, $_POST; strip quotes, validate Y-m-d
        $allInput = $request->all();
        $startDateRaw = $allInput['start_date'] ?? $request->input('start_date') ?? ($_POST['start_date'] ?? null);
        $endDateRaw = $allInput['end_date'] ?? $request->input('end_date') ?? ($_POST['end_date'] ?? null);
        $start_date = $this->parseDateInput($startDateRaw, date('Y-m-d'));
        $end_date = $this->parseDateInput($endDateRaw, date('Y-m-d', strtotime('+30 days')));

        // Campaign days limit (admin general settings)
        $daysLimit = getCampaignDaysLimit();
        $startDt = \DateTime::createFromFormat('Y-m-d', $start_date);
        $endDt = \DateTime::createFromFormat('Y-m-d', $end_date);
        if ($startDt && $endDt) {
            $interval = $startDt->diff($endDt);
            $daysDiff = (int) $interval->days;
            if ($daysDiff > $daysLimit) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Campaign duration cannot exceed {$daysLimit} days. Please adjust start and end dates.",
                ], 400);
            }
        }
        $lats = strip_tags($this->h->real_string($request->input('lats', '')));
        $longs = strip_tags($this->h->real_string($request->input('longs', '')));
        $status = $request->input('status', 'Pending');
        
        // Get user ID from authenticated user (token only)
        $uid = null;
        if (auth()->check()) {
            $uid = auth()->user()->id;
        }
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first. Token required."
            ], 401);
        }

        // Main image: web uses 'image', old API uses 'main_img'
        // Must use getFilePath('campaign') so image shows in admin & frontend (assets/universal/images/campaign/)
        $mainImage = null;
        $gallery = [];
        $imageFile = $request->file('image') ?: $request->file('main_img');
        if ($imageFile) {
            $mainImage = fileUploader($imageFile, getFilePath('campaign'), getFileSize('campaign'), null, getThumbSize('campaign'));
            if ($mainImage) {
                $gallery = [$mainImage];
            }
        }

        if (empty($mainImage)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Image required. Upload 'image' or 'main_img'."
            ], 401);
        }
        
        // Map status: 'Pending' -> 2, 'Approved' -> 1, 'Cancelled' -> 0
        $campaignStatus = 2; // default to pending
        if ($status == 'Approved') {
            $campaignStatus = 1;
        } elseif ($status == 'Cancelled' || $status == 'Rejected') {
            $campaignStatus = 0;
        }

        // Generate slug from name
        $slug = \Illuminate\Support\Str::slug($name);
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (\DB::table('campaigns')->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $campaignData = [
            'user_id' => $uid,
            'category_id' => (int) round((float) $cat_id),
            'name' => $name,
            'slug' => $slug,
            'description' => $description ?: 'Campaign description',
            'short_description' => $short_description,
            'image' => $mainImage,
            'gallery' => !empty($gallery) ? json_encode($gallery) : null,
            'goal_amount' => $platformGoalAmount,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $campaignStatus,
            'location' => $location,
            'raised_amount' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (Schema::hasColumn('campaigns', 'goal_amount_usd')) {
            $campaignData['goal_amount_usd'] = $platformGoalAmount;
        }
        if (Schema::hasColumn('campaigns', 'original_goal_amount')) {
            $campaignData['original_goal_amount'] = $originalGoalAmount;
        }
        if (Schema::hasColumn('campaigns', 'original_currency')) {
            $campaignData['original_currency'] = $originalCurrency;
        }
        if (Schema::hasColumn('campaigns', 'exchange_rate_used')) {
            $campaignData['exchange_rate_used'] = $exchangeRateUsed;
        }

        try {
            // Use Laravel DB facade directly to get better error handling
            $check = \DB::table('campaigns')->insertGetId($campaignData);

            if ($check) {
                return response()->json([
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "ResponseMsg" => "Fund Raise Submited Wait For Approval!!",
                    "fund_id" => $check
                ]);
            }

            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Fund creation failed! Please try again."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Fund creation failed! Error: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Parse date input: strip quotes (e.g. from curl --form 'start_date="2026-03-01"'), trim, validate Y-m-d.
     *
     * @param mixed $value Raw input value
     * @param string $default Default date in Y-m-d if invalid
     * @return string Valid Y-m-d date string
     */
    private function parseDateInput($value, string $default): string
    {
        if (empty($value) && $value !== '0') {
            return $default;
        }
        if (is_array($value)) {
            $value = $value[0] ?? null;
            if (empty($value)) return $default;
        }
        $cleaned = trim((string) $value, " \t\n\r\"'");
        $parsed = \DateTime::createFromFormat('Y-m-d', $cleaned);
        if ($parsed && $parsed->format('Y-m-d') === $cleaned) {
            return $cleaned;
        }
        $ts = strtotime($cleaned);
        if ($ts !== false) {
            $d = date('Y-m-d', $ts);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return $d;
        }
        return $default;
    }

    /**
     * Upload a single image (e.g. main_img) and return stored path relative to public.
     */
    private function uploadSingleImage($file, $url)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }
        $basePath = base_path('public');
        $targetPath = $basePath . $url;
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $newName = uniqid() . date('YmdHis') . mt_rand() . '.' . $ext;
        $file->move($targetPath, $newName);
        return ltrim($url, '/') . $newName;
    }

    /**
     * Process file uploads
     */
    private function processFileUploads($prefix, $count, $url)
    {
        $basePath = base_path('public');
        $targetPath = $basePath . $url;
        
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        $uploadedFiles = [];

        for ($i = 0; $i < $count; $i++) {
            $fileKey = $prefix . $i;
            if ($_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $newName = uniqid() . date('YmdHis') . mt_rand() . '.jpg';
                $fileUrl = ltrim($url, '/') . $newName;
                $uploadedFiles[] = $fileUrl;

                move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath . $newName);
            }
        }

        return $uploadedFiles;
    }

    /**
     * Category Wise Fund
     */
    public function categoryWiseFund(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);
        if (empty($data['cat_id'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $cat_id = (int) round((float) $data['cat_id']);
        $timestamp = date("Y-m-d");

        // Use campaigns table instead of tbl_fund
        // campaigns table: status = 1 (approved), status = 2 (pending), status = 0 (rejected)
        // For public API discovery: show only approved, non-expired campaigns (Kickstarter style)
        if ($cat_id != 0) {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE category_id=" . $cat_id . " AND status = 1 AND (end_date IS NULL OR end_date >= '" . $timestamp . "') ORDER BY id DESC");
        } else {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE status = 1 AND (end_date IS NULL OR end_date >= '" . $timestamp . "') ORDER BY id DESC");
        }

        // Check if query was successful
        if (!$sel) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Home Data Get Successfully!!!",
                "catwisefund" => []
            ]);
        }

        $cp = [];
        while ($rows = $sel->fetch_assoc()) {
            if (!$rows) break;
            
            // Use helper function to format campaign data (exclude_story for list - detail in fundById)
            $fundData = $this->formatCampaignData($rows, ['exclude_story' => true]);
            $cp[] = $fundData;
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Home Data Get Successfully!!!",
            "catwisefund" => $cp
        ]);
    }

    /**
     * Search Fund
     */
    public function searchFund(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['keyword'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $keyword = $this->h->real_string($data['keyword']);
        $timestamp = date("Y-m-d");

        // Search in campaigns table
        // campaigns table: status = 1 (approved), status = 2 (pending), status = 0 (rejected)
        // Search in name (title), description (fund_story), and location fields
        $keywordEscaped = $this->h->real_string($keyword); // Already escaped by real_string
        $selpop = $this->h->queryfire("SELECT * FROM campaigns 
            WHERE status = 1 
            AND (end_date IS NULL OR end_date >= '" . $timestamp . "')
            AND (
                name LIKE '%" . $keywordEscaped . "%' 
                OR description LIKE '%" . $keywordEscaped . "%'
                OR location LIKE '%" . $keywordEscaped . "%'
            )
            ORDER BY id DESC");

        // Check if query was successful
        if (!$selpop) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Fund Data Get Successfully!!!",
                "fundlist" => []
            ]);
        }

        $listnearby = [];
        while ($pop = $selpop->fetch_assoc()) {
            if (!$pop) break;
            
            // Use helper function to format campaign data (exclude_story for list - detail in fundById)
            $fundData = $this->formatCampaignData($pop, [
                'patient_photo' => $pop['image'] ?? '',
                'exclude_story' => true,
            ]);
            $listnearby[] = $fundData;
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Fund Data Get Successfully!!!",
            "fundlist" => $listnearby
        ]);
    }
}

