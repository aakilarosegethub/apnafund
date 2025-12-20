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
@extends($activeTheme . 'layouts.frontend')
@section('style')
<style>
        body {
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            line-height: 1.6;
            color: #333;
        }

        /* Header */
        .main-header {
            background: #05ce78;
            padding: 20px 0;
            margin: 0;
            border: none;
        }

        .main-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .main-header .logo {
            text-align: center;
            margin: 0;
        }

        .main-header .logo-img {
            height: 40px;
            width: auto;
        }

        /* Navigation */
        .nav-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            margin-top: 10px;
        }

        .nav-left,
        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .nav-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        .btn-start-fundraiser {
            background: #fff;
            color: #05ce78;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-start-fundraiser:hover {
            background: #f0f0f0;
            color: #05ce78;
            text-decoration: none;
        }

        /* Main Fundraiser Layout */
        .fundraiser-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        .fundraiser-main {
            flex: 2;
            min-width: 0;
        }

        .fundraiser-sidebar {
            flex: 1;
            max-width: 380px;
            position: sticky;
            top: 20px;
        }

        /* Hero Banner */
        .fundraiser-hero {
            position: relative;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .fundraiser-banner {
            width: 100%;
            height: 400px;
            position: relative;
            border-radius: 20px;
            overflow: hidden;
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
        }

        .banner-overlay img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 20px;
        }

        .banner-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 13px 0;
            color: #333;
            text-align: left;
        }

        /* Organizer Section */
        .organizer-section {
            margin-bottom: 30px;
        }

        .organizer-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .organizer-info {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .organizer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            margin-right: 15px;
        }

        .organizer-details h4 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .organizer-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .donation-protected {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #05ce78;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Description */
        .fundraiser-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 25px;
        }

        .read-more {
            color: #05ce78;
            text-decoration: none;
            font-weight: 600;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        /* Engagement */
        .engagement-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .engagement-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 0.95rem;
        }

        .engagement-item i {
            color: #05ce78;
            font-size: 1rem;
        }

        /* Actions */
        .fundraiser-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
        }

        .btn-donate {
            background: #fff;
            color: #333;
            border: 1px solid #ddd;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-donate:hover {
            background: #f8f9fa;
            color: #333;
            text-decoration: none;
        }

        .btn-share {
            background: #fff;
            color: #333;
            border: 1px solid #ddd;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-share:hover {
            background: #f8f9fa;
            color: #333;
            text-decoration: none;
        }

        /* Sidebar */
        .sidebar-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }

        .progress-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .progress-text {
            flex: 1;
        }

        .progress-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .progress-subtitle {
            font-size: 0.95rem;
            color: #666;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .progress-circle {
            position: relative;
            width: 80px;
            height: 80px;
            margin-left: 20px;
        }

        .progress-ring {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .progress-ring-bg {
            transition: all 0.3s ease;
        }

        .progress-ring-fill {
            transition: stroke-dashoffset 0.5s ease;
        }

        .progress-percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            z-index: 2;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }

        .btn-share-card {
            background: #333;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-share-card:hover {
            background: #444;
            color: #fff;
            text-decoration: none;
        }

        .btn-donate-card {
            background: #05ce78;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-donate-card:hover {
            background: #05ce78;
            color: #fff;
            text-decoration: none;
        }

        .donation-stats {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #666;
            font-size: 0.95rem;
        }

        .donation-stats i {
            color: #8b5cf6;
            font-size: 1.1rem;
        }

        .recent-donations {
            margin-top: 20px;
        }

        .donation-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .donation-item:last-child {
            border-bottom: none;
        }

        .donation-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .donation-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: #666;
        }

        .donation-details {
            display: flex;
            flex-direction: column;
        }

        .donation-name {
            font-weight: 500;
            color: #333;
            font-size: 0.9rem;
        }

        .donation-amount {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }

        .donation-type {
            color: #666;
            font-size: 0.8rem;
            text-decoration: underline;
        }

        .donation-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .donation-action-btn {
            background: #fff;
            color: #333;
            border: 1px solid #ddd;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .donation-action-btn:hover {
            background: #f8f9fa;
            color: #333;
            text-decoration: none;
        }

        .donation-action-btn i {
            margin-right: 4px;
        }

        /* Words of Support */
        .support-section {
            margin-top: 40px;
        }

        .support-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .support-prompt {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .support-placeholder {
            background: #05ce78;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 15px;
            color: #333;
            text-align: center;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .support-placeholder p {
            margin: 0 !important;
            color: #fff;
        }

        /* Fundraiser Details */
        .fundraiser-details {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        /* Rewards Section */
        .rewards-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }

        .rewards-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .rewards-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rewards-title i {
            color: #05ce78;
        }

        .rewards-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-add-reward, .btn-add-first-reward {
            background: #05ce78;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }

        .btn-add-reward:hover, .btn-add-first-reward:hover {
            background: #04b866;
        }

        .btn-manage-rewards {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }

        .btn-manage-rewards:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }

        .rewards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .reward-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .reward-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .reward-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .reward-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            flex: 1;
        }

        .reward-amount {
            background: #05ce78;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .reward-image {
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
        }

        .reward-image img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .reward-content {
            margin-bottom: 20px;
        }

        .reward-description {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .reward-details {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }

        .reward-type, .reward-quantity {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            color: #666;
        }

        .reward-type i, .reward-quantity i {
            color: #05ce78;
        }

        .reward-terms {
            color: #888;
            font-size: 0.85rem;
            font-style: italic;
        }

        .reward-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-get-reward {
            background: #05ce78;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-get-reward:hover {
            background: #04b866;
            color: white;
        }

        .reward-admin-actions {
            display: flex;
            gap: 8px;
        }

        .btn-edit-reward, .btn-delete-reward {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #666;
            padding: 8px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-edit-reward:hover {
            background: #e9ecef;
            color: #05ce78;
        }

        .btn-delete-reward:hover {
            background: #f8d7da;
            color: #dc3545;
            border-color: #f5c6cb;
        }

        .no-rewards {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-rewards i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .no-rewards h4 {
            margin-bottom: 10px;
            color: #333;
        }

        .no-rewards p {
            margin-bottom: 20px;
        }

        /* Reward Modal */
        .reward-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .reward-modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .reward-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
            border-radius: 12px 12px 0 0;
        }

        .reward-modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.3rem;
        }

        .reward-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
            padding: 5px;
        }

        .reward-modal-close:hover {
            color: #333;
        }

        .reward-modal-body {
            padding: 30px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #05ce78;
        }

        .input-group {
            display: flex;
            align-items: center;
        }

        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-right: none;
            padding: 10px 12px;
            border-radius: 6px 0 0 6px;
            color: #666;
            font-weight: 500;
        }

        .input-group input {
            border-radius: 0 6px 6px 0;
        }

        .image-preview {
            margin-top: 10px;
            text-align: center;
        }

        .preview-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .btn-save {
            background: #05ce78;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-save:hover {
            background: #04b866;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .reward-modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #666;
        }

        .report-link {
            color: #999;
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .report-link:hover {
            color: #666;
        }

        /* Features Section */
        .features-section {
            background: #05ce78;
            padding: 50px 0;
            margin: 50px 0;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature-card {
            text-align: center;
            padding: 30px 20px;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
        }

        .feature-icon i {
            color: #05ce78;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 10px;
        }

        .feature-description {
            color: #fff;
            line-height: 1.6;
        }

        /* Related Fundraisers */
        .related-fundraisers {
            margin: 50px 0;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .related-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .related-card:hover {
            transform: translateY(-5px);
        }

        .related-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .related-content {
            padding: 20px;
        }

        .related-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .related-organizer {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .related-amount {
            color: #05ce78;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            background: #333;
            color: #fff;
            padding: 40px 0 20px;
            margin-top: 50px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-section h4 {
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: #ccc;
            text-decoration: none;
        }

        .footer-section ul li a:hover {
            color: #05ce78;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #555;
            color: #ccc;
        }

        /* Reviews and Comments Section */
        .reviews-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #f0f0f0;
        }

        .reviews-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .reviews-prompt {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.4;
        }

        .review-form-container {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }

        .review-form-container h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .review-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .rating-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .rating-container label {
            font-weight: 500;
            color: #333;
            font-size: 0.95rem;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            gap: 2px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label.star {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star-rating label.star:hover,
        .star-rating label.star:hover~label.star,
        .star-rating input[type="radio"]:checked~label.star {
            color: #ffd700;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #05ce78;
            box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        }

        .btn-submit-review {
            background: #05ce78;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            align-self: flex-start;
        }

        .btn-submit-review:hover {
            background: #04b368;
        }

        .reviews-display {
            margin-top: 30px;
        }

        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .reviews-header h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .reviews-filter select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            background: #fff;
            cursor: pointer;
        }

        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .review-item {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            transition: box-shadow 0.3s ease;
        }

        .review-item:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #05ce78;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .reviewer-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .reviewer-name {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .review-rating {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stars {
            color: #ffd700;
            font-size: 0.9rem;
        }

        .rating-text {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }

        .review-date {
            color: #666;
            font-size: 0.85rem;
        }

        .review-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 10px 0;
        }

        .review-content {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }

        .review-actions {
            display: flex;
            gap: 15px;
        }

        .btn-like-review,
        .btn-reply-review {
            background: none;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-like-review:hover,
        .btn-reply-review:hover {
            background: #f8f9fa;
            border-color: #05ce78;
            color: #05ce78;
        }

        .btn-like-review i {
            color: #05ce78;
        }

        /* News Link and Modal Styles */
        .news-link-container {
            margin-top: 20px;
        }

        .news-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
            gap: 10px;
        }

        .news-link:hover {
            background: #e9ecef;
            color: #05ce78;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .news-link i:first-child {
            color: #05ce78;
            font-size: 1.1rem;
        }

        .news-link i:last-child {
            color: #666;
            font-size: 0.9rem;
        }

        /* News Modal */
        .news-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .news-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .news-modal-content {
            background: #fff;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .news-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 30px;
            border-bottom: 1px solid #f0f0f0;
        }

        .news-modal-header h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .news-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .news-modal-close:hover {
            background: #f8f9fa;
            color: #333;
        }

        .news-modal-body {
            padding: 30px;
        }

        .news-cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .news-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .news-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .news-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .news-date {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
        }

        .news-category {
            background: #05ce78;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .news-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 10px 0;
            line-height: 1.4;
        }

        .news-content {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }

        .news-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }

        .news-author {
            color: #05ce78;
            font-weight: 600;
        }

        .news-time {
            color: #666;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .fundraiser-container {
                flex-direction: column;
            }

            .fundraiser-sidebar {
                max-width: 100%;
            }

            .banner-title {
                font-size: 2rem;
            }

            .fundraiser-actions {
                flex-direction: column;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .related-grid {
                grid-template-columns: 1fr;
            }

            .reviews-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .review-header {
                flex-direction: column;
                gap: 10px;
            }

            .review-actions {
                flex-wrap: wrap;
            }

            /* News Modal Responsive */
            .news-modal-content {
                width: 95%;
                max-height: 90vh;
                margin: 20px;
            }

            .news-modal-header {
                padding: 20px;
            }

            .news-modal-header h3 {
                font-size: 1.2rem;
            }

            .news-modal-body {
                padding: 20px;
            }

            .news-card {
                padding: 15px;
            }

            .news-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .news-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }

        /* Share Modal - Swipe Up Animation */
        .share-modal {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .share-modal.show {
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .share-modal-content {
            background: #fff;
            border-radius: 20px 20px 0 0;
            width: 100%;
            max-width: 800px;
            max-height: 85vh;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.2);
        }

        .share-modal.show .share-modal-content {
            transform: translateY(0);
        }

        .share-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
        }

        .share-modal-header::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 4px;
            background: #ddd;
            border-radius: 2px;
        }

        .share-modal-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            text-align: center;
            flex: 1;
        }

        .share-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .share-modal-close:hover {
            background: #f8f9fa;
            color: #333;
        }

        .share-modal-body {
            padding: 25px;
        }

        .share-url-section {
            margin-bottom: 30px;
        }

        .share-url-section h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .url-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .url-input-group input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 14px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .url-input-group input:focus {
            outline: none;
            border-color: #05ce78;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        }

        .copy-btn {
            padding: 12px 20px;
            background: #05ce78;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .copy-btn:hover {
            background: #04a85f;
            transform: translateY(-1px);
        }

        .social-platforms {
            margin-bottom: 30px;
        }

        .social-platforms h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .platforms-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .platform-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 20px 15px;
            border: 2px solid #f0f0f0;
            border-radius: 15px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            background: #fff;
        }

        .platform-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            text-decoration: none;
            color: #333;
        }

        .platform-btn.facebook:hover {
            border-color: #1877f2;
            background: #f0f7ff;
        }

        .platform-btn.twitter:hover {
            border-color: #1da1f2;
            background: #f0f9ff;
        }

        .platform-btn.whatsapp:hover {
            border-color: #25d366;
            background: #f0fff4;
        }

        .platform-btn.telegram:hover {
            border-color: #0088cc;
            background: #f0f9ff;
        }

        .platform-btn.email:hover {
            border-color: #ea4335;
            background: #fff5f5;
        }

        .platform-btn.linkedin:hover {
            border-color: #0077b5;
            background: #f0f7ff;
        }

        .platform-btn.instagram:hover {
            border-color: #e4405f;
            background: #fff0f3;
        }

        .platform-btn.reddit:hover {
            border-color: #ff4500;
            background: #fff5f0;
        }

        .platform-btn.pinterest:hover {
            border-color: #bd081c;
            background: #fff0f0;
        }

        .platform-btn.copy:hover {
            border-color: #05ce78;
            background: #f0fff4;
        }

        .platform-icon {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        .platform-btn.facebook .platform-icon { color: #1877f2; }
        .platform-btn.twitter .platform-icon { color: #1da1f2; }
        .platform-btn.whatsapp .platform-icon { color: #25d366; }
        .platform-btn.telegram .platform-icon { color: #0088cc; }
        .platform-btn.email .platform-icon { color: #ea4335; }
        .platform-btn.linkedin .platform-icon { color: #0077b5; }
        .platform-btn.instagram .platform-icon { color: #e4405f; }
        .platform-btn.reddit .platform-icon { color: #ff4500; }
        .platform-btn.pinterest .platform-icon { color: #bd081c; }
        .platform-btn.copy .platform-icon { color: #05ce78; }

        .platform-name {
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
        }

        .campaign-preview {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .campaign-preview h5 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .campaign-preview p {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .campaign-preview .preview-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .preview-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #05ce78;
            font-weight: 600;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .platforms-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
            }
            
            .platform-btn {
                padding: 15px 10px;
            }
            
            .platform-icon {
                font-size: 1.8rem;
            }
            
            .platform-name {
                font-size: 0.8rem;
            }
            
            .share-modal-content {
                max-height: 90vh;
                max-width: 95%;
            }
        }

        /* Toast Notifications */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            z-index: 9999;
            max-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .toast-notification.success {
            background-color: #05ce78;
        }

        .toast-notification.error {
            background-color: #dc3545;
        }

        .toast-notification.info {
            background-color: #333;
        }

        /* Load More Button */
        .btn-load-more {
            width: 100%;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            font-weight: 600;
            color: #333;
            transition: all 0.3s ease;
        }

        .btn-load-more:hover {
            background: #e9ecef;
            border-color: #05ce78;
            color: #05ce78;
        }

        /* Form Improvements */
        .form-group input[type="email"] {
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 0.3s ease;
            width: 100%;
        }

        .form-group input[type="email"]:focus {
            outline: none;
            border-color: #05ce78;
            box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        }

        /* Donations Modal */
        .donations-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .donations-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .donations-modal-content {
            background: #fff;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease-out;
        }

        .donations-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 30px;
            border-bottom: 1px solid #f0f0f0;
        }

        .donations-modal-header h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .donations-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .donations-modal-close:hover {
            background: #f8f9fa;
            color: #333;
        }

        .donations-modal-body {
            padding: 30px;
        }

        .modal-donation-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .modal-donation-item:last-child {
            border-bottom: none;
        }

        .modal-donation-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-donation-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #05ce78;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .modal-donation-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .modal-donation-name {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        .modal-donation-date {
            color: #666;
            font-size: 0.85rem;
        }

        .modal-donation-amount {
            font-weight: 700;
            color: #05ce78;
            font-size: 1.1rem;
        }
    </style>
@endsection

@section('frontend')
@php
            $goalAmount = @$campaignData->goal_amount ?? 0;
            $raisedAmount = @$campaignData->raised_amount ?? 0;
            
            // If raised_amount is 0, try to calculate from deposits
            if ($raisedAmount == 0) {
                $raisedAmount = $campaignData->deposits()
                    ->where('status', \App\Constants\ManageStatus::PAYMENT_SUCCESS)
                    ->sum('amount');
            }
            
            $percentage = donationPercentage($goalAmount, $raisedAmount);
        @endphp
<!-- Main Fundraiser Section -->
    <div class="fundraiser-container mt-4">
        <!-- Main Content -->
        <div class="fundraiser-main">
            <!-- Hero Banner -->
            <div class="fundraiser-hero">
                <h1 class="banner-title">{{ __(@$campaignData->name) }}</h1>
                <div class="fundraiser-banner">
                    <div class="banner-overlay">
                        @if(@$campaignData->youtube_url ?? @$campaign->youtube_url)
                            <!-- YouTube Video - Simple Direct Embed -->
                            @php
                                $youtubeUrl = @$campaignData->youtube_url ?? @$campaign->youtube_url;
                                $videoId = '';
                                if (strpos($youtubeUrl, 'youtu.be/') !== false) {
                                    $videoId = explode('youtu.be/', $youtubeUrl)[1];
                                    $videoId = explode('?', $videoId)[0];
                                } elseif (strpos($youtubeUrl, 'youtube.com/watch?v=') !== false) {
                                    $videoId = explode('v=', $youtubeUrl)[1];
                                    $videoId = explode('&', $videoId)[0];
                                } elseif (strpos($youtubeUrl, 'youtube.com/embed/') !== false) {
                                    $videoId = explode('embed/', $youtubeUrl)[1];
                                    $videoId = explode('?', $videoId)[0];
                                }
                            @endphp
                            
                            @if($videoId)
                                <div style="width: 100%; height: 400px; border-radius: 10px; overflow: hidden; position: relative;">
                                    <!-- Campaign Image with Play Button (Initially Visible) -->
                                    <div id="campaign-image-{{ $videoId }}" style="width: 100%; height: 100%; position: relative; display: block;">
                                        <img src="{{ getImage(getFilePath('campaign') . '/' . (@$campaignData->image ?? @$campaign->image), getFileSize('campaign')) }}" 
                                             alt="{{ @$campaignData->name ?? @$campaign->name }}" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                        
                                        <!-- Play Button Overlay -->
                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; cursor: pointer;"
                                             onclick="showVideo('{{ $videoId }}')">
                                            <i class="fas fa-play" style="font-size: 30px; color: #05ce78; margin-left: 5px;"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- YouTube Video (Initially Hidden) -->
                                    <div id="youtube-video-{{ $videoId }}" style="width: 100%; height: 100%; display: none;">
                                        <iframe id="video-iframe-{{ $videoId }}" 
                                                src="" 
                                                style="width: 100%; height: 100%; border: none;" 
                                                frameborder="0" 
                                                allowfullscreen
                                                allow="autoplay; encrypted-media">
                                        </iframe>
                                    </div>
                                </div>
                            @else
                                <!-- Fallback to campaign image if video ID not found -->
                                <img src="{{ getImage(getFilePath('campaign') . '/' . (@$campaignData->image ?? @$campaign->image), getFileSize('campaign')) }}" alt="{{ @$campaignData->name ?? @$campaign->name }}">
                            @endif
                        @else
                            <!-- Regular Image Display -->
                        <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaignData->image, getFileSize('campaign')) }}"
                                alt="{{ @$campaignData->name }}">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Organizer Section -->
            <div class="organizer-section">
                <div class="organizer-info">
                    <div class="organizer-avatar">
                        @if($campaignData->user->image)
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $campaignData->user->image) }}" alt="{{ $campaignData->user->fullname }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ strtoupper(substr($campaignData->user->fullname, 0, 2)) }}
                        @endif
                    </div>
                    <div class="organizer-details">
                        <h4>{{ $campaignData->user->fullname }}</h4>
                        <p>is organizing this fundraiser</p>
                    </div>
                </div>
                <div class="donation-protected">
                    <i class="fas fa-shield-alt"></i>
                    Donation protected
                </div>
            </div>

            <!-- Description -->
            <div class="fundraiser-description">
                <h2 class="donation-details__title" data-aos="fade-up" data-aos-duration="1500">{{ __(@$campaignData->name) }}</h2>
            <div class="donation-details__desc" data-aos="fade-up" data-aos-duration="1500">
                @php echo @$campaignData->description @endphp
            </div>
            </div>


            <!-- Actions -->
            <div class="fundraiser-actions">
                <a href="{{ url('campaign/' . @$campaignData->slug . '/contribute') }}" class="btn-donate">Contribute</a>
                <a href="#" class="btn-share" onclick="openShareModal()">Share</a>
            </div>

            <!-- Organizer Details -->
            <div class="organizer-section">
                <h3>Organizer</h3>
                <div class="organizer-info">
                    <div class="organizer-avatar">
                        @if($campaignData->user->image)
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $campaignData->user->image) }}" alt="{{ $campaignData->user->fullname }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ strtoupper(substr($campaignData->user->fullname, 0, 2)) }}
                        @endif
                    </div>
                    <div class="organizer-details">
                        <h4>{{ $campaignData->user->fullname }}</h4>
                        <p>Organizer</p>
                        <p>{{ $campaignData->user->country_name ?? 'Location not specified' }}</p>
                    </div>
                </div>
                <a href="#" class="btn-share">Contact</a>
            </div>

            <!-- Reviews and Comments Section -->
            <div class="reviews-section">
                <h3>Comments & Reviews</h3>
                <p class="reviews-prompt">Share your thoughts and experiences about this campaign.</p>

                <!-- Review Form -->
                <div class="review-form-container">
                    <h4>Write a Comment</h4>
                    @if(!auth()->check())
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px;">
                            <i class="fas fa-info-circle"></i> 
                            You can comment as a guest. Just fill in your name and email below.
                        </p>
                    @endif
                    <form class="review-form" id="reviewForm" method="POST" action="{{ route('campaign.comment', $campaignData->slug) }}">
                        @csrf

                        <div class="form-group">
                            <label for="reviewTitle">Comment Title:</label>
                            <input type="text" id="reviewTitle" name="title" placeholder="Give your comment a title">
                        </div>

                        <div class="form-group">
                            <label for="reviewContent">Your Comment:</label>
                            <textarea id="reviewContent" name="comment" rows="4"
                                placeholder="Share your thoughts about this campaign..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="reviewerName">Your Name:</label>
                            <input type="text" id="reviewerName" name="name" placeholder="Enter your name" required>
                        </div>

                        <div class="form-group">
                            <label for="reviewerEmail">Your Email:</label>
                            <input type="email" id="reviewerEmail" name="email" placeholder="Enter your email" required>
                        </div>

                        <button type="submit" class="btn-submit-review">Submit Comment</button>
                        <button type="button" class="btn-submit-review" onclick="submitFormDirectly()" style="margin-left: 10px; background: #007bff;">Submit Directly (Test)</button>
                    </form>
                </div>

                <!-- Reviews Display -->
                <div class="reviews-display">
                    <div class="reviews-header">
                        <div>
                            <h4>Recent Comments</h4>
                            @php
                                $avgRating = $comments->whereNotNull('rating')->avg('rating');
                                $totalReviews = $comments->whereNotNull('rating')->count();
                            @endphp
                            @if($avgRating)
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">
                                    <span style="color: #ffd700;">★★★★★</span> 
                                    {{ number_format($avgRating, 1) }} average rating 
                                    ({{ $totalReviews }} {{ $totalReviews == 1 ? 'review' : 'reviews' }})
                                </p>
                            @endif
                        </div>
                        <div class="reviews-filter">
                            <select id="filterReviews">
                                <option value="all">All Reviews</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                    </div>

                    <div class="reviews-list" id="reviewsList">
                        @forelse ($comments as $comment)
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
                        @empty
                            <div class="review-item">
                                <div class="review-content text-center" style="color: #666; font-style: italic; padding: 40px 20px;">
                                    <i class="fas fa-star" style="font-size: 2rem; color: #ddd; margin-bottom: 15px; display: block;"></i>
                                    <h5 style="margin-bottom: 10px; color: #333;">No reviews yet</h5>
                                    <p>Be the first to leave a review and share your experience with this campaign!</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Fundraiser Details -->
            <div class="fundraiser-details">
                <div class="detail-item">
                    <span>Created 4 d ago</span>
                    <span>Medical</span>
                </div>
                <a href="{{ route('report.fundraiser') }}" class="report-link">
                    <i class="fas fa-flag"></i>
                    Report fundraiser
                </a>
            </div>

            <!-- Rewards Section -->
            <div class="rewards-section">
                <div class="rewards-header">
                    <h3 class="rewards-title">
                        <i class="fas fa-gift"></i>
                        Campaign Rewards
                    </h3>
                    @auth
                        @if(auth()->id() == $campaignData->user_id)
                            <div class="rewards-actions">
                                <button class="btn-add-reward" onclick="openRewardModal()">
                                    <i class="fas fa-plus"></i>
                                    Add More Rewards
                                </button>
                                <a href="{{ route('user.rewards.index', $campaignData->slug) }}" class="btn-manage-rewards">
                                    <i class="fas fa-cog"></i>
                                    Manage All Rewards
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
                
                <div class="rewards-grid" id="rewardsGrid">
                    @php
                        $campaignRewards = $campaignData->rewards()->active()->orderBy('minimum_amount')->get();
                    @endphp
                    
                    @if($campaignRewards->count() > 0)
                        @foreach($campaignRewards as $reward)
                            <div class="reward-card" data-reward-id="{{ $reward->id }}">
                                <div class="reward-header">
                                    <h4 class="reward-title">{{ $reward->title }}</h4>
                                    <span class="reward-amount">{{ $setting->cur_sym . showAmount($reward->minimum_amount) }}</span>
                                </div>
                                
                                @if($reward->image)
                                    <div class="reward-image">
                                        <img src="{{ getImage(getFilePath('reward') . '/' . $reward->image, getThumbSize('reward')) }}" 
                                             alt="{{ $reward->title }}">
                                    </div>
                                @endif
                                
                                <div class="reward-content">
                                    <p class="reward-description">{{ $reward->description }}</p>
                                    
                                    <div class="reward-details">
                                        <div class="reward-type">
                                            <i class="fas fa-{{ $reward->type == 'physical' ? 'box' : 'download' }}"></i>
                                            {{ ucfirst($reward->type) }} Reward
                                        </div>
                                        
                                        @if($reward->quantity)
                                            <div class="reward-quantity">
                                                <i class="fas fa-layer-group"></i>
                                                {{ $reward->getRemainingQuantity() }} left
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($reward->terms_conditions)
                                        <div class="reward-terms">
                                            <small>{{ $reward->terms_conditions }}</small>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="reward-actions">
                                    <a href="{{ route('campaign.donate', $campaignData->slug) }}?reward={{ $reward->id }}" 
                                       class="btn-get-reward">
                                        Get This Reward
                                    </a>
                                    
                                    @auth
                                        @if(auth()->id() == $campaignData->user_id)
                                            <div class="reward-admin-actions">
                                                <button class="btn-edit-reward" onclick="editReward({{ $reward->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-delete-reward" onclick="deleteReward({{ $reward->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-rewards">
                            <i class="fas fa-gift"></i>
                            <h4>No Rewards Available</h4>
                            <p>This campaign doesn't have any rewards yet.</p>
                            @auth
                                @if(auth()->id() == $campaignData->user_id)
                                    <button class="btn-add-first-reward" onclick="openRewardModal()">
                                        <i class="fas fa-plus"></i>
                                        Add Your First Reward
                                    </button>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="fundraiser-sidebar">
            <!-- Progress Card -->
            <div class="sidebar-card mt-5">
                <div class="progress-section">
                    <div class="progress-text">
                        <h3 class="progress-title">{{ $setting->cur_sym . showAmount($raisedAmount) }} raised</h3>
                        <div class="progress-subtitle align-items-center">
                            <span>{{ $percentage . '%' }} of goal</span>
                            <span>{{ showAmount($goalAmount).$setting->cur_sym }} goal</span>
                        </div>
                    </div>
                    <div class="progress-circle">
                        <svg viewBox="0 0 100 100" class="progress-ring">
                            <circle class="progress-ring-bg" cx="50" cy="50" r="40" stroke-width="8" fill="none" stroke="#e8e8e8"/>
                            <circle 
                                class="progress-ring-fill" 
                                cx="50" cy="50" r="40" 
                                stroke-width="8" 
                                fill="none" 
                                stroke="#05ce78" 
                                stroke-linecap="round" 
                                stroke-dasharray="251.2" 
                                stroke-dashoffset="{{ 251.2 - (251.2 * floatval($percentage) / 100) }}"
                            />
                        </svg>
                        {}
                        <span class="progress-percentage">{{ $percentage . '%' }}</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="#" class="btn-share-card" onclick="openShareModal()">Share</a>
                    <a href="{{ url('campaign/' . @$campaignData->slug . '/contribute') }}" class="btn-donate-card">Contribute now</a>
                </div>

                <div class="donation-stats">
                    <i class="fas fa-chart-line"></i>
                    <span>{{ $donations->count() }} people just donated</span>
                </div>

                <div class="recent-donations">
                    @forelse ($donations as $donation)
                        <div class="donation-item">
                            <div class="donation-info">
                                <i class="fas fa-heart" style="color: #666; font-size: 0.9rem;"></i>
                                <div class="donation-details">
                                    <span class="donation-name">{{ __($donation->donorName) }}</span>
                                    <span class="donation-amount">{{ $setting->cur_sym . showAmount($donation->amount) }}</span>
                                </div>
                            </div>
                            <span class="donation-type">
                                @if($loop->first)
                                    Recent donation
                                @elseif($donation->amount == $donations->max('amount'))
                                    Top donation
                                @else
                                    {{ diffForHumans($donation->created_at) }}
                                @endif
                            </span>
                        </div>
                    @empty
                        <div class="donation-item">
                            <div class="donation-info">
                                <i class="fas fa-heart" style="color: #666; font-size: 0.9rem;"></i>
                                <div class="donation-details">
                                    <span class="donation-name">No donations yet</span>
                                    <span class="donation-amount">Be the first!</span>
                                </div>
                            </div>
                            <span class="donation-type">Start donating</span>
                        </div>
                    @endforelse
                    
                    @if($donations->count() > 0)
                        <div class="donation-actions">
                            <button class="donation-action-btn" id="showAllDonations">See all</button>
                            <button class="donation-action-btn" id="showTopDonations">
                                <i class="fas fa-star"></i>
                                See top
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Latest News Link -->
            <div class="news-link-container mt-4">
                <a href="#" class="news-link" id="newsLink">
                    <i class="fas fa-newspaper"></i>
                    <span>Latest News & Updates</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Donations Modal -->
    <div class="donations-modal" id="donationsModal">
        <div class="donations-modal-content">
            <div class="donations-modal-header">
                <h3 id="donationsModalTitle">All Donations</h3>
                <button class="donations-modal-close" id="closeDonationsModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="donations-modal-body">
                <div id="donationsList"></div>
            </div>
        </div>
    </div>

    <!-- Reward Modal -->
    <div class="reward-modal" id="rewardModal">
        <div class="reward-modal-content">
            <div class="reward-modal-header">
                <h3 id="rewardModalTitle">Add New Reward</h3>
                <button class="reward-modal-close" id="closeRewardModal" onclick="closeRewardModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="reward-modal-body">
                <form id="rewardForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="rewardId" name="reward_id">
                    <input type="hidden" id="campaignSlug" name="campaign_slug" value="{{ $campaignData->slug }}">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rewardTitle">Reward Title *</label>
                            <input type="text" id="rewardTitle" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="rewardAmount">Minimum Amount *</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $setting->cur_sym }}</span>
                                <input type="number" id="rewardAmount" name="minimum_amount" step="0.01" min="1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="rewardDescription">Description *</label>
                        <textarea id="rewardDescription" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rewardType">Reward Type *</label>
                            <select id="rewardType" name="type" required>
                                <option value="">Select Type</option>
                                <option value="physical">Physical Reward</option>
                                <option value="digital">Digital Reward</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rewardQuantity">Available Quantity</label>
                            <input type="number" id="rewardQuantity" name="quantity" min="1" placeholder="Leave empty for unlimited">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rewardColor">Color Theme *</label>
                            <select id="rewardColor" name="color_theme" required>
                                <option value="">Select Color</option>
                                <option value="primary">Primary (Blue)</option>
                                <option value="success">Success (Green)</option>
                                <option value="warning">Warning (Yellow)</option>
                                <option value="danger">Danger (Red)</option>
                                <option value="info">Info (Cyan)</option>
                                <option value="secondary">Secondary (Gray)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rewardImage">Reward Image</label>
                            <input type="file" id="rewardImage" name="image" accept="image/*">
                            <div id="imagePreview" class="image-preview" style="display: none;">
                                <img id="previewImg" class="preview-image">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="rewardTerms">Terms & Conditions</label>
                        <textarea id="rewardTerms" name="terms_conditions" rows="2" placeholder="Any special terms or conditions for this reward"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeRewardModal()">Cancel</button>
                        <button type="submit" class="btn-save">Save Reward</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div class="share-modal" id="shareModal">
        <div class="share-modal-content">
            <div class="share-modal-header">
                <h3>Share Campaign</h3>
                <button class="share-modal-close" id="closeShareModal" onclick="closeShareModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="share-modal-body">

                <!-- Share URL Section -->
                <div class="share-url-section">
                    <h4>Campaign Link</h4>
                    <div class="url-input-group">
                        <input type="text" id="shareUrl" value="{{ url('campaign/' . @$campaignData->slug) }}" readonly>
                        <button class="copy-btn" onclick="copyShareUrl()">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>

                <!-- Social Platforms -->
                <div class="social-platforms">
                    <h4>Share on Social Media</h4>
                    <div class="platforms-grid">
                        <a href="#" class="platform-btn facebook" onclick="shareOnFacebook()">
                            <i class="fab fa-facebook-f platform-icon"></i>
                            <span class="platform-name">Facebook</span>
                        </a>
                        <a href="#" class="platform-btn twitter" onclick="shareOnTwitter()">
                            <i class="fab fa-twitter platform-icon"></i>
                            <span class="platform-name">Twitter</span>
                        </a>
                        <a href="#" class="platform-btn whatsapp" onclick="shareOnWhatsApp()">
                            <i class="fab fa-whatsapp platform-icon"></i>
                            <span class="platform-name">WhatsApp</span>
                        </a>
                        <a href="#" class="platform-btn telegram" onclick="shareOnTelegram()">
                            <i class="fab fa-telegram platform-icon"></i>
                            <span class="platform-name">Telegram</span>
                        </a>
                        <a href="#" class="platform-btn email" onclick="shareViaEmail(); return false;">
                            <i class="fas fa-envelope platform-icon"></i>
                            <span class="platform-name">Email</span>
                        </a>
                        <a href="#" class="platform-btn linkedin" onclick="shareOnLinkedIn()">
                            <i class="fab fa-linkedin-in platform-icon"></i>
                            <span class="platform-name">LinkedIn</span>
                        </a>
                        <a href="#" class="platform-btn instagram" onclick="shareOnInstagram(); return false;">
                            <i class="fab fa-instagram platform-icon"></i>
                            <span class="platform-name">Instagram</span>
                        </a>
                        <a href="#" class="platform-btn reddit" onclick="shareOnReddit()">
                            <i class="fab fa-reddit-alien platform-icon"></i>
                            <span class="platform-name">Reddit</span>
                        </a>
                        <a href="#" class="platform-btn pinterest" onclick="shareOnPinterest()">
                            <i class="fab fa-pinterest platform-icon"></i>
                            <span class="platform-name">Pinterest</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/select2.css') }}">
@endpush

@push('page-style')
    <style>
        .anonymous-alert-text .alert {
            background-color: #cff4fc !important;
        }

        .select2-results__option {
            padding-left: 16px;
        }

        /* Share Modal Styles */
        .share-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .share-modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .share-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
        }

        .share-modal-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }

        .share-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .share-modal-close:hover {
            background: #f8f9fa;
            color: #333;
        }

        .share-modal-body {
            padding: 25px;
        }

        .share-option {
            margin-bottom: 25px;
        }

        .input-group {
            display: flex;
            gap: 10px;
        }

        .input-group input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .input-group button {
            padding: 12px 20px;
            background: #05ce78;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .input-group button:hover {
            background: #04a85f;
        }

        .social-share-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
        }

        .social-share-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: all 0.2s ease;
            text-align: center;
        }

        .social-share-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: white;
            text-decoration: none;
        }

        .social-share-btn.facebook { background: #1877f2; }
        .social-share-btn.twitter { background: #1da1f2; }
        .social-share-btn.whatsapp { background: #25d366; }
        .social-share-btn.telegram { background: #0088cc; }
        .social-share-btn.email { background: #ea4335; }

        .social-share-btn i {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .share-modal-content {
                margin: 10% auto;
                width: 95%;
            }
            
            .social-share-buttons {
                grid-template-columns: 1fr;
            }
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 15px 20px;
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast-success {
            border-left: 4px solid #05ce78;
        }

        .toast-error {
            border-left: 4px solid #dc3545;
        }

        .toast i {
            font-size: 1.2rem;
        }

        .toast-success i {
            color: #05ce78;
        }

        .toast-error i {
            color: #dc3545;
        }
    </style>
@endpush

@push('page-script-lib')
@endpush

@push('page-script')
    <script>
        (function ($) {
            'use strict'

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            $('#anonymousDonation').on('change', function () {
                if ($(this).is(':checked')) {
                    $('.anonymous-alert-text').removeClass('d-none')
                } else {
                    $('.anonymous-alert-text').addClass('d-none')
                }
            })

            $('[name=gateway]').change(function() {
                if (!$('[name=gateway]').val()) {
                    $('.preview-details').addClass('d-none')

                    return false
                }

                var resource       = $('[name=gateway] option:selected').data('gateway')
                var fixed_charge   = parseFloat(resource.fixed_charge)
                var percent_charge = parseFloat(resource.percent_charge)
                var rate           = parseFloat(resource.rate)

                $('.min').text(parseFloat(resource.min_amount).toFixed(2))
                $('.max').text(parseFloat(resource.max_amount).toFixed(2))

                var amount = parseFloat($('[name=amount]').val())

                if (!amount) amount = 0

                if (amount <= 0) {
                    $('.preview-details').addClass('d-none')

                    return false
                }

                $('.preview-details').removeClass('d-none')

                var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2)
                $('.charge').text(charge)

                var payable = parseFloat(parseFloat(amount) + parseFloat(charge)).toFixed(2)
                $('.payable').text(payable)

                var final_amount = (parseFloat(parseFloat(amount) + parseFloat(charge)) * rate).toFixed(2)
                $('.final_amount').text(final_amount)

                if (resource.currency != '{{ $setting->site_cur }}') {
                    var rateElement = `<span>@lang('Conversion Rate')</span> <span class="fw-bold">1 {{ __($setting->site_cur) }} = <span class="rate">${rate}</span> <span class="method_currency">${resource.currency}</span></span>`;

                    $('.rate-element').html(rateElement)
                    $('.rate-element').removeClass('d-none')
                    $('.rate-element').addClass('d-flex')
                    $('.in-site-cur').removeClass('d-none')
                    $('.in-site-cur').addClass('d-flex')
                } else {
                    $('.rate-element').html('')
                    $('.rate-element').addClass('d-none')
                    $('.rate-element').removeClass('d-flex')
                    $('.in-site-cur').addClass('d-none')
                    $('.in-site-cur').removeClass('d-flex')
                }

                $('.method_currency').text(resource.currency)
                $('[name=currency]').val(resource.currency)
            })

            $('[name=amount]').on('input', function() {
                $('[name=gateway]').change()
            })

            $(document).on('change', '[name=donationAmount]', function () {
                $('[name=gateway]').change()
            })

            // Pre-fill form with user data if logged in
            @if(auth()->check())
                $(document).ready(function() {
                    $('#reviewerName').val('{{ auth()->user()->fullname }}');
                    $('#reviewerEmail').val('{{ auth()->user()->email }}');
                    $('#reviewerName, #reviewerEmail').prop('readonly', true);
                });
            @endif

            // Review Form Submission
            $('#reviewForm').on('submit', function(e) {
                e.preventDefault();
                
                console.log('Form submission started...');
                
                // Basic validation
                var name = $('#reviewerName').val().trim();
                var email = $('#reviewerEmail').val().trim();
                var comment = $('#reviewContent').val().trim();
                
                console.log('Form data:', {name: name, email: email, comment: comment});
                
                if (!name) {
                    showToast('error', 'Please enter your name.');
                    return false;
                }
                
                if (!email) {
                    showToast('error', 'Please enter your email.');
                    return false;
                }
                
                if (!comment) {
                    showToast('error', 'Please enter your comment.');
                    return false;
                }
                
                // Basic email validation
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showToast('error', 'Please enter a valid email address.');
                    return false;
                }
                
                var formData = new FormData(this);
                var submitBtn = $(this).find('.btn-submit-review');
                var originalText = submitBtn.text();
                
                // Disable submit button
                submitBtn.prop('disabled', true).text('Submitting...');
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        console.log('Submitting comment form...');
                    },
                    success: function(response) {
                        // Show success toast
                        showToast('success', 'Comment submitted successfully! Please wait for admin approval.');
                        
                        // Reset form
                        $('#reviewForm')[0].reset();
                        
                        // Reload page after a short delay to show new comment
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        console.error('Comment submission error:', xhr);
                        var message = 'An error occurred while submitting your comment.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessages = [];
                            for (var field in errors) {
                                errorMessages.push(errors[field][0]);
                            }
                            message = errorMessages.join(', ');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.status === 419) {
                            message = 'Session expired. Please refresh the page and try again.';
                        } else if (xhr.status === 422) {
                            message = 'Please check your form data and try again.';
                        }
                        showToast('error', message);
                        
                        // Fallback: submit form normally if AJAX fails
                        console.log('AJAX failed, submitting form normally...');
                        setTimeout(function() {
                            $('#reviewForm')[0].submit();
                        }, 2000);
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Review Filtering
            $('#filterReviews').on('change', function() {
                var selectedRating = $(this).val();
                var reviewItems = $('.review-item');
                
                if (selectedRating === 'all') {
                    reviewItems.show();
                } else {
                    reviewItems.hide();
                    reviewItems.filter('[data-rating="' + selectedRating + '"]').show();
                }
            });

            // Toast Notification Function
            function showToast(type, message) {
                // Create toast element
                var toast = $('<div class="toast-notification ' + type + '">' + message + '</div>');
                
                // Add to page
                $('body').append(toast);
                
                // Animate in
                setTimeout(function() {
                    toast.css('transform', 'translateX(0)');
                }, 100);
                
                // Remove after 5 seconds
                setTimeout(function() {
                    toast.css('transform', 'translateX(100%)');
                    setTimeout(function() {
                        toast.remove();
                    }, 300);
                }, 5000);
            }

            // Load more reviews functionality
            var skip = {{ $comments->count() }};
            var loading = false;
            
            function loadMoreReviews() {
                if (loading) return;
                
                loading = true;
                var loadMoreBtn = $('<button class="btn-load-more">Loading more reviews...</button>');
                
                if ($('.btn-load-more').length === 0) {
                    $('#reviewsList').after(loadMoreBtn);
                }
                
                $.ajax({
                    url: '{{ route("campaign.comment.fetch", $campaignData->slug) }}',
                    type: 'GET',
                    data: { skip: skip },
                    success: function(response) {
                        if (response.html) {
                            $('#reviewsList').append(response.html);
                            skip += 5;
                            
                            if (response.remaining_comments <= 0) {
                                $('.btn-load-more').remove();
                            }
                        }
                    },
                    error: function() {
                        showToast('error', 'Failed to load more reviews.');
                    },
                    complete: function() {
                        loading = false;
                        $('.btn-load-more').remove();
                    }
                });
            }
            
            // Add load more button if there are more reviews
            @if($commentCount > $comments->count())
                var loadMoreBtn = $('<button class="btn-load-more">Load More Reviews</button>');
                $('#reviewsList').after(loadMoreBtn);
                
                $(document).on('click', '.btn-load-more', function() {
                    loadMoreReviews();
                });
            @endif

            // Donations Modal Functionality
            var donationsData = @json($donations);
            
            // Show all donations
            $('#showAllDonations').on('click', function() {
                showDonationsModal('All Donations', donationsData);
            });
            
            // Show top donations
            $('#showTopDonations').on('click', function() {
                var topDonations = [...donationsData].sort((a, b) => parseFloat(b.amount) - parseFloat(a.amount));
                showDonationsModal('Top Donations', topDonations);
            });
            
            // Close modal
            $('#closeDonationsModal').on('click', function() {
                $('#donationsModal').removeClass('show');
            });
            
            // Close modal when clicking outside
            $('#donationsModal').on('click', function(e) {
                if (e.target === this) {
                    $(this).removeClass('show');
                }
            });
            
            function showDonationsModal(title, donations) {
                $('#donationsModalTitle').text(title);
                var html = '';
                
                if (donations.length > 0) {
                    donations.forEach(function(donation) {
                        var donorName = donation.donorName || 'Anonymous';
                        var donorInitials = donorName.split(' ').map(n => n[0]).join('').toUpperCase();
                        var donationDate = new Date(donation.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                        
                        html += `
                            <div class="modal-donation-item">
                                <div class="modal-donation-info">
                                    <div class="modal-donation-avatar">
                                        ${donorInitials}
                                    </div>
                                    <div class="modal-donation-details">
                                        <div class="modal-donation-name">${donorName}</div>
                                        <div class="modal-donation-date">${donationDate}</div>
                                    </div>
                                </div>
                                <div class="modal-donation-amount">
                                    {{ $setting->cur_sym }}${parseFloat(donation.amount).toLocaleString()}
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = `
                        <div class="modal-donation-item">
                            <div style="text-align: center; width: 100%; color: #666; font-style: italic; padding: 20px;">
                                No donations found
                            </div>
                        </div>
                    `;
                }
                
                $('#donationsList').html(html);
                $('#donationsModal').addClass('show');
            }

            // Share Modal Functions - Swipe Up Animation
            function openShareModal() {
                const modal = document.getElementById('shareModal');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                // Trigger animation after display is set
                setTimeout(() => {
                    modal.classList.add('show');
                }, 10);
            }

            
                
            // Close modal when clicking outside
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('shareModal');
                if (modal) {
                    modal.addEventListener('click', function(event) {
                        if (event.target === this) {
                            closeShareModal();
                        }
                    });
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeShareModal();
                }
            });

            // Touch/swipe gestures for mobile
            let startY = 0;
            let currentY = 0;
            let isDragging = false;
            const modal = document.getElementById('shareModal');
            const modalContent = document.querySelector('.share-modal-content');

            modal.addEventListener('touchstart', function(e) {
                startY = e.touches[0].clientY;
                isDragging = true;
            });

            modal.addEventListener('touchmove', function(e) {
                if (!isDragging) return;
                currentY = e.touches[0].clientY;
                const diff = currentY - startY;
                
                if (diff > 0) {
                    modalContent.style.transform = `translateY(${Math.min(diff, 100)}px)`;
                }
            });

            modal.addEventListener('touchend', function(e) {
                if (!isDragging) return;
                isDragging = false;
                
                const diff = currentY - startY;
                if (diff > 100) {
                    closeShareModal();
                } else {
                    modalContent.style.transform = 'translateY(0)';
                }
            });

            // Toast notification function
            function showToast(type, message) {
                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;
                toast.innerHTML = `
                    <div class="toast-content">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        <span>${message}</span>
                    </div>
                `;
                
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);
                
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 300);
                }, 3000);
            }

        })(jQuery)

        // Direct form submission function (for testing)
        function submitFormDirectly() {
            var name = document.getElementById('reviewerName').value.trim();
            var email = document.getElementById('reviewerEmail').value.trim();
            var comment = document.getElementById('reviewContent').value.trim();
            
            if (!name || !email || !comment) {
                showToast('error', 'Please fill in all required fields.');
                return;
            }
            
            // Basic email validation
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('error', 'Please enter a valid email address.');
                return;
            }
            
            console.log('Submitting form directly...');
            document.getElementById('reviewForm').submit();
        }

        // Form validation function
        function validateCommentForm() {
            var name = document.getElementById('reviewerName').value.trim();
            var email = document.getElementById('reviewerEmail').value.trim();
            var comment = document.getElementById('reviewContent').value.trim();
            
            if (!name) {
                showToast('error', 'Please enter your name.');
                return false;
            }
            
            if (!email) {
                showToast('error', 'Please enter your email.');
                return false;
            }
            
            if (!comment) {
                showToast('error', 'Please enter your comment.');
                return false;
            }
            
            // Basic email validation
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('error', 'Please enter a valid email address.');
                return false;
            }
            
            return true;
        }

        // Global showToast function
        function showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            toast.innerHTML = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);
            
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }

        // Simple function to show video
        function showVideo(videoId) {
            // Hide campaign image
            const imageDiv = document.getElementById('campaign-image-' + videoId);
            if (imageDiv) {
                imageDiv.style.display = 'none';
            }
            
            // Show YouTube video
            const videoDiv = document.getElementById('youtube-video-' + videoId);
            const iframe = document.getElementById('video-iframe-' + videoId);
            
            if (videoDiv && iframe) {
                videoDiv.style.display = 'block';
                
                // Set iframe source with autoplay
                iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1&showinfo=0&controls=1&mute=0`;
            }
        }
        // Share Modal Functions - Swipe Up Animation
function openShareModal() {
    const modal = document.getElementById('shareModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Trigger animation after display is set
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
        }

            function closeShareModal() {
                const modal = document.getElementById('shareModal');
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
                
                // Hide modal after animation completes
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 400);
            }
            function copyShareUrl() {
                const shareUrl = document.getElementById('shareUrl');
                shareUrl.select();
                shareUrl.setSelectionRange(0, 99999);
                
                try {
                    document.execCommand('copy');
                    showToast('success', 'Link copied to clipboard!');
                } catch (err) {
                    // Fallback for modern browsers
                    navigator.clipboard.writeText(shareUrl.value).then(function() {
                        showToast('success', 'Link copied to clipboard!');
                    }).catch(function() {
                        showToast('error', 'Failed to copy link');
                    });
                }
            }

            // Social Sharing Functions
            function shareOnFacebook() {
                const url = encodeURIComponent(document.getElementById('shareUrl').value);
                const title = encodeURIComponent('{{ @$campaignData->name }}');
                const description = encodeURIComponent('{{ Str::limit(strip_tags(@$campaignData->description), 100) }}');
                const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`;
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }

            function shareOnTwitter() {
                const url = encodeURIComponent(document.getElementById('shareUrl').value);
                const title = encodeURIComponent('{{ @$campaignData->name }}');
                const shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                }

            function shareOnWhatsApp() {
                const url = encodeURIComponent(document.getElementById('shareUrl').value);
                const title = encodeURIComponent('{{ @$campaignData->name }}');
                const shareUrl = `https://wa.me/?text=${title}%20${url}`;
                window.open(shareUrl, '_blank');
            }

            function shareOnTelegram() {
                const url = encodeURIComponent(document.getElementById('shareUrl').value);
                const title = encodeURIComponent('{{ @$campaignData->name }}');
                const shareUrl = `https://t.me/share/url?url=${url}&text=${title}`;
                window.open(shareUrl, '_blank');
            }

            function shareViaEmail() {
                const url = document.getElementById('shareUrl').value;
                const title = '{{ @$campaignData->name }}';
                const subject = encodeURIComponent(`Check out this campaign: ${title}`);
                const body = encodeURIComponent(`I found this interesting campaign and thought you might want to check it out:\n\n${title}\n\n${url}`);
                const shareUrl = `mailto:?subject=${subject}&body=${body}`;
                window.location.href = shareUrl;
                return false;
            }

            function shareOnLinkedIn() {
                const url = encodeURIComponent(document.getElementById('shareUrl').value);
                const title = encodeURIComponent('{{ @$campaignData->name }}');
                const shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }

            function shareOnInstagram() {
                // Copy the link to clipboard first
                copyShareUrl();
                
                // Show a more detailed message
                setTimeout(() => {
                    showToast('info', 'Link copied! Open Instagram and paste it in your story or post.');
                }, 1000);
                
                return false;
            }

            function shareOnReddit() {
                const url = encodeURIComponent(document.getElementById('shareUrl').value);
                const title = encodeURIComponent('{{ @$campaignData->name }}');
                const shareUrl = `https://reddit.com/submit?url=${url}&title=${title}`;
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }

            function shareOnPinterest() {
                const url = encodeURIComponent(document.getElementById('shareUrl').value);
                const title = encodeURIComponent('{{ @$campaignData->name }}');
                const image = encodeURIComponent('{{ getImage(getFilePath("campaign") . "/" . @$campaignData->image, getFileSize("campaign")) }}');
                const shareUrl = `https://pinterest.com/pin/create/button/?url=${url}&media=${image}&description=${title}`;
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }

            // Event listeners for share modal
            document.addEventListener('DOMContentLoaded', function() {
                const closeBtn = document.getElementById('closeShareModal');
                if (closeBtn) {
                    closeBtn.addEventListener('click', closeShareModal);
                }
            });

            // Reward Modal Functions
            function openRewardModal() {
                const modal = document.getElementById('rewardModal');
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                
                // Reset form
                document.getElementById('rewardForm').reset();
                document.getElementById('rewardModalTitle').textContent = 'Add New Reward';
                document.getElementById('rewardId').value = '';
                document.getElementById('imagePreview').style.display = 'none';
            }

            function closeRewardModal() {
                const modal = document.getElementById('rewardModal');
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            function editReward(rewardId) {
                // Fetch reward data and populate form
                fetch(`/user/campaign/{{ $campaignData->slug }}/rewards/${rewardId}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const reward = data.reward;
                            document.getElementById('rewardModalTitle').textContent = 'Edit Reward';
                            document.getElementById('rewardId').value = reward.id;
                            document.getElementById('rewardTitle').value = reward.title;
                            document.getElementById('rewardDescription').value = reward.description;
                            document.getElementById('rewardAmount').value = reward.minimum_amount;
                            document.getElementById('rewardType').value = reward.type;
                            document.getElementById('rewardQuantity').value = reward.quantity || '';
                            document.getElementById('rewardColor').value = reward.color_theme;
                            document.getElementById('rewardTerms').value = reward.terms_conditions || '';
                            
                            // Show existing image if available
                            if (reward.image) {
                                const preview = document.getElementById('imagePreview');
                                const previewImg = document.getElementById('previewImg');
                                previewImg.src = reward.image_url;
                                preview.style.display = 'block';
                            }
                            
                            openRewardModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching reward:', error);
                        alert('Error loading reward data');
                    });
            }

            function deleteReward(rewardId) {
                if (confirm('Are you sure you want to delete this reward?')) {
                    fetch(`/user/campaign/{{ $campaignData->slug }}/rewards/${rewardId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove reward card from DOM
                            const rewardCard = document.querySelector(`[data-reward-id="${rewardId}"]`);
                            if (rewardCard) {
                                rewardCard.remove();
                            }
                            
                            // Check if no rewards left
                            const rewardsGrid = document.getElementById('rewardsGrid');
                            if (rewardsGrid.children.length === 0) {
                                location.reload(); // Reload to show no rewards message
                            }
                        } else {
                            alert('Error deleting reward');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting reward:', error);
                        alert('Error deleting reward');
                    });
                }
            }

            // Image preview functionality
            document.getElementById('rewardImage').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                }
            });

            // Form submission
            document.getElementById('rewardForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const rewardId = document.getElementById('rewardId').value;
                const isEdit = rewardId !== '';
                
                const url = isEdit 
                    ? `/user/campaign/{{ $campaignData->slug }}/rewards/${rewardId}/update`
                    : `/user/campaign/{{ $campaignData->slug }}/rewards/store`;
                
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeRewardModal();
                        location.reload(); // Reload to show updated rewards
                    } else {
                        alert('Error saving reward: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error saving reward:', error);
                    alert('Error saving reward');
                });
            });

            // Close modal when clicking outside
            document.getElementById('rewardModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeRewardModal();
                }
            });
    </script>
@endpush
