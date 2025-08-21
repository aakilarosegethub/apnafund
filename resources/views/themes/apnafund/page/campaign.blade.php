@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('frontend')

    <!-- Campaign Listing Section -->
    <section class="campaign-listing py-5">
        <div class="container">
            <div class="row">
                <!-- Filters Sidebar -->
                <div class="col-lg-3">
                    <div class="filters-sidebar">
                        <!-- Category Filter -->
                        <div class="filter-card mb-4">
                            <h5 class="filter-title">
                                <i class="fas fa-tags me-2"></i>Categories
                            </h5>
                            <div class="filter-content">
                                <div class="category-filter">
                                    <label class="category-item active" data-category="all">
                                        <input type="radio" name="category" value="all" {{ request('category') == 'all' || !request('category') ? 'checked' : '' }}>
                                        <span class="category-name">All Categories</span>
                                        <span class="category-count">{{ $campaigns->total() ?? 0 }}</span>
                                    </label>
                                    @foreach ($categories as $category)
                                        <label class="category-item" data-category="{{ $category->slug }}">
                                            <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'checked' : '' }}>
                                            <span class="category-name">{{ __($category->name) }}</span>
                                            <span class="category-count">{{ $category->campaigns_count ?? 0 }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Search Filter -->
                        <div class="filter-card mb-4">
                            <h5 class="filter-title">
                                <i class="fas fa-search me-2"></i>Search Campaigns
                            </h5>
                            <div class="filter-content">
                                <div class="search-filter">
                                    <input type="text" class="form-control" placeholder="Search campaigns..." id="campaign-name" value="{{ request('name') }}">
                                    <button class="btn btn-primary w-100 mt-2 search-campaign">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Date Filter -->
                        <div class="filter-card mb-4">
                            <h5 class="filter-title">
                                <i class="fas fa-calendar me-2"></i>Date Range
                            </h5>
                            <div class="filter-content">
                                <div class="date-filter">
                                    <input type="text" class="form-control date-picker" placeholder="Select date range" id="date-range" value="{{ request('date_range') }}">
                                    <button class="btn btn-outline-primary w-100 mt-2 filter-by-date">
                                        <i class="fas fa-filter me-2"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sort Options -->
                        <div class="filter-card">
                            <h5 class="filter-title">
                                <i class="fas fa-sort me-2"></i>Sort By
                            </h5>
                            <div class="filter-content">
                                <select class="form-select" id="sort-campaigns">
                                    <option value="latest">Latest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="goal-high">Highest Goal</option>
                                    <option value="goal-low">Lowest Goal</option>
                                    <option value="raised-high">Most Raised</option>
                                    <option value="raised-low">Least Raised</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Grid -->
                <div class="col-lg-9">
                    <!-- Results Header -->
                    <div class="results-header mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h4 class="mb-0">Campaigns</h4>
                                <small class="text-muted">Showing {{ $campaigns->count() }} of {{ $campaigns->total() }} campaigns</small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="view-options">
                                    <button class="btn btn-outline-secondary btn-sm active" data-view="grid">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" data-view="list">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Cards -->
                     @if($campaigns->count() > 0)
                    <div class="project-grid" id="campaign-container">
                        @foreach ($campaigns as $campaign)
                            <div class="campaign-item col-md-6 col-lg-4">
                            <a href="{{ route('campaign.show', $campaign->slug) }}" class="project-card-link">
                                @include('partials.campaign-item', ['campaignId' => $campaign->id, 'featured' => true])
                            </a>
                        </div>
                        @endforeach
                    
                        @else
                            <div class="empty-state text-center py-5">
                                <div class="empty-icon mb-4">
                                    <i class="fas fa-search fa-3x text-muted"></i>
                                </div>
                                <h5 class="text-muted mb-3">No campaigns found</h5>
                                <p class="text-muted">{{ __($emptyMessage) }}</p>
                                <a href="{{ route('campaign') }}" class="btn btn-primary">
                                    <i class="fas fa-refresh me-2"></i>Clear Filters
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Pagination -->
                    @if ($campaigns->hasPages())
                        <div class="pagination-wrapper mt-5">
                            {{ paginateLinks($campaigns) }}
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

        /* Filters Sidebar */
        .filters-sidebar {
            position: sticky;
            top: 20px;
        }

        .filter-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            padding: 20px;
            border: 1px solid #e9ecef;
        }

        .filter-title {
            color: #333;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f8f9fa;
        }

        /* Category Filter */
        .category-filter {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .category-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .category-item:hover {
            background: #f8f9fa;
            border-color: #05ce78;
        }

        .category-item.active {
            background: #05ce78;
            color: white;
            border-color: #05ce78;
        }

        .category-item input[type="radio"] {
            display: none;
        }

        .category-name {
            font-weight: 500;
        }

        .category-count {
            background: rgba(5, 206, 120, 0.1);
            color: #05ce78;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .category-item.active .category-count {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Search Filter */
        .search-filter input {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 12px 15px;
        }

        .search-filter input:focus {
            border-color: #05ce78;
            box-shadow: 0 0 0 0.2rem rgba(5, 206, 120, 0.25);
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

        /* Project Grid */
        .project-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .campaign-item {
            width: 100%;
        }

        .project-grid.list-view {
            grid-template-columns: 1fr;
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
            .project-grid {
                grid-template-columns: 1fr;
            }
            
            .filters-sidebar {
                position: static;
                margin-bottom: 30px;
            }
        }

        @media (min-width: 769px) and (max-width: 1200px) {
            .project-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1201px) {
            .project-grid {
                grid-template-columns: repeat(2, 1fr);
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
        (function ($) {
            'use strict'

            // Initialize datepicker
            $('.date-picker').datepicker({
                dateFormat: 'dd-mm-yyyy',
            })

            $('.date-picker').on('input keyup keydown keypress', function() {
                return false
            })

            // Category filter
            $('.category-item').on('click', function (event) {
                event.preventDefault()
                
                $('.category-item').removeClass('active')
                $(this).addClass('active')
                
                let category = $(this).data('category')
                $('input[name="category"]').val(category)
                
                $('.search-form').submit()
            })

            // Search filter
            $('.search-campaign').on('click', function () {
                let name = $('#campaign-name').val()
                $('input[name="name"]').val(name)
                $('.search-form').submit()
            })

            // Date filter
            $('.filter-by-date').on('click', function () {
                let range = $('#date-range').val()
                $('input[name="date_range"]').val(range)
                $('.search-form').submit()
            })

            // Sort filter
            $('#sort-campaigns').on('change', function() {
                let sort = $(this).val()
                $('input[name="sort"]').val(sort)
                $('.search-form').submit()
            })

            // View toggle
            $('.view-options .btn').on('click', function() {
                $('.view-options .btn').removeClass('active')
                $(this).addClass('active')
                
                let view = $(this).data('view')
                if (view === 'list') {
                    $('#campaign-container').addClass('list-view')
                } else {
                    $('#campaign-container').removeClass('list-view')
                }
            })

            // Enter key search
            $('#campaign-name').on('keypress', function(e) {
                if (e.which === 13) {
                    $('.search-campaign').click()
                }
            })

        })(jQuery)
    </script>
@endpush
