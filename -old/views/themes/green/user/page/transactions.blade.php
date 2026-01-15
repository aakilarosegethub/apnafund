@extends($activeTheme . 'layouts.dashboard')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="row gy-3 align-items-end mb-4">
                            <div class="col-xl-6 col-lg-5 col-sm-6 col-xsm-6">
                                <label class="form--label">@lang('Transaction Number')</label>
                                <input type="text" class="form--control" name="search" value="{{ request('search') }}">
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-6 col-xsm-6">
                                <label class="form--label">@lang('Remark')</label>
                                <select class="form--control form-select" name="remark">
                                    <option value="">@lang('Any')</option>

                                    @foreach ($remarks as $remark)
                                        <option value="{{ $remark->remark }}" @selected(request('remark') == $remark->remark)>
                                            {{ __(keyToTitle($remark->remark)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-3">
                                <button type="submit" class="btn btn--base w-100">@lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped table-borderless table--responsive--xl">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Trx')</th>
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Post Balance')</th>
                                <th>@lang('Reward')</th>
                                <th>@lang('Details')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>
                                        {{ @$transactions->firstItem() + $loop->index }}
                                    </td>
                                    <td>{{ @$transaction->trx }}</td>
                                    <td>
                                        <span>
                                            <span class="d-block">{{ showDateTime(@$transaction->created_at) }}</span>
                                            <span class="d-block">{{ diffForHumans(@$transaction->created_at) }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="@if ($transaction->trx_type == '+') text--success @else text--danger @endif">
                                            {{ showAmount(@$transaction->amount) . ' ' . __($setting->site_cur) }}
                                        </span>
                                    </td>
                                    <td>{{ showAmount(@$transaction->post_balance) . ' ' . __($setting->site_cur) }}</td>
                                    <td>
                                        @if(@$transaction->reward_id && @$transaction->reward)
                                            <span class="badge {{ @$transaction->reward_fulfilled ? 'badge--success' : 'badge--warning' }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __(@$transaction->reward->title) }}">
                                                <i class="ti ti-gift"></i> @lang('Reward')
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ __(strLimit(@$transaction->reward->title, 20)) }}</small>
                                            <br>
                                            @if(@$transaction->reward_fulfilled)
                                                <small class="text-success"><i class="ti ti-check"></i> @lang('Fulfilled')</small>
                                            @else
                                                <small class="text-warning"><i class="ti ti-clock"></i> @lang('Pending')</small>
                                            @endif
                                        @elseif(@$transaction->deposit && @$transaction->deposit->reward_id && @$transaction->deposit->reward)
                                            <span class="badge badge--warning" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __(@$transaction->deposit->reward->title) }}">
                                                <i class="ti ti-gift"></i> @lang('Reward')
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ __(strLimit(@$transaction->deposit->reward->title, 20)) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ __(@$transaction->details) }}</div>
                                        @if((@$transaction->reward_id && @$transaction->reward) || (@$transaction->deposit && @$transaction->deposit->reward_id && @$transaction->deposit->reward))
                                            @php
                                                $reward = @$transaction->reward ?: @$transaction->deposit->reward;
                                                $campaign = @$transaction->deposit->campaign;
                                            @endphp
                                            <a href="javascript:void(0)" class="btn btn--sm btn--base mt-1 rewardInfoBtn" 
                                               data-reward_title="{{ __(@$reward->title) }}"
                                               data-reward_description="{{ __(@$reward->description) }}"
                                               data-reward_minimum="{{ $setting->cur_sym . showAmount(@$reward->minimum_amount) }}"
                                               data-campaign_name="{{ @$campaign ? __(@$campaign->name) : '' }}"
                                               data-reward_fulfilled="{{ @$transaction->reward_fulfilled ? '1' : '0' }}"
                                               data-reward_fulfilled_at="{{ @$transaction->reward_fulfilled_at ? showDateTime(@$transaction->reward_fulfilled_at) : '' }}"
                                               data-reward_fulfillment_note="{{ @$transaction->reward_fulfillment_note }}">
                                                <i class="ti ti-info-circle"></i> @lang('Reward Info')
                                            </a>
                                        @endif
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
    </div>

    {{-- Reward Info Modal --}}
    <div class="modal custom--modal fade" id="rewardInfoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5"><i class="ti ti-gift"></i> @lang('Reward Information')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group rewardInfoData"></ul>
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

            // Reward info button
            $('.rewardInfoBtn').on('click', function() {
                let modal = $('#rewardInfoModal')
                let html  = ''

                if($(this).data('reward_title')) {
                    const isFulfilled = $(this).data('reward_fulfilled') == '1';
                    const fulfilledAt = $(this).data('reward_fulfilled_at') || '';
                    const fulfillmentNote = $(this).data('reward_fulfillment_note') || '';
                    
                    html += `<li class="list-group-item">
                                <span class="text--base">@lang('Campaign')</span>
                                <span>${$(this).data('campaign_name') || '-'}</span>
                            </li>
                            <li class="list-group-item">
                                <span class="text--base"><i class="ti ti-gift"></i> @lang('Reward Title')</span>
                                <strong>${$(this).data('reward_title')}</strong>
                            </li>
                            <li class="list-group-item">
                                <span class="text--base">@lang('Reward Description')</span>
                                <p class="mt-2 mb-0">${$(this).data('reward_description') || '-'}</p>
                            </li>
                            <li class="list-group-item">
                                <span class="text--base">@lang('Minimum Amount')</span>
                                <strong class="text--success">${$(this).data('reward_minimum')}</strong>
                            </li>
                            <li class="list-group-item">
                                <span class="text--base">@lang('Fulfillment Status')</span>
                                ${isFulfilled ? 
                                    '<span class="badge badge--success"><i class="ti ti-check"></i> @lang("Fulfilled")</span>' : 
                                    '<span class="badge badge--warning"><i class="ti ti-clock"></i> @lang("Pending")</span>'
                                }
                            </li>`
                    
                    if(isFulfilled && fulfilledAt) {
                        html += `<li class="list-group-item">
                                    <span class="text--base">@lang('Fulfilled At')</span>
                                    <span>${fulfilledAt}</span>
                                </li>`
                    }
                    
                    if(fulfillmentNote) {
                        html += `<li class="list-group-item">
                                    <span class="text--base">@lang('Fulfillment Note')</span>
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

                modal.find('.rewardInfoData').html(html)
                modal.modal('show')
            })
        })(jQuery)
    </script>
@endpush
