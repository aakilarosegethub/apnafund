@foreach ($comments as $comment)
    <div class="review-item" data-rating="{{ $comment->rating ?? 0 }}">
        <div class="review-header">
            <div class="reviewer-info">
                <div class="reviewer-avatar">
                    @if($comment->user && $comment->user->image)
                        <img src="{{ getImage(getFilePath('userProfile') . '/' . $comment->user->image) }}" alt="{{ $comment->user->fullname }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr($comment->user ? $comment->user->fullname : $comment->name, 0, 2)) }}
                    @endif
                </div>
                <div class="reviewer-details">
                    <h5 class="reviewer-name">{{ $comment->user ? $comment->user->fullname : $comment->name }}</h5>
                    <div class="review-rating">
                        @if($comment->rating)
                            <span class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $comment->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </span>
                            <span class="rating-text">{{ $comment->rating }}.0</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="review-date">{{ showDateTime($comment->created_at, 'd M, Y') }}</div>
        </div>
        @if($comment->title)
            <h6 class="review-title">{{ $comment->title }}</h6>
        @endif
        <p class="review-content">{{ $comment->comment }}</p>
        <div class="review-actions">
            <button class="btn-like-review">
                <i class="fas fa-thumbs-up"></i>
                <span>0</span>
            </button>
            <button class="btn-reply-review">
                <i class="fas fa-reply"></i>
                Reply
            </button>
        </div>
    </div>
@endforeach
