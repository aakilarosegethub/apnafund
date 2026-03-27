<?php

namespace App\Http\Controllers\Gateway\StripeV3;

use Exception;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Deposit;
use App\Models\User;
use Stripe\Checkout\Session;
use UnexpectedValueException;
use App\Constants\ManageStatus;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Stripe\Exception\SignatureVerificationException;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $stripeAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $alias     = $deposit->gateway->alias;

        Stripe::setApiKey("$stripeAcc->secret_key");
        Stripe::setApiVersion("2020-03-02");

        // Charge in gateway currency (deposits.final_amount); deposits.amount stays in platform currency.
        $currency = strtolower((string) $deposit->method_currency);
        $final = round((float) $deposit->final_amount, 2);
        $zeroDecimal = ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf'];
        $unitAmount = in_array($currency, $zeroDecimal, true)
            ? (int) round($final)
            : (int) round($final * 100);

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode'                 => 'payment',
                'line_items'           => [
                    [
                        'price_data' => [
                            'currency'     => $currency,
                            'product_data' => [
                                'name'        => bs('site_name'),
                                'description' => 'Donation with Stripe',
                                'images'      => [asset('assets/universal/images/logoFavicon/logo_dark.png')],
                            ],
                            'unit_amount'  => $unitAmount,
                        ],
                        'quantity'   => 1,
                    ]
                ],
                'cancel_url'           => gatewayRedirectUrlFull(false),
                'success_url'          => gatewayRedirectUrlFull(true),
            ]);
        } catch (Exception $e) {
            $errorContext = [
                'exception_class' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trx' => $deposit->trx ?? null,
                'deposit_id' => $deposit->id ?? null,
                'gateway' => 'StripeV3',
            ];
            if (method_exists($e, 'getHttpStatus')) {
                $errorContext['http_status'] = $e->getHttpStatus();
            }
            if (method_exists($e, 'getHttpBody')) {
                $errorContext['http_body'] = $e->getHttpBody();
            }
            if (method_exists($e, 'getJsonBody')) {
                $errorContext['json_body'] = $e->getJsonBody();
            }
            if (method_exists($e, 'getStripeCode')) {
                $errorContext['stripe_code'] = $e->getStripeCode();
            }
            if (method_exists($e, 'getError') && $e->getError()) {
                $errorContext['stripe_error'] = $e->getError();
            }
            \Log::error('StripeV3 checkout session create failed', $errorContext);

            $send['error']   = true;
            $send['message'] = $e->getMessage();

            return json_encode($send);
        }

        $send['view']        = 'user.payment.' . $alias;
        $send['session']     = $session;
        $send['stripeJSAcc'] = $stripeAcc;
        $deposit->btc_wallet = json_decode(json_encode($session))->id;
        $deposit->save();

        return json_encode($send);
    }

    public function ipn()
    {
        dd(request()->all(), request()->getContent());

        $stripeAcc         = GatewayCurrency::where('gateway_alias', 'StripeV3')->first();
        $gateway_parameter = json_decode($stripeAcc->gateway_parameter);

        Stripe::setApiKey($gateway_parameter->secret_key);
        Stripe::setApiVersion("2020-03-02");

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $gateway_parameter->end_point; // main
        $payload         = @file_get_contents('php://input');
        $event           = json_decode($payload);
        $sig_header      = (isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';

        /*try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }*/

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            $deposit = Deposit::where('btc_wallet', $session->id)->first();

            if ($deposit && $deposit->status == ManageStatus::PAYMENT_INITIATE) {
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
                        \Log::info('Payment success email sent to user (StripeV3): ' . $deposit->email);
                    } else {
                        \Log::warning('USER_PAYMENT_SUCCESS template not found or inactive. Email not sent to: ' . $deposit->email);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send payment success email to user (StripeV3): ' . $e->getMessage(), [
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
                            \Log::info('Payment success email sent to admin (StripeV3): ' . $adminEmail);
                        } else {
                            \Log::warning('ADMIN_PAYMENT_SUCCESS template not found or inactive. Email not sent to admin.');
                        }
                    } else {
                        \Log::warning('Admin email (site_email) not configured. Admin email not sent (StripeV3).');
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send payment success email to admin (StripeV3): ' . $e->getMessage(), [
                        'admin_email' => $adminEmail ?? 'not set',
                        'deposit_id' => $deposit->id
                    ]);
                }
            }
        }

        http_response_code(200);
    }
}
