@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Creator Campaign Fee Settings')</h3>
            </div>
            <div class="card-body">
                <form class="row g-4" action="{{ route('admin.creator-payout.settings.update') }}" method="POST">
                    @csrf
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Platform Fee Type')</label>
                        <select class="form--control form-select" name="platform_fee_type" required>
                            <option value="percentage" {{ $settings->platform_fee_type === 'percentage' ? 'selected' : '' }}>
                                @lang('Percentage')
                            </option>
                            <option value="fixed" {{ $settings->platform_fee_type === 'fixed' ? 'selected' : '' }}>
                                @lang('Fixed Amount')
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Platform Fee Value')</label>
                        <input type="text"
                               class="form--control"
                               name="platform_fee_value"
                               value="{{ $settings->platform_fee_value }}"
                               placeholder="@lang('e.g. 5 or 100')"
                               required>
                        <small class="form-text text-muted">
                            @lang('If percentage, enter a value between 0-100. If fixed, enter currency amount.')
                        </small>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Marketing Fee Percentage')</label>
                        <input type="text"
                               class="form--control"
                               name="marketing_fee_percent"
                               value="{{ $settings->marketing_fee_percent }}"
                               placeholder="@lang('e.g. 2.5')"
                               required>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Chargeback Withholding Percentage')</label>
                        <input type="text"
                               class="form--control"
                               name="chargeback_withholding_percent"
                               value="{{ $settings->chargeback_withholding_percent }}"
                               placeholder="@lang('Default 5')"
                               required>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Proof of Fulfillment Withholding Percentage')</label>
                        <input type="text"
                               class="form--control"
                               name="fulfillment_withholding_percent"
                               value="{{ $settings->fulfillment_withholding_percent }}"
                               placeholder="@lang('30 - 50')"
                               required>
                        <small class="form-text text-muted">@lang('Allowed range: 30% to 50%')</small>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn--base px-4">@lang('Save Settings')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
