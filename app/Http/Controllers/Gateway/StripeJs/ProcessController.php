<?php

namespace App\Http\Controllers\Gateway\StripeJs;

use Exception;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Customer;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\Request;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $stripeJSAcc        = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $val['key']         = $stripeJSAcc->publishable_key;
        $val['name']        = $deposit->user_id ? $deposit->user->fullname : $deposit->full_name;
        $val['description'] = "Payment with Stripe";
        $val['amount']      = round($deposit->final_amount, 2) * 100;
        $val['currency']    = $deposit->method_currency;
        $send['val']        = $val;
        $alias              = $deposit->gateway->alias;

        $send['src']    = "https://checkout.stripe.com/checkout.js";
        $send['view']   = 'user.payment.' . $alias;
        $send['method'] = 'post';
        $send['url']    = route('ipn.' . $deposit->gateway->alias);

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track   = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->first();

        if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
            $toast[] = ['error', 'Invalid request.'];

            return to_route(gatewayRedirectUrl())->withToasts($toast);
        }

        $stripeJSAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        Stripe::setApiKey($stripeJSAcc->secret_key);
        Stripe::setApiVersion("2020-03-02");

        try {
            $customer = Customer::create([
                'email'  => $request->stripeEmail,
                'source' => $request->stripeToken,
            ]);
        } catch (Exception $e) {
            $toast[] = ['error', $e->getMessage()];

            return to_route(gatewayRedirectUrl())->withToasts($toast);
        }

        try {
            $charge = Charge::create([
                'customer'    => $customer->id,
                'description' => 'Payment with Stripe',
                'amount'      => round($deposit->final_amount, 2) * 100,
                'currency'    => $deposit->method_currency,
            ]);
        } catch (Exception $e) {
            $toast[] = ['error', $e->getMessage()];

            return to_route(gatewayRedirectUrl())->withToasts($toast);
        }

        if ($charge['status'] == 'succeeded') {
            PaymentController::campaignDataUpdate($deposit);
            
            // Reload deposit to get updated data
            $deposit->refresh();
            $campaign = $deposit->campaign;
            
            // Get user data for email
            $user = User::find($deposit->user_id);
            if (!$user) {
                $user = (object) [
                    'fullname' => $deposit->full_name,
                    'username' => $deposit->email,
                    'email' => $deposit->email,
                    'mobile' => $deposit->phone,
                ];
            }
            
            // Send email to donor (user)
            try {
                $userTemplate = \App\Models\NotificationTemplate::where('act', 'USER_PAYMENT_SUCCESS')
                    ->where('email_status', ManageStatus::ACTIVE)
                    ->first();
                
                if ($userTemplate) {
                    notify($user, 'USER_PAYMENT_SUCCESS', [
                        'full_name' => $deposit->full_name,
                        'email' => $deposit->email,
                        'campaign_name' => $campaign->name,
                        'amount' => showAmount($deposit->amount),
                        'method_name' => $deposit->gatewayCurrency()->name,
                        'trx' => $deposit->trx,
                        'date' => showDateTime($deposit->updated_at, 'd M, Y h:i A'),
                        'campaign_url' => route('campaign.show', $campaign->slug),
                        'admin_url' => urlPath('admin.donations.done'),
                    ], ['email']);
                    \Log::info('Payment success email sent to user (StripeJs): ' . $deposit->email);
                } else {
                    \Log::warning('USER_PAYMENT_SUCCESS template not found or inactive. Email not sent to: ' . $deposit->email);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send payment success email to user (StripeJs): ' . $e->getMessage(), [
                    'user_email' => $deposit->email,
                    'deposit_id' => $deposit->id
                ]);
            }
            
            // Send email to admin
            try {
                $adminEmail = bs('site_email');
                if ($adminEmail) {
                    $adminTemplate = \App\Models\NotificationTemplate::where('act', 'ADMIN_PAYMENT_SUCCESS')
                        ->where('email_status', ManageStatus::ACTIVE)
                        ->first();
                    
                    if ($adminTemplate) {
                        $adminUser = (object) [
                            'fullname' => 'Admin',
                            'username' => 'admin',
                            'email' => $adminEmail,
                        ];
                        
                        notify($adminUser, 'ADMIN_PAYMENT_SUCCESS', [
                            'full_name' => $deposit->full_name,
                            'email' => $deposit->email,
                            'campaign_name' => $campaign->name,
                            'amount' => showAmount($deposit->amount),
                            'method_name' => $deposit->gatewayCurrency()->name,
                            'trx' => $deposit->trx,
                            'date' => showDateTime($deposit->updated_at, 'd M, Y h:i A'),
                            'campaign_url' => route('campaign.show', $campaign->slug),
                            'admin_url' => urlPath('admin.donations.done'),
                        ], ['email']);
                        \Log::info('Payment success email sent to admin (StripeJs): ' . $adminEmail);
                    } else {
                        \Log::warning('ADMIN_PAYMENT_SUCCESS template not found or inactive. Email not sent to admin.');
                    }
                } else {
                    \Log::warning('Admin email (site_email) not configured. Admin email not sent (StripeJs).');
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send payment success email to admin (StripeJs): ' . $e->getMessage(), [
                    'admin_email' => $adminEmail ?? 'not set',
                    'deposit_id' => $deposit->id
                ]);
            }
            
            $toast[] = ['success', 'Payment completed successfully'];

            return to_route(gatewayRedirectUrl(true))->withToasts($toast);
        } else {
            $toast[] = ['error', 'Failed to process'];

            return to_route(gatewayRedirectUrl())->withToasts($toast);
        }
    }
}
