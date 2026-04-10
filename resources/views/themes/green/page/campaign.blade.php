@php
        
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')

    <!-- Campaign Listing Section -->
    <section class="campaign-listing py-5" style="margin-top: 80px;">
        <div class="container">
            <!-- Top Filters Bar -->
            <div class="top-filters-bar mb-4">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="search-wrapper">
                            <input type="text" class="form-control" placeholder="Search campaigns..." id="campaign-name" value="{{ request('name') }}">
                            <button class="btn btn-primary search-campaign">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-12 col-md-4 col-lg-4">
                        <select class="form-select" id="filter-category">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ __($category->name) }} ({{ $category->campaigns_count ?? 0 }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div class="col-12 col-md-4 col-lg-4">
                        <select class="form-select" id="sort-campaigns">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="goal-high" {{ request('sort') == 'goal-high' ? 'selected' : '' }}>Highest Goal</option>
                            <option value="goal-low" {{ request('sort') == 'goal-low' ? 'selected' : '' }}>Lowest Goal</option>
                            <option value="raised-high" {{ request('sort') == 'raised-high' ? 'selected' : '' }}>Most Raised</option>
                            <option value="raised-low" {{ request('sort') == 'raised-low' ? 'selected' : '' }}>Least Raised</option>
                        </select>
                    </div>

                    <!-- View Toggle -->
                    
                </div>
            </div>

            <!-- Results Header -->
            <div class="results-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-0">Campaigns</h4>
                        <small class="text-muted" id="campaigns-count">Showing <span id="campaigns-shown">{{ $campaigns->count() }}</span> of {{ $campaigns->total() }} campaigns</small>
                    </div>
                </div>
            </div>

            <!-- Campaign Grid -->
            <div class="row">
                <div class="col-12">

                    <!-- Campaign Cards -->
                    @if($campaigns->count() > 0)
                    <div class="row g-4" id="campaign-container"
                         data-load-more-url="{{ route('campaign.load-more') }}"
                         data-total="{{ $campaigns->total() }}"
                         data-category-slug="{{ request()->routeIs('campaign.category') ? request()->route('slug') : '' }}"
                         data-has-more="{{ $campaigns->hasMorePages() ? '1' : '0' }}">
                        @foreach ($campaigns as $campaign)
                            @include($activeTheme . 'partials.campaign-card-item', ['campaign' => $campaign])
                        @endforeach
                    </div>
                    {{-- See more button + infinite scroll sentinel --}}
                    @if($campaigns->hasMorePages())
                    <div class="col-12 text-center py-4" id="see-more-wrapper">
                        <button type="button" class="btn btn-outline-primary btn-lg px-5" id="see-more-btn">
                            <i class="fas fa-chevron-down me-2"></i>See more
                        </button>
                        <div id="load-more-sentinel" style="min-height: 1px;"></div>
                    </div>
                    @endif
                    @else
                        <div class="empty-state text-center py-5">
                            <div class="empty-icon mb-4">
                                <i class="fas fa-search fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-3">No campaigns found</h5>
                            <p class="text-muted">Try adjusting your filters</p>
                            <a href="{{ route('campaign') }}" class="btn btn-primary">
                                <i class="fas fa-refresh me-2"></i>Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Hidden Form for Filters -->
    <form action="{{ route('campaign') }}" method="GET" class="d-none search-form">
        <input type="hidden" name="category" value="{{ request('category') }}">
        <input type="hidden" name="name" value="{{ request('name') }}">
        <input type="hidden" name="date_range" value="{{ request('date_range') }}">
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    </form>
@endsection

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset('assets/universal/css/datepicker.css') }}">
@endpush

@section('style')
    @include($activeTheme . 'partials.campaign-card-styles')
    
    <style>
        /* Fix header overlap */
        .campaign-listing {
            margin-top: 80px;
        }
        
        @media (max-width: 768px) {
            .campaign-listing {
                margin-top: 60px;
            }
        }
        /* Hero Section */
        .campaign-hero {
            position: relative;
            overflow: hidden;
        }

        .campaign-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        /* Top Filters Bar */
        .top-filters-bar {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        .search-wrapper {
            position: relative;
            display: flex;
            gap: 8px;
        }

        .search-wrapper input {
            flex: 1;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 12px 15px;
        }

        .search-wrapper input:focus {
            border-color: #05ce78;
            box-shadow: 0 0 0 0.2rem rgba(5, 206, 120, 0.25);
            outline: none;
        }

        .search-wrapper .btn {
            border-radius: 8px;
            padding: 12px 20px;
            min-width: 50px;
        }

        .form-select {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 12px 15px;
        }

        .form-select:focus {
            border-color: #05ce78;
            box-shadow: 0 0 0 0.2rem rgba(5, 206, 120, 0.25);
            outline: none;
        }

        /* Date Filter */
        .date-picker {
            caret-color: transparent;
            cursor: pointer;
        }

        /* Results Header */
        .results-header {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        .view-options .btn {
            border-radius: 8px;
            margin-left: 5px;
        }

        .view-options .btn.active {
            background: #05ce78;
            border-color: #05ce78;
            color: white;
        }

        /* Campaign Cards */
        .campaign-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none !important;
        }
        
        .campaign-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
        }
        
        a.text-decoration-none:hover .campaign-card {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
        }
        
        .campaign-image {
            display: block;
            width: 100%;
        }
        
        a.text-decoration-none {
            display: block;
            width: 100%;
            height: 100%;
        }

        /* List View */
        #campaign-container.list-view .campaign-item {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        #campaign-container.list-view .campaign-card {
            display: flex;
            flex-direction: row;
        }
        
        #campaign-container.list-view .campaign-image {
            width: 300px;
            height: 200px;
            border-radius: 12px 0 0 12px !important;
            flex-shrink: 0;
        }

        /* Empty State */
        .empty-state {
            background: #fff;
            border-radius: 15px;
            padding: 60px 20px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-filters-bar {
                padding: 15px;
            }

            .search-wrapper {
                flex-direction: column;
            }

            .search-wrapper .btn {
                width: 100%;
            }

            .view-options {
                flex-direction: row;
            }
            
            #campaign-container.list-view .campaign-card {
                flex-direction: column;
            }
            
            #campaign-container.list-view .campaign-image {
                width: 100%;
                border-radius: 12px 12px 0 0 !important;
            }
        }
    </style>
@endsection

@push('page-script-lib')
    <script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
    <script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
@endpush

@push('page-script')
    <script>
        // Function to build URL and navigate
        function applyFilters() {
            const category  = $('#filter-category').val() || '';
            const name      = $('#campaign-name').val() || '';
            const sort      = $('#sort-campaigns').val() || '';
            const dateRange = $('#date-range').val() || '';
            
            // Base URLs
            const baseCampaignUrl   = '{{ route("campaign") }}'; // /campaigns
            const categoryBaseUrl   = '{{ url("campaigns/category") }}'; // /campaigns/category

            // Decide base URL: pretty category URL when category is selected
            let baseUrl;
            if (category) {
                baseUrl = categoryBaseUrl + '/' + encodeURIComponent(category);
            } else {
                baseUrl = baseCampaignUrl;
            }

            // Build query parameters (exclude category, already in path)
            const params = new URLSearchParams();
            if (name) params.append('name', name);
            if (sort) params.append('sort', sort);
            if (dateRange) params.append('date_range', dateRange);
            
            const url = params.toString() ? baseUrl + '?' + params.toString() : baseUrl;
            
            console.log('Navigating to:', url);
            window.location.href = url;
        }

        // Wait for DOM and jQuery
        $(document).ready(function() {
            'use strict'

            console.log('Initializing campaign filters...');
            console.log('jQuery version:', $.fn.jquery);

            // Check if form exists
            if ($('.search-form').length === 0) {
                console.warn('Search form not found, using direct navigation');
            }

            // Initialize datepicker if available
            if (typeof $.fn.datepicker !== 'undefined') {
                $('.date-picker').datepicker({
                    dateFormat: 'dd-mm-yyyy',
                })

                $('.date-picker').on('input keyup keydown keypress', function() {
                    return false
                })
            }

            // Category filter (select box) - Auto submit on change
            $('#filter-category').on('change', function() {
                console.log('Category changed:', $(this).val());
                applyFilters();
            })

            // Search filter
            $('.search-campaign').on('click', function (e) {
                e.preventDefault();
                console.log('Search button clicked');
                applyFilters();
            })

            // Date filter
            $('.filter-by-date').on('click', function (e) {
                e.preventDefault();
                console.log('Date filter clicked');
                applyFilters();
            })

            // Sort filter - Auto submit on change
            $('#sort-campaigns').on('change', function() {
                console.log('Sort changed:', $(this).val());
                applyFilters();
            })

            // View toggle (no page refresh needed)
            $('.view-options .btn').on('click', function(e) {
                e.preventDefault();
                $('.view-options .btn').removeClass('active')
                $(this).addClass('active')
                
                let view = $(this).data('view')
                if (view === 'list') {
                    $('#campaign-container').addClass('list-view')
                } else {
                    $('#campaign-container').removeClass('list-view')
                }
            })
            
            // Initialize view based on current state
            if ($('.view-options .btn.active').length > 0 && $('.view-options .btn.active').data('view') === 'list') {
                $('#campaign-container').addClass('list-view')
            }

            // Enter key search
            $('#campaign-name').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault()
                    $('.search-campaign').click()
                }
            })

            // Infinite scroll - load more on scroll to bottom
            var loadMorePage = 2;
            var isLoading = false;
            var hasMore = $('#campaign-container').data('has-more') === 1;

            function loadMoreCampaigns() {
                if (!hasMore || isLoading) return;
                var $container = $('#campaign-container');
                if (!$container.length) return;

                isLoading = true;
                var $btn = $('#see-more-btn');
                if ($btn.length) {
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
                }
                var params = new URLSearchParams();
                params.append('page', loadMorePage);
                var catSlug = $container.data('category-slug');
                if (catSlug) params.append('category_slug', catSlug);
                if ($('#campaign-name').val()) params.append('name', $('#campaign-name').val());
                if ($('#sort-campaigns').val()) params.append('sort', $('#sort-campaigns').val());
                if ($('#date-range').val()) params.append('date_range', $('#date-range').val());
                if ($('#filter-category').val()) params.append('category', $('#filter-category').val());

                $.get($container.data('load-more-url') + '?' + params.toString())
                    .done(function(res) {
                        if (res.html) {
                            $container.append(res.html);
                            var newCount = $container.find('.campaign-item').length;
                            $('#campaigns-shown').text(newCount);
                        }
                        hasMore = res.hasMore;
                        if (res.hasMore) loadMorePage = res.nextPage;
                        else $('#see-more-wrapper').hide();
                    })
                    .fail(function() { hasMore = false; })
                    .always(function() {
                        isLoading = false;
                        $('#see-more-btn').prop('disabled', false).html('<i class="fas fa-chevron-down me-2"></i>See more');
                    });
            }

            $('#see-more-btn').on('click', function() { loadMoreCampaigns(); });

            $('#see-more-btn').on('click', function() { loadMoreCampaigns(); });

            $(window).on('scroll', function() {
                if (!hasMore || isLoading) return;
                var scrollBottom = $(document).height() - $(window).height() - $(window).scrollTop();
                if (scrollBottom < 400) loadMoreCampaigns();
            });

            console.log('Campaign filters initialized successfully');
        });
    </script>
@endpush
