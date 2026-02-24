<?php

namespace App\Http\Controllers\Api;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Category;
use App\Services\CurrencyService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class CampaignController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $campaigns = Campaign::with('category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(function (Campaign $campaign) {
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'slug' => $campaign->slug,
                    'category_id' => $campaign->category_id,
                    'category' => $campaign->category->name ?? null,
                    'short_description' => $campaign->short_description,
                    'goal_amount' => $campaign->goal_amount,
                    'start_date' => optional($campaign->start_date)->format('Y-m-d'),
                    'end_date' => optional($campaign->end_date)->format('Y-m-d'),
                    'status' => $campaign->status,
                    'image_url' => $campaign->image
                        ? getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign'))
                        : null,
                    'youtube_url' => $campaign->youtube_url,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $campaigns,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'name' => ['required', 'string', 'max:190', Rule::unique('campaigns', 'name')],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:30'],
            'image' => ['required', File::types(['png', 'jpg', 'jpeg', 'webp'])],
            'youtube_url' => ['nullable', 'url'],
            'goal_amount' => ['required', 'numeric', 'gt:0'],
            'start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after:start_date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->filled('youtube_url')) {
            $youtubeUrl = $request->input('youtube_url');
            if (!preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)[\w\-]+/i', $youtubeUrl)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a valid YouTube URL',
                ], 422);
            }
        }

        if (!$request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => ['image' => ['Image is required.']],
            ], 422);
        }

        $imageFile = $request->file('image');
        if (!$imageFile->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => ['image' => [$imageFile->getErrorMessage()]],
            ], 422);
        }

        $category = Category::active()->where('id', $request->input('category_id'))->first();
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Selected category not found or inactive',
            ], 422);
        }

        $imageOriginal = fileUploader($imageFile, getFilePath('campaignOriginal'));
        $image = fileUploader($imageFile, getFilePath('campaign'), getFileSize('campaign'), null, getThumbSize('campaign'));

        $currencyService = app(CurrencyService::class);
        $creatorCurrencyCode = $currencyService->detectCurrencyCode($request->user());
        $creatorCurrency = $currencyService->getOrCreateByCode($creatorCurrencyCode);
        $exchangeRate = $currencyService->getRateToUsd($creatorCurrency);
        $goalAmount = (float) $request->input('goal_amount');
        $goalAmountUsd = $currencyService->convertToUsd($goalAmount, $creatorCurrency);

        $slugBase = Str::slug($request->input('name'));
        $slug = $slugBase;
        $counter = 1;
        while (Campaign::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        $campaign = Campaign::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->input('category_id'),
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'short_description' => $request->input('short_description'),
            'image' => $image,
            'image_original' => $imageOriginal,
            'youtube_url' => $request->input('youtube_url'),
            'goal_amount' => $goalAmountUsd,
            'goal_amount_usd' => $goalAmountUsd,
            'original_goal_amount' => $goalAmount,
            'original_currency' => $creatorCurrency->code,
            'exchange_rate_used' => $exchangeRate,
            'start_date' => Carbon::parse($request->input('start_date')),
            'end_date' => Carbon::parse($request->input('end_date')),
            'status' => ManageStatus::CAMPAIGN_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaign created successfully',
            'data' => [
                'id' => $campaign->id,
                'slug' => $campaign->slug,
            ],
        ], 201);
    }

    public function update(Request $request, Campaign $campaign): JsonResponse
    {
        
        if (!$campaign->canBeEditedBy($request->user()->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit this campaign',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'name' => ['required', 'string', 'max:190', Rule::unique('campaigns', 'name')->ignore($campaign->id)],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:30'],
            'image' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'webp'])],
            'youtube_url' => ['nullable', 'url'],
            'goal_amount' => ['required', 'numeric', 'gt:0'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after:start_date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->filled('youtube_url')) {
            $youtubeUrl = $request->input('youtube_url');
            if (!preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)[\w\-]+/i', $youtubeUrl)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a valid YouTube URL',
                ], 422);
            }
        }

        $category = Category::active()->where('id', $request->input('category_id'))->first();
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Selected category not found or inactive',
            ], 422);
        }

        $campaign->category_id = $request->input('category_id');
        $campaign->name = $request->input('name');
        $campaign->short_description = $request->input('short_description');
        $campaign->description = $request->input('description');

        $slugBase = Str::slug($request->input('name'));
        $slug = $slugBase;
        $counter = 1;
        while (Campaign::where('slug', $slug)->where('id', '!=', $campaign->id)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }
        $campaign->slug = $slug;

        $currencyService = app(CurrencyService::class);
        $creatorCurrencyCode = $currencyService->detectCurrencyCode($campaign->user ?? $request->user());
        $creatorCurrency = $currencyService->getOrCreateByCode($creatorCurrencyCode);
        $exchangeRate = $currencyService->getRateToUsd($creatorCurrency);
        $goalAmount = (float) $request->input('goal_amount');
        $goalAmountUsd = $currencyService->convertToUsd($goalAmount, $creatorCurrency);

        $campaign->goal_amount = $goalAmountUsd;
        $campaign->goal_amount_usd = $goalAmountUsd;
        $campaign->original_goal_amount = $goalAmount;
        $campaign->original_currency = $creatorCurrency->code;
        $campaign->exchange_rate_used = $exchangeRate;
        $campaign->start_date = Carbon::parse($request->input('start_date'));
        $campaign->end_date = Carbon::parse($request->input('end_date'));

        if ($request->has('youtube_url')) {
            $campaign->youtube_url = $request->input('youtube_url') ?: null;
        }

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            if (!$imageFile->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['image' => [$imageFile->getErrorMessage()]],
                ], 422);
            }
            $campaign->image_original = fileUploader($imageFile, getFilePath('campaignOriginal'), null, $campaign->image_original);
            $campaign->image = fileUploader($imageFile, getFilePath('campaign'), getFileSize('campaign'), $campaign->image, getThumbSize('campaign'));
        }

        $campaign->save();

        return response()->json([
            'success' => true,
            'message' => 'Campaign updated successfully',
            'data' => [
                'id' => $campaign->id,
                'slug' => $campaign->slug,
            ],
        ]);
    }
}
