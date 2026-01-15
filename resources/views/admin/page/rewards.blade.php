@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">@lang('Rewards Tracking')</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.rewards.tracking', ['filter' => 'all']) }}" class="btn btn--sm {{ $filter == 'all' ? 'btn--base' : 'btn--secondary' }}">
                            @lang('All')
                        </a>
                        <a href="{{ route('admin.rewards.tracking', ['filter' => 'received']) }}" class="btn btn--sm {{ $filter == 'received' ? 'btn--base' : 'btn--secondary' }}">
                            <i class="ti ti-arrow-down"></i> @lang('Received')
                        </a>
                        <a href="{{ route('admin.rewards.tracking', ['filter' => 'paid']) }}" class="btn btn--sm {{ $filter == 'paid' ? 'btn--base' : 'btn--secondary' }}">
                            <i class="ti ti-arrow-up"></i> @lang('Paid')
                        </a>
                        <a href="{{ route('admin.rewards.tracking', ['filter' => 'pending']) }}" class="btn btn--sm {{ $filter == 'pending' ? 'btn--base' : 'btn--secondary' }}">
                            <i class="ti ti-clock"></i> @lang('Pending')
                        </a>
                        <a href="{{ route('admin.rewards.tracking', ['filter' => 'fulfilled']) }}" class="btn btn--sm {{ $filter == 'fulfilled' ? 'btn--base' : 'btn--secondary' }}">
                            <i class="ti ti-check"></i> @lang('Fulfilled')
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table--striped table-borderless table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('S.N.')</th>
                            <th>@lang('Campaign')</th>
                            <th>@lang('Reward')</th>
                            <th>@lang('Creator')</th>
                            <th>@lang('Contributor')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Transaction')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>
                                    {{ @$transactions->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    @if(@$transaction->deposit && @$transaction->deposit->campaign)
                                        <a href="{{ route('admin.campaigns.details', @$transaction->deposit->campaign->id) }}">
                                            <span class="text-overflow-1 text--base">
                                                {{ __(strLimit(@$transaction->deposit->campaign->name, 25)) }}
                                            </span>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $reward = @$transaction->reward ?: (@$transaction->deposit && @$transaction->deposit->reward ? @$transaction->deposit->reward : null);
                                    @endphp
                                    @if($reward)
                                        <span class="badge {{ @$transaction->reward_fulfilled ? 'badge--success' : 'badge--warning' }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __($reward->title) }}">
                                            <i class="ti ti-gift"></i> {{ __(strLimit($reward->title, 20)) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ __(strLimit($reward->description, 30)) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(@$transaction->deposit && @$transaction->deposit->campaign && @$transaction->deposit->campaign->user)
                                        <a href="{{ route('admin.user.details', @$transaction->deposit->campaign->user->id) }}">
                                            {{ __(@$transaction->deposit->campaign->user->fullname) }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(@$transaction->deposit && @$transaction->deposit->user)
                                        <a href="{{ route('admin.user.details', @$transaction->deposit->user->id) }}">
                                            {{ __(@$transaction->deposit->user->fullname) }}
                                        </a>
                                    @elseif(@$transaction->user)
                                        <a href="{{ route('admin.user.details', @$transaction->user->id) }}">
                                            {{ __(@$transaction->user->fullname) }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $setting->cur_sym . showAmount(@$transaction->amount) }}
                                </td>
                                <td>
                                    <small class="text-muted">{{ @$transaction->trx }}</small>
                                </td>
                                <td>
                                    {{ showDateTime(@$transaction->created_at) }}
                                    <br>
                                    <small class="text-muted">{{ diffForHumans(@$transaction->created_at) }}</small>
                                </td>
                                <td>
                                    @if(@$transaction->remark == 'donation_received')
                                        @if(@$transaction->reward_fulfilled)
                                            <span class="badge badge--success">
                                                <i class="ti ti-check"></i> @lang('Fulfilled')
                                            </span>
                                        @else
                                            <span class="badge badge--warning">
                                                <i class="ti ti-clock"></i> @lang('Pending')
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge badge--info">
                                            <i class="ti ti-gift"></i> @lang('Reward Selected')
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn--icon btn--base rewardDetailsBtn" 
                                       data-reward_title="{{ $reward ? __($reward->title) : '' }}"
                                       data-reward_description="{{ $reward ? __($reward->description) : '' }}"
                                       data-reward_minimum="{{ $reward ? $setting->cur_sym . showAmount($reward->minimum_amount) : '' }}"
                                       data-campaign_name="{{ @$transaction->deposit && @$transaction->deposit->campaign ? __(@$transaction->deposit->campaign->name) : '' }}"
                                       data-creator_name="{{ @$transaction->deposit && @$transaction->deposit->campaign && @$transaction->deposit->campaign->user ? __(@$transaction->deposit->campaign->user->fullname) : '' }}"
                                       data-contributor_name="{{ @$transaction->deposit && @$transaction->deposit->user ? __(@$transaction->deposit->user->fullname) : '' }}"
                                       data-contributor_email="{{ @$transaction->deposit && @$transaction->deposit->user ? __(@$transaction->deposit->user->email) : '' }}"
                                       data-reward_fulfilled="{{ @$transaction->reward_fulfilled ? '1' : '0' }}"
                                       data-reward_fulfilled_at="{{ @$transaction->reward_fulfilled_at ? showDateTime(@$transaction->reward_fulfilled_at) : '' }}"
                                       data-reward_fulfillment_note="{{ @$transaction->reward_fulfillment_note }}"
                                       data-filter="{{ @$transaction->remark }}">
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

                @if ($transactions->hasPages())
                    {{ $transactions->links() }}
                @endif
            </div>
        </div>
    </div>

    {{-- Reward Details Modal --}}
    <div class="modal custom--modal fade" id="rewardDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5"><i class="ti ti-gift"></i> @lang('Reward Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group rewardDetailsData"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--sm btn--secondary" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script>
        (function($) {
            "use strict"

            $('[data-bs-toggle="tooltip"]').each(function(index, element) {
                new bootstrap.Tooltip(element)
            })

            // Reward details button
            $('.rewardDetailsBtn').on('click', function() {
                let modal = $('#rewardDetailsModal')
                let html  = ''
                const filter = $(this).data('filter')

                if($(this).data('campaign_name')) {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="element--label">@lang('Campaign'):</span>
                                <span>${$(this).data('campaign_name')}</span>
                            </li>`
                }

                if($(this).data('creator_name')) {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="element--label">@lang('Creator'):</span>
                                <span>${$(this).data('creator_name')}</span>
                            </li>`
                }

                if($(this).data('contributor_name')) {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="element--label">@lang('Contributor'):</span>
                                <span>${$(this).data('contributor_name')}</span>
                            </li>`
                    if($(this).data('contributor_email')) {
                        html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="element--label">@lang('Contributor Email'):</span>
                                    <span>${$(this).data('contributor_email')}</span>
                                </li>`
                    }
                }

                if($(this).data('reward_title')) {
                    html += `<li class="list-group-item">
                                <span class="element--label"><i class="ti ti-gift"></i> @lang('Reward Title'):</span>
                                <strong>${$(this).data('reward_title')}</strong>
                            </li>
                            <li class="list-group-item">
                                <span class="element--label">@lang('Reward Description'):</span>
                                <p class="mt-2 mb-0">${$(this).data('reward_description') || '-'}</p>
                            </li>
                            <li class="list-group-item">
                                <span class="element--label">@lang('Minimum Amount'):</span>
                                <strong class="text--success">${$(this).data('reward_minimum')}</strong>
                            </li>`
                }

                if(filter == 'donation_received') {
                    const isFulfilled = $(this).data('reward_fulfilled') == '1'
                    const fulfilledAt = $(this).data('reward_fulfilled_at') || ''
                    const fulfillmentNote = $(this).data('reward_fulfillment_note') || ''
                    
                    html += `<li class="list-group-item">
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
                    
                    if(!isFulfilled) {
                        html += `<li class="list-group-item bg-warning bg-opacity-10">
                                    <div class="alert alert-warning mb-0">
                                        <i class="ti ti-alert-triangle"></i> 
                                        <strong>@lang('Note'):</strong> @lang('This reward needs to be fulfilled for the contributor.')
                                    </div>
                                </li>`
                    }
                }

                modal.find('.rewardDetailsData').html(html)
                modal.modal('show')
            })
        })(jQuery)
    </script>
@endpush

@push('page-style')
    <style>
        .element--label {
            font-weight: 700;
            color: hsl(var(--secondary));
        }
    </style>
@endpush

