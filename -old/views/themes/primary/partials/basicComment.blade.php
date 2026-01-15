@foreach ($comments as $comment)
    <div class="donation-details__comment">
        <div class="donation-details__comment__img">
            <img src="{{ getImage(getFilePath('userProfile') . '/' . @$comment->user->image, getFileSize('userProfile')) }}" alt="image">
        </div>
        <div class="donation-details__comment__txt">
            <h4 class="donation-details__comment__name">{{ __(@$comment->user ? @$comment->user->fullname : @$comment->name) }}</h4>
            <p class="donation-details__comment__date">{{ showDateTime(@$comment->created_at, 'd M, Y') }}</p>
            <p class="donation-details__comment__desc">{{ __(@$comment->comment) }}</p>
        </div>
    </div>
@endforeach
