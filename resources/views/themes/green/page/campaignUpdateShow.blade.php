@php
    $activeTheme = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('title', $update->title . ' - Campaign Update')

@section('content')
@push('styles')
<style>
    body {
        padding-top: 80px;
        background: #f8f9fa;
    }
    .update-detail-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 40px 20px 60px;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 30px;
        color: #198754;
        background: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .back-link:hover {
        background: #198754;
        color: white;
        transform: translateX(-5px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }
    .update-header {
        background: white;
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    .update-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.2;
        color: #212529;
    }
    .update-meta {
        display: flex;
        align-items: center;
        gap: 25px;
        flex-wrap: wrap;
    }
    .update-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 0.95rem;
    }
    .update-meta-item i {
        color: #198754;
    }
    .update-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    .update-content {
        background: #fff;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 30px;
        line-height: 1.8;
        font-size: 1.1rem;
        color: #495057;
    }
    .update-content h1,
    .update-content h2,
    .update-content h3 {
        margin-top: 30px;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .update-content p {
        margin-bottom: 15px;
    }
    .update-content img {
        max-width: 100%;
        border-radius: 8px;
        margin: 20px 0;
    }
    .comments-section {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 1px solid #f0f0f0;
        margin-top: 30px;
    }
    .comments-section h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .comment-form {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }
    .comment-form h3 {
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 15px;
    }
    .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }
    .btn-submit {
        background: #028858;
        color: #fff;
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        font-size: 15px;
    }
    .btn-submit:hover {
        background: #027a4d;
    }
    .comment-item {
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 0;
    }
    .comment-item:last-child {
        border-bottom: none;
    }
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .comment-author {
        font-weight: 600;
        color: #333;
    }
    .comment-date {
        color: #999;
        font-size: 0.9rem;
    }
    .comment-rating {
        color: #ffc107;
        margin-bottom: 5px;
    }
    .comment-text {
        color: #666;
        line-height: 1.6;
    }
    .no-comments {
        text-align: center;
        padding: 40px;
        color: #999;
    }
    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #028858;
        text-decoration: none;
        font-weight: 600;
    }
    .back-link:hover {
        text-decoration: underline;
    }
</style>
@endpush

<div class="update-detail-container">
    <a href="{{ route('campaign.updates', $campaign->slug) }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Updates
    </a>

    <div class="update-header">
        <h1>{{ $update->title }}</h1>
        <div class="update-meta">
            <div class="update-meta-item">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ $update->created_at->format('F d, Y') }}</span>
            </div>
            <div class="update-meta-item">
                <i class="fas fa-user-circle"></i>
                <span>{{ $update->user->fullname ?? $update->user->username ?? 'Campaign Creator' }}</span>
            </div>
            <div class="update-meta-item">
                <i class="fas fa-clock"></i>
                <span>{{ $update->created_at->diffForHumans() }}</span>
            </div>
            <div class="update-meta-item">
                <i class="fas fa-comments"></i>
                <span>{{ $commentCount }} {{ $commentCount == 1 ? 'Comment' : 'Comments' }}</span>
            </div>
        </div>
    </div>

    @if($update->image)
    <img src="{{ getImage(getFilePath('campaign') . '/' . $update->image, getFileSize('campaign')) }}" 
         alt="{{ $update->title }}" 
         class="update-image">
    @endif

    <div class="update-content">
        {!! $update->content !!}
    </div>

    <div class="comments-section">
        <h2>Comments ({{ $commentCount }})</h2>

        @auth
        <div class="comment-form">
            <h3>Leave a Comment</h3>
            <form action="{{ route('campaign.update.comment', [$campaign->slug, $update->slug]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="comment">Your Comment *</label>
                    <textarea name="comment" id="comment" required placeholder="Share your thoughts..."></textarea>
                </div>
                <button type="submit" class="btn-submit">Post Comment</button>
            </form>
        </div>
        @else
        <div class="login-prompt">
            <p><i class="fas fa-lock"></i> Please <a href="{{ route('user.login.form') }}">login</a> to leave a comment.</p>
        </div>
        @endauth

        @forelse($comments as $comment)
        <div class="comment-item">
            <div class="comment-header">
                <span class="comment-author">{{ $comment->user->username ?? 'Anonymous' }}</span>
                <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            @if($comment->rating)
            <div class="comment-rating">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= $comment->rating ? 'text-warning' : 'text-muted' }}"></i>
                @endfor
            </div>
            @endif
            <div class="comment-text">{{ $comment->comment }}</div>
        </div>
        @empty
        <div class="no-comments">
            <i class="fas fa-comments"></i>
            <p>No comments yet. Be the first to share your thoughts!</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
