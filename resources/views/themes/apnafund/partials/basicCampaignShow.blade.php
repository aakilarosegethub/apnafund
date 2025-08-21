<div class="donation-details__img" data-aos="fade-up" data-aos-duration="1500">
    <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaignData->image, getFileSize('campaign')) }}" alt="image">
</div>
<nav>
    <div class="nav nav-tabs custom--tab" id="nav-tab" role="tablist">
        <button type="button" class="nav-link active" id="nav-desc-tab" data-bs-toggle="tab" data-bs-target="#nav-desc" role="tab" aria-controls="nav-desc" aria-selected="true">
            @lang('Description')
        </button>
        <button type="button" class="nav-link" id="nav-image-tab" data-bs-toggle="tab" data-bs-target="#nav-image" role="tab" aria-controls="nav-image" aria-selected="false">
            @lang('Relevant Images')
        </button>

        @if (@$campaignData->document)
            <button type="button" class="nav-link" id="nav-document-tab" data-bs-toggle="tab" data-bs-target="#nav-document" role="tab" aria-controls="nav-document" aria-selected="false">
                @lang('Relevant Document')
            </button>
        @endif

        @if (!request()->routeIs('upcoming.show'))
            <button type="button" class="nav-link" id="nav-comment-tab" data-bs-toggle="tab" data-bs-target="#nav-comment" role="tab" aria-controls="nav-comment" aria-selected="false">
                @lang('Comments')
            </button>
        @endif
    </div>
</nav>
<div class="tab-content mb-4" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-desc" role="tabpanel" aria-labelledby="nav-desc-tab" tabindex="0">
        <div class="donation-details__txt">
            <h2 class="donation-details__title" data-aos="fade-up" data-aos-duration="1500">{{ __(@$campaignData->name) }}</h2>
            <div class="donation-details__desc" data-aos="fade-up" data-aos-duration="1500">
                @php echo @$campaignData->description @endphp
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="nav-image" role="tabpanel" aria-labelledby="nav-image-tab" tabindex="0">
        <div class="row g-4">
            @foreach ($campaignData->gallery as $image)
                <div class="col-md-4 col-sm-6 col-xsm-6">
                    <div class="donation-details__relevent-img">
                        <a href="{{ getImage(getFilePath('campaign') . '/' . @$image, getFileSize('campaign')) }}" data-lightbox="Campaign Name">
                            <img src="{{ getImage(getFilePath('campaign') . '/' . @$image, getFileSize('campaign')) }}" alt="image">
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if (@$campaignData->document)
        <div class="tab-pane fade" id="nav-document" role="tabpanel" aria-labelledby="nav-document-tab" tabindex="0">
            <div class="donation-details__document">
                <object data="{{ asset(getFilePath('document') . '/' . @$campaignData->document) }}" type="application/pdf"></object>
            </div>
        </div>
    @endif

    @if (!request()->routeIs('upcoming.show'))
        <div class="tab-pane fade" id="nav-comment" role="tabpanel" aria-labelledby="nav-comment-tab" tabindex="0">
            <div @class(['donation-details__comments', 'border-bottom-none' => @$campaignData->user_id == @$authUser->id])>
                <h3 class="donation-details__subtitle">@lang('Comments') ({{ @$commentCount }})</h3>

                @if (count($comments))
                    <div id="loadMoreComment">
                        @foreach ($comments->take(5) as $comment)
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
                    </div>
                @else
                    <p>{{ __($emptyMessage) }}</p>
                @endif

                @if (count($comments) > 5)
                    <div class="text-center loadComment">
                        <button type="button" class="btn btn--base loadCommentButton" data-url="{{ route('campaign.comment.fetch', $campaignData->slug) }}">
                            @lang('Load More')
                        </button>
                    </div>
                @endif
            </div>

            @if (request()->routeIs('campaign.show') && $campaignData->user_id != @$authUser->id)
                <div class="donation-details__post__comment">
                    <h3 class="donation-details__subtitle">@lang('Post a comment')</h3>
                    <form action="{{ route('campaign.comment', @$campaignData->slug) }}" method="POST" class="row g-4">
                        @csrf
                        <div class="col-sm-6">
                            <input type="text" name="name" class="form--control" value="{{ old('name', @$authUser->fullname) }}" placeholder="Your Name*" required @readonly($authUser)>
                        </div>
                        <div class="col-sm-6">
                            <input type="email" name="email" class="form--control" value="{{ old('email', @$authUser->email) }}" placeholder="Your Email*" required @readonly($authUser)>
                        </div>
                        <div class="col-12">
                            <textarea class="form--control" name="comment" rows="10" placeholder="Your Comment*" required>{{ old('comment') }}</textarea>
                        </div>
                        <div class="col-12 d-flex justify-content-center">
                            <button type="submit" class="btn btn--base">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    @endif
</div>

@push('page-style')
    <style>
        .border-bottom-none {
            border-bottom: none;
        }

        .loadComment {
            padding-top: 35px;
        }
    </style>
@endpush

@push('page-script')
    <script>
        (function($) {
            "use strict"

            let showComments = 5

            $('.loadCommentButton').on('click', function() {
                $(this).addClass('btn-disabled').attr("disabled", true)

                let url = $(this).data('url')
                let skip = showComments
                let _this = $(this)

                $.ajax({
                    type: "GET",
                    url: url,
                    data: {
                        skip,
                    },
                    success: function(response) {
                        $('#loadMoreComment').append(response.html)
                        _this.removeClass('btn-disabled').attr("disabled", false)
                        showComments += 5

                        if (response.remaining_comments == 0) {
                            $('.loadComment').addClass('d-none')
                        }
                    },
                    error: function(errorData) {
                        if (errorData.status === 400) {
                            console.log(errorData.responseJSON.error.skip[0])
                        } else {
                            showToasts('error', errorData.responseJSON.message)
                        }

                        _this.removeClass('btn-disabled').attr("disabled", false)
                    }
                })
            })
        })(jQuery)
    </script>
@endpush
