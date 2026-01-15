@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <!-- Blog Content -->
    <div class="blog-container">
        <div class="blog-header">
            <h1 class="blog-title">{{ __(@$storyData->data_info->title) }}</h1>
            <div class="blog-meta">
                <span>@lang('Published on') {{ @$storyData->created_at ? @$storyData->created_at->format('F d, Y') : 'N/A' }}</span>
            </div>
            <div class="blog-author">
                <div class="author-avatar">
                    <img src="{{ getImage('assets/images/site/success_story/' . @$storyData->data_info->image, '100x100') }}" alt="Story Image">
                </div>
                <div class="author-info">
                    <p class="author-name">@lang('Success Story')</p>
                    <p class="author-title">@lang('Apna Crowdfunding')</p>
                </div>
            </div>
        </div>

        <img src="{{ getImage('assets/images/site/success_story/' . @$storyData->data_info->image, '855x475') }}" alt="Story Image" class="blog-featured-image">

        <div class="blog-content">
            <div class="blog-section">
                <div class="blog-section-content">
                    @php echo @$storyData->data_info->details @endphp
                </div>
            </div>

            <!-- Share Section -->
            <div class="blog-section">
                <h2 class="blog-section-title">@lang('Share This Story')</h2>
                <div class="blog-section-content">
                    <div class="input--group mb-4">
                        <input type="text" class="form--control" id="shareLink" readonly value="{{ url()->current() }}">
                        <span class="badge bg--success share-link__badge">@lang('Copied')</span>
                        <button class="btn btn--base share-link__copy px-3">
                            <i class="ti ti-copy"></i>
                        </button>
                    </div>
                    <ul class="social-list social-list-2">
                        <li class="social-list__item">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" class="social-list__link flex-center" target="_blank">
                                <i class="ti ti-brand-facebook"></i>
                            </a>
                        </li>
                        <li class="social-list__item">
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode(@$storyData->data_info->title) }}&amp;url={{ urlencode(url()->current()) }}" class="social-list__link flex-center" target="_blank">
                                <i class="ti ti-brand-x"></i>
                            </a>
                        </li>
                        <li class="social-list__item">
                            <a href="http://www.linkedin.com/shareArticle?url={{ urlencode(url()->current()) }}" class="social-list__link flex-center" target="_blank">
                                <i class="ti ti-brand-linkedin"></i>
                            </a>
                        </li>
                        <li class="social-list__item">
                            <a href="https://pinterest.com/pin/create/bookmarklet/?media={{ $seoContents['image'] }}&url={{ urlencode(url()->current()) }}&description={{ urlencode(@$storyData->data_info->title) }}" class="social-list__link flex-center" target="_blank">
                                <i class="ti ti-brand-pinterest"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Related Articles -->
            <div class="related-articles">
                <h3 class="related-title">@lang('More Stories')</h3>
                <div class="row">
                    @if (count($moreStories))
                        @foreach ($moreStories as $moreStory)
                            <div class="col-md-4 mb-4">
                                <div class="related-card">
                                    <img src="{{ getImage('assets/images/site/success_story/thumb_' . @$moreStory->data_info->image, '415x230') }}" alt="Story Image" class="related-card-img">
                                    <div class="related-card-body">
                                        <h4 class="related-card-title">{{ __(strLimit(@$moreStory->data_info->title, 45)) }}</h4>
                                        <p class="related-card-text">{{ __(strLimit(@$moreStory->data_info->details, 100)) }}</p>
                                        <a href="{{ route('stories.show', @$moreStory->id) }}" class="related-card-link">@lang('Read more') <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <p class="text-center">{{ __($emptyMessage) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Blog Content Styles */
        .blog-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .blog-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .blog-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .blog-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .blog-author {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
        }

        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .author-info {
            text-align: left;
        }

        .author-name {
            font-weight: 600;
            margin-bottom: 0;
        }

        .author-title {
            color: #666;
            font-size: 0.85rem;
        }

        .blog-featured-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .blog-content {
            line-height: 1.8;
        }

        .blog-section {
            margin-bottom: 40px;
        }

        .blog-section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .blog-section-content {
            color: #444;
        }

        /* Related Articles */
        .related-articles {
            margin-top: 60px;
        }

        .related-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .related-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .related-card-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .related-card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .related-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .related-card-text {
            color: #666;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .related-card-link {
            color: #05ce78;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .related-card-link:hover {
            text-decoration: underline;
        }

        .related-card-link i {
            margin-left: 5px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .blog-title {
                font-size: 2rem;
            }

            .blog-featured-image {
                max-height: 300px;
            }

            .blog-section-title {
                font-size: 1.3rem;
            }
        }
    </style>

    <script>
        // Copy link functionality
        document.addEventListener('DOMContentLoaded', function() {
            const shareLink = document.getElementById('shareLink');
            const copyBtn = document.querySelector('.share-link__copy');
            const badge = document.querySelector('.share-link__badge');

            if (copyBtn && shareLink) {
                copyBtn.addEventListener('click', function() {
                    shareLink.select();
                    document.execCommand('copy');
                    
                    // Show copied badge
                    badge.style.display = 'inline-block';
                    setTimeout(() => {
                        badge.style.display = 'none';
                    }, 2000);
                });
            }
        });
    </script>
@endsection
