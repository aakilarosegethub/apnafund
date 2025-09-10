@props(['campaigns' => []])

<div class="input--group">
    <select class="form--control form--control--sm" name="campaign">
        <option value="">@lang('All Campaigns')</option>
        @foreach($campaigns as $campaign)
            <option value="{{ $campaign->id }}" {{ request('campaign') == $campaign->id ? 'selected' : '' }}>
                {{ __($campaign->name) }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn--sm btn--icon btn--base">
        <i class="ti ti-search"></i>
    </button>
</div>
