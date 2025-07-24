@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="row g-lg-4 g-3">
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.user.index') }}" class="dashboard-widget-1">
                    <div class="dashboard-widget-1__icon">
                        <i class="ti ti-users"></i>
                    </div>
                    <div class="dashboard-widget-1__content">
                        <h3 class="dashboard-widget-1__number">{{ $widget['totalUsers'] }}</h3>
                        <p class="dashboard-widget-1__txt">@lang('Total Users')</p>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.user.active') }}" class="dashboard-widget-1 dashboard-widget-1__info">
                    <div class="dashboard-widget-1__icon">
                        <i class="ti ti-user-check"></i>
                    </div>
                    <div class="dashboard-widget-1__content">
                        <h3 class="dashboard-widget-1__number">{{ $widget['activeUsers'] }}</h3>
                        <p class="dashboard-widget-1__txt">@lang('Active Users')</p>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.user.email.unconfirmed') }}" class="dashboard-widget-1 dashboard-widget-1__warning">
                    <div class="dashboard-widget-1__icon">
                        <i class="ti ti-at"></i>
                    </div>
                    <div class="dashboard-widget-1__content">
                        <h3 class="dashboard-widget-1__number">{{ $widget['emailUnconfirmedUsers'] }}</h3>
                        <p class="dashboard-widget-1__txt">@lang('Email Unconfirmed Users')</p>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.user.mobile.unconfirmed') }}" class="dashboard-widget-1 dashboard-widget-1__danger">
                    <div class="dashboard-widget-1__icon">
                        <i class="ti ti-message-off"></i>
                    </div>
                    <div class="dashboard-widget-1__content">
                        <h3 class="dashboard-widget-1__number">{{ $widget['mobileUnconfirmedUsers'] }}</h3>
                        <p class="dashboard-widget-1__txt">@lang('Mobile Unconfirmed Users')</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="dashboard-widget-row">
            <a href="{{ route('admin.donations.done') }}" class="dashboard-widget-2 dashboard-widget-2__success">
                <div class="dashboard-widget-2__icon">
                    <i class="ti ti-moneybag"></i>
                </div>
                <h3 class="dashboard-widget-2__number">{{ $setting->cur_sym }}{{ showAmount($widget['depositDone']) }}</h3>
                <p class="dashboard-widget-2__txt">@lang('Total Donated Amount')</p>
                <div class="dashboard-widget-2__vector">
                    <img src="{{ asset('assets/admin/images/completed.png') }}" alt="Image">
                </div>
            </a>
            <a href="{{ route('admin.donations.pending') }}" class="dashboard-widget-2 dashboard-widget-2__warning">
                <div class="dashboard-widget-2__icon">
                    <i class="ti ti-rotate-clockwise-2"></i>
                </div>
                <h3 class="dashboard-widget-2__number">{{ $widget['depositPending'] }}</h3>
                <p class="dashboard-widget-2__txt">@lang('Pending Donations')</p>
                <div class="dashboard-widget-2__vector">
                    <img src="{{ asset('assets/admin/images/pending.png') }}" alt="Image">
                </div>
            </a>
            <a href="{{ route('admin.donations.cancelled') }}" class="dashboard-widget-2 dashboard-widget-2__danger">
                <div class="dashboard-widget-2__icon">
                    <i class="ti ti-x"></i>
                </div>
                <h3 class="dashboard-widget-2__number">{{ $widget['depositCancelled'] }}</h3>
                <p class="dashboard-widget-2__txt">@lang('Cancelled Donations')</p>
                <div class="dashboard-widget-2__vector">
                    <img src="{{ asset('assets/admin/images/cancelled.png') }}" alt="Image">
                </div>
            </a>
            <a href="{{ route('admin.donations.index') }}" class="dashboard-widget-2 dashboard-widget-2__info">
                <div class="dashboard-widget-2__icon">
                    <i class="ti ti-coins"></i>
                </div>
                <h3 class="dashboard-widget-2__number">{{ $setting->cur_sym }}{{ showAmount($widget['depositCharge']) }}</h3>
                <p class="dashboard-widget-2__txt">@lang('Total Charge for Donated Amount')</p>
                <div class="dashboard-widget-2__vector">
                    <img src="{{ asset('assets/admin/images/charge.png') }}" alt="Image">
                </div>
            </a>
        </div>
    </div>

    <div class="col-12">
        <div class="row g-lg-4 g-3">
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.campaigns.upcoming') }}" class="dashboard-widget-4">
                    <div class="dashboard-widget-4__content">
                        <div class="dashboard-widget-4__icon">
                            <i class="ti ti-calendar-month"></i>
                        </div>
                        <p class="dashboard-widget-4__txt">@lang('Upcoming Campaigns')</p>
                    </div>
                    <h3 class="dashboard-widget-4__number">{{ $widget['upcomingCampaignCount'] }}</h3>
                    <div class="dashboard-widget-4__vector">
                        <i class="ti ti-calendar-month"></i>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.campaigns.running') }}" class="dashboard-widget-4 dashboard-widget-4__info">
                    <div class="dashboard-widget-4__content">
                        <div class="dashboard-widget-4__icon">
                            <i class="ti ti-run"></i>
                        </div>
                        <p class="dashboard-widget-4__txt text--primary">@lang('Running Campaigns')</p>
                    </div>
                    <h3 class="dashboard-widget-4__number">{{ $widget['runningCampaignCount'] }}</h3>
                    <div class="dashboard-widget-4__vector">
                        <i class="ti ti-run"></i>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.campaigns.pending') }}" class="dashboard-widget-4 dashboard-widget-4__warning">
                    <div class="dashboard-widget-4__content">
                        <div class="dashboard-widget-4__icon">
                            <i class="ti ti-loader"></i>
                        </div>
                        <p class="dashboard-widget-4__txt text--warning">@lang('Pending Campaigns')</p>
                    </div>
                    <h3 class="dashboard-widget-4__number">{{ $widget['pendingCampaignCount'] }}</h3>
                    <div class="dashboard-widget-4__vector">
                        <i class="ti ti-loader"></i>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.campaigns.expired') }}" class="dashboard-widget-4 dashboard-widget-4__danger">
                    <div class="dashboard-widget-4__content">
                        <div class="dashboard-widget-4__icon">
                            <i class="ti ti-battery"></i>
                        </div>
                        <p class="dashboard-widget-4__txt text--danger">@lang('Expired Campaigns')</p>
                    </div>
                    <h3 class="dashboard-widget-4__number">{{ $widget['expiredCampaignCount'] }}</h3>
                    <div class="dashboard-widget-4__vector">
                        <i class="ti ti-battery"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="row g-lg-4 g-3">
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.withdraw.done') }}" class="dashboard-widget-3 bg-img" data-background-image="{{ asset('assets/admin/images/widget-bg.png') }}">
                    <div class="dashboard-widget-3__top">
                        <h3 class="dashboard-widget-3__number">{{ $setting->cur_sym }}{{ showAmount($widget['withdrawDone']) }}</h3>
                        <div class="dashboard-widget-3__icon">
                            <i class="ti ti-cash-banknote"></i>
                        </div>
                    </div>
                    <p class="dashboard-widget-3__txt">@lang('Total Withdrawn Amount')</p>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.withdraw.index') }}" class="dashboard-widget-3 dashboard-widget-3__info bg-img" data-background-image="{{ asset('assets/admin/images/widget-bg.png') }}">
                    <div class="dashboard-widget-3__top">
                        <h3 class="dashboard-widget-3__number">{{ $setting->cur_sym }}{{ showAmount($widget['withdrawCharge']) }}</h3>
                        <div class="dashboard-widget-3__icon">
                            <i class="ti ti-coins"></i>
                        </div>
                    </div>
                    <p class="dashboard-widget-3__txt">@lang('Total Charge for Withdrawn Amount')</p>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.withdraw.pending') }}" class="dashboard-widget-3 dashboard-widget-3__warning bg-img" data-background-image="{{ asset('assets/admin/images/widget-bg.png') }}">
                    <div class="dashboard-widget-3__top">
                        <h3 class="dashboard-widget-3__number">{{ $widget['withdrawPending'] }}</h3>
                        <div class="dashboard-widget-3__icon">
                            <i class="ti ti-rotate-clockwise-2"></i>
                        </div>
                    </div>
                    <p class="dashboard-widget-3__txt">@lang('Pending Withdrawal')</p>
                </a>
            </div>
            <div class="col-xl-3 col-sm-6">
                <a href="{{ route('admin.withdraw.cancelled') }}" class="dashboard-widget-3 dashboard-widget-3__danger bg-img" data-background-image="{{ asset('assets/admin/images/widget-bg.png') }}">
                    <div class="dashboard-widget-3__top">
                        <h3 class="dashboard-widget-3__number">{{ $widget['withdrawCancelled'] }}</h3>
                        <div class="dashboard-widget-3__icon">
                            <i class="ti ti-circle-x"></i>
                        </div>
                    </div>
                    <p class="dashboard-widget-3__txt">@lang('Cancelled Withdrawal')</p>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="custom--card h-auto">
            <div class="card-header">
                <h3 class="title">@lang('Donation') & @lang('Withdraw')</h3>
                <small>@lang('Progress report for last 12 months')</small>
            </div>
            <div class="card-body px-0 pb-0">
                <div id="chart"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="custom--card border-0 h-auto table-responsive">
            <table class="table table-borderless table--striped table--responsive--md">
                <thead>
                    <tr>
                        <th>@lang('User')</th>
                        <th>@lang('Email') | @lang('Phone')</th>
                        <th>@lang('Country') | @lang('Joined')</th>
                        <th>@lang('Balance')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestUsers as $user)
                        <tr>
                            <td>
                                <div class="table-card-with-image">
                                    <div class="table-card-with-image__img">
                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$user->image, getFileSize('userProfile'), true) }}" alt="Image">
                                    </div>
                                    <div class="table-card-with-image__content">
                                        <p class="fw-semibold">{{ $user->fullname }}</p>
                                        <p class="fw-semibold">
                                            <a href="{{ route('admin.user.details', $user->id) }}"> <small>@</small>{{ $user->username }}</a>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <p>{{ $user->email }}</p>
                                    <p>{{ $user->mobile }}</p>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <p class="fw-bold" title="{{ __(@$user->country_name) }}">{{ $user->country_code }}</p>
                                    <p>{{ diffForHumans($user->created_at) }}</p>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold">{{ $setting->cur_sym }}{{ showAmount($user->balance) }}</span>
                            </td>
                        </tr>
                    @empty
                        @include('admin.partials.noData')
                    @endforelse
                </tbody>
            </table>
        </div>
   </div>

    <div class="col-12">
        <div class="change-password-modal">
            <div class="change-password-modal__body">
                <button class="btn btn--sm btn--icon btn-outline--secondary change-password-modal__close modal-close">
                    <i class="ti ti-x"></i>
                </button>
                <div class="change-password-modal__img">
                    <img src="{{ asset('assets/admin/images/light.png') }}" alt="Image">
                </div>
                <h3 class="change-password-modal__title">@lang('Security Advisory')</h3>
                <p class="change-password-modal__desc">@lang('Immediate Change of Default Username and Password Required')</p>
                <div class="change-password-modal__btn">
                    <a href="{{ route('admin.profile') }}" class="btn btn--sm btn--base">
                        @lang('Change')
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script-lib')
    <script src="{{ asset('assets/admin/js/page/apexcharts.js') }}"></script>
@endpush

@push('page-script')
    <script>
        "use strict";

        @if ($passwordAlert)
            (function($) {
                $('.change-password-modal').addClass('active');
            })(jQuery);
        @endif

        let options = {
            series: [{
                name: 'Total Donation',
                data: [
                    @foreach($months as $month)
                        {{ getAmount(@$depositsMonth->where('months', $month)->first()->depositAmount) }},
                    @endforeach
                ]
            }, {
                name: 'Total Withdraw',
                data: [
                    @foreach($months as $month)
                        {{ getAmount(@$withdrawalMonth->where('months',$month)->first()->withdrawAmount) }},
                    @endforeach
                ]
            }],
            chart: {
                type: 'bar',
                height: 392,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($months),
            },
            yaxis: {
                title: {
                    text: "{{ __($setting->cur_sym) }}",
                    style: {
                        color: '#7c97bb'
                    }
                }
            },
            grid: {
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "{{ __($setting->cur_sym) }}" + val + " "
                    }
                }
            }
        };

        let chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endpush
