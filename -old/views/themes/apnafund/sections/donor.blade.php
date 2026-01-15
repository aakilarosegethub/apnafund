@php
    $donorContent = getSiteData('top_donor.content', true);
    $topDonors    = App\Models\Deposit::whereNot('user_id', 0)
                            ->where('donor_type', ManageStatus::KNOWN_DONOR)
                            ->where('status', ManageStatus::PAYMENT_SUCCESS)
                            ->selectRaw('user_id, sum(amount) as total_donation, full_name')
                            ->groupBy('user_id')
                            ->orderByDesc('total_donation')
                            ->limit(20)
                            ->get();
@endphp

<div class="donor py-120 bg-light-gradient">
    <span class="donor__bg bg-img" data-background-image="{{ getImage('assets/images/site/top_donor/' . @$donorContent->data_info->background_image, '1920x700') }}"></span>
    <div class="container">
        <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
            <div class="col-lg-6">
                <div class="section-heading text-center">
                    <h2 class="section-heading__title mx-auto">{{ __(@$donorContent->data_info->section_heading) }}</h2>
                    <p class="section-heading__desc">{{ __(@$donorContent->data_info->description) }}</p>
                </div>
            </div>
        </div>
        <div class="row g-4 donor__row">
            @forelse ($topDonors as $topDonor)
                <div class="col-lg-3 col-md-4 col-sm-6 col-xsm-6" data-aos="fade-up" data-aos-duration="1500">
                    <div class="donor__card">
                        <span class="donor__number">{{ $loop->iteration }}</span>
                        <span class="donor__txt">
                            <span class="donor__name">{{ __(@$topDonor->full_name) }}</span>
                            <span class="donor__amount">{{ $setting->cur_sym . showAmount(@$topDonor->total_donation) }}</span>
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-center" data-aos="fade-up" data-aos-duration="1500">{{ __($emptyMessage) }}</p>
            @endforelse
        </div>
    </div>
</div>