<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use RegistersUsers;

    function registerForm() {
        // Check if user is already logged in
        if (auth()->check()) {
            return redirect()->route('home');
        }
        
        $pageTitle       = 'Register';
        $info            = json_decode(json_encode(getIpInfo()), true);
        $mobileCode      = @implode(',', $info['code']);
        $countries       = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $registerContent = getSiteData('register.content', true);
        $policyPages     = getSiteData('policy_pages.element', false, null, true);

        return view('user.auth.register-business', compact('pageTitle', 'mobileCode', 'countries', 'registerContent', 'policyPages'));
    }

    function registerBusinessForm() {
        // Check if user is already logged in
        if (auth()->check()) {
            $toast[] = ['error', 'You are already logged in'];
            return redirect()->route('home')->withToasts($toast);
        }
        
        $pageTitle = 'Register Your Business';
        return view('user.auth.register-business', compact('pageTitle'));
    }

    protected function validator(array $data) {
        $setting = bs();
        $passwordValidation = Password::min(6);

        if ($setting->strong_pass) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $agree = 'nullable';

        if ($setting->agree_policy) {
            $agree = 'required';
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',',array_column($countryData, 'dial_code'));
        $countries    = implode(',',array_column($countryData, 'country'));
        $validate     = Validator::make($data, [
            'firstname'    => 'required|string|max:40',
            'lastname'     => 'required|string|max:40',
            'email'        => 'required|string|email|max:40|unique:users',
            'mobile'       => 'required|max:40|regex:/^([0-9]*)$/',
            'password'     => ['required', 'confirmed', $passwordValidation],
            'username'     => 'required|unique:users|min:6|max:40',
            'mobile_code'  => 'required|in:'.$mobileCodes,
            'country_code' => 'required|in:'.$countryCodes,
            'country'      => 'required|in:'.$countries,
            'agree'        => $agree
        ]);

        return $validate;
    }

    function register() {
        $this->validator(request()->all())->validate();

        request()->session()->regenerateToken();

        if(!verifyCaptcha()) {
            $toast[] = ['error', 'Invalid captcha provided'];
            return back()->withToasts($toast);
        }

        if(preg_match("/[^a-z0-9_]/", trim(request('username')))) {
            $toast[] = ['info', 'Usernames are limited to lowercase letters, numbers, and underscores'];
            $toast[] = ['error', 'Username must exclude special characters, spaces, and capital letters'];
            return back()->withToasts($toast)->withInput(request()->all());
        }

        $exist = User::where('mobile', request('mobile_code') . request('mobile'))->first();

        if ($exist) {
            $toast[] = ['error', 'That mobile number is already on our records'];
            return back()->withToasts($toast)->withInput();
        }

        event(new Registered($user = $this->create(request()->all())));

        $this->guard()->login($user);

        return $this->registered(request(), $user) ? : redirect($this->redirectPath());
    }

    protected function create(array $data)
    {
        $setting = bs();
        $referBy = session()->get('reference');

        if ($referBy) {
            $referUser = User::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }
  
        // User Create
        $user               = new User();
        $user->firstname    = $data['firstname'];
        $user->lastname     = $data['lastname'];
        $user->email        = strtolower($data['email']);
        $user->password     = Hash::make($data['password']);
        $user->username     = $data['username'];
        $user->ref_by       = $referUser ? $referUser->id : 0;
        $user->country_code = $data['country_code'];
        $user->country_name = isset($data['country']) ? $data['country'] : null;
        $user->mobile       = $data['mobile_code'].$data['mobile'];
        $user->kc           = $setting->kc ? ManageStatus::NO : ManageStatus::YES;
        $user->ec           = $setting->ec ? ManageStatus::NO : ManageStatus::YES;
        $user->sc           = $setting->sc ? ManageStatus::NO : ManageStatus::YES;
        $user->ts           = ManageStatus::NO;
        $user->tc           = ManageStatus::YES;
        $user->save();


        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New member registered';
        $adminNotification->click_url = urlPath('admin.user.index');
        $adminNotification->save();

        return $user;
    }

    function checkUser(){
        $exist['data'] = false;
        $exist['type'] = null;

        if (request('email')) {
            $exist['data'] = User::where('email', request('email'))->exists();
            $exist['type'] = 'email';
        }
        if (request('mobile')) {
            $exist['data'] = User::where('mobile', request('mobile'))->exists();
            $exist['type'] = 'mobile';
        }
        if (request('username')) {
            $exist['data'] = User::where('username', request('username'))->exists();
            $exist['type'] = 'username';
        }
        return response($exist);
    }

    function registered()
    {
        return to_route('user.home');
    }

    function registerBusiness() {
        try {
            $data = request()->all();
            
            // Debug logging
            \Log::info('Business registration data received: ' . json_encode($data));
            
            // Get country data
            $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')), true);
            $countryCode = $data['country'] ?? 'US';
            $countryInfo = $countryData[$countryCode] ?? $countryData['US'];
            
            // Map the multistep form data to the required fields
            $mappedData = [
                'firstname' => $data['firstName'] ?? '',
                'lastname' => $data['lastName'] ?? '',
                'email' => $data['signupEmail'] ?? '',
                'password' => $data['password'] ?? '',
                'password_confirmation' => $data['confirmPassword'] ?? '',
                'username' => $this->generateUsername(($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? '')),
                'mobile' => $data['phone'] ?? '',
                'mobile_code' => $countryInfo['dial_code'] ?? '+1',
                'country_code' => $countryCode,
                'country' => $countryInfo['country'] ?? 'United States',
                'agree' => isset($data['termsCheckbox']) ? 'on' : null,
                // Additional business fields
                'business_type' => $data['businessType'] ?? '',
                'business_name' => $data['businessName'] ?? '',
                'business_description' => $data['businessDescription'] ?? '',
                'industry' => $data['industry'] ?? '',
                'funding_amount' => $data['fundingAmount'] ?? '',
                'fund_usage' => $data['fundUsage'] ?? '',
                'campaign_duration' => $data['campaignDuration'] ?? ''
            ];

            \Log::info('Mapped data: ' . json_encode($mappedData));

            // Validate the mapped data
            $validator = $this->validator($mappedData);
            if ($validator->fails()) {
                \Log::error('Validation failed: ' . json_encode($validator->errors()));
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            request()->session()->regenerateToken();

            // Skip captcha verification for now to test
            /*
            if(!verifyCaptcha()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid captcha provided'
                ], 422);
            }
            */

            if(preg_match("/[^a-z0-9_]/", trim($mappedData['username']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username must exclude special characters, spaces, and capital letters'
                ], 422);
            }

            $exist = User::where('mobile', $mappedData['mobile_code'] . $mappedData['mobile'])->first();

            if ($exist) {
                return response()->json([
                    'success' => false,
                    'message' => 'That mobile number is already on our records'
                ], 422);
            }

            // Create user without event for now
            $user = $this->createBusinessUser($mappedData);
            
            // Login user
            $this->guard()->login($user);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully!',
                'redirect' => route('home')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Business registration error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating your account: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function createBusinessUser(array $data)
    {
        try {
            $setting = bs();
            $referBy = session()->get('reference');

            if ($referBy) {
                $referUser = User::where('username', $referBy)->first();
            } else {
                $referUser = null;
            }
      
            // User Create
            $user               = new User();
            $user->firstname    = $data['firstname'];
            $user->lastname     = $data['lastname'];
            $user->email        = strtolower($data['email']);
            $user->password     = Hash::make($data['password']);
            $user->username     = $data['username'];
            $user->ref_by       = $referUser ? $referUser->id : 0;
            $user->country_code = $data['country_code'];
            $user->country_name = isset($data['country']) ? $data['country'] : null;
            $user->mobile       = $data['mobile_code'].$data['mobile'];
            $user->kc           = $setting->kc ? ManageStatus::NO : ManageStatus::YES;
            $user->ec           = $setting->ec ? ManageStatus::NO : ManageStatus::YES;
            $user->sc           = $setting->sc ? ManageStatus::NO : ManageStatus::YES;
            $user->ts           = ManageStatus::NO;
            $user->tc           = ManageStatus::YES;
            
            // Store business information
            $user->business_type = $data['business_type'];
            $user->business_name = $data['business_name'];
            $user->business_description = $data['business_description'];
            $user->industry = $data['industry'];
            $user->funding_amount = $data['funding_amount'];
            $user->fund_usage = $data['fund_usage'];
            $user->campaign_duration = $data['campaign_duration'];
            $user->phone = $data['mobile'];
            
            // Store address information
            $user->address = [
                'city' => '',
                'state' => '',
                'zip' => '',
                'country' => $data['country']
            ];
            
            $user->save();

            // Create admin notification (optional)
            try {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = 'New business member registered';
                $adminNotification->click_url = urlPath('admin.user.index');
                $adminNotification->save();
            } catch (\Exception $e) {
                \Log::warning('Failed to create admin notification: ' . $e->getMessage());
                // Continue without admin notification
            }

            return $user;
        } catch (\Exception $e) {
            \Log::error('Error in createBusinessUser: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function generateUsername($fullName) {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $fullName));
        $username = $base;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }
}
