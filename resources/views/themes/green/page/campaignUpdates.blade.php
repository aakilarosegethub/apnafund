@php
    $activeTheme = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('title', 'Campaign Updates - ' . $campaign->name)

@section('content')
@push('styles')
<style>
    body {
        padding-top: 80px;
        background: #f8f9fa;
    }
    .updates-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 40px 20px 60px;
    }
    .updates-header {
        background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 40px;
        box-shadow: 0 4px 20px rgba(25, 135, 84, 0.2);
        color: white;
        position: relative;
        overflow: hidden;
    }
    .updates-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .updates-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }
    .updates-header p {
        font-size: 1.1rem;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }
    .updates-header .campaign-name {
        font-weight: 600;
        color: #fff;
    }
    .back-to-campaign {
        margin-bottom: 20px;
        display: inline-block;
    }
    .back-to-campaign a {
        color: #198754;
        text-decoration: none;
        font-weight: 600;
        background: white;
        padding: 10px 20px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .back-to-campaign a:hover {
        background: #198754;
        color: white;
        transform: translateX(-5px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }
    .updates-count {
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        margin-bottom: 30px;
        display: inline-block;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .updates-count i {
        color: #198754;
        margin-right: 10px;
    }
    .updates-count strong {
        color: #198754;
        font-size: 1.2rem;
    }
    .updates-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 30px;
    }
    .update-card {
        background: #fff;
        border-radius: 16px;
        padding: 0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .update-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        border-color: #198754;
    }
    .update-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-bottom: 3px solid #198754;
    }
    .update-content {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .update-card h2 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 12px;
        line-height: 1.3;
    }
    .update-card h2 a {
        color: #212529;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .update-card h2 a:hover {
        color: #198754;
    }
    .update-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 15px 25px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-size: 0.85rem;
    }
    .update-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6c757d;
    }
    .update-meta-item i {
        color: #198754;
        font-size: 0.9rem;
    }
    .update-excerpt {
        color: #495057;
        line-height: 1.6;
        margin-bottom: 15px;
        font-size: 0.95rem;
        flex: 1;
    }
    .read-more {
        color: white;
        background: #198754;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(25, 135, 84, 0.3);
        align-self: flex-start;
    }
    .read-more:hover {
        background: #157347;
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.4);
    }
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        color: #198754;
        opacity: 0.3;
    }
    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 10px;
    }
    .empty-state p {
        color: #6c757d;
        font-size: 1.1rem;
    }
    @media (max-width: 768px) {
        .updates-header h1 {
            font-size: 1.75rem;
        }
        .updates-header p {
            font-size: 1rem;
        }
        .updates-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .update-card h2 {
            font-size: 1.3rem;
        }
        .update-image {
            height: 200px;
        }
        .update-content {
            padding: 20px;
        }
    }
</style>
@endpush

<div class="updates-container">
    <div class="back-to-campaign">
        <a href="{{ route('campaign.show', $campaign->slug) }}">
            <i class="fas fa-arrow-left"></i> Back to Campaign
        </a>
    </div>
    
    <div class="updates-header">
        <h1><i class="fas fa-bullhorn"></i> Campaign Updates</h1>
        <p>Latest news and progress updates from <span class="campaign-name">{{ $campaign->name }}</span></p>
    </div>

    <div class="updates-count">
        <i class="fas fa-newspaper"></i>
        <strong>{{ $updates->total() }}</strong> {{ $updates->total() == 1 ? 'Update' : 'Updates' }} Published
    </div>

    <div class="updates-grid">
        @forelse($updates as $update)
        <div class="update-card">
            @if($update->image)
            <img src="{{ getImage(getFilePath('campaign') . '/' . $update->image, getFileSize('campaign')) }}" 
                 alt="{{ $update->title }}" 
                 class="update-image">
            @endif
            
            <div class="update-meta">
                <div class="update-meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ $update->created_at->format('M d, Y') }}</span>
                </div>
                <div class="update-meta-item">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ $update->user->fullname ?? $update->user->username ?? 'Creator' }}</span>
                </div>
                <div class="update-meta-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $update->created_at->diffForHumans() }}</span>
                </div>
            </div>
            
            <div class="update-content">
                <h2>
                    <a href="{{ route('campaign.update.show', [$campaign->slug, $update->slug]) }}">{{ $update->title }}</a>
                </h2>
                <div class="update-excerpt">
                    {!! strLimit(strip_tags($update->content), 150) !!}
                </div>
                <a href="{{ route('campaign.update.show', [$campaign->slug, $update->slug]) }}" class="read-more">
                    Read More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column: 1 / -1;">
            <i class="fas fa-newspaper"></i>
            <h3>No Updates Yet</h3>
            <p>This campaign hasn't posted any updates yet. Check back later for news and progress updates!</p>
        </div>
        @endforelse
    </div>

    @if($updates->hasPages())
    <div style="margin-top: 40px;">
        {{ $updates->links() }}
    </div>
    @endif
</div>
@endsection
