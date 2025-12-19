@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <!-- Hero Section -->
    <section class="help-hero bg-light py-5">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">How can we help you?</h1>
                    <p class="lead mb-4">Find answers to your questions and get the support you need</p>
                    <div class="search-box-main">
                        <div class="search-input-container">
                            <input type="text" class="form-control" placeholder="Search for help topics..." id="mainSearch">
                        </div>
                        <!-- Featured Articles Dropdown -->
                        <div class="featured-dropdown" id="featuredDropdown">
                            <div class="dropdown-header">
                                <span class="fw-bold">Featured articles</span>
                            </div>
                            <div class="dropdown-content" id="featuredArticles">
                                <!-- Featured articles will be loaded dynamically -->
                            </div>
                            <div class="search-loading" id="searchLoading" style="display: none;">
                                <div class="d-flex align-items-center justify-content-center p-3">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                        <span class="visually-hidden">Searching...</span>
                                    </div>
                                    <span class="text-muted">Searching...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Help Categories - Dynamic Content -->
    <section class="help-categories py-5 bg-light">
        <div class="container">
            <!-- Loading State -->
            <div id="loadingState" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading help categories...</p>
                </div>

            <!-- Error State -->
            <div id="errorState" class="text-center py-5" style="display: none;">
                <div class="alert alert-danger">
                    <h5>Failed to load help categories</h5>
                    <p>Please try again later or contact support.</p>
                    <button class="btn btn-primary" onclick="loadHelpCategories()">Retry</button>
                </div>
            </div>
            
            <!-- Dynamic Categories Container -->
            <div id="categoriesContainer" class="row" style="display: none;">
                @if(!empty($helpData['data']))
                    @php $categories = $helpData['data']; @endphp
                    @foreach($categories as $category)
                        <div class="col-md-6">
                            <div class="category-section bg-white p-4 rounded shadow-sm h-100">
                                <h4 class="fw-bold mb-4">{{ $category['name'] }}</h4>
                                <ul class="list-unstyled help-links">
                                    @foreach(array_slice($category['posts'] ?? [], 0, 5) as $post)
                                        <li class="mb-3">
                                            <a href="{{ $post['permalink'] }}" target="_blank" class="text-decoration-none d-flex justify-content-between align-items-center">
                                                <span>{{ $post['title'] }}</span>
                                                <i class="fas fa-arrow-right text-muted"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    @if(!empty($lastUpdated))
    <div class="container mb-3">
        <p class="text-muted small mb-0">Last updated: {{ \Carbon\Carbon::parse($lastUpdated)->diffForHumans() }}</p>
    </div>
    @endif

    <!-- Additional Resources Section -->
    <section class="additional-resources py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-5">
                    <h2 class="h3 fw-bold">Additional resources</h2>
                </div>
            </div>
            <div class="row g-4">
                <!-- Education Hub -->
                <div class="col-md-4">
                    <a href="#" class="resource-card text-decoration-none">
                        <div class="resource-image education-hub">
                            <div class="resource-icon">
                                <svg width="80" height="80" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Rocket illustration -->
                                    <rect x="0" y="0" width="100" height="100" fill="url(#grid)" opacity="0.3"/>
                                    <defs>
                                        <pattern id="grid" width="5" height="5" patternUnits="userSpaceOnUse">
                                            <path d="M 5 0 L 0 0 0 5" fill="none" stroke="#fff" stroke-width="0.5" opacity="0.5"/>
                                        </pattern>
                                    </defs>
                                    <!-- Rocket body -->
                                    <ellipse cx="50" cy="60" rx="8" ry="25" fill="#f8f9fa"/>
                                    <ellipse cx="50" cy="50" rx="6" ry="20" fill="#fff"/>
                                    <!-- Rocket tip -->
                                    <polygon points="50,25 44,50 56,50" fill="#f8f9fa"/>
                                    <!-- Window -->
                                    <circle cx="50" cy="45" r="4" fill="#05ce78"/>
                                    <!-- Flames -->
                                    <ellipse cx="45" cy="85" rx="3" ry="8" fill="#05ce78"/>
                                    <ellipse cx="50" cy="88" rx="3" ry="10" fill="#05ce78"/>
                                    <ellipse cx="55" cy="85" rx="3" ry="8" fill="#05ce78"/>
                                </svg>
                            </div>
                        </div>
                        <div class="resource-content">
                            <h5 class="fw-bold mb-0">Education Hub</h5>
                        </div>
                    </a>
                </div>

                <!-- Products -->
                <div class="col-md-4">
                    <a href="#" class="resource-card text-decoration-none">
                        <div class="resource-image products">
                            <div class="resource-icon">
                                <svg width="80" height="80" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Shopping bag illustration -->
                                    <rect x="25" y="35" width="50" height="45" rx="5" fill="#2dd15a"/>
                                    <rect x="30" y="40" width="40" height="35" rx="3" fill="#05ce78"/>
                                    <!-- Handles -->
                                    <path d="M 35 35 Q 35 25 45 25 Q 55 25 55 35" stroke="#05ce78" stroke-width="3" fill="none"/>
                                    <path d="M 45 35 Q 45 25 55 25 Q 65 25 65 35" stroke="#05ce78" stroke-width="3" fill="none"/>
                                    <!-- Highlight -->
                                    <rect x="32" y="42" width="8" height="8" rx="2" fill="#7fff8e"/>
                                    <rect x="45" y="50" width="12" height="4" rx="2" fill="#7fff8e"/>
                                </svg>
                            </div>
                        </div>
                        <div class="resource-content">
                            <h5 class="fw-bold mb-0">Products</h5>
                        </div>
                    </a>
                </div>

                <!-- ApnaCrowdfunding Project Updates -->
                <div class="col-md-4">
                    <a href="#" class="resource-card text-decoration-none">
                        <div class="resource-image project-updates">
                            <div class="resource-icon">
                                <svg width="80" height="80" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Shamrock/clover illustration on nature background -->
                                    <rect x="0" y="0" width="100" height="100" fill="#2d5016"/>
                                    <!-- Flowers scattered -->
                                    <circle cx="20" cy="25" r="3" fill="#ffd700"/>
                                    <circle cx="75" cy="20" r="2.5" fill="#ffd700"/>
                                    <circle cx="85" cy="65" r="3.5" fill="#ffd700"/>
                                    <circle cx="15" cy="70" r="2" fill="#ffd700"/>
                                    <circle cx="25" cy="85" r="3" fill="#ffd700"/>
                                    <circle cx="80" cy="80" r="2.5" fill="#ffd700"/>
                                    <!-- Main shamrock -->
                                    <g transform="translate(50,50)">
                                        <path d="M 0 0 Q -10 -15 -20 -10 Q -15 0 0 0" fill="none" stroke="#fff" stroke-width="3"/>
                                        <path d="M 0 0 Q 10 -15 20 -10 Q 15 0 0 0" fill="none" stroke="#fff" stroke-width="3"/>
                                        <path d="M 0 0 Q -5 15 0 25 Q 5 15 0 0" fill="none" stroke="#fff" stroke-width="3"/>
                                        <circle cx="-12" cy="-8" r="8" fill="none" stroke="#fff" stroke-width="3"/>
                                        <circle cx="12" cy="-8" r="8" fill="none" stroke="#fff" stroke-width="3"/>
                                        <circle cx="0" cy="12" r="8" fill="none" stroke="#fff" stroke-width="3"/>
                                    </g>
                                </svg>
                            </div>
                        </div>
                        <div class="resource-content">
                            <h5 class="fw-bold mb-0">ApnaCrowdfunding Project Updates</h5>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for showing all posts in a category -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Category Posts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalPostsList">
                        <!-- Posts will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Page JavaScript -->
    <script>
        console.log('Help page script is loading...');

    // Global variable to store all help data
    let helpData = null;
    let allArticles = [];

    // Prefer server-provided data to avoid client fetch
    @if(!empty($helpData))
        helpData = @json($helpData);
        try {
            if (helpData && helpData.data) {
                allArticles = extractAllArticles(helpData.data);
                // Immediately reveal categories container rendered by PHP
                document.addEventListener('DOMContentLoaded', function(){
                    showCategories();
                    renderFeaturedArticles(allArticles.slice(0, 4));
                });
            }
        } catch (e) {
            console.warn('Failed to hydrate from server data:', e);
        }
    @endif

    // API endpoint
    const API_URL = 'https://apnacrowdfunding.com/help-admin/wp-json/zia-api/v1/categories-with-posts';

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        initializeSearch();
        // If no server data, fallback to client load
        if (!helpData || !helpData.data) {
            loadHelpCategories();
        } else {
            // Ensure loading/error states hidden when server data exists
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('errorState').style.display = 'none';
            document.getElementById('categoriesContainer').style.display = 'flex';
        }
    });

    // Load help categories from API
    async function loadHelpCategories() {
        try {
            showLoading();
            console.log('Starting API call to:', API_URL);
            
            const response = await fetch(API_URL, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
                mode: 'cors' // Enable CORS
            });
            
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('API Response:', data);
            
            if (data.status === 'success' && data.data) {
                helpData = data;
                allArticles = extractAllArticles(data.data);
                renderCategories(data.data);
                renderFeaturedArticles(allArticles.slice(0, 4)); // Show first 4 as featured
                showCategories();
            } else {
                throw new Error('Invalid API response structure');
            }
        } catch (error) {
            console.error('Error loading help categories:', error);
            console.error('Error type:', error.name);
            console.error('Error message:', error.message);
            
            // Check for network/CORS errors and load fallback
            if (error.name === 'TypeError' || 
                error.message.includes('CORS') || 
                error.message.includes('fetch') ||
                error.message.includes('Failed to fetch') ||
                error.message.includes('NetworkError')) {
                console.log('Network/CORS error detected, loading fallback data...');
                loadFallbackData();
            } else {
                showError();
            }
        }
    }

    // Fallback data based on the API structure
    function loadFallbackData() {
        console.log('Loading fallback data...');
        const fallbackData = {
            "status": "success",
            "data": [
                {
                    "id": 2,
                    "name": "Getting Started",
                    "slug": "apnacrowdfunding-basics",
                    "description": "",
                    "count": 5,
                    "posts": [
                        {
                            "id": 7,
                            "title": "How to sign up",
                            "slug": "how-to-sign-up",
                            "excerpt": "Signing up is the first step to join our crowdfunding platform. Create your account and start your journey.",
                            "date": "2025-08-29 10:34:10",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        },
                        {
                            "id": 11,
                            "title": "How to start a campaign",
                            "slug": "how-to-start-a-campaign",
                            "excerpt": "Anyone can start a campaign after creating an account. Learn the basics of launching your first project.",
                            "date": "2025-08-29 10:38:08",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        },
                        {
                            "id": 13,
                            "title": "How to edit your profile",
                            "slug": "how-to-edit-your-profile",
                            "excerpt": "Update your profile information, add a photo, and customize your public presence on the platform.",
                            "date": "2025-08-29 10:39:58",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "Backer Questions",
                    "slug": "backer-questions",
                    "description": "",
                    "count": 3,
                    "posts": [
                        {
                            "id": 15,
                            "title": "How do I back a project?",
                            "slug": "how-do-i-back-a-project",
                            "excerpt": "Find projects you want to support and learn how to make pledges safely and securely.",
                            "date": "2025-08-29 10:44:30",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        },
                        {
                            "id": 17,
                            "title": "How do refunds work?",
                            "slug": "how-do-refunds-work",
                            "excerpt": "Understand our refund policy and how to get your money back if needed.",
                            "date": "2025-08-29 10:51:50",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        },
                        {
                            "id": 19,
                            "title": "Can I change my pledge?",
                            "slug": "can-i-change-my-pledge",
                            "excerpt": "Learn how to modify or cancel your existing pledges before the campaign ends.",
                            "date": "2025-08-29 10:54:19",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        }
                    ]
                },
                {
                    "id": 4,
                    "name": "Creator Questions",
                    "slug": "creators-questions",
                    "description": "",
                    "count": 2,
                    "posts": [
                        {
                            "id": 21,
                            "title": "How do I launch my campaign?",
                            "slug": "how-do-i-launch-my-campaign",
                            "excerpt": "Step-by-step guide to preparing and launching your crowdfunding campaign successfully.",
                            "date": "2025-08-29 10:56:21",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        },
                        {
                            "id": 25,
                            "title": "How to communicate with backers?",
                            "slug": "how-to-communicate-with-backers",
                            "excerpt": "Best practices for keeping your supporters engaged and informed throughout your campaign.",
                            "date": "2025-08-29 10:58:38",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        }
                    ]
                },
                {
                    "id": 3,
                    "name": "Payments & Billing",
                    "slug": "payments-billing",
                    "description": "",
                    "count": 3,
                    "posts": [
                        {
                            "id": 27,
                            "title": "How payments are processed",
                            "slug": "how-payments-are-processed",
                            "excerpt": "Learn about our secure payment processing and when charges occur.",
                            "date": "2025-08-29 10:59:44",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        },
                        {
                            "id": 29,
                            "title": "Failed payment solutions",
                            "slug": "failed-payment-solutions",
                            "excerpt": "What to do when your payment fails and how to resolve common issues.",
                            "date": "2025-08-29 11:01:07",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        },
                        {
                            "id": 23,
                            "title": "What are the fees?",
                            "slug": "what-are-the-fees",
                            "excerpt": "Understand our transparent fee structure for both creators and backers.",
                            "date": "2025-08-29 10:57:36",
                            "author": "admin",
                            "featured_image": false,
                            "permalink": "#"
                        }
                    ]
                }
            ]
        };

        // Process fallback data same as API data
        helpData = fallbackData;
        allArticles = extractAllArticles(fallbackData.data);
        renderCategories(fallbackData.data);
        renderFeaturedArticles(allArticles.slice(0, 4));
        showCategories();
        
        // Show a notice that we're using fallback data
        const notice = document.createElement('div');
        notice.className = 'alert alert-info mt-3';
        notice.innerHTML = '<strong>Note:</strong> Using local help data. Some links may not be functional.';
        document.querySelector('.container').appendChild(notice);
    }

    // Extract all articles from categories for search
    function extractAllArticles(categories) {
        let articles = [];
        categories.forEach(category => {
            category.posts.forEach(post => {
                articles.push({
                    ...post,
                    category: category.name,
                    categorySlug: category.slug
                });
            });
        });
        return articles;
    }

    // Render categories
    function renderCategories(categories) {
        const container = document.getElementById('categoriesContainer');
        container.innerHTML = '';

        categories.forEach(category => {
            const categoryHtml = createCategoryHtml(category);
            container.appendChild(categoryHtml);
        });
    }

    // Create category HTML element
    function createCategoryHtml(category) {
        const colDiv = document.createElement('div');
        colDiv.className = 'col-md-6';
        
        const postsToShow = category.posts.slice(0, 5); // Show only first 5 posts
        const hasMorePosts = category.posts.length > 5;

        colDiv.innerHTML = `
            <div class="category-section bg-white p-4 rounded shadow-sm h-100">
                <h4 class="fw-bold mb-4">${category.name}</h4>
                <ul class="list-unstyled help-links">
                    ${postsToShow.map(post => `
                        <li class="mb-3">
                            <a href="${post.permalink}" target="_blank" class="text-decoration-none d-flex justify-content-between align-items-center">
                                <span>${post.title}</span>
                                <i class="fas fa-arrow-right text-muted"></i>
                            </a>
                        </li>
                    `).join('')}
                </ul>
                ${hasMorePosts ? `<a href="#" class="see-all-link text-decoration-underline fw-bold" onclick="showAllPosts('${category.slug}', '${category.name}')">See all (${category.count})</a>` : ''}
            </div>
        `;

        return colDiv;
    }

    // Render featured articles in dropdown
    function renderFeaturedArticles(articles) {
        const container = document.getElementById('featuredArticles');
        container.innerHTML = articles.map(article => `
            <a href="${article.permalink}" target="_blank" class="dropdown-item">
                <span>${article.title}</span>
                <small class="text-muted">ApnaCrowdfunding Support > ${article.category}</small>
            </a>
        `).join('');
    }

    // Initialize search functionality
    function initializeSearch() {
        const searchInput = document.getElementById('mainSearch');
        const featuredDropdown = document.getElementById('featuredDropdown');
        let searchTimeout;

        searchInput.addEventListener('focus', function() {
            if (allArticles.length > 0) {
                featuredDropdown.classList.add('show');
            }
        });

        searchInput.addEventListener('blur', function() {
            // Delay hiding to allow clicking on dropdown items
            setTimeout(() => {
                featuredDropdown.classList.remove('show');
            }, 200);
        });

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            if (query.length > 0) {
                // Debounce the search to avoid too many API calls
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            } else {
                renderFeaturedArticles(allArticles.slice(0, 4));
                if (allArticles.length > 0) {
                    featuredDropdown.classList.add('show');
                }
            }
        });
    }

    // Perform search using API
    async function performSearch(query) {
        try {
            // Show loading state
            document.getElementById('searchLoading').style.display = 'block';
            document.getElementById('featuredArticles').style.display = 'none';
            
            const searchUrl = `https://apnacrowdfunding.com/help-admin/wp-json/zia-api/v1/search?query=${encodeURIComponent(query)}`;
            
            const response = await fetch(searchUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
                mode: 'cors'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Hide loading state
            document.getElementById('searchLoading').style.display = 'none';
            document.getElementById('featuredArticles').style.display = 'block';
            
            if (data.status === 'success' && data.data) {
                renderSearchResultsFromAPI(data.data);
                document.getElementById('featuredDropdown').classList.add('show');
            } else {
                // Fallback to local search if API fails
                const filteredArticles = searchArticles(query);
                renderSearchResults(filteredArticles);
                document.getElementById('featuredDropdown').classList.add('show');
            }
        } catch (error) {
            console.error('Search API error:', error);
            // Hide loading state
            document.getElementById('searchLoading').style.display = 'none';
            document.getElementById('featuredArticles').style.display = 'block';
            // Fallback to local search
            const filteredArticles = searchArticles(query);
            renderSearchResults(filteredArticles);
            document.getElementById('featuredDropdown').classList.add('show');
        }
    }

    // Render search results from API
    function renderSearchResultsFromAPI(searchData) {
        const container = document.getElementById('featuredArticles');
        
        if (searchData.total_results === 0) {
            container.innerHTML = '<div class="dropdown-item"><span>No results found</span></div>';
            return;
        }
        
        let resultsHtml = '';
        
        // If we have posts_by_category, use that structure
        if (searchData.posts_by_category) {
            Object.keys(searchData.posts_by_category).forEach(categoryKey => {
                const categoryData = searchData.posts_by_category[categoryKey];
                categoryData.posts.forEach(post => {
                    resultsHtml += `
                        <a href="${post.permalink}" target="_blank" class="dropdown-item">
                            <span>${post.title}</span>
                            <small class="text-muted">ApnaCrowdfunding Support > ${categoryData.category_info.name}</small>
                        </a>
                    `;
                });
            });
        } 
        // If we have all_posts, use that structure
        else if (searchData.all_posts) {
            searchData.all_posts.forEach(post => {
                resultsHtml += `
                    <a href="${post.permalink}" target="_blank" class="dropdown-item">
                        <span>${post.title}</span>
                        <small class="text-muted">ApnaCrowdfunding Support</small>
                    </a>
                `;
            });
        }
        
        container.innerHTML = resultsHtml;
    }

    // Search articles
    function searchArticles(query) {
        const lowercaseQuery = query.toLowerCase();
        return allArticles.filter(article => 
            article.title.toLowerCase().includes(lowercaseQuery) ||
            article.excerpt.toLowerCase().includes(lowercaseQuery) ||
            article.category.toLowerCase().includes(lowercaseQuery)
        ).slice(0, 6); // Limit to 6 results
    }

    // Render search results
    function renderSearchResults(articles) {
        const container = document.getElementById('featuredArticles');
        if (articles.length === 0) {
            container.innerHTML = '<div class="dropdown-item"><span>No results found</span></div>';
        } else {
            container.innerHTML = articles.map(article => `
                <a href="${article.permalink}" target="_blank" class="dropdown-item">
                    <span>${article.title}</span>
                    <small class="text-muted">ApnaCrowdfunding Support > ${article.category}</small>
                </a>
            `).join('');
        }
    }

    // Show all posts for a category in modal
    function showAllPosts(categorySlug, categoryName) {
        const category = helpData.data.find(cat => cat.slug === categorySlug);
        if (category) {
            // Update modal title
            document.getElementById('categoryModalLabel').textContent = categoryName;
            
            // Create posts list HTML
            const postsListHtml = category.posts.map(post => `
                <div class="border-bottom pb-3 mb-3">
                    <h6 class="mb-2">
                        <a href="${post.permalink}" target="_blank" class="text-decoration-none">
                            ${post.title}
                        </a>
                    </h6>
                    <p class="text-muted small mb-2">${post.excerpt}</p>
                    <small class="text-muted">
                        Published on ${formatDate(post.date)} by ${post.author}
                    </small>
                </div>
            `).join('');
            
            // Update modal content
            document.getElementById('modalPostsList').innerHTML = postsListHtml;
            
            // Show modal - try Bootstrap 5 first, then jQuery fallback
            try {
                if (typeof bootstrap !== 'undefined') {
                    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
                    modal.show();
                } else if (typeof $ !== 'undefined') {
                    $('#categoryModal').modal('show');
                } else {
                    // Simple fallback - just show the modal by changing display
                    document.getElementById('categoryModal').style.display = 'block';
                    document.getElementById('categoryModal').classList.add('show');
                }
            } catch (e) {
                console.error('Error showing modal:', e);
                // Fallback alert if modal fails
                alert('All posts in ' + categoryName + ':\n\n' + category.posts.map(post => '• ' + post.title).join('\n'));
            }
        }
    }

    // Format date helper
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    // UI State Management
    function showLoading() {
        document.getElementById('loadingState').style.display = 'block';
        document.getElementById('errorState').style.display = 'none';
        document.getElementById('categoriesContainer').style.display = 'none';
    }

    function showError() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('errorState').style.display = 'block';
        document.getElementById('categoriesContainer').style.display = 'none';
    }

    function showCategories() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('errorState').style.display = 'none';
        document.getElementById('categoriesContainer').style.display = 'flex';
    }
    </script>
@endsection

@section('style')
<style>
        /* Custom styles for help center */
        .help-hero {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Loading and error states */
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        #loadingState p {
            color: #6c757d;
            font-size: 1.1rem;
            margin-top: 1rem;
        }

        #errorState .alert {
            max-width: 500px;
            margin: 0 auto;
        }

        /* Animation for categories appearing */
        .category-section {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }

        .category-section:nth-child(1) { animation-delay: 0.1s; }
        .category-section:nth-child(2) { animation-delay: 0.2s; }
        .category-section:nth-child(3) { animation-delay: 0.3s; }
        .category-section:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modal styles */
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        #modalPostsList .border-bottom:last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        #modalPostsList h6 a {
            color: #05ce78;
        }

        #modalPostsList h6 a:hover {
            color: #04a85f;
            text-decoration: underline !important;
        }
        
        .search-input-container {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-input-container .form-control {
            border-radius: 50px;
            padding: 15px 25px;
            border: 2px solid #e9ecef;
            font-size: 16px;
            width: 100%;
            background: white;
            transition: all 0.3s ease;
        }
        
        .search-input-container .form-control:focus {
            border-color: #05ce78;
            box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
            outline: none;
        }
        
        /* Featured Articles Dropdown */
        .search-box-main {
            position: relative;
        }
        
        .featured-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            margin-top: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .featured-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-header {
            padding: 15px 20px 10px;
            border-bottom: 1px solid #f1f3f4;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }
        
        .dropdown-content {
            padding: 0;
        }
        
        .dropdown-item {
            display: block;
            padding: 15px 20px;
            text-decoration: none;
            color: #495057;
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
            border-radius: 0 0 8px 8px;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
            color: #05ce78;
            text-decoration: none;
        }
        
        .dropdown-item span {
            display: block;
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        .dropdown-item small {
            font-size: 12px;
            line-height: 1.3;
        }
        
        /* Search Loading Styles */
        .search-loading {
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            border-radius: 0 0 8px 8px;
        }
        
        .search-loading .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .featured-dropdown {
                margin-left: 10px;
                margin-right: 10px;
                left: 10px;
                right: 10px;
            }
        }
        
        .topic-card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef !important;
        }
        
        .topic-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
            border-color: #05ce78 !important;
        }
        
        .category-section {
            transition: all 0.3s ease;
        }
        
        .category-section:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
        }
        
        .help-links li a {
            color: #495057;
            padding: 12px 0;
            display: flex;
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.2s ease;
        }
        
        .help-links li a:hover {
            color: #05ce78;
            background: #f8f9fa;
            margin: 0 -16px;
            padding-left: 16px;
            padding-right: 16px;
            border-radius: 6px;
        }
        
        .help-links li a:hover i {
            color: #05ce78;
            transform: translateX(3px);
        }
        
        .see-all-link {
            color: #05ce78;
            font-size: 14px;
            margin-top: 10px;
            display: inline-block;
        }
        
        .see-all-link:hover {
            color: #04a85f;
        }
        
        /* Additional Resources Styles */
        .additional-resources {
            background: #fff;
        }
        
        .resource-card {
            display: block;
            color: inherit;
            transition: all 0.3s ease;
        }
        
        .resource-card:hover {
            color: inherit;
            transform: translateY(-5px);
        }
        
        .resource-image {
            height: 200px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .education-hub {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }
        
        .products {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .project-updates {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
        }
        
        .resource-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .resource-content h5 {
            color: #1f2937;
            font-size: 1.1rem;
        }
        
        .resource-card:hover .resource-content h5 {
            color: #05ce78;
        }
        
        /* Responsive adjustments for resources */
        @media (max-width: 768px) {
            .resource-image {
                height: 150px;
                margin-bottom: 15px;
            }
            
            .resource-content h5 {
                font-size: 1rem;
            }
        }
        
        .support-option {
            transition: all 0.3s ease;
        }
        
        .support-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
        }
        
        .btn-primary {
            background: #05ce78;
            border-color: #05ce78;
        }
        
        .btn-primary:hover {
            background: #04a85f;
            border-color: #04a85f;
        }
        
        .btn-outline-primary {
            color: #05ce78;
            border-color: #05ce78;
        }
        
        .btn-outline-primary:hover {
            background: #05ce78;
            border-color: #05ce78;
            color: white;
        }
        
        .text-primary {
            color: #05ce78 !important;
        }
        
        /* Header adjustments */
        .header {
            background: #05ce78;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Footer adjustments */
        .footer {
            background: #343a40;
            color: white;
        }
        
        .footer-tagline {
            color: #adb5bd;
        }
        
        .copyright {
            color: #adb5bd;
        }
        
        .email {
            color: white;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .help-hero h1 {
                font-size: 2rem;
            }
            
            .search-box-main .form-control,
            .search-box-main .btn {
                padding: 12px 20px;
            }
            
            .topic-card {
                margin-bottom: 20px;
            }
        }
    </style>
@endsection