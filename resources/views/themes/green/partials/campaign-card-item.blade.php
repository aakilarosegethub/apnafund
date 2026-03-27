{{-- Single campaign card for listing (used in campaign page + load-more) --}}
<div class="col-lg-4 col-md-6 campaign-item">
    <a href="{{ route('campaign.show', $campaign->slug) }}" class="text-decoration-none text-dark d-block">
        <div class="campaign-card h-100 rounded overflow-hidden shadow-sm" style="border-radius: 12px; cursor: pointer;">
            <div class="campaign-image" style="background-image: url('{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 250px; width: 100%; display: block; border-top-left-radius: 12px; border-top-right-radius: 12px;"></div>
            <div class="p-4">
                <h6 class="fw-semibold mb-2">{{ Str::limit($campaign->name, 40) }}</h6>
                <p class="text-muted small mb-3">{{ Str::limit(strip_tags($campaign->short_description ?? $campaign->description), 60) }}</p>
                <div class="progress mb-3" style="height: 6px;">
                    @php
                        $raised = $campaign->raised_amount ?? 0;
                        $goal = $campaign->goal_amount ?? 1;
                        $percentage = min(100, ($raised / $goal) * 100);
                        $curSym = $setting->cur_sym ?? getDefaultCurrency();
                    @endphp
                    <div class="progress-bar bg-success" style="width:{{ $percentage }}%"></div>
                </div> 
                <div class="d-flex justify-content-between small fw-semibold text-dark">
                    <span>{{ showCurrency(round(usdToLocal($raised), 0), 0) }} RAISED


                    </span>
                    <span>
                        @if($campaign->end_date)
                            @php
                                try {
                                    $endDate = \Carbon\Carbon::parse($campaign->end_date);
                                    $now = \Carbon\Carbon::now();
                                    if ($endDate->isPast() || $endDate->isToday()) {
                                        $daysText = 'ENDED';
                                    } else {
                                        $daysLeft = $now->diffInDays($endDate, false);
                                        $daysLeft = max(0, (int)$daysLeft);
                                        $daysText = $daysLeft . ' DAYS LEFT';
                                    }
                                } catch (\Exception $e) {
                                    $daysText = 'ONGOING';
                                }
                            @endphp
                            {{ $daysText }}
                        @else
                            ONGOING
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </a>
</div>
