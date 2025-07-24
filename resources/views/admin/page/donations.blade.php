@extends('admin.layouts.master')

@section('master')
    @if(request()->routeIs('admin.donations.index'))
        <div class="col-12">
            <div class="row g-lg-4 g-3">
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('admin.donations.done') }}" class="dashboard-widget-3 dashboard-widget-3__success bg-img" data-background-image="{{ asset('assets/admin/images/widget-bg.png') }}">
                        <div class="dashboard-widget-3__top">
                            <h3 class="dashboard-widget-3__number">{{ $setting->cur_sym }}{{ showAmount($done) }}</h3>
                            <div class="dashboard-widget-3__icon">
                                <i class="ti ti-circle-check"></i>
                            </div>
                        </div>
                        <p class="dashboard-widget-3__txt">@lang('Done Donation Amount')</p>
                    </a>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('admin.donations.index') }}" class="dashboard-widget-3 dashboard-widget-3__info bg-img" data-background-image="{{ asset('assets/admin/images/widget-bg.png') }}">
                        <div class="dashboard-widget-3__top">
                            <h3 class="dashboard-widget-3__number">{{ $setting->cur_sym }}{{ showAmount($charge) }}</h3>
                            <div class="dashboard-widget-3__icon">
                                <i class="ti ti-coins"></i>
                            </div>
                        </div>
                        <p class="dashboard-widget-3__txt">@lang('Total Charge for Donated Amount')</p>
                    </a>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('admin.donations.pending') }}" class="dashboard-widget-3 dashboard-widget-3__warning bg-img" data-background-image="{{ asset('assets/admin/images/widget-bg.png') }}">
                        <div class="dashboard-widget-3__top">
                            <h3 class="dashboard-widget-3__number">{{ $setting->cur_sym }}{{ showAmount($pending) }}</h3>
                            <div class="dashboard-widget-3__icon">
                                <i class="ti ti-rotate-clockwise-2"></i>
                            </div>
                        </div>
                        <p class="dashboard-widget-3__txt">@lang('Pending Donation Amount')</p>
                    </a>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('admin.donations.cancelled') }}" class="dashboard-widget-3 dashboard-widget-3__danger bg-img" data-background-image="{{ asset('assets/admin/images/widget-bg.png') }}">
                        <div class="dashboard-widget-3__top">
                            <h3 class="dashboard-widget-3__number">{{ $setting->cur_sym }}{{ showAmount($cancelled) }}</h3>
                            <div class="dashboard-widget-3__icon">
                                <i class="ti ti-circle-x"></i>
                            </div>
                        </div>
                        <p class="dashboard-widget-3__txt">@lang('Cancelled Donation Amount')</p>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="col-12">
        <table class="table table--striped table-borderless table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Donor')</th>
                    <th>@lang('Gateway') | @lang('Transaction')</th>
                    <th>@lang('Donation Date')</th>
                    <th>@lang('User Type')</th>
                    <th>@lang('Donation Type')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Conversion')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($deposits as $deposit)
                    <tr>
                        <td>
                            <div class="table-card-with-image">
                                <div class="table-card-with-image__img">
                                    @if($deposit->user && $deposit->donor_type)
                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . $deposit->user->image, getFileSize('userProfile'), true) }}" alt="Image">
                                    @else
                                        <img src="{{ asset('assets/universal/images/avatar.png') }}" alt="Image">
                                    @endif
                                </div>
                                <div class="table-card-with-image__content">
                                    <p class="fw-semibold">{{ __($deposit->donorName) }}</p>

                                    @if($deposit->user && $deposit->donor_type)
                                        <p class="fw-semibold">
                                            <a href="{{ appendQuery('search', $deposit->user->username) }}"> <small>@</small>{{ $deposit->user->username }}</a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <a href="{{ appendQuery('method', $deposit->gateway->alias) }}" class="fw-semibold text--base">{{ __($deposit->gateway->name) }}</a>
                                <p>{{ $deposit->trx }}</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p>{{ showDateTime($deposit->created_at) }}</p>
                                <p>{{ diffForHumans($deposit->created_at) }}</p>
                            </div>
                        </td>
                        <td>
                            @if ($deposit->user_id)
                                <span class="badge badge--success">@lang('Registered')</span>
                            @else
                                <span class="badge badge--primary">@lang('Guest')</span>
                            @endif
                        </td>
                        <td>
                            @if ($deposit->donor_type)
                                <span class="badge badge--success">@lang('Visible')</span>
                            @else
                                <span class="badge badge--primary">@lang('Anonymous')</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <p>{{ $setting->cur_sym . showAmount($deposit->amount) }} + <span class="text--danger" title="@lang('Charge')">{{ __($setting->cur_sym) }}{{ showAmount($deposit->charge) }}</span></p>
                                <p class="fw-semibold" title="Amount With Charge">{{ showAmount($deposit->amount + $deposit->charge) . ' ' . __($setting->site_cur) }}</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p>1 {{ __($setting->site_cur) }} = {{ showAmount($deposit->rate, 4) . ' ' . __($deposit->method_currency) }}</p>
                                <p class="fw-semibold">{{ showAmount($deposit->final_amount) . ' ' . __($deposit->method_currency) }}</p>
                            </div>
                        </td>
                        <td>
                            @if ($deposit->status == ManageStatus::PAYMENT_PENDING)
                                <span class="badge badge--warning">@lang('Pending')</span>
                            @elseif ($deposit->status == ManageStatus::PAYMENT_SUCCESS)
                                <span class="badge badge--success">@lang('Succeeded')</span>
                            @elseif ($deposit->status == ManageStatus::PAYMENT_CANCEL)
                                <span class="badge badge--danger">@lang('Cancelled')</span>
                            @else
                                <span class="badge badge--secondary">@lang('Initiated')</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="#depositDetails" class="btn btn--icon btn--sm btn-outline--base detailBtn" title="@lang('Details')"
                                   data-bs-toggle      = "offcanvas"
                                   data-date           = "{{ showDateTime($deposit->created_at) }}"
                                   data-trx            = "{{ $deposit->trx }}"
                                   data-username       = "{{ @$deposit->user->username }}"
                                   data-method         = "{{ __(@$deposit->gateway->name) }}"
                                   data-amount         = "{{ showAmount($deposit->amount) }} {{ __($setting->site_cur) }}"
                                   data-charge         = "{{ showAmount($deposit->charge) }} {{ __($setting->site_cur) }}"
                                   data-after_charge   = "{{ showAmount($deposit->amount + $deposit->charge) }} {{ __($setting->site_cur) }}"
                                   data-rate           = "1 {{ __($setting->site_cur) }} = {{ showAmount($deposit->rate) }} {{ __($deposit->baseCurrency()) }}"
                                   data-donated        = "{{ showAmount($deposit->final_amount) }} {{ __($deposit->method_currency) }}"
                                   data-status         = "{{ $deposit->status }}"
                                   data-user_data      = "{{ json_encode($deposit->detail) }}"
                                   data-admin_feedback = "{{ $deposit->admin_feedback }}"
                                   data-user_type      = "{{ $deposit->user_id }}"
                                   data-donation_type  = "{{ $deposit->donor_type }}"
                                   data-donor_name     = "{{ $deposit->donorName }}"
                                   data-donor_email    = "{{ $deposit->donorEmail }}"
                                   data-donor_phone    = "{{ $deposit->donorPhone }}"
                                   data-donor_country  = "{{ $deposit->donorCountry }}"
                                   data-url            = "{{ route('admin.file.download', ['filePath' => 'verify']) }}"
                                >
                                    <i class="ti ti-eye"></i>
                                </a>

                                @if ($deposit->status == ManageStatus::PAYMENT_PENDING)
                                    <div class="custom--dropdown">
                                        <button type="button" class="btn btn--icon btn--sm btn--base" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button type="button" class="dropdown-item decisionBtn" data-question="@lang('Do you want to approve this donation?')" data-action="{{ route('admin.donations.approve', $deposit->id) }}">
                                                    <span class="dropdown-icon"><i class="ti ti-circle-check text--success"></i></span> @lang('Approve')
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item cancelBtn" data-action="{{ route('admin.donations.reject', $deposit->id) }}">
                                                    <span class="dropdown-icon"><i class="ti ti-circle-x text--danger"></i></span> @lang('Reject')
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    @include('admin.partials.noData')
                @endforelse
            </tbody>
        </table>

        @if ($deposits->hasPages())
            {{ paginateLinks($deposits) }}
        @endif
    </div>

    <div class="col-12">
        <div class="custom--offcanvas offcanvas offcanvas-end" tabindex="-1" id="depositDetails" aria-labelledby="depositDetailsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="depositDetailsLabel">@lang('Donation Details')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <h6 class="mb-2">@lang('Basic Information')</h6>
                <table class="table table-borderless mb-3">
                    <tbody class="basic-details"></tbody>
                </table>

                <div class="user-data"></div>
            </div>
        </div>
    </div>

    <x-decisionModal />

    <div class="col-12">
        <div class="custom--modal modal fade" id="cancelDepositModal" tabindex="-1" aria-labelledby="cancelDepositModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                    <div class="modal-body modal-alert">
                        <div class="text-center">
                            <div class="modal-thumb">
                                <img src="{{ asset('assets/admin/images/light.png') }}" alt="Image">
                            </div>
                            <h2 class="modal-title" id="cancelDepositModalLabel">@lang('Cancel Donation')</h2>
                            <form action="" method="POST">
                                @csrf
                                <label class="form--label">@lang('Reason') :</label>
                                <textarea class="form--control form--control--sm" name="admin_feedback" required></textarea>

                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <button type="button" class="btn btn--sm btn-outline--base" data-bs-dismiss="modal">@lang('No')</button>
                                    <button type="submit" class="btn btn--sm btn--base">@lang('Yes')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb')
    <x-searchForm placeholder="TRX / Username" dateSearch="yes" />
@endpush

@push('page-script')
    <script>
        (function ($) {
            "use strict";

            $('.detailBtn').on('click', function () {
                let donorHtml, donationHtml;

                if ($(this).data('user_type')) {
                    donorHtml = '<span class="badge badge--success">@lang("Registered")</span>';
                } else {
                    donorHtml = '<span class="badge badge--primary">@lang("Guest")</span>';
                }

                if ($(this).data('donation_type')) {
                    donationHtml = '<span class="badge badge--success">@lang('Visible')</span>';
                } else {
                    donationHtml = '<span class="badge badge--primary">@lang('Anonymous')</span>';
                }

                let statusHtml = ``;
                let donationStatus = $(this).data('status');

                if (donationStatus === 2) {
                    statusHtml += `<span class="badge badge--warning">@lang('Pending')</span>`;
                } else if (donationStatus === 1) {
                    statusHtml += `<span class="badge badge--success">@lang('Succeeded')</span>`;
                } else if (donationStatus === 3) {
                    statusHtml += `<span class="badge badge--danger">@lang('Cancelled')</span>`;
                } else {
                    statusHtml += `<span class="badge badge--secondary">@lang('Initiated')</span>`;
                }

                let basicHtml = `<tr>
                                    <td class="fw-bold">@lang('Date')</td>
                                    <td>${$(this).data('date')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Trx Number')</td>
                                    <td>${$(this).data('trx')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Username')</td>
                                    <td>${$(this).data('username')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Method')</td>
                                    <td>${$(this).data('method')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Amount')</td>
                                    <td>${$(this).data('amount')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Charge')</td>
                                    <td>${$(this).data('charge')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('After Charge')</td>
                                    <td>${$(this).data('after_charge')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Rate')</td>
                                    <td>${$(this).data('rate')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Donated')</td>
                                    <td>${$(this).data('donated')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Status')</td>
                                    <td>${statusHtml}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Donor Name')</td>
                                    <td>${$(this).data('donor_name')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Donor Email')</td>
                                    <td>${$(this).data('donor_email')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Donor Phone')</td>
                                    <td>${$(this).data('donor_phone')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Donor Country')</td>
                                    <td>${$(this).data('donor_country')}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('User Type')</td>
                                    <td>${donorHtml}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Donation Type')</td>
                                    <td>${donationHtml}</td>
                                </tr>`;

                if ($(this).data('admin_feedback')) {
                    basicHtml += `<tr>
                                    <td class="fw-bold">@lang('Admin Feedback')</td>
                                    <td>${$(this).data('admin_feedback')}</td>
                                </tr>`;
                }

                $('.basic-details').html(basicHtml);

                let userData = $(this).data('user_data');
                let downloadUrl = $(this).data('url');

                if (userData) {
                    let infoHtml = `<h6 class="mb-2">@lang('Donation User Data')</h6>
                                    <table class="table table-borderless mb-3">
                                        <tbody>`;

                    userData.forEach(element => {
                        if (!element.value) { return; }

                        if (element.type !== 'file') {
                            infoHtml += `<tr>
                                            <td class="fw-bold">${element.name}</td>
                                            <td>${element.value}</td>
                                         </tr>`;
                        } else {
                            infoHtml += `<tr>
                                            <td class="fw-bold">${element.name}</td>
                                            <td>
                                                <a href="${downloadUrl}&fileName=${element.value}" class="btn btn--sm btn-outline--secondary">
                                                    <i class="ti ti-download"></i> @lang('Attachment')
                                                </a>
                                            </td>
                                        </tr>`;
                        }
                    });

                    infoHtml += `</tbody>
                            </table>`;

                    $('.user-data').html(infoHtml);
                } else {
                    $('.user-data').empty();
                }
            });

            $('.cancelBtn').on('click', function () {
                let modal = $('#cancelDepositModal');

                modal.find('form').attr('action', $(this).data('action'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
