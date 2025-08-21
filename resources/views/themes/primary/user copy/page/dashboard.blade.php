@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            @if (@$user->kc == ManageStatus::UNVERIFIED || @$user->kc == ManageStatus::PENDING)
                <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                    <div class="col-12">
                        <div class="section-heading">
                            @if (@$user->kc == ManageStatus::UNVERIFIED)
                                <div class="alert alert-danger" role="alert">
                                    <h6 class="alert-heading mb-2">{{ __(@$kycContent->data_info->verification_required_heading) }}</h4>
                                    <p>{{ __(@$kycContent->data_info->verification_required_details) }} <a href="{{ route('user.kyc.form') }}">@lang('Click here')</a> @lang('to verify.')</p>
                                </div>
                            @elseif (@$user->kc == ManageStatus::PENDING)
                                <div class="alert alert-warning" role="alert">
                                    <h6 class="alert-heading mb-2">{{ __(@$kycContent->data_info->verification_pending_heading) }}</h4>
                                    <p>{{ __(@$kycContent->data_info->verification_pending_details) }} <a href="{{ route('user.kyc.data') }}">@lang('See')</a> @lang('kyc data.')</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-md-4 g-3 dashboard-card__row pb-60">
                <div class="col-xl-3 col-md-4 col-sm-6 col-xsm-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card__icon">
                            <i class="ti ti-timeline-event"></i>
                        </div>
                        <div class="dashboard-card__txt">
                            <span class="dashboard-card__number">{{ @$widgetData['campaignCount'] }}</span>
                            <span class="dashboard-card__title">@lang('Total Campaign')</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-4 col-sm-6 col-xsm-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card__icon">
                            <i class="ti ti-hourglass"></i>
                        </div>
                        <div class="dashboard-card__txt">
                            <span class="dashboard-card__number">{{ @$widgetData['pendingCampaignCount'] }}</span>
                            <span class="dashboard-card__title">@lang('Pending Campaign')</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-4 col-sm-6 col-xsm-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card__icon">
                            <i class="ti ti-rosette-discount-check"></i>
                        </div>
                        <div class="dashboard-card__txt">
                            <span class="dashboard-card__number">{{ @$widgetData['approvedCampaignCount'] }}</span>
                            <span class="dashboard-card__title">@lang('Approved Campaign')</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-4 col-sm-6 col-xsm-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card__icon">
                            <i class="ti ti-xbox-x"></i>
                        </div>
                        <div class="dashboard-card__txt">
                            <span class="dashboard-card__number">{{ @$widgetData['rejectedCampaignCount'] }}</span>
                            <span class="dashboard-card__title">@lang('Rejected Campaign')</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-4 col-sm-6 col-xsm-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card__icon">
                            <i class="ti ti-building-bank"></i>
                        </div>
                        <div class="dashboard-card__txt">
                            <span class="dashboard-card__number">{{ $setting->cur_sym . showAmount(@$widgetData['receivedDonation']) }}</span>
                            <span class="dashboard-card__title">@lang('Received Donation')</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-4 col-sm-6 col-xsm-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card__icon">
                            <i class="ti ti-cash-register"></i>
                        </div>
                        <div class="dashboard-card__txt">
                            <span class="dashboard-card__number">{{ $setting->cur_sym . showAmount(@$widgetData['sendDonation']) }}</span>
                            <span class="dashboard-card__title">@lang('My Donation')</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-4 col-sm-6 col-xsm-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card__icon">
                            <i class="ti ti-wallet"></i>
                        </div>
                        <div class="dashboard-card__txt">
                            <span class="dashboard-card__number">{{ $setting->cur_sym . showAmount(@$user->balance) }}</span>
                            <span class="dashboard-card__title">@lang('Available Balance')</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-4 col-sm-6 col-xsm-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card__icon">
                            <i class="ti ti-moneybag"></i>
                        </div>
                        <div class="dashboard-card__txt">
                            <span class="dashboard-card__number">{{ $setting->cur_sym . showAmount(@$widgetData['withdrawalAmount']) }}</span>
                            <span class="dashboard-card__title">@lang('Withdrawal Amount')</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="custom--card">
                        <div class="card-header">
                            <h3 class="title">@lang('Monthly Donation Report')</h3>
                        </div>
                        <div id="donationReport"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="custom--card">
                        <div class="card-header">
                            <h3 class="title">@lang('Monthly Withdraw Report')</h3>
                        </div>
                        <div id="withdrawReport"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script-lib')
    <script src="{{ asset($activeThemeTrue . 'js/apexcharts.js') }}"></script>
@endpush

@push('page-script')
    <script>
        (function($) {
            'use strict'

            if ($('#donationReport').length) {
                var baseColorForChart = $('html').css('--success')
                var donationReportOptions = {
                    series: [{
                        name: 'Donation',
                        color: "hsl(" + baseColorForChart + " / .5)",
                        data: JSON.parse('<?php echo json_encode($donations); ?>')
                    }],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0,
                            gradientToColors: ["hsl(" + baseColorForChart + " / .1)"],
                            inverseColors: false,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100]
                        }
                    },
                    chart: {
                        height: 400,
                        type: 'area',
                        toolbar: {
                            show: false
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    title: {
                        text: 'Year - ' + new Date().getFullYear(),
                        align: 'center'
                    },
                    xaxis: {
                        fill: '#FFFFFF',
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        labels: {
                            format: 'dddd',
                        },
                        axisBorder: {
                            show: false,
                        },
                    },
                    responsive: [
                        {
                            breakpoint: 1199,
                            options: {
                                chart: {
                                    height: 300,
                                },
                            },
                        }, 
                        {
                            breakpoint: 991,
                            options: {
                                chart: {
                                    height: 350,
                                },
                            },
                        }, 
                        {
                            breakpoint: 767,
                            options: {
                                chart: {
                                    height: 300,
                                },
                            },
                        }, 
                        {
                            breakpoint: 575,
                            options: {
                                chart: {
                                    height: 250,
                                },
                            },
                        }
                    ]
                }

                var donationReport = new ApexCharts(document.querySelector("#donationReport"), donationReportOptions)
                donationReport.render()
            }

            if ($('#withdrawReport').length) {
                var baseColorForChart = $('html').css('--warning')
                var withdrawReportOptions = {
                    series: [{
                        name: 'Withdraw',
                        color: "hsl(" + baseColorForChart + " / .5)",
                        data: JSON.parse('<?php echo json_encode($withdrawals); ?>')
                    }],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0,
                            gradientToColors: ["hsl(" + baseColorForChart + " / .1)"],
                            inverseColors: false,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100]
                        }
                    },
                    chart: {
                        height: 400,
                        type: 'area',
                        toolbar: {
                            show: false
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    title: {
                        text: 'Year - ' + new Date().getFullYear(),
                        align: 'center'
                    },
                    xaxis: {
                        fill: '#FFFFFF',
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        labels: {
                            format: 'dddd',
                        },
                        axisBorder: {
                            show: false,
                        },
                    },
                    responsive: [
                        {
                            breakpoint: 1199,
                            options: {
                                chart: {
                                    height: 300,
                                },
                            },
                        }, 
                        {
                            breakpoint: 991,
                            options: {
                                chart: {
                                    height: 350,
                                },
                            },
                        }, 
                        {
                            breakpoint: 767,
                            options: {
                                chart: {
                                    height: 300,
                                },
                            },
                        }, 
                        {
                            breakpoint: 575,
                            options: {
                                chart: {
                                    height: 250,
                                },
                            },
                        }
                    ]
                }

                var withdrawReport = new ApexCharts(document.querySelector("#withdrawReport"), withdrawReportOptions)
                withdrawReport.render()
            }
        })(jQuery)
    </script>
@endpush
