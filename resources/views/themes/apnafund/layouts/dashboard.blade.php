@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
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
    <link rel="stylesheet" href="{{ asset('apnafund/assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('apnafund/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ getSiteFavicon() }}" type="image/png">
    <style>
        /* Dashboard Specific Styles */
        .dashboard-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .dashboard-header {
            background: #05ce78;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .dashboard-nav {
            background: #fff;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .nav-tabs {
            border: none;
            gap: 10px;
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            color: #6c757d;
            background: transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .nav-tabs .nav-link:hover {
            color: #05ce78;
            background: rgba(5, 206, 120, 0.1);
            text-decoration: none;
        }

        .nav-tabs .nav-link.active {
            background: #05ce78;
            color: #fff;
            box-shadow: 0 4px 15px rgba(5, 206, 120, 0.3);
            text-decoration: none;
        }

        .dashboard-content {
            padding: 40px 0;
        }

        .content-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #05ce78;
            box-shadow: 0 0 0 0.2rem rgba(5, 206, 120, 0.25);
        }

        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
        }

        .btn-primary {
            background: #05ce78;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #04b367;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(5, 206, 120, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .preview-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .preview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.15);
        }

        .preview-image {
            height: 200px;
            background: linear-gradient(135deg, #05ce78, #04b367);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 48px;
        }

        .preview-content {
            padding: 25px;
        }

        .preview-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .preview-description {
            color: #6c757d;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .preview-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .preview-category {
            background: rgba(5, 206, 120, 0.1);
            color: #05ce78;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .preview-amount {
            font-weight: 700;
            color: #05ce78;
            font-size: 1.1rem;
        }

        .preview-progress {
            margin-bottom: 15px;
        }

        .progress {
            height: 8px;
            border-radius: 10px;
            background: #e9ecef;
        }

        .progress-bar {
            background: #05ce78;
            border-radius: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #05ce78;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }

        .file-upload {
            border: 2px dashed #e9ecef;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload:hover {
            border-color: #05ce78;
            background: rgba(5, 206, 120, 0.05);
        }

        .file-upload i {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .file-upload-text {
            color: #6c757d;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }

        .alert-success {
            background: rgba(5, 206, 120, 0.1);
            color: #05ce78;
        }

        .alert-info {
            background: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        @media (max-width: 768px) {
            .nav-tabs {
                flex-wrap: wrap;
            }
            
            .nav-tabs .nav-link {
                font-size: 14px;
                padding: 10px 20px;
            }
            
            .content-card {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    @include($activeTheme.'partials.header')

    <!-- Dashboard Navigation -->
    <nav class="dashboard-nav">
        <div class="container">
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                @foreach($dashboardNavigation as $nav)
                <li class="nav-item" role="presentation">
                    <a href="{{ route($nav['route']) }}" class="nav-link {{ request()->routeIs($nav['route']) ? 'active' : '' }}">
                        <i class="{{ $nav['icon'] }} me-2"></i>{{ $nav['title'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </nav>
    <div class="container dashboard-content">

    @yield('frontend')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="{{ asset('apnafund/assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Preview functionality
        function previewGig() {
            const title = document.getElementById('gigTitle').value || 'Your Gig Title';
            const description = document.getElementById('gigDescription').value || 'Your gig description will appear here...';
            const category = document.getElementById('gigCategory').value || 'Category';
            const amount = document.getElementById('targetAmount').value || '0';
            
            const previewCard = document.querySelector('#gigPreview .preview-card');
            previewCard.querySelector('.preview-title').textContent = title;
            previewCard.querySelector('.preview-description').textContent = description;
            previewCard.querySelector('.preview-category').textContent = category;
            previewCard.querySelector('.preview-amount').textContent = '$' + amount;
            
            // Show success message
            showAlert('Preview updated successfully!', 'success');
        }

        // Switch to create page
        function switchToCreatePage() {
            window.location.href = '{{ route("user.campaign.create") }}';
        }

        // Show alert function
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.dashboard-content .container');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // File upload handling
        document.getElementById('gigImages').addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                showAlert(`${files.length} image(s) selected successfully!`, 'info');
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Rewards functionality
        function saveReward() {
            const title = document.getElementById('rewardTitle').value;
            const type = document.getElementById('rewardType').value;
            const description = document.getElementById('rewardDescription').value;
            const minAmount = document.getElementById('minimumAmount').value;
            const quantity = document.getElementById('rewardQuantity').value;
            const color = document.getElementById('rewardColor').value;
            const autoActivate = document.getElementById('autoActivate').checked;

            if (!title || !type || !description || !minAmount) {
                showAlert('Please fill in all required fields!', 'info');
                return;
            }

            // Here you would typically save to database
            // For now, we'll just show a success message
            showAlert('Reward created successfully!', 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addRewardModal'));
            modal.hide();
            
            // Reset form
            document.getElementById('addRewardForm').reset();
        }

        // Reward image upload handling
        document.getElementById('rewardImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    showAlert('Image size should be less than 2MB!', 'info');
                    this.value = '';
                    return;
                }
                showAlert('Reward image uploaded successfully!', 'success');
            }
        });

        // Reward color theme preview
        document.getElementById('rewardColor').addEventListener('change', function(e) {
            const color = e.target.value;
            // You can add preview functionality here
            console.log('Selected color theme:', color);
        });

        // Edit reward function
        function editReward(rewardId) {
            // Implementation for editing rewards
            showAlert('Edit functionality will be implemented here!', 'info');
        }

        // Delete reward function
        function deleteReward(rewardId) {
            if (confirm('Are you sure you want to delete this reward?')) {
                // Implementation for deleting rewards
                showAlert('Reward deleted successfully!', 'success');
            }
        }
    </script>
    <script src="{{ asset('assets/universal/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/universal/js/bootstrap.js') }}"></script>
        <script src="{{ asset('apnafund/assets/themes/'.bs('active_theme').'/js/slick.min.js') }}"></script>
        <script src="{{ asset($activeThemeTrue . 'js/viewport.jquery.js') }}"></script>
        <script src="{{ asset($activeThemeTrue . 'js/lightbox.min.js') }}"></script>
        <script src="{{ asset($activeThemeTrue . 'js/aos.js') }}"></script>
        <script src="{{ asset($activeThemeTrue . 'js/main.js') }}"></script>

        @include('partials.plugins')
        @include('partials.toasts')

        @stack('page-script-lib')
        Test here
        @yield('page-script')
</body>

</html> 