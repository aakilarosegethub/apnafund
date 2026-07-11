<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CreatorCampaignPayout;
use App\Models\CreatorCampaignPayoutAction;
use App\Services\CreatorCampaignPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreatorPayoutController extends Controller
{
    public function index(CreatorCampaignPayoutService $service)
    {
        $pageTitle = 'Creator Payout Management';
        $settings = $service->getSettings();
        $today = now()->toDateString();
        $scope = request('scope', 'successful');

        $campaignsQuery = Campaign::with(['user', 'payoutBank', 'creatorPayout']);

        if ($scope === 'successful') {
            $campaignsQuery->approve()
                ->where(function ($query) use ($today) {
                    $query->where('end_date', '<', $today)
                        ->orWhereExists(function ($subQuery) {
                            $subQuery->select(DB::raw(1))
                                ->from('deposits')
                                ->whereColumn('deposits.campaign_id', 'campaigns.id')
                                ->where('deposits.status', ManageStatus::PAYMENT_SUCCESS)
                                ->groupBy('deposits.campaign_id')
                                ->havingRaw('SUM(deposits.amount) >= campaigns.goal_amount');
                        });
                });
        }

        $campaigns = $campaignsQuery
            ->latest()
            ->paginate(getPaginate())
            ->appends(request()->all());

        $adminId = auth()->guard('admin')->id();

        foreach ($campaigns as $campaign) {
            if (! $campaign->creatorPayout) {
                $payout = $service->ensurePayoutRecord($campaign, $adminId);
                if ($payout) {
                    $campaign->setRelation('creatorPayout', $payout);
                }
            }
        }

        return view('admin.creator_payout.index', compact('pageTitle', 'campaigns', 'settings', 'scope'));
    }

    public function show(CreatorCampaignPayout $payout)
    {
        $pageTitle = 'Creator Payout Details';
        $payout->load(['campaign.user', 'campaign.payoutBank', 'actions.admin']);

        return view('admin.creator_payout.show', compact('pageTitle', 'payout'));
    }

    public function partialPayout(Request $request, CreatorCampaignPayout $payout)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
        ]);

        $available = $payout->availableForPayout();
        $amount = (float) $request->amount;

        if ($amount > $available) {
            $toast[] = ['error', 'Partial payout exceeds available amount'];

            return back()->withToasts($toast);
        }

        $payout->total_paid_amount = round((float) $payout->total_paid_amount + $amount, 2);
        $totalPayable = (float) $payout->net_payable_amount + (float) $payout->released_withheld_amount;
        $payout->payout_status = $payout->total_paid_amount >= $totalPayable ? 'paid' : 'partial';
        $payout->save();

        $this->logAction($payout, 'partial_payout', $amount, $request->notes);

        $toast[] = ['success', 'Partial payout recorded successfully'];

        return back()->withToasts($toast);
    }

    public function fullPayout(Request $request, CreatorCampaignPayout $payout)
    {
        $available = $payout->availableForPayout();

        if ($available <= 0) {
            $toast[] = ['error', 'No available amount to payout'];

            return back()->withToasts($toast);
        }

        $totalPayable = (float) $payout->net_payable_amount + (float) $payout->released_withheld_amount;
        $payout->total_paid_amount = round($totalPayable, 2);
        $payout->payout_status = 'paid';
        $payout->save();

        $this->logAction($payout, 'full_payout', $available, $request->input('notes'));

        $toast[] = ['success', 'Full payout recorded successfully'];

        return back()->withToasts($toast);
    }

    public function markFulfillmentComplete(Request $request, CreatorCampaignPayout $payout)
    {
        if ($payout->fulfillment_status === 'completed') {
            $toast[] = ['info', 'Fulfillment already marked as completed'];

            return back()->withToasts($toast);
        }

        $payout->fulfillment_status = 'completed';
        $payout->released_withheld_amount = (float) $payout->fulfillment_withholding_amount;
        $payout->fulfillment_released_at = now();
        $payout->save();

        $this->logAction($payout, 'fulfillment_completed', null, $request->input('notes'));

        $toast[] = ['success', 'Fulfillment marked as completed'];

        return back()->withToasts($toast);
    }

    protected function logAction(CreatorCampaignPayout $payout, string $type, ?float $amount = null, ?string $notes = null): void
    {
        CreatorCampaignPayoutAction::create([
            'creator_campaign_payout_id' => $payout->id,
            'admin_id' => auth()->guard('admin')->id(),
            'action_type' => $type,
            'amount' => $amount,
            'notes' => $notes,
            'meta' => [
                'payout_status' => $payout->payout_status,
                'fulfillment_status' => $payout->fulfillment_status,
            ],
        ]);
    }
}
