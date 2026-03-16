@extends($activeTheme . 'layouts.dashboard')

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
                                <th>@lang('Reward')</th>
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
                                        @if(@$donation->reward_id && @$donation->reward)
                                            @php
                                                $transaction = \App\Models\Transaction::where('trx', $donation->trx)
                                                    ->where('remark', 'donation_received')
                                                    ->where('user_id', auth()->id())
                                                    ->first();
                                                $isFulfilled = @$transaction && @$transaction->reward_fulfilled;
                                            @endphp
                                            <span class="badge {{ $isFulfilled ? 'badge--success' : 'badge--warning' }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __(@$donation->reward->title) }}">
                                                <i class="ti ti-gift"></i> @lang('Reward')
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ __(strLimit(@$donation->reward->title, 20)) }}</small>
                                            <br>
                                            @if($isFulfilled)
                                                <small class="text-success"><i class="ti ti-check"></i> @lang('Fulfilled')</small>
                                            @else
                                                <small class="text-warning"><i class="ti ti-clock"></i> @lang('Pending')</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        1 {{ $setting->site_cur }} = {{ showAmount(@$donation->rate, 4) . ' ' . __(@$donation->method_currency) }}
                                        <br>
                                        <strong>
                                            {{ showAmount(@$donation->final_amount) . ' ' . __(@$donation->method_currency) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="javascript:void(0)" class="btn btn--icon btn--base detailsBtn" 
                                               data-campaign="{{ @$donation->campaign->name }}" 
                                               data-campaign_url="{{ route('campaign.show', @$donation->campaign->slug) }}" 
                                               data-donor_name="{{ @$donation->donorName }}" 
                                               data-donor_email="{{ @$donation->donorEmail }}" 
                                               data-donor_phone="{{ @$donation->donorPhone }}" 
                                               data-donor_country="{{ @$donation->donorCountry }}"
                                               data-reward_id="{{ @$donation->reward_id }}"
                                               data-reward_title="{{ @$donation->reward ? __(@$donation->reward->title) : '' }}"
                                               data-reward_description="{{ @$donation->reward ? __(@$donation->reward->description) : '' }}"
                                               data-reward_fulfilled="{{ $isFulfilled ? '1' : '0' }}"
                                               data-reward_fulfilled_at="{{ @$transaction && @$transaction->reward_fulfilled_at ? showDateTime(@$transaction->reward_fulfilled_at) : '' }}"
                                               data-reward_fulfillment_note="{{ @$transaction ? @$transaction->reward_fulfillment_note : '' }}">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                            @if(@$donation->reward_id && @$donation->reward && !$isFulfilled)
                                                <a href="javascript:void(0)" class="btn btn--icon btn--success fulfillRewardBtn" 
                                                   data-transaction_id="{{ @$transaction ? @$transaction->id : '' }}"
                                                   data-deposit_id="{{ @$donation->id }}"
                                                   data-trx="{{ @$donation->trx }}"
                                                   data-reward_title="{{ __(@$donation->reward->title) }}">
                                                    <i class="ti ti-check"></i>
                                                </a>
                                            @endif
                                        </div>
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
                    <h5 class="modal-title fs-5">@lang('Details of Contribution')</h5>
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

                // Add reward information if available
                if($(this).data('reward_id') && $(this).data('reward_title')) {
                    const isFulfilled = $(this).data('reward_fulfilled') == '1';
                    const fulfilledAt = $(this).data('reward_fulfilled_at') || '';
                    const fulfillmentNote = $(this).data('reward_fulfillment_note') || '';
                    
                    html += `<li class="list-group-item">
                                <span class="element--label text-warning"><i class="ti ti-gift"></i> @lang('Selected Reward'):</span>
                                <div class="mt-2">
                                    <strong>${$(this).data('reward_title')}</strong>
                                    <br>
                                    <small class="text-muted">${$(this).data('reward_description') || ''}</small>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <span class="element--label">@lang('Fulfillment Status'):</span>
                                ${isFulfilled ? 
                                    '<span class="badge badge--success"><i class="ti ti-check"></i> @lang("Fulfilled")</span>' : 
                                    '<span class="badge badge--warning"><i class="ti ti-clock"></i> @lang("Pending")</span>'
                                }
                            </li>`
                    
                    if(isFulfilled && fulfilledAt) {
                        html += `<li class="list-group-item">
                                    <span class="element--label">@lang('Fulfilled At'):</span>
                                    <span>${fulfilledAt}</span>
                                </li>`
                    }
                    
                    if(fulfillmentNote) {
                        html += `<li class="list-group-item">
                                    <span class="element--label">@lang('Fulfillment Note'):</span>
                                    <p class="mt-2 mb-0">${fulfillmentNote}</p>
                                </li>`
                    }
                }

                modal.find('.donationData').html(html)
                modal.modal('show')
            })

            // Fulfill reward button
            $('.fulfillRewardBtn').on('click', function() {
                const transactionId = $(this).data('transaction_id');
                const depositId = $(this).data('deposit_id');
                const trx = $(this).data('trx');
                const rewardTitle = $(this).data('reward_title');
                
                if(!confirm(`@lang('Are you sure you want to mark this reward as fulfilled?')`)) {
                    return;
                }
                
                const note = prompt('@lang("Add a note (optional):")', '');
                
                $.ajax({
                    url: '{{ route("user.reward.fulfill") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        transaction_id: transactionId,
                        deposit_id: depositId,
                        trx: trx,
                        note: note || ''
                    },
                    success: function(response) {
                        if(response.success) {
                            location.reload();
                        } else {
                            alert(response.message || 'Error fulfilling reward');
                        }
                    },
                    error: function() {
                        alert('Error fulfilling reward');
                    }
                });
            })
        })(jQuery)
    </script>
@endpush
