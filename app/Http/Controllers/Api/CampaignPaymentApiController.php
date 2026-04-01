<?php

namespace App\Http\Controllers\Api;

use App\Models\AdminNotification;
use App\Models\Campaign;
use App\Models\Gateway;
use App\Models\PayoutBank;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Campaign Payment tab — payout banks list + save bank/account (web: User\CampaignController@updatePayment).
 * Endpoint: /api/campaign_payment.php — op=list|save (Bearer).
 */
class CampaignPaymentApiController extends BaseApiController
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

    public function payment(Request $request): JsonResponse
    {
        $uid = $this->getUserId($request);
        if (empty($uid)) {
            return $this->jsonLegacy(401, '401', false, 'Unauthenticated! Please provide a valid token.');
        }

        $data = $this->getRequestData($request);
        $op = strtolower(trim((string) ($data['op'] ?? $request->input('op', ''))));

        $cid = $data['campaign_id'] ?? $data['fund_id'] ?? $request->input('campaign_id') ?? $request->input('fund_id');
        $slug = $data['slug'] ?? $request->input('slug');
        $hasCampaign = ($cid !== null && $cid !== '') || (!empty($slug));

        if ($op === '' && $request->isMethod('GET') && $hasCampaign) {
            $op = 'list';
        }
        if ($op === '') {
            return $this->jsonLegacy(400, '400', false, 'Parameter op is required: list, save (or GET with campaign_id/slug for list).');
        }

        $campaign = $this->resolveCampaign($request);
        if ($campaign instanceof JsonResponse) {
            return $campaign;
        }

        if ($campaign->isExpired()) {
            return $this->jsonLegacy(400, '400', false, 'This campaign has expired and cannot be edited.');
        }

        if ($op === 'list') {
            if (!$campaign->canBeEditedBy($uid)) {
                return $this->jsonLegacy(403, '403', false, 'You do not have permission to manage this campaign.');
            }

            $countryResolution = $this->resolveCountryForGatewayList($request, $data);
            if (isset($countryResolution['error']) && $countryResolution['error'] instanceof JsonResponse) {
                return $countryResolution['error'];
            }
            $countryName = $countryResolution['country_name'] ?? null;
            $countryId = $countryResolution['country_id'] ?? null;

            $banks = PayoutBank::where('status', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(fn (PayoutBank $b) => $this->formatPayoutBank($b));

            $campaign->loadMissing('payoutBank');
            $current = [
                'payout_bank_id' => $campaign->payout_bank_id,
                'bank_account_number' => $campaign->bank_account_number,
                'bank_account_email' => $campaign->bank_account_email,
            ];
            if ($campaign->payout_bank_id && $campaign->payoutBank) {
                $current['payout_bank'] = $this->formatPayoutBank($campaign->payoutBank);
            }

            return $this->jsonLegacy(200, '200', true, 'Payout banks and campaign payment details.', [
                'payout_banks' => $banks->values()->all(),
                'campaign_payment' => $current,
                'country_id' => $countryId,
                'country_name' => $countryName,
                'allowed_gateways' => $this->getAllowedGatewaysByCountry($countryName),
            ]);
        }

        if ($op === 'save') {
            if (!$campaign->canBeEditedBy($uid)) {
                return $this->jsonLegacy(403, '403', false, 'You do not have permission to update payment details for this campaign.');
            }

            $payload = array_merge($data, $request->all());
            $v = Validator::make($payload, [
                'payout_bank_id' => 'required|exists:payout_banks,id',
                'bank_account_number' => 'required|string|max:255',
            ]);
            if ($v->fails()) {
                return $this->jsonLegacy(422, '422', false, $v->errors()->first(), ['errors' => $v->errors()]);
            }

            $payoutBank = PayoutBank::where('id', (int) $payload['payout_bank_id'])
                ->where('status', true)
                ->first();
            if (!$payoutBank) {
                return $this->jsonLegacy(400, '400', false, 'Selected bank is not available.');
            }

            $campaign->payout_bank_id = (int) $payload['payout_bank_id'];
            $campaign->bank_account_number = $payload['bank_account_number'];
            $campaign->bank_account_email = filter_var($payload['bank_account_number'], FILTER_VALIDATE_EMAIL)
                ? $payload['bank_account_number']
                : null;
            $campaign->save();

            $user = User::find($uid);
            $label = $user ? ($user->fullname ?? $user->username) : 'User';
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $uid;
            $adminNotification->title = $label . ' has updated payment details for campaign: ' . $campaign->name;
            $adminNotification->click_url = urlPath('admin.campaigns.index');
            $adminNotification->save();

            $campaign->load('payoutBank');

            return $this->jsonLegacy(200, '200', true, 'Payment details updated successfully.', [
                'campaign_payment' => [
                    'payout_bank_id' => $campaign->payout_bank_id,
                    'bank_account_number' => $campaign->bank_account_number,
                    'bank_account_email' => $campaign->bank_account_email,
                    'payout_bank' => $campaign->payoutBank ? $this->formatPayoutBank($campaign->payoutBank) : null,
                ],
            ]);
        }

        return $this->jsonLegacy(400, '400', false, 'Invalid op. Use list, save.');
    }

    protected function formatPayoutBank(PayoutBank $b): array
    {
        return [
            'id' => $b->id,
            'name' => $b->name,
            'code' => $b->code,
            'description' => $b->description,
            'sort_order' => $b->sort_order,
        ];
    }

    protected function resolveCountryForGatewayList(Request $request, array $data): array
    {
        $countryIdRaw = $data['country_id'] ?? $request->input('country_id');
        if ($countryIdRaw !== null && $countryIdRaw !== '') {
            $countryId = (int) $countryIdRaw;
            $allCountries = getAdminDefaultAllCountryNames();
            if ($countryId < 1 || $countryId > count($allCountries)) {
                return [
                    'error' => $this->jsonLegacy(400, '400', false, 'Invalid country_id.'),
                ];
            }

            $countryName = $allCountries[$countryId - 1];
            $allowed = getSiteAllowedCountryNames();
            if (!in_array($countryName, $allowed, true)) {
                return [
                    'error' => $this->jsonLegacy(400, '400', false, 'Country is not allowed for this site.'),
                ];
            }

            return [
                'country_id' => $countryId,
                'country_name' => $countryName,
            ];
        }

        $country = trim((string) ($data['country'] ?? $request->input('country', '')));
        if ($country === '') {
            return ['country_id' => null, 'country_name' => null];
        }

        $allowed = getSiteAllowedCountryNames();
        if (!in_array($country, $allowed, true)) {
            return [
                'error' => $this->jsonLegacy(400, '400', false, 'Country is not allowed for this site.'),
            ];
        }

        $allCountries = getAdminDefaultAllCountryNames();
        $idx = array_search($country, $allCountries, true);

        return [
            'country_id' => $idx === false ? null : ($idx + 1),
            'country_name' => $country,
        ];
    }

    protected function getAllowedGatewaysByCountry(?string $country): array
    {
        $query = Gateway::active()
            ->with(['currencies' => function ($q) {
                $q->where('status', 1)
                    ->orderBy('id')
                    ->select(
                        'id',
                        'method_code',
                        'currency',
                        'min_amount',
                        'max_amount',
                        'fixed_charge',
                        'percent_charge'
                    );
            }]);

        if (is_string($country) && $country !== '') {
            $localCode = strtoupper(getCurrencyCodeForCountryName($country));
            $query->forGatewayRegion($country, $localCode);
        }

        return $query->orderBy('id')->get()->map(function (Gateway $gateway) {
            $currency = $gateway->currencies->first();

            return [
                'id' => $gateway->id,
                'code' => $gateway->code,
                'name' => $gateway->name,
                'alias' => $gateway->alias ?? $gateway->name,
                'min_amount' => $currency ? (float) $currency->min_amount : 0.0,
                'max_amount' => $currency ? (float) $currency->max_amount : 0.0,
                'fixed_charge' => $currency ? (float) $currency->fixed_charge : 0.0,
                'percent_charge' => $currency ? (float) $currency->percent_charge : 0.0,
                'currency' => $currency ? strtoupper((string) $currency->currency) : null,
            ];
        })->values()->all();
    }

    /**
     * @return Campaign|JsonResponse
     */
    protected function resolveCampaign(Request $request)
    {
        $data = $this->getRequestData($request);
        $rawId = $data['campaign_id'] ?? $data['fund_id'] ?? $request->input('campaign_id') ?? $request->input('fund_id');
        $slug = $data['slug'] ?? $request->input('slug');

        $campaign = null;
        if ($rawId !== null && $rawId !== '') {
            $campaign = Campaign::where('id', (int) $rawId)->first();
        } elseif (!empty($slug)) {
            $campaign = Campaign::where('slug', $slug)->first();
        }

        if (!$campaign) {
            return $this->jsonLegacy(404, '404', false, 'Campaign not found. Provide campaign_id, fund_id, or slug.');
        }

        return $campaign;
    }
}
