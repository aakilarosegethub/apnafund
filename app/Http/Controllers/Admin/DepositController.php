<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;

class DepositController extends Controller
{
    function index() {
        $pageTitle   = 'All Donations';
        $depositData = $this->donationData('index', true);
        $deposits    = $depositData['data'];
        $summary     = $depositData['summary'];
        $done        = $summary['done'];
        $pending     = $summary['pending'];
        $cancelled   = $summary['cancelled'];
        $charge      = $summary['charge'];
        $campaigns   = \App\Models\Campaign::select('id', 'name')->orderBy('name')->get();
        $donorUsers  = $this->donorUsersForFilter();

        return view('admin.page.donations', compact('pageTitle', 'deposits', 'done', 'pending', 'cancelled', 'charge', 'campaigns', 'donorUsers'));
    }

    function pending() {
        $pageTitle = 'Pending Donations';
        $deposits  = $this->donationData('adminPending');
        $campaigns = \App\Models\Campaign::select('id', 'name')->orderBy('name')->get();
        $donorUsers = $this->donorUsersForFilter();

        return view('admin.page.donations', compact('pageTitle', 'deposits', 'campaigns', 'donorUsers'));
    }

    function done() {
        $pageTitle = 'Done Donations';
        $deposits  = $this->donationData('done');
        $campaigns = \App\Models\Campaign::select('id', 'name')->orderBy('name')->get();
        $donorUsers = $this->donorUsersForFilter();

        return view('admin.page.donations', compact('pageTitle', 'deposits', 'campaigns', 'donorUsers'));
    }

    function cancelled() {
        $pageTitle = 'Cancelled Donations';
        $deposits  = $this->donationData('cancelled');
        $campaigns = \App\Models\Campaign::select('id', 'name')->orderBy('name')->get();
        $donorUsers = $this->donorUsersForFilter();

        return view('admin.page.donations', compact('pageTitle', 'deposits', 'campaigns', 'donorUsers'));
    }

    /**
     * Registered users who have at least one deposit (for admin donation filters).
     */
    protected function donorUsersForFilter()
    {
        return User::query()
            ->whereIn('id', function ($q) {
                $q->select('user_id')
                    ->from('deposits')
                    ->whereNotNull('user_id')
                    ->groupBy('user_id');
            })
            ->orderBy('username')
            ->get(['id', 'username', 'email']);
    }

    protected function donationData($scope = null, $summary = false) {
        if ($scope) {
            if ($scope === 'index') {
                $deposits = Deposit::with(['gateway', 'user', 'campaign', 'reward'])->adminIndex();
            } elseif ($scope === 'adminPending') {
                $deposits = Deposit::with(['gateway', 'user', 'campaign', 'reward'])->adminPending();
            } else {
                $deposits = Deposit::with(['gateway', 'user', 'campaign', 'reward'])->$scope();
            }
        } else {
            $deposits = Deposit::with(['gateway', 'user', 'campaign', 'reward']);
        }

        $deposits = $deposits->searchable(['receiver_id', 'trx', 'user:username', 'campaign:name'])->dateFilter();

        // Filter by payment method
        if (request('method')) {
            $method   = Gateway::where('alias', request('method'))->firstOrFail();
            $deposits = $deposits->where('method_code', $method->code);
        }

        // Filter by campaign
        if (request('campaign')) {
            $deposits = $deposits->where('campaign_id', request('campaign'));
        }

        // Filter by registered donor user
        if (request()->filled('user_id')) {
            $uid = (int) request('user_id');
            if ($uid > 0) {
                $deposits = $deposits->where('user_id', $uid);
            }
        }

        if (!$summary) {
            return $deposits->latest()->paginate(getPaginate());
        } else {
            $doneSummary      = (clone $deposits)->done()->sum('amount');
            // Pending: gateway processing (any method) + manual initiated (proof not submitted yet)
            $pendingSummary = (clone $deposits)->where(function ($q) {
                $q->where('status', ManageStatus::PAYMENT_PENDING)
                    ->orWhere(function ($q2) {
                        $q2->where('status', ManageStatus::PAYMENT_INITIATE)
                            ->where('method_code', '>=', 1000);
                    });
            })->sum('amount');
            $cancelledSummary = (clone $deposits)->cancelled()->sum('amount');
            $chargeSummary    = (clone $deposits)->done()->sum('charge');

            return [
                'data'    => $deposits->latest()->paginate(getPaginate()),
                'summary' => [
                    'done'      => $doneSummary,
                    'pending'   => $pendingSummary,
                    'cancelled' => $cancelledSummary,
                    'charge'    => $chargeSummary,
                ]
            ];
        }
    }

    function approve($id) {
        if (!admin_can('donations.approve')) {
            $toast[] = ['error', 'You do not have permission to approve donations.'];
            return back()->withToasts($toast);
        }
        $deposit = Deposit::where('id', $id)->pending()->firstOrFail();
        PaymentController::campaignDataUpdate($deposit, true);

        $toast[] = ['success', 'Donation approval success'];

        return back()->withToasts($toast);
    }

    function reject($id) {
        if (!admin_can('donations.approve')) {
            $toast[] = ['error', 'You do not have permission to reject donations.'];
            return back()->withToasts($toast);
        }
        $this->validate(request(), [
            'admin_feedback' => 'required|max:255',
        ]);

        $deposit                 = Deposit::where('id', $id)->pending()->firstOrFail();
        $deposit->status         = ManageStatus::PAYMENT_CANCEL;
        $deposit->admin_feedback = request('admin_feedback');
        $deposit->save();

        $user = User::find($deposit->user_id);

        if (!$user) {
            $user = [
                'fullname' => $deposit->full_name,
                'username' => $deposit->email,
                'email'    => $deposit->email,
                'mobile'   => $deposit->phone,
            ];
        }

        $campaign = $deposit->campaign;

        notify($user, 'DONATION_REJECT', [
            'method_name'       => $deposit->gatewayCurrency()->name,
            'method_currency'   => $deposit->method_currency,
            'method_amount'     => showAmount($deposit->final_amount),
            'amount'            => showAmount($deposit->amount),
            'charge'            => showAmount($deposit->charge),
            'rate'              => showAmount($deposit->rate),
            'trx'               => $deposit->trx,
            'campaign_name'     => $campaign->name,
            'rejection_message' => request('admin_feedback'),
        ]);

        $toast[] = ['success', 'Donation rejection success'];

        return back()->withToasts($toast);
    }

    function rewardsTracking() {
        $pageTitle = 'Rewards Tracking';
        $filter = request('filter', 'all'); // all, received, paid, pending, fulfilled
        
        $transactions = Transaction::whereNotNull('reward_id')
            ->with(['reward', 'deposit.campaign', 'deposit.user', 'user'])
            ->latest();
        
        // Apply filters
        if ($filter == 'received') {
            $transactions = $transactions->where('remark', 'donation_received');
        } elseif ($filter == 'paid') {
            $transactions = $transactions->where('remark', 'donation_given');
        } elseif ($filter == 'pending') {
            $transactions = $transactions->where('reward_fulfilled', false);
        } elseif ($filter == 'fulfilled') {
            $transactions = $transactions->where('reward_fulfilled', true);
        }
        
        $transactions = $transactions->paginate(getPaginate());
        
        $emptyMessage = 'No rewards found';
        return view('admin.page.rewards', compact('pageTitle', 'transactions', 'filter', 'emptyMessage'));
    }
}
