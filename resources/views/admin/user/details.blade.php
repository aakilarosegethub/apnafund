@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="row g-lg-4 g-3">
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.transaction.index') }}?search={{ $user->username }}" class="dashboard-widget-4">
                    <div class="dashboard-widget-4__content">
                        <div class="dashboard-widget-4__icon">
                            <i class="ti ti-wallet"></i>
                        </div>
                        <p class="dashboard-widget-4__txt">@lang('Balance')</p>
                    </div>
                    <h3 class="dashboard-widget-4__number">{{ showAmount($user->balance) . ' ' . __($setting->site_cur) }}</h3>
                    <div class="dashboard-widget-4__vector">
                        <i class="ti ti-wallet"></i>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.donations.index') }}?search={{ $user->id }}" class="dashboard-widget-4 dashboard-widget-4__success">
                    <div class="dashboard-widget-4__content">
                        <div class="dashboard-widget-4__icon">
                            <i class="ti ti-moneybag"></i>
                        </div>
                        <p class="dashboard-widget-4__txt">@lang('Total Received Donation')</p>
                    </div>
                    <h3 class="dashboard-widget-4__number">{{ showAmount($totalReceivedDonation) . ' ' . __($setting->site_cur) }}</h3>
                    <div class="dashboard-widget-4__vector">
                        <i class="ti ti-moneybag"></i>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.withdraw.index') }}?search={{ $user->username }}" class="dashboard-widget-4 dashboard-widget-4__warning">
                    <div class="dashboard-widget-4__content">
                        <div class="dashboard-widget-4__icon">
                            <i class="ti ti-building-bank"></i>
                        </div>
                        <p class="dashboard-widget-4__txt">@lang('Total Withdrawals')</p>
                    </div>
                    <h3 class="dashboard-widget-4__number">{{ showAmount($totalWithdrawal) . ' ' . __($setting->site_cur) }}</h3>
                    <div class="dashboard-widget-4__vector">
                        <i class="ti ti-building-bank"></i>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.donations.index') }}?search={{ $user->username }}" class="dashboard-widget-4 dashboard-widget-4__info">
                    <div class="dashboard-widget-4__content">
                        <div class="dashboard-widget-4__icon">
                            <i class="ti ti-cash"></i>
                        </div>
                        <p class="dashboard-widget-4__txt">@lang('Total Given Donation')</p>
                    </div>
                    <h3 class="dashboard-widget-4__number">{{ showAmount($totalGivenDonation) . ' ' . __($setting->site_cur) }}</h3>
                    <div class="dashboard-widget-4__vector">
                        <i class="ti ti-cash"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="row g-lg-4 g-3">
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.transaction.index') }}?search={{ $user->username }}" class="dashboard-widget-1">
                    <div class="dashboard-widget-1__icon">
                        <i class="ti ti-arrows-right-left"></i>
                    </div>
                    <div class="dashboard-widget-1__content">
                        <h3 class="dashboard-widget-1__number">{{ $totalTransactions }}</h3>
                        <p class="dashboard-widget-1__txt">@lang('Total Transactions')</p>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.campaigns.approved') }}?search={{ $user->username }}" class="dashboard-widget-1 dashboard-widget-1__info">
                    <div class="dashboard-widget-1__icon">
                        <i class="ti ti-circle-check"></i>
                    </div>
                    <div class="dashboard-widget-1__content">
                        <h3 class="dashboard-widget-1__number">{{ $totalApprovedCampaigns }}</h3>
                        <p class="dashboard-widget-1__txt">@lang('Total Approved Campaigns')</p>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.campaigns.pending') }}?search={{ $user->username }}" class="dashboard-widget-1 dashboard-widget-1__warning">
                    <div class="dashboard-widget-1__icon">
                        <i class="ti ti-hourglass"></i>
                    </div>
                    <div class="dashboard-widget-1__content">
                        <h3 class="dashboard-widget-1__number">{{ $totalPendingCampaigns }}</h3>
                        <p class="dashboard-widget-1__txt">@lang('Total Pending Campaigns')</p>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.campaigns.rejected') }}?search={{ $user->username }}" class="dashboard-widget-1 dashboard-widget-1__danger">
                    <div class="dashboard-widget-1__icon">
                        <i class="ti ti-circle-x"></i>
                    </div>
                    <div class="dashboard-widget-1__content">
                        <h3 class="dashboard-widget-1__number">{{ $totalRejectedCampaigns }}</h3>
                        <p class="dashboard-widget-1__txt">@lang('Total Rejected Campaigns')</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Information About') {{ $user->fullname }}</h3>
            </div>
            <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label required">@lang('First Name')</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form--control" name="firstname" value="{{ $user->firstname }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label required">@lang('Last Name')</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form--control" name="lastname" value="{{ $user->lastname }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label required">@lang('Email')</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="email" class="form--control" name="email" value="{{ $user->email }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label required">@lang('Country')</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form--control form-select select-2" name="country" required>
                                        @foreach($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}">{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label required">@lang('Mobile')</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input--group">
                                        <span class="input-group-text mobile-code"></span>
                                        <input type="number" class="form--control" name="mobile" id="mobile" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label">@lang('City')</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form--control" name="city" value="{{ @$user->address->city }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label">@lang('State')</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form--control" name="state" value="{{ @$user->address->state }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label">@lang('Zip Code')</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form--control" name="zip" value="{{ @$user->address->zip }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top">
                    <div class="row gy-3 checkbox-separator">
                        <div class="col-xl-3 col-sm-6">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <label class="col-form--label required">@lang('Email Confirmation')</label>
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="ec" @if($user->ec) checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <label class="col-form--label required">@lang('Mobile Confirmation')</label>
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="sc" @if($user->sc) checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <label class="col-form--label required">@lang('2FA Confirmation')</label>
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="ts" @if($user->ts) checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <label class="col-form--label required">@lang('KYC Confirmation')</label>
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="kc" @if($user->kc == ManageStatus::VERIFIED) checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top">
                    <div class="d-flex justify-content-center">
                        <button class="btn btn--base px-4" type="submit">@lang('Submit')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Business Information Section -->
    @if($user->business_type || $user->business_name || $user->industry)
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Business Information')</h3>
            </div>
            <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label">@lang('Business Type')</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form--control" name="business_type">
                                        <option value="">Select business type</option>
                                        <option value="startup" {{ $user->business_type == 'startup' ? 'selected' : '' }}>Startup</option>
                                        <option value="small-business" {{ $user->business_type == 'small-business' ? 'selected' : '' }}>Small Business</option>
                                        <option value="nonprofit" {{ $user->business_type == 'nonprofit' ? 'selected' : '' }}>Non-Profit Organization</option>
                                        <option value="creative-project" {{ $user->business_type == 'creative-project' ? 'selected' : '' }}>Creative Project</option>
                                        <option value="tech-company" {{ $user->business_type == 'tech-company' ? 'selected' : '' }}>Technology Company</option>
                                        <option value="manufacturing" {{ $user->business_type == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                        <option value="retail" {{ $user->business_type == 'retail' ? 'selected' : '' }}>Retail Business</option>
                                        <option value="service" {{ $user->business_type == 'service' ? 'selected' : '' }}>Service Business</option>
                                        <option value="other" {{ $user->business_type == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label">@lang('Business Name')</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form--control" name="business_name" value="{{ $user->business_name }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label">@lang('Industry')</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form--control" name="industry">
                                        <option value="">Select industry</option>
                                        <option value="technology" {{ $user->industry == 'technology' ? 'selected' : '' }}>Technology</option>
                                        <option value="healthcare" {{ $user->industry == 'healthcare' ? 'selected' : '' }}>Healthcare</option>
                                        <option value="education" {{ $user->industry == 'education' ? 'selected' : '' }}>Education</option>
                                        <option value="finance" {{ $user->industry == 'finance' ? 'selected' : '' }}>Finance</option>
                                        <option value="retail" {{ $user->industry == 'retail' ? 'selected' : '' }}>Retail</option>
                                        <option value="manufacturing" {{ $user->industry == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                        <option value="food-beverage" {{ $user->industry == 'food-beverage' ? 'selected' : '' }}>Food & Beverage</option>
                                        <option value="creative-arts" {{ $user->industry == 'creative-arts' ? 'selected' : '' }}>Creative Arts</option>
                                        <option value="environmental" {{ $user->industry == 'environmental' ? 'selected' : '' }}>Environmental</option>
                                        <option value="other" {{ $user->industry == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label">@lang('Funding Amount')</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form--control" name="funding_amount">
                                        <option value="">Select funding amount</option>
                                        <option value="under-10k" {{ $user->funding_amount == 'under-10k' ? 'selected' : '' }}>Under $10,000</option>
                                        <option value="10k-50k" {{ $user->funding_amount == '10k-50k' ? 'selected' : '' }}>$10,000 - $50,000</option>
                                        <option value="50k-100k" {{ $user->funding_amount == '50k-100k' ? 'selected' : '' }}>$50,000 - $100,000</option>
                                        <option value="100k-500k" {{ $user->funding_amount == '100k-500k' ? 'selected' : '' }}>$100,000 - $500,000</option>
                                        <option value="500k-1m" {{ $user->funding_amount == '500k-1m' ? 'selected' : '' }}>$500,000 - $1,000,000</option>
                                        <option value="over-1m" {{ $user->funding_amount == 'over-1m' ? 'selected' : '' }}>Over $1,000,000</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label">@lang('Fund Usage')</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form--control" name="fund_usage">
                                        <option value="">Select primary use</option>
                                        <option value="product-development" {{ $user->fund_usage == 'product-development' ? 'selected' : '' }}>Product Development</option>
                                        <option value="marketing" {{ $user->fund_usage == 'marketing' ? 'selected' : '' }}>Marketing & Advertising</option>
                                        <option value="equipment" {{ $user->fund_usage == 'equipment' ? 'selected' : '' }}>Equipment & Infrastructure</option>
                                        <option value="inventory" {{ $user->fund_usage == 'inventory' ? 'selected' : '' }}>Inventory & Supplies</option>
                                        <option value="expansion" {{ $user->fund_usage == 'expansion' ? 'selected' : '' }}>Business Expansion</option>
                                        <option value="research" {{ $user->fund_usage == 'research' ? 'selected' : '' }}>Research & Development</option>
                                        <option value="operating-costs" {{ $user->fund_usage == 'operating-costs' ? 'selected' : '' }}>Operating Costs</option>
                                        <option value="other" {{ $user->fund_usage == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4">
                                    <label class="col-form--label">@lang('Campaign Duration')</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form--control" name="campaign_duration">
                                        <option value="">Select campaign length</option>
                                        <option value="30-days" {{ $user->campaign_duration == '30-days' ? 'selected' : '' }}>30 days</option>
                                        <option value="60-days" {{ $user->campaign_duration == '60-days' ? 'selected' : '' }}>60 days</option>
                                        <option value="90-days" {{ $user->campaign_duration == '90-days' ? 'selected' : '' }}>90 days</option>
                                        <option value="120-days" {{ $user->campaign_duration == '120-days' ? 'selected' : '' }}>120 days</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-2">
                                    <label class="col-form--label">@lang('Business Description')</label>
                                </div>
                                <div class="col-lg-10">
                                    <textarea class="form--control" name="business_description" rows="4">{{ $user->business_description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top">
                    <div class="d-flex justify-content-center">
                        <button class="btn btn--base px-4" type="submit">@lang('Update Business Info')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="col-12">
        <div class="custom--modal modal fade" id="balanceUpdateModal" tabindex="-1" aria-labelledby="balanceUpdateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                 <div class="modal-content">
                      <div class="modal-header">
                           <h2 class="modal-title" id="balanceUpdateModalLabel"></h2>
                           <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x"></i></button>
                      </div>
                      <form action="{{ route('admin.user.add.sub.balance', $user->id) }}" method="POST">
                            @csrf

                            <input type="hidden" name="act">

                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="rol-12">
                                        <label class="form--label required">@lang('Amount')</label>
                                        <div class="input--group">
                                            <input type="number" step="any" min="0" class="form--control form--control--sm" name="amount" placeholder="@lang('Kindly enter an amount that is positive')" required>
                                            <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form--label required">@lang('Remark')</label>
                                        <textarea class="form--control form--control--sm" name="remark" placeholder="@lang('Remark')" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer gap-2">
                                <button type="button" class="btn btn--sm btn--secondary" data-bs-dismiss="modal">@lang('Close')</button>
                                <button class="btn btn--sm btn--base" type="submit">@lang('Submit')</button>
                            </div>
                      </form>
                 </div>
            </div>
       </div>
    </div>

    <div class="col-12">
        <div class="custom--modal modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                 <div class="modal-content">
                    <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x"></i></button>

                    <form action="{{ route('admin.user.status', $user->id) }}" method="POST">
                        @csrf
                        <div class="modal-body text-center modal-alert">
                            <div class="modal-thumb">
                                <img src="{{ asset('assets/admin/images/light.png') }}" alt="Image">
                            </div>
                            <h2 class="modal-title" id="statusModalLabel">
                                {{ $user->status ? trans('Ban User') : trans('Unban User') }}
                            </h2>
                            <p class="mb-3">
                                @if ($user->status)
                                    @lang('Banning this user will restrict their access to the dashboard').
                                @else
                                    @lang('Do you confirm the action to unban on this user')?
                                @endif
                            </p>

                            @if ($user->status)
                                <label class="form--label required">@lang('Reason') :</label>
                                <textarea class="form--control form--control--sm mb-3" name="ban_reason" required></textarea>
                            @else
                                <b class="mb-2">@lang('Banning reason was')</b>
                                <p class="mb-4">{{ __($user->ban_reason) }}</p>
                            @endif

                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn--sm btn--secondary" data-bs-dismiss="modal">@lang('No')</button>
                                <button class="btn btn--sm btn--base" type="submit">@lang('Yes')</button>
                            </div>
                        </div>
                    </form>
                 </div>
            </div>
       </div>
    </div>
@endsection

@push('breadcrumb')
    <div class="custom--dropdown">
        <button class="btn btn--sm btn--icon btn--base" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ti ti-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="{{route('admin.user.login', $user->id)}}" target="_blank" class="dropdown-item text--info"><span class="dropdown-icon"><i class="ti ti-login-2"></i></span> @lang('Login as User')</a>
            </li>
            <li>
                <button type="button" class="dropdown-item text--success balanceUpdateBtn" data-act="add"><span class="dropdown-icon"><i class="ti ti-circle-plus"></i></span> @lang('Add Balance')</button>
            </li>
            <li>
                <button type="button" class="dropdown-item text--warning balanceUpdateBtn" data-act="sub"><span class="dropdown-icon"><i class="ti ti-circle-minus"></i></span> @lang('Sub Balance')</button>
            </li>
            <li>
                @if ($user->status)
                    <button type="button" class="dropdown-item text--danger" data-bs-toggle="modal" data-bs-target="#statusModal">
                        <span class="dropdown-icon"><i class="ti ti-user-cancel"></i></span> @lang('Ban User')
                    </button>
                @else
                    <button type="button" class="dropdown-item text--base" data-bs-toggle="modal" data-bs-target="#statusModal">
                        <span class="dropdown-icon"><i class="ti ti-user-check"></i></span> @lang('Unban User')
                    </button>
                @endif
            </li>
        </ul>
    </div>
@endpush

@push('page-script')
    <script>
        (function($){
            "use strict";

            $('.balanceUpdateBtn').on('click', function () {
                let modal = $('#balanceUpdateModal');
                let act   = $(this).data('act');

                modal.find('[name=act]').val(act);

                if (act === 'add') {
                    modal.find('.modal-title').text(`@lang('Add Balance')`);
                } else {
                    modal.find('.modal-title').text(`@lang('Subtract Balance')`);
                }

                modal.modal('show');
            });

            let mobileElement = $('.mobile-code');

            $('[name=country]').change(function() {
                mobileElement.text(`+${$('[name=country] :selected').data('mobile_code')}`);
            });

            $('[name=country]').val('{{@$user->country_code}}');

            let dialCode     = $('[name=country] :selected').data('mobile_code');
            let mobileNumber = `{{ $user->mobile }}`;
            mobileNumber     = mobileNumber.replace(dialCode, '');

            $('[name=mobile]').val(mobileNumber);

            mobileElement.text(`+${dialCode}`);
        })(jQuery);
    </script>
@endpush
