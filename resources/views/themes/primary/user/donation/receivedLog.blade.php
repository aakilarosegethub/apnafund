@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <form action="" class="input--group">
                            <input type="text" class="form--control" name="search" value="{{ request('search') }}" placeholder="@lang('Search by transaction')">
                            <button type="submit" class="btn btn--sm btn--base">
                                <i class="ti ti-search"></i>
                            </button>
                        </form>
                    </div>
                    <table class="table table-striped table-borderless table--responsive--xl">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Campaign')</th>
                                <th>@lang('Gateway') | @lang('Trx')</th>
                                <th>@lang('Initiated')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Conversion')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($donations as $donation)
                                <tr>
                                    <td>
                                        {{ @$donations->firstItem() + $loop->index }}
                                    </td>
                                    <td>
                                        <a href="{{ route('campaign.show', @$donation->campaign->slug) }}">
                                            <span class="text-overflow-1 text--base">
                                                {{ __(strLimit(@$donation->campaign->name, 20)) }}
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="text--base">{{ __(@$donation->gateway->name) }}</span>
                                        <br>
                                        <small data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="@lang('Transaction Number')">
                                            {{ @$donation->trx }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ showDateTime(@$donation->created_at) }}
                                        <br>
                                        {{ diffForHumans(@$donation->created_at) }}
                                    </td>
                                    <td>
                                        {{ $setting->cur_sym . showAmount(@$donation->amount) }}
                                    </td>
                                    <td>
                                        1 {{ $setting->site_cur }} = {{ showAmount(@$donation->rate, 4) . ' ' . __(@$donation->method_currency) }}
                                        <br>
                                        <strong>
                                            {{ showAmount(@$donation->final_amount) . ' ' . __(@$donation->method_currency) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" class="btn btn--icon btn--base detailsBtn" data-campaign="{{ @$donation->campaign->name }}" data-campaign_url="{{ route('campaign.show', @$donation->campaign->slug) }}" data-donor_name="{{ @$donation->donorName }}" data-donor_email="{{ @$donation->donorEmail }}" data-donor_phone="{{ @$donation->donorPhone }}" data-donor_country="{{ @$donation->donorCountry }}">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">
                                        {{ __($emptyMessage) }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($donations->hasPages())
                        {{ $donations->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Details Modal --}}
    <div class="modal custom--modal fade" id="detailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5">@lang('Details of Donation')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group donationData"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--sm btn--secondary" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .element--label {
            font-weight: 700;
            color: hsl(var(--secondary));
        }
    </style>
@endpush

@push('page-script')
    <script>
        (function($) {
            "use strict"

            $('[data-bs-toggle="tooltip"]').each(function(index, element) {
                new bootstrap.Tooltip(element)
            })

            $('.detailsBtn').on('click', function() {
                let modal = $('#detailsModal')
                let html  = ''

                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="element--label">@lang('Campaign'):</span>
                            <a href="${$(this).data('campaign_url')}">${$(this).data('campaign')}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="element--label">@lang('Donor Name'):</span>
                            <span>${$(this).data('donor_name')}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="element--label">@lang('Donor Email'):</span>
                            <span>${$(this).data('donor_email')}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="element--label">@lang('Donor Phone'):</span>
                            <span>${$(this).data('donor_phone')}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="element--label">@lang('Donor Country'):</span>
                            <span>${$(this).data('donor_country')}</span>
                        </li>`

                modal.find('.donationData').html(html)
                modal.modal('show')
            })
        })(jQuery)
    </script>
@endpush
