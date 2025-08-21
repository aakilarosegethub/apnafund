@php
    // Example of using helper functions instead of hardcoded values
    $themeColors = getThemeColors();
    $dashboardStats = getDashboardStats();
    $recentActivities = getRecentActivities();
    $gigCategories = getGigCategories();
    $rewardTypes = getRewardTypes();
    $rewardColorThemes = getRewardColorThemes();
    $fileUploadLimits = getFileUploadLimits();
    $dashboardNavigation = getDashboardNavigation();
    $notificationTypes = getNotificationTypes();
    $userMenuItems = getUserMenuItems();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ getBusinessDashboardTitle() }} - {{ bs('site_name') }}</title>
    <link rel="shortcut icon" href="{{ getSiteFavicon() }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('apnafund/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .example-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .helper-function {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: monospace;
        }
        .color-preview {
            width: 50px;
            height: 30px;
            border-radius: 5px;
            display: inline-block;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Helper Functions Example</h1>
        <p>This page demonstrates how to use helper functions instead of hardcoded values.</p>

        <!-- Site Information -->
        <div class="example-section">
            <h3>Site Information</h3>
            <div class="helper-function">
                Site Name: {{ bs('site_name') }}
            </div>
            <div class="helper-function">
                Site Logo: <img src="{{ getSiteLogo('light') }}" alt="Logo" style="height: 30px;">
            </div>
            <div class="helper-function">
                Favicon: <img src="{{ getSiteFavicon() }}" alt="Favicon" style="height: 20px;">
            </div>
            <div class="helper-function">
                Default Currency: {{ getDefaultCurrency() }}
            </div>
            <div class="helper-function">
                Currency Code: {{ getDefaultCurrencyCode() }}
            </div>
        </div>

        <!-- Theme Colors -->
        <div class="example-section">
            <h3>Theme Colors</h3>
            <div class="helper-function">
                Primary Color: 
                <span class="color-preview" style="background: {{ $themeColors['primary'] }};"></span>
                {{ $themeColors['primary'] }}
            </div>
            <div class="helper-function">
                Secondary Color: 
                <span class="color-preview" style="background: {{ $themeColors['secondary'] }};"></span>
                {{ $themeColors['secondary'] }}
            </div>
            <div class="helper-function">
                Gradient: 
                <span class="color-preview" style="background: {{ $themeColors['gradient'] }};"></span>
                {{ $themeColors['gradient'] }}
            </div>
        </div>

        <!-- Dashboard Stats -->
        <div class="example-section">
            <h3>Dashboard Statistics</h3>
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $dashboardStats['active_gigs'] }}</h5>
                            <p class="card-text">Active Gigs</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ getDefaultCurrency() }}{{ number_format($dashboardStats['total_raised']) }}</h5>
                            <p class="card-text">Total Raised</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ number_format($dashboardStats['total_donors']) }}</h5>
                            <p class="card-text">Total Donors</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $dashboardStats['success_rate'] }}%</h5>
                            <p class="card-text">Success Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="example-section">
            <h3>Recent Activities</h3>
            @foreach($recentActivities as $activity)
            <div class="alert alert-info">
                <i class="{{ $activity['icon'] }} {{ $activity['color'] }}"></i>
                <strong>{{ $activity['title'] }}</strong>
                <br>
                <small>{{ $activity['description'] }}</small>
            </div>
            @endforeach
        </div>

        <!-- Gig Categories -->
        <div class="example-section">
            <h3>Gig Categories</h3>
            <div class="row">
                @foreach($gigCategories as $key => $category)
                <div class="col-md-4 mb-2">
                    <span class="badge bg-primary">{{ $category }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Reward Types -->
        <div class="example-section">
            <h3>Reward Types</h3>
            <div class="row">
                @foreach($rewardTypes as $key => $type)
                <div class="col-md-3 mb-2">
                    <span class="badge bg-success">{{ $type }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- File Upload Limits -->
        <div class="example-section">
            <h3>File Upload Limits</h3>
            <div class="helper-function">
                Image Upload: {{ formatBytes($fileUploadLimits['image']['max_size']) }} max, 
                {{ implode(', ', $fileUploadLimits['image']['allowed_types']) }} formats
            </div>
            <div class="helper-function">
                Reward Image: {{ formatBytes($fileUploadLimits['reward_image']['max_size']) }} max, 
                {{ implode(', ', $fileUploadLimits['reward_image']['allowed_types']) }} formats
            </div>
        </div>

        <!-- Dashboard Navigation -->
        <div class="example-section">
            <h3>Dashboard Navigation</h3>
            <ul class="nav nav-pills">
                @foreach($dashboardNavigation as $nav)
                <li class="nav-item">
                    <a class="nav-link" href="#{{ $nav['id'] }}">
                        <i class="{{ $nav['icon'] }}"></i> {{ $nav['title'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Notification Types -->
        <div class="example-section">
            <h3>Notification Types</h3>
            @foreach($notificationTypes as $type => $notification)
            <div class="alert alert-light">
                <i class="{{ $notification['icon'] }}"></i> {{ $notification['title'] }}
            </div>
            @endforeach
        </div>

        <!-- User Menu Items -->
        <div class="example-section">
            <h3>User Menu Items</h3>
            <div class="list-group">
                @foreach($userMenuItems as $menuItem)
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="{{ $menuItem['icon'] }}"></i> {{ $menuItem['title'] }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Notification Count -->
        <div class="example-section">
            <h3>Notification Count</h3>
            <div class="helper-function">
                Current notifications: <span class="badge bg-danger">{{ getNotificationCount() }}</span>
            </div>
        </div>

        <!-- Default Images -->
        <div class="example-section">
            <h3>Default Images</h3>
            <div class="helper-function">
                User Avatar: <img src="{{ getDefaultUserAvatar() }}" alt="Avatar" style="height: 30px;">
            </div>
            <div class="helper-function">
                Campaign Image: <img src="{{ getDefaultCampaignImage() }}" alt="Campaign" style="height: 30px;">
            </div>
        </div>
    </div>

    <script src="{{ asset('apnafund/assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html> 