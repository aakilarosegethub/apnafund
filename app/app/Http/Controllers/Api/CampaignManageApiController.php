<?php

namespace App\Http\Controllers\Api;

use App\Constants\ManageStatus;
use App\Models\AdminNotification;
use App\Models\Campaign;
use App\Models\CampaignFaq;
use App\Models\CampaignUpdate;
use App\Models\Comment;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

/**
 * Mobile/API CRUD for campaign story (description), rewards, FAQs, and backer updates.
 * Mirrors web: User\CampaignController (story, FAQ, updates) and User\RewardController.
 *
 * Most endpoints expect Bearer token (auth:sanctum) and query/body param {@see op}.
 * campaign_faq.php: op=list|get is public (approved campaigns); GET without op defaults to list; POST without op infers list/get from slug/campaign_id + faq_id. Mutations require Bearer.
 * Identify campaign with {@see campaign_id} or {@see fund_id} or {@see slug}.
 */
class CampaignManageApiController extends BaseApiController
{
    protected function jsonLegacy(int $http, string $code, bool $ok, string $msg, array $extra = []): JsonResponse
    {
        $payload = array_merge([
            'ResponseCode' => (string) $code,
            'Result' => $ok ? 'true' : 'false',
            'ResponseMsg' => $msg,
        ], $extra);

        return response()->json($payload, $http);
    }

    /**
     * Resolve campaign by id or slug; ensure current user may edit.
     *
     * @return array{campaign: Campaign}|array{response: JsonResponse}
     */
    protected function resolveEditableCampaign(Request $request, bool $checkExpired = true): array
    {
        $uid = $this->getUserId($request);
        if (empty($uid)) {
            return ['response' => $this->jsonLegacy(401, '401', false, 'Unauthenticated! Please provide a valid token.')];
        }

        $data = $this->getRequestData($request);
        $rawId = $data['campaign_id'] ?? $data['fund_id'] ?? $request->input('campaign_id') ?? $request->input('fund_id');
        $slug = $data['slug'] ?? $request->input('slug');

        $campaign = null;
        if ($rawId !== null && $rawId !== '') {
            $campaign = Campaign::where('id', (int) $rawId)->first();
        } elseif (! empty($slug)) {
            $campaign = Campaign::where('slug', $slug)->first();
        }

        if (! $campaign) {
            return ['response' => $this->jsonLegacy(404, '404', false, 'Campaign not found. Provide campaign_id, fund_id, or slug.')];
        }

        if (! $campaign->canBeEditedBy($uid)) {
            return ['response' => $this->jsonLegacy(403, '403', false, 'You do not have permission to manage this campaign.')];
        }

        if ($checkExpired && $campaign->isExpired()) {
            return ['response' => $this->jsonLegacy(400, '400', false, 'This campaign has expired and cannot be edited.')];
        }

        return ['campaign' => $campaign];
    }

    /**
     * Resolve campaign for public FAQ read (list/get): approved campaigns, or draft/pending when caller may edit.
     *
     * @return array{campaign: Campaign}|array{response: JsonResponse}
     */
    protected function resolveCampaignForPublicFaqRead(Request $request): array
    {
        $data = $this->getRequestData($request);
        $rawId = $data['campaign_id'] ?? $data['fund_id'] ?? $request->input('campaign_id') ?? $request->input('fund_id');
        $slug = $data['slug'] ?? $request->input('slug') ?? $request->query('slug');

        $campaign = null;
        if ($rawId !== null && $rawId !== '') {
            $campaign = Campaign::where('id', (int) $rawId)->first();
        } elseif (! empty($slug)) {
            $campaign = Campaign::where('slug', $slug)->first();
        }

        if (! $campaign) {
            return ['response' => $this->jsonLegacy(404, '404', false, 'Campaign not found. Provide campaign_id, fund_id, or slug.')];
        }

        $uid = $this->getUserId($request);
        if ((int) $campaign->status === ManageStatus::CAMPAIGN_APPROVED) {
            return ['campaign' => $campaign];
        }
        if (! empty($uid) && $campaign->canBeEditedBy($uid)) {
            return ['campaign' => $campaign];
        }

        return ['response' => $this->jsonLegacy(404, '404', false, 'Campaign not found.')];
    }

    protected function op(Request $request): string
    {
        $data = $this->getRequestData($request);

        return strtolower(trim((string) ($data['op'] ?? $request->input('op', ''))));
    }

    /**
     * Story = campaigns.description (web "Story" tab).
     * op=read | save
     */
    public function story(Request $request): JsonResponse
    {
        $op = $this->op($request) ?: 'read';

        if ($op === 'read') {
            $resolved = $this->resolveEditableCampaign($request, false);
            if (isset($resolved['response'])) {
                return $resolved['response'];
            }
            $campaign = $resolved['campaign'];

            return $this->jsonLegacy(200, '200', true, 'Story loaded.', [
                'campaign_id' => $campaign->id,
                'slug' => $campaign->slug,
                'description' => $campaign->description ?? '',
                'fund_story' => $campaign->description ?? '',
            ]);
        }

        if ($op === 'save' || $op === 'update') {
            $resolved = $this->resolveEditableCampaign($request, true);
            if (isset($resolved['response'])) {
                return $resolved['response'];
            }
            $campaign = $resolved['campaign'];

            $data = array_merge($this->getRequestData($request), $request->all());
            $v = Validator::make($data, [
                'description' => 'required|string|min:30',
            ], [
                'description.required' => 'The story description is required.',
                'description.min' => 'The story description must be at least 30 characters.',
            ]);

            if ($v->fails()) {
                return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
            }

            $campaign->description = $data['description'];
            $campaign->save();

            return $this->jsonLegacy(200, '200', true, 'Story saved successfully.', [
                'campaign_id' => $campaign->id,
                'slug' => $campaign->slug,
            ]);
        }

        return $this->jsonLegacy(400, '400', false, 'Invalid op. Use read or save.');
    }

    /**
     * Rewards CRUD (web User\RewardController).
     * op=list|get|create|update|delete|toggle_active
     */
    public function rewards(Request $request): JsonResponse
    {
        $op = $this->op($request);
        if ($op === '') {
            return $this->jsonLegacy(400, '400', false, 'Parameter op is required: list, get, create, update, delete, toggle_active.');
        }

        $resolved = $this->resolveEditableCampaign($request, $op !== 'list' && $op !== 'get');
        if (isset($resolved['response'])) {
            return $resolved['response'];
        }
        $campaign = $resolved['campaign'];

        if ($op === 'list') {
            $rows = $campaign->rewards()->orderBy('minimum_amount')->get()->map(function (Reward $r) {
                return $this->formatReward($r);
            });

            return $this->jsonLegacy(200, '200', true, 'Rewards list.', ['rewards' => $rows->values()->all()]);
        }

        if ($op === 'get') {
            $rewardId = (int) ($request->input('reward_id') ?? $this->getRequestData($request)['reward_id'] ?? 0);
            if ($rewardId < 1) {
                return $this->jsonLegacy(400, '400', false, 'reward_id is required.');
            }
            $reward = $campaign->rewards()->where('id', $rewardId)->first();
            if (! $reward) {
                return $this->jsonLegacy(404, '404', false, 'Reward not found.');
            }

            return $this->jsonLegacy(200, '200', true, 'Reward loaded.', ['reward' => $this->formatReward($reward)]);
        }

        if ($op === 'create') {
            $v = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:3',
                'minimum_amount' => 'required|numeric|min:1',
                'quantity' => 'nullable|integer|min:1',
                'type' => 'nullable|in:digital,physical',
                'color_theme' => 'nullable|string|max:64',
                'terms_conditions' => 'nullable|string',
                'image' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'gif', 'webp'])->max(5120)],
            ]);

            if ($v->fails()) {
                return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
            }

            $reward = new Reward;
            $reward->campaign_id = $campaign->id;
            $reward->title = $request->title;
            $reward->description = $request->description;
            $reward->minimum_amount = $request->minimum_amount;
            $reward->quantity = $request->quantity;
            $reward->type = $request->input('type', 'physical');
            $reward->color_theme = $request->input('color_theme', 'primary');
            $reward->terms_conditions = $request->terms_conditions;

            if ($request->hasFile('image')) {
                try {
                    $reward->image = fileUploader($request->image, getFilePath('reward'), getFileSize('reward'), null, getThumbSize('reward'));
                } catch (\Exception $e) {
                    return $this->jsonLegacy(400, '400', false, 'Image upload failed: '.$e->getMessage());
                }
            }

            $reward->save();

            return $this->jsonLegacy(200, '200', true, 'Reward created successfully.', ['reward' => $this->formatReward($reward)]);
        }

        if ($op === 'update') {
            $rewardId = (int) ($request->input('reward_id') ?? $this->getRequestData($request)['reward_id'] ?? 0);
            if ($rewardId < 1) {
                return $this->jsonLegacy(400, '400', false, 'reward_id is required.');
            }
            $reward = $campaign->rewards()->where('id', $rewardId)->first();
            if (! $reward) {
                return $this->jsonLegacy(404, '404', false, 'Reward not found.');
            }

            $v = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:3',
                'minimum_amount' => 'required|numeric|min:1',
                'quantity' => 'nullable|integer|min:1',
                'type' => 'nullable|in:digital,physical',
                'color_theme' => 'nullable|string|max:64',
                'terms_conditions' => 'nullable|string',
                'image' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'gif', 'webp'])->max(5120)],
            ]);

            if ($v->fails()) {
                return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
            }

            $reward->title = $request->title;
            $reward->description = $request->description;
            $reward->minimum_amount = $request->minimum_amount;
            $reward->quantity = $request->quantity;
            if ($request->filled('type')) {
                $reward->type = $request->input('type');
            }
            if ($request->filled('color_theme')) {
                $reward->color_theme = $request->input('color_theme');
            }
            $reward->terms_conditions = $request->terms_conditions;

            if ($request->hasFile('image')) {
                try {
                    $reward->image = fileUploader($request->image, getFilePath('reward'), getFileSize('reward'), $reward->image, getThumbSize('reward'));
                } catch (\Exception $e) {
                    return $this->jsonLegacy(400, '400', false, 'Image upload failed: '.$e->getMessage());
                }
            }

            $reward->save();

            return $this->jsonLegacy(200, '200', true, 'Reward updated successfully.', ['reward' => $this->formatReward($reward)]);
        }

        if ($op === 'delete') {
            $rewardId = (int) ($request->input('reward_id') ?? $this->getRequestData($request)['reward_id'] ?? 0);
            if ($rewardId < 1) {
                return $this->jsonLegacy(400, '400', false, 'reward_id is required.');
            }
            $reward = $campaign->rewards()->where('id', $rewardId)->first();
            if (! $reward) {
                return $this->jsonLegacy(404, '404', false, 'Reward not found.');
            }
            $reward->delete();

            return $this->jsonLegacy(200, '200', true, 'Reward deleted successfully.');
        }

        if ($op === 'toggle_active') {
            $rewardId = (int) ($request->input('reward_id') ?? $this->getRequestData($request)['reward_id'] ?? 0);
            if ($rewardId < 1) {
                return $this->jsonLegacy(400, '400', false, 'reward_id is required.');
            }
            $reward = $campaign->rewards()->where('id', $rewardId)->first();
            if (! $reward) {
                return $this->jsonLegacy(404, '404', false, 'Reward not found.');
            }
            $reward->is_active = ! $reward->is_active;
            $reward->save();

            return $this->jsonLegacy(200, '200', true, 'Reward status updated.', ['reward' => $this->formatReward($reward)]);
        }

        return $this->jsonLegacy(400, '400', false, 'Invalid op for campaign_rewards.php.');
    }

    protected function formatReward(Reward $r): array
    {
        $imageUrl = null;
        if ($r->image) {
            $imageUrl = getImage(getFilePath('reward').'/'.$r->image, getThumbSize('reward'));
        }

        return [
            'id' => $r->id,
            'campaign_id' => $r->campaign_id,
            'title' => $r->title,
            'description' => $r->description,
            'minimum_amount' => (string) $r->minimum_amount,
            'quantity' => $r->quantity,
            'claimed_count' => $r->claimed_count,
            'type' => $r->type,
            'color_theme' => $r->color_theme,
            'terms_conditions' => $r->terms_conditions,
            'image' => $r->image,
            'image_url' => $imageUrl,
            'is_active' => (bool) $r->is_active,
        ];
    }

    /**
     * Campaign FAQ CRUD (web CampaignController FAQ routes).
     * op=list|get|create|update|delete
     */
    public function faqs(Request $request): JsonResponse
    {
        $op = $this->op($request);
        if ($op === '' && $request->isMethod('GET')) {
            $op = 'list';
        }
        // Public read without `op` (mobile POST JSON): infer list vs get from campaign + faq_id
        if ($op === '' && $request->isMethod('post')) {
            $data = $this->getRequestData($request);
            $rawId = $data['campaign_id'] ?? $data['fund_id'] ?? $request->input('campaign_id') ?? $request->input('fund_id');
            $slug = $data['slug'] ?? $request->input('slug');
            $hasCampaign = ($rawId !== null && $rawId !== '') || (! empty($slug));
            $faqId = (int) ($data['faq_id'] ?? $request->input('faq_id') ?? 0);
            if ($hasCampaign && $faqId < 1) {
                $op = 'list';
            } elseif ($hasCampaign && $faqId >= 1) {
                $op = 'get';
            }
        }
        if ($op === '') {
            return $this->jsonLegacy(400, '400', false, 'Parameter op is required: list, get, create, update, delete.');
        }

        if ($op === 'list' || $op === 'get') {
            $resolved = $this->resolveCampaignForPublicFaqRead($request);
        } else {
            $resolved = $this->resolveEditableCampaign($request, true);
        }
        if (isset($resolved['response'])) {
            return $resolved['response'];
        }
        $campaign = $resolved['campaign'];

        if ($op === 'list') {
            $faqs = CampaignFaq::where('campaign_id', $campaign->id)->orderBy('order')->orderBy('id')->get();

            return $this->jsonLegacy(200, '200', true, 'FAQ list.', ['faqs' => $faqs->toArray()]);
        }

        if ($op === 'get') {
            $faqId = (int) ($request->input('faq_id') ?? $this->getRequestData($request)['faq_id'] ?? 0);
            if ($faqId < 1) {
                return $this->jsonLegacy(400, '400', false, 'faq_id is required.');
            }
            $faq = CampaignFaq::where('id', $faqId)->where('campaign_id', $campaign->id)->first();
            if (! $faq) {
                return $this->jsonLegacy(404, '404', false, 'FAQ not found.');
            }

            return $this->jsonLegacy(200, '200', true, 'FAQ loaded.', ['faq' => $faq->toArray()]);
        }

        if ($op === 'create') {
            $data = array_merge($this->getRequestData($request), $request->all());
            $v = Validator::make($data, [
                'question' => 'required|string|max:500',
                'answer' => 'required|string|max:2000',
                'order' => 'nullable|integer|min:0',
            ]);

            if ($v->fails()) {
                return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
            }

            $faq = new CampaignFaq;
            $faq->campaign_id = $campaign->id;
            $faq->question = $data['question'];
            $faq->answer = $data['answer'];
            $faq->order = $data['order'] ?? 0;
            $faq->save();

            return $this->jsonLegacy(200, '200', true, 'FAQ added successfully.', ['faq' => $faq->toArray()]);
        }

        if ($op === 'update') {
            $faqId = (int) ($request->input('faq_id') ?? $this->getRequestData($request)['faq_id'] ?? 0);
            if ($faqId < 1) {
                return $this->jsonLegacy(400, '400', false, 'faq_id is required.');
            }
            $faq = CampaignFaq::where('id', $faqId)->where('campaign_id', $campaign->id)->first();
            if (! $faq) {
                return $this->jsonLegacy(404, '404', false, 'FAQ not found.');
            }

            $data = array_merge($this->getRequestData($request), $request->all());
            $v = Validator::make($data, [
                'question' => 'required|string|max:500',
                'answer' => 'required|string|max:2000',
                'order' => 'nullable|integer|min:0',
            ]);

            if ($v->fails()) {
                return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
            }

            $faq->question = $data['question'];
            $faq->answer = $data['answer'];
            $faq->order = $data['order'] ?? $faq->order;
            $faq->save();

            return $this->jsonLegacy(200, '200', true, 'FAQ updated successfully.', ['faq' => $faq->toArray()]);
        }

        if ($op === 'delete') {
            $faqId = (int) ($request->input('faq_id') ?? $this->getRequestData($request)['faq_id'] ?? 0);
            if ($faqId < 1) {
                return $this->jsonLegacy(400, '400', false, 'faq_id is required.');
            }
            $faq = CampaignFaq::where('id', $faqId)->where('campaign_id', $campaign->id)->first();
            if (! $faq) {
                return $this->jsonLegacy(404, '404', false, 'FAQ not found.');
            }
            $faq->delete();

            return $this->jsonLegacy(200, '200', true, 'FAQ deleted successfully.');
        }

        return $this->jsonLegacy(400, '400', false, 'Invalid op for campaign_faq.php.');
    }

    /**
     * Public GET: campaign backer updates (no token). Same visibility rules as campaign_faq list/get.
     * Query: campaign_id | fund_id | slug; op=list (default) or op=get; get requires update_id.
     * Optional Bearer: owner/collaborator may see unpublished updates.
     */
    public function postUpdatesPublicRead(Request $request): JsonResponse
    {
        $op = $this->op($request);
        if ($op === '') {
            $op = 'list';
        }
        if ($op !== 'list' && $op !== 'get') {
            return $this->jsonLegacy(400, '400', false, 'Public read supports only op=list or op=get. Use POST with Bearer for create/update/delete.');
        }

        $resolved = $this->resolveCampaignForPublicFaqRead($request);
        if (isset($resolved['response'])) {
            return $resolved['response'];
        }
        $campaign = $resolved['campaign'];

        $uid = $this->getUserId($request);
        $canManage = ! empty($uid) && $campaign->canBeEditedBy($uid);

        if ($op === 'list') {
            $baseQuery = $canManage ? $campaign->allUpdates() : $campaign->updates();
            $rows = $baseQuery->get()->map(function (CampaignUpdate $u) {
                return $this->formatCampaignUpdate($u);
            });

            return $this->jsonLegacy(200, '200', true, 'Updates list.', ['updates' => $rows->values()->all()]);
        }

        $updateId = (int) ($request->input('update_id') ?? $this->getRequestData($request)['update_id'] ?? 0);
        if ($updateId < 1) {
            return $this->jsonLegacy(400, '400', false, 'update_id is required.');
        }
        $update = CampaignUpdate::where('id', $updateId)->where('campaign_id', $campaign->id)->first();
        if (! $update) {
            return $this->jsonLegacy(404, '404', false, 'Update not found.');
        }
        if (! $update->is_published && ! $canManage) {
            return $this->jsonLegacy(404, '404', false, 'Update not found.');
        }

        return $this->jsonLegacy(200, '200', true, 'Update loaded.', ['update' => $this->formatCampaignUpdate($update)]);
    }

    /**
     * Backer-facing updates (campaign_updates table / CampaignUpdate model).
     * op=list|get|create|update|delete — requires Bearer (auth:sanctum). Prefer GET {@see postUpdatesPublicRead} for list/get without token.
     */
    public function postUpdates(Request $request): JsonResponse
    {
        $op = $this->op($request);
        if ($op === '') {
            return $this->jsonLegacy(400, '400', false, 'Parameter op is required: list, get, create, update, delete.');
        }

        $resolved = $this->resolveEditableCampaign($request, $op !== 'list' && $op !== 'get');
        if (isset($resolved['response'])) {
            return $resolved['response'];
        }
        $campaign = $resolved['campaign'];

        if ($op === 'list') {
            $rows = $campaign->allUpdates()->get()->map(function (CampaignUpdate $u) {
                return $this->formatCampaignUpdate($u);
            });

            return $this->jsonLegacy(200, '200', true, 'Updates list.', ['updates' => $rows->values()->all()]);
        }

        if ($op === 'get') {
            $updateId = (int) ($request->input('update_id') ?? $this->getRequestData($request)['update_id'] ?? 0);
            if ($updateId < 1) {
                return $this->jsonLegacy(400, '400', false, 'update_id is required.');
            }
            $update = CampaignUpdate::where('id', $updateId)->where('campaign_id', $campaign->id)->first();
            if (! $update) {
                return $this->jsonLegacy(404, '404', false, 'Update not found.');
            }

            return $this->jsonLegacy(200, '200', true, 'Update loaded.', ['update' => $this->formatCampaignUpdate($update)]);
        }

        if ($op === 'create') {
            $v = Validator::make($request->all(), [
                'title' => 'required|string|max:500',
                'content' => 'required|string|min:30',
                'is_published' => 'nullable|boolean',
                'image' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'webp'])->max(5120)],
            ]);

            if ($v->fails()) {
                return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
            }

            $update = new CampaignUpdate;
            $update->campaign_id = $campaign->id;
            $update->user_id = $this->getUserId($request);
            $update->title = $request->title;
            $update->content = $request->content;
            $update->slug = slug($request->title).'-'.time();
            $update->is_published = $request->boolean('is_published', true);

            if ($request->hasFile('image')) {
                try {
                    $update->image = fileUploader($request->image, getFilePath('campaign'), getFileSize('campaign'));
                } catch (\Exception $e) {
                    return $this->jsonLegacy(400, '400', false, 'Image upload failed: '.$e->getMessage());
                }
            }

            $update->save();

            return $this->jsonLegacy(200, '200', true, 'Update added successfully.', ['update' => $this->formatCampaignUpdate($update)]);
        }

        if ($op === 'update') {
            $updateId = (int) ($request->input('update_id') ?? $this->getRequestData($request)['update_id'] ?? 0);
            if ($updateId < 1) {
                return $this->jsonLegacy(400, '400', false, 'update_id is required.');
            }
            $update = CampaignUpdate::where('id', $updateId)->where('campaign_id', $campaign->id)->first();
            if (! $update) {
                return $this->jsonLegacy(404, '404', false, 'Update not found.');
            }

            $v = Validator::make($request->all(), [
                'title' => 'required|string|max:500',
                'content' => 'required|string|min:30',
                'is_published' => 'nullable|boolean',
                'image' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'webp'])->max(5120)],
            ]);

            if ($v->fails()) {
                return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
            }

            $update->title = $request->title;
            $update->content = $request->content;
            if ($request->has('is_published')) {
                $update->is_published = $request->boolean('is_published');
            }
            $update->slug = slug($request->title).'-'.time();

            if ($request->hasFile('image')) {
                try {
                    $update->image = fileUploader($request->image, getFilePath('campaign'), getFileSize('campaign'), $update->image);
                } catch (\Exception $e) {
                    return $this->jsonLegacy(400, '400', false, 'Image upload failed: '.$e->getMessage());
                }
            }

            $update->save();

            return $this->jsonLegacy(200, '200', true, 'Update saved successfully.', ['update' => $this->formatCampaignUpdate($update)]);
        }

        if ($op === 'delete') {
            $updateId = (int) ($request->input('update_id') ?? $this->getRequestData($request)['update_id'] ?? 0);
            if ($updateId < 1) {
                return $this->jsonLegacy(400, '400', false, 'update_id is required.');
            }
            $update = CampaignUpdate::where('id', $updateId)->where('campaign_id', $campaign->id)->first();
            if (! $update) {
                return $this->jsonLegacy(404, '404', false, 'Update not found.');
            }
            if ($update->image) {
                fileManager()->removeFile(getFilePath('campaign').'/'.$update->image);
            }
            $update->delete();

            return $this->jsonLegacy(200, '200', true, 'Update deleted successfully.');
        }

        return $this->jsonLegacy(400, '400', false, 'Invalid op for campaign_post_updates.php.');
    }

    protected function formatCampaignUpdate(CampaignUpdate $u): array
    {
        $imageUrl = $u->image ? getImage(getFilePath('campaign').'/'.$u->image, getFileSize('campaign')) : null;

        return [
            'id' => $u->id,
            'campaign_id' => $u->campaign_id,
            'user_id' => $u->user_id,
            'title' => $u->title,
            'content' => $u->content,
            'slug' => $u->slug,
            'image' => $u->image,
            'image_url' => $imageUrl,
            'is_published' => (bool) $u->is_published,
            'created_at' => $u->created_at?->toDateTimeString(),
            'updated_at' => $u->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * Required documents list for a campaign (country-aware).
     * Endpoint: /api/campaign_required_documents.php
     */
    public function requiredDocuments(Request $request): JsonResponse
    {
        $resolved = $this->resolveEditableCampaign($request, false);
        if (isset($resolved['response'])) {
            return $resolved['response'];
        }
        $campaign = $resolved['campaign'];

        $country = optional($campaign->user)->country_name ?: session('user_detected_country');
        $requirements = getCampaignDocumentRequirements(true, $country);
        $existingDocs = is_array($campaign->verification_documents) ? $campaign->verification_documents : [];

        $documents = array_map(function ($doc) use ($existingDocs) {
            $fieldKey = $doc['field_key'] ?? '';
            $existingFile = $fieldKey ? ($existingDocs[$fieldKey] ?? null) : null;

            return [
                'field_key' => $fieldKey,
                'label' => $doc['label'] ?? $fieldKey,
                'is_required' => (bool) ($doc['is_required'] ?? false),
                'is_global' => (bool) ($doc['is_global'] ?? true),
                'countries' => (array) ($doc['countries'] ?? []),
                'uploaded' => ! empty($existingFile),
                'file' => $existingFile,
                'file_url' => $existingFile ? asset(getFilePath('document').'/'.$existingFile) : null,
            ];
        }, $requirements);

        return $this->jsonLegacy(200, '200', true, 'Required documents loaded.', [
            'campaign_id' => $campaign->id,
            'slug' => $campaign->slug,
            'documents' => $documents,
        ]);
    }

    /**
     * Submit required verification documents for campaign.
     * Endpoint: /api/campaign_required_documents_submit.php (POST multipart)
     */
    public function submitRequiredDocuments(Request $request): JsonResponse
    {
        $resolved = $this->resolveEditableCampaign($request, true);
        if (isset($resolved['response'])) {
            return $resolved['response'];
        }
        $campaign = $resolved['campaign'];

        $country = optional($campaign->user)->country_name ?: session('user_detected_country');
        $requirements = getCampaignDocumentRequirements(true, $country);
        $existingDocs = is_array($campaign->verification_documents) ? $campaign->verification_documents : [];

        $rules = [];
        foreach ($requirements as $doc) {
            $fieldKey = $doc['field_key'] ?? null;
            if (! $fieldKey) {
                continue;
            }
            $alreadyUploaded = ! empty($existingDocs[$fieldKey]);
            $base = 'file|mimes:pdf,jpg,jpeg,png,webp|max:10240';
            $rules['documents.'.$fieldKey] = (! empty($doc['is_required']) && ! $alreadyUploaded) ? ('required|'.$base) : ('nullable|'.$base);
        }

        $v = Validator::make($request->all(), $rules, [
            'documents.*.required' => 'This document is required.',
            'documents.*.mimes' => 'Document must be PDF/JPG/JPEG/PNG/WEBP.',
            'documents.*.max' => 'Document size must be under 10 MB.',
        ]);
        if ($v->fails()) {
            return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
        }

        $updatedDocs = $existingDocs;
        foreach ($requirements as $doc) {
            $fieldKey = $doc['field_key'] ?? null;
            if (! $fieldKey || ! $request->hasFile('documents.'.$fieldKey)) {
                continue;
            }
            $oldFile = $updatedDocs[$fieldKey] ?? null;
            $updatedDocs[$fieldKey] = fileUploader(
                $request->file('documents.'.$fieldKey),
                getFilePath('document'),
                getFileSize('document'),
                $oldFile
            );
        }

        $campaign->verification_documents = $updatedDocs;
        $campaign->save();

        return $this->jsonLegacy(200, '200', true, 'Required documents submitted successfully.', [
            'campaign_id' => $campaign->id,
            'slug' => $campaign->slug,
            'verification_documents' => $updatedDocs,
        ]);
    }

    /**
     * POST /api/campaign_update_comment.php — comment on a published update (Bearer Sanctum; no CSRF).
     * JSON/form: slug | campaign_id | fund_id, update_id, comment, optional title. user_id from token.
     */
    public function storeUpdateCommentApi(Request $request): JsonResponse
    {
        $uid = $this->getUserId($request);
        if (! $uid) {
            return $this->jsonLegacy(401, '401', false, 'Unauthenticated! Please provide a valid token.');
        }

        $user = User::find($uid);
        if (! $user || ! $user->status) {
            return $this->jsonLegacy(403, '403', false, 'Your account is not active.');
        }

        $data = array_merge($this->getRequestData($request), $request->all());
        $slug = isset($data['slug']) ? trim((string) $data['slug']) : null;
        $campaignId = (int) ($data['campaign_id'] ?? $data['fund_id'] ?? 0);
        $updateId = (int) ($data['update_id'] ?? 0);

        if ($updateId < 1) {
            return $this->jsonLegacy(400, '400', false, 'update_id is required.');
        }

        $campaign = null;
        if ($slug !== null && $slug !== '') {
            $campaign = Campaign::where('slug', $slug)->approve()->first();
        } elseif ($campaignId > 0) {
            $campaign = Campaign::where('id', $campaignId)->approve()->first();
        }

        if (! $campaign) {
            return $this->jsonLegacy(404, '404', false, 'Campaign not found. Provide slug or campaign_id.');
        }

        $update = CampaignUpdate::where('id', $updateId)
            ->where('campaign_id', $campaign->id)
            ->where('is_published', true)
            ->first();

        if (! $update) {
            return $this->jsonLegacy(404, '404', false, 'Update not found.');
        }

        $v = Validator::make($data, [
            'comment' => 'required|string|max:1000',
            'title' => 'nullable|string|max:200',
        ]);

        if ($v->fails()) {
            return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
        }

        $comment = new Comment;
        $comment->user_id = $user->id;
        $comment->campaign_id = $campaign->id;
        $comment->update_id = $update->id;
        $comment->name = $user->fullname ?? $user->username;
        $comment->email = $user->email;
        $comment->comment = $data['comment'];
        $comment->rating = null;
        $comment->title = $data['title'] ?? null;
        $comment->status = ManageStatus::CAMPAIGN_COMMENT_APPROVED;
        $comment->save();

        $adminNotification = new AdminNotification;
        $adminNotification->user_id = $user->id;
        $adminNotification->title = ($user->fullname ?? $user->username).' commented on a campaign update.';
        $adminNotification->click_url = urlPath('admin.comments.index');
        $adminNotification->save();

        try {
            if ((int) $campaign->user_id > 0 && (int) $campaign->user_id !== (int) $user->id) {
                \App\Models\UserNotification::notifyCreatorUpdateCommentPublished(
                    (int) $campaign->user_id,
                    $campaign,
                    (string) ($comment->name ?? ''),
                    (string) ($update->title ?? '')
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Creator update-comment UserNotification failed', ['error' => $e->getMessage()]);
        }

        return $this->jsonLegacy(200, '200', true, 'Your comment has been posted.', [
            'user_id' => $user->id,
            'comment' => [
                'id' => $comment->id,
                'campaign_id' => $comment->campaign_id,
                'update_id' => $comment->update_id,
                'name' => $comment->name,
                'comment' => $comment->comment,
                'title' => $comment->title,
                'created_at' => $comment->created_at?->toDateTimeString(),
            ],
        ]);
    }

    /**
     * POST /api/campaign_comment.php — add campaign comment.
     * If Bearer token/user_id exists: use user table name/email.
     * If guest: name + email required.
     */
    public function storeCampaignCommentApi(Request $request): JsonResponse
    {
        $data = array_merge($this->getRequestData($request), $request->all());
        $slug = isset($data['slug']) ? trim((string) $data['slug']) : null;
        $campaignId = (int) ($data['campaign_id'] ?? $data['fund_id'] ?? 0);
        $requestUserId = (int) ($data['user_id'] ?? 0);

        $campaign = null;
        if ($slug !== null && $slug !== '') {
            $campaign = Campaign::where('slug', $slug)->approve()->first();
        } elseif ($campaignId > 0) {
            $campaign = Campaign::where('id', $campaignId)->approve()->first();
        }

        if (! $campaign) {
            return $this->jsonLegacy(404, '404', false, 'Campaign not found. Provide slug or campaign_id.');
        }

        $authUserId = (int) ($this->getUserId($request) ?? 0);
        $resolvedUserId = $authUserId > 0 ? $authUserId : $requestUserId;
        $user = null;
        if ($resolvedUserId > 0) {
            $user = User::find($resolvedUserId);
            if (! $user || ! $user->status) {
                return $this->jsonLegacy(403, '403', false, 'User not found or inactive.');
            }
        }

        if ($user && (int) $campaign->user_id === (int) $user->id) {
            return $this->jsonLegacy(403, '403', false, 'You cannot comment on your own campaign.');
        }

        $rules = [
            'comment' => 'required|string|max:1000',
            'title' => 'nullable|string|max:200',
        ];

        if (! $user) {
            $rules['name'] = 'required|string|max:80';
            $rules['email'] = 'required|email|max:120';
        }

        $v = Validator::make($data, $rules);
        if ($v->fails()) {
            return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
        }

        $comment = new Comment;
        $comment->user_id = $user?->id;
        $comment->campaign_id = $campaign->id;
        $comment->update_id = null;
        $comment->name = $user ? ($user->fullname ?? $user->username) : trim((string) $data['name']);
        $comment->email = $user ? (string) $user->email : trim((string) $data['email']);
        $comment->comment = trim((string) $data['comment']);
        $comment->rating = null;
        $comment->title = isset($data['title']) ? trim((string) $data['title']) : null;
        $comment->status = ManageStatus::CAMPAIGN_COMMENT_APPROVED;
        $comment->save();

        $adminNotification = new AdminNotification;
        $adminNotification->user_id = $user?->id ?? 0;
        $adminNotification->title = ($comment->name ?: 'Guest').' has commented on a campaign.';
        $adminNotification->click_url = urlPath('admin.comments.index');
        $adminNotification->save();

        try {
            if ((int) $campaign->user_id > 0 && (int) $campaign->user_id !== (int) ($comment->user_id ?? 0)) {
                \App\Models\UserNotification::notifyCreatorReviewPublished(
                    (int) $campaign->user_id,
                    $campaign,
                    (string) ($comment->name ?? '')
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Creator comment-published (API) UserNotification failed', ['error' => $e->getMessage()]);
        }

        return $this->jsonLegacy(200, '200', true, 'Your comment has been posted.', [
            'user_id' => $comment->user_id,
            'comment' => [
                'id' => $comment->id,
                'campaign_id' => $comment->campaign_id,
                'name' => $comment->name,
                'email' => $comment->email,
                'comment' => $comment->comment,
                'title' => $comment->title,
                'created_at' => $comment->created_at?->toDateTimeString(),
            ],
        ]);
    }

    /**
     * GET/POST /api/campaign_update_comments.php — list comments of a campaign update.
     * Public read for approved campaigns + published updates.
     */
    public function listUpdateCommentsApi(Request $request): JsonResponse
    {
        $data = array_merge($this->getRequestData($request), $request->all());
        $slug = isset($data['slug']) ? trim((string) $data['slug']) : null;
        $campaignId = (int) ($data['campaign_id'] ?? $data['fund_id'] ?? 0);
        $updateId = (int) ($data['update_id'] ?? 0);
        $skip = max(0, (int) ($data['skip'] ?? 0));
        $limit = (int) ($data['limit'] ?? 10);
        if ($limit < 1) {
            $limit = 10;
        }
        if ($limit > 50) {
            $limit = 50;
        }

        if ($updateId < 1) {
            return $this->jsonLegacy(400, '400', false, 'update_id is required.');
        }

        $campaign = null;
        if ($slug !== null && $slug !== '') {
            $campaign = Campaign::where('slug', $slug)->approve()->first();
        } elseif ($campaignId > 0) {
            $campaign = Campaign::where('id', $campaignId)->approve()->first();
        }
        if (! $campaign) {
            return $this->jsonLegacy(404, '404', false, 'Campaign not found. Provide slug or campaign_id.');
        }

        $update = CampaignUpdate::where('id', $updateId)
            ->where('campaign_id', $campaign->id)
            ->where('is_published', true)
            ->first();
        if (! $update) {
            return $this->jsonLegacy(404, '404', false, 'Update not found.');
        }

        $baseQuery = Comment::with('user')
            ->where('campaign_id', $campaign->id)
            ->where('update_id', $update->id)
            ->approve()
            ->latest();

        $total = (clone $baseQuery)->count();
        $rows = $baseQuery->skip($skip)->limit($limit)->get();

        $comments = $rows->map(function (Comment $comment) {
            $displayName = $comment->user ? ($comment->user->fullname ?? $comment->user->username) : $comment->name;

            return [
                'id' => $comment->id,
                'campaign_id' => $comment->campaign_id,
                'update_id' => $comment->update_id,
                'name' => $displayName,
                'email' => $comment->email,
                'title' => $comment->title,
                'comment' => $comment->comment,
                'rating' => $comment->rating,
                'created_at' => $comment->created_at?->toDateTimeString(),
                'is_guest' => ! $comment->user_id,
                'user' => $comment->user ? [
                    'id' => $comment->user->id,
                    'name' => $comment->user->fullname ?? $comment->user->username,
                    'username' => $comment->user->username,
                ] : null,
            ];
        })->values()->all();

        return $this->jsonLegacy(200, '200', true, 'Update comments list.', [
            'campaign_id' => $campaign->id,
            'update_id' => $update->id,
            'skip' => $skip,
            'limit' => $limit,
            'total_comments' => $total,
            'remaining_comments' => max(0, $total - ($skip + count($comments))),
            'comments' => $comments,
        ]);
    }
}
