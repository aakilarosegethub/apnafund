@extends('admin.layouts.master')

@section('master')
<div class="col-lg-8 col-xl-6 mx-auto">
    <div class="custom--card">
        <div class="card-header">
            <h3 class="title">{{ __($pageTitle) }}</h3>
        </div>
        <div class="card-body">
            <p class="text-muted mb-2">@lang('Contribution') #{{ $deposit->id }} — <strong>{{ $deposit->trx }}</strong></p>
            @if($deposit->campaign)
                <p class="mb-2">@lang('Campaign'): {{ $deposit->campaign->name }}</p>
            @endif
            <p class="mb-4">@lang('Amount'): {{ showAmount($deposit->amount) }} {{ __($deposit->method_currency) }}</p>

            <form action="{{ route('admin.donations.approve', $deposit->id) }}" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                @csrf
                <button type="submit" class="btn btn--base">@lang('Confirm approve')</button>
                <a href="{{ route('admin.donations.pending') }}" class="btn btn-outline--secondary">@lang('Cancel')</a>
            </form>
        </div>
    </div>
</div>
@endsection
