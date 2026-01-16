@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')
<style>
    .success-story-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    .success-story-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }
</style>

<section class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-center mb-4">Success Stories</h1>
            <p class="text-center text-muted">Inspiring stories of successful campaigns and their impact</p>
        </div>
    </div>
    
    <div class="row g-4">
        @forelse ($successElements as $successElement)
            <div class="col-lg-4 col-md-6">
                <div class="success-story-card h-100 rounded overflow-hidden shadow-sm" style="border-radius: 12px; cursor: pointer;">
                    @php
                        $storyInfo = is_array($successElement->data_info) ? $successElement->data_info : (array)$successElement->data_info;
                        $storySlug = $storyInfo['slug'] ?? $successElement->id;
                    @endphp
                    <a href="{{ route('stories.show', $storySlug) }}" class="text-decoration-none text-dark d-block">
                        <div style="background-image: url('{{ getImage('assets/images/site/success_story/thumb_' . @$successElement->data_info->image, '415x230') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 250px; width: 100%; display: block; border-top-left-radius: 12px; border-top-right-radius: 12px;"></div>
                        <div class="p-4">
                            <h5 class="fw-semibold mb-2">{{ Str::limit(@$successElement->data_info->title, 40) }}</h5>
                            <p class="text-muted small mb-3">{{ Str::limit(strip_tags(@$successElement->data_info->details), 100) }}</p>
                            <span class="text-success fw-semibold small">Read More <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <p class="text-muted">No success stories available yet.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($successElements->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $successElements->links() }}
                </div>
            </div>
        </div>
    @endif
</section>
@endsection
