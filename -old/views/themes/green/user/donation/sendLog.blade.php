@extends($activeTheme . 'layouts.dashboard')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div class="d-flex gap-2">
                            <a href="{{ route('user.donation.history') }}" class="btn btn--sm {{ !request('filter') ? 'btn--base' : 'btn--secondary' }}">
                                @lang('All')
                            </a>
                            <a href="{{ route('user.donation.history', ['filter' => 'reward']) }}" class="btn btn--sm {{ request('filter') == 'reward' ? 'btn--base' : 'btn--secondary' }}">
                                <i class="ti ti-gift"></i> @lang('Reward Only')
                            </a>
                        </div>
                        <form action="" method="GET" class="input--group">
                            @if(request('filter'))
                                <input type="hidden" name="filter" value="{{ request('filter') }}">
                            @endif
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
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($deposits as $deposit)
                                <tr>
                                    <td>
                                        {{ @$deposits->firstItem() + $loop->index }}
                                    </td>
                                    <td>
                                        <a href="{{ route('campaign.show', @$deposit->campaign->slug) }}">
                                            <span class="text-overflow-1 text--base">
                                                {{ __(strLimit(@$deposit->campaign->name, 20)) }}
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="text--base">{{ __(@$deposit->gateway->name) }}</span>
                                        <br>
                                        <small data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="@lang('Transaction Number')">
                                            {{ @$deposit->trx }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ showDateTime(@$deposit->created_at) }}
                                        <br>
                                        {{ diffForHumans(@$deposit->created_at) }}
                                    </td>
                                    <td>
                                        {{ $setting->cur_sym . showAmount(@$deposit->amount) }} + <span class="text-danger" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="@lang('Charge')">{{ showAmount(@$deposit->charge) }}</span>
                                        <br>
                                        <strong data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="@lang('Amount With Charge')">
                                            {{ showAmount(@$deposit->amount + @$deposit->charge) . ' ' . __($setting->site_cur) }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if(@$deposit->reward_id && @$deposit->reward)
                                            <span class="badge badge--warning" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __(@$deposit->reward->title) }}">
                                                <i class="ti ti-gift"></i> @lang('Reward')
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ __(strLimit(@$deposit->reward->title, 20)) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        1 {{ $setting->site_cur }} = {{ showAmount(@$deposit->rate, 4) . ' ' . __(@$deposit->method_currency) }}
                                        <br>
                                        <strong>
                                            {{ showAmount(@$deposit->final_amount) . ' ' . __(@$deposit->method_currency) }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if (@$deposit->status == ManageStatus::PAYMENT_PENDING)
                                            <span class="badge badge--warning">@lang('Pending')</span>
                                        @elseif (@$deposit->status == ManageStatus::PAYMENT_SUCCESS)
                                            <span class="badge badge--success">@lang('Succeeded')</span>
                                        @elseif (@$deposit->status == ManageStatus::PAYMENT_CANCEL)
                                            <span class="badge badge--danger">@lang('Canceled')</span>
                                        @else
                                            <span class="badge badge--secondary">@lang('Initiated')</span>
                                        @endif
                                    </td>

                                    @php
                                        $details = $deposit->details != null ? json_encode($deposit->details) : null;
                                    @endphp

                                    <td>
                                        <a href="javascript:void(0)" class="btn btn--icon btn--base @if ($deposit->method_code >= 1000) detailsBtn @else rewardDetailsBtn @endif" 
                                           @if ($deposit->method_code >= 1000) 
                                               data-info="{{ $details }}" 
                                               data-url="{{ route('user.file.download', ['filePath' => 'verify']) }}" 
                                           @endif 
                                           @if ($deposit->status == ManageStatus::PAYMENT_CANCEL) 
                                               data-admin_feedback="{{ $deposit->admin_feedback }}" 
                                           @endif
                                           @if(@$deposit->reward_id && @$deposit->reward)
                                               data-reward_id="{{ @$deposit->reward_id }}"
                                               data-reward_title="{{ __(@$deposit->reward->title) }}"
                                               data-reward_description="{{ __(@$deposit->reward->description) }}"
                                               data-reward_minimum="{{ $setting->cur_sym . showAmount(@$deposit->reward->minimum_amount) }}"
                                           @endif>
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

                    @if ($deposits->hasPages())
                        {{ $deposits->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Details Modal --}}
    <div class="modal custom--modal fade" id="detailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5">@lang('Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData"></ul>
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

            $('.detailsBtn').on('click', function() {
                let modal    = $('#detailsModal')
                let userData = $(this).data('info')
                let html     = ''

                if (userData) {
                    let fileDownloadUrl = $(this).data('url')

                    userData.forEach(element => {
                        if (!element.value) return

                        if (element.type != 'file') {
                            html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>${element.name}</span>
                                        <span>${element.value}</span>
                                    </li>`
                        } else {
                            html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>${element.name}</span>
                                        <span>
                                            <a href="${fileDownloadUrl}&fileName=${element.value}">
                                                <i class="ti ti-circle-arrow-down"></i> @lang('Attachment')
                                            </a>
                                        </span>
                                    </li>`
                        }
                    })
                }

                if ($(this).data('admin_feedback') != undefined) {
                    html += `<li class="list-group-item">
                                <span class="text--base">@lang('Admin Feedback')</span>
                                <p class="mt-2 mb-0 fs-16">${$(this).data('admin_feedback')}</p>
                            </li>`
                }

                modal.find('.userData').html(html)
                modal.modal('show')
            })

            // Reward details button
            $('.rewardDetailsBtn').on('click', function() {
                let modal = $('#detailsModal')
                let html  = ''

                // Add reward information if available
                if($(this).data('reward_id') && $(this).data('reward_title')) {
                    html += `<li class="list-group-item">
                                <span class="text--base"><i class="ti ti-gift"></i> @lang('Selected Reward')</span>
                                <div class="mt-2">
                                    <strong>${$(this).data('reward_title')}</strong>
                                    <br>
                                    <small class="text-muted">${$(this).data('reward_description') || ''}</small>
                                    <br>
                                    <small class="text-muted">@lang('Minimum Amount'): ${$(this).data('reward_minimum')}</small>
                                </div>
                            </li>`
                }

                modal.find('.userData').html(html)
                modal.modal('show')
            })
        })(jQuery)
    </script>
@endpush
