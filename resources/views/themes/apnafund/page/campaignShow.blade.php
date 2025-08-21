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
            background: #05ce78;
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

        .progress-circle svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .progress-circle-bg {
            fill: none;
            stroke: #e8e8e8;
            stroke-width: 8;
        }

        .progress-circle-fill {
            fill: none;
            stroke: #05ce78;
            stroke-width: 8;
            stroke-linecap: round;
            stroke-dasharray: 226;
            stroke-dashoffset: 160;
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
    </style>
@endsection

@section('frontend')
@php
            $percentage = donationPercentage($campaignData->goal_amount, $campaignData->raised_amount);
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
                        <!-- Banner image content can go here if needed -->
                        <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaignData->image, getFileSize('campaign')) }}"
                            alt="">
                    </div>
                </div>
            </div>

            <!-- Organizer Section -->
            <div class="organizer-section">
                <div class="organizer-info">
                    <div class="organizer-avatar">JL</div>
                    <div class="organizer-details">
                        <h4>Jenny Langer</h4>
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

            <!-- Engagement -->
            <div class="engagement-section">
                <div class="engagement-item">
                    <i class="fas fa-heart"></i>
                    <span>1</span>
                </div>
                <div class="engagement-item">
                    <img src="assets/images/banner-1.jpg" alt="Banner"
                        style="width: 30px; height: 30px; border-radius: 4px; object-fit: cover;">
                </div>
            </div>

            <!-- Actions -->
            <div class="fundraiser-actions">
                <a href="{{ url('campaign/' . @$campaignData->slug . '/contribute') }}" class="btn-donate">Contribute</a>
                <a href="#" class="btn-share">Share</a>
            </div>

            <!-- Organizer Details -->
            <div class="organizer-section">
                <h3>Organizer</h3>
                <div class="organizer-info">
                    <div class="organizer-avatar">JL</div>
                    <div class="organizer-details">
                        <h4>Jenny Langer</h4>
                        <p>Organizer</p>
                        <p>Philadelphia, PA</p>
                    </div>
                </div>
                <a href="#" class="btn-share">Contact</a>
            </div>

            <!-- Words of Support -->
            <div class="support-section">
                <h3>Words of support</h3>
                <p class="support-prompt">Please donate to share words of support.</p>
                <div class="support-placeholder">
                    <p>No words of support yet</p>
                </div>
                <div class="support-placeholder">
                    <p>No words of support yet</p>
                </div>
                <div class="support-placeholder">
                    <p>No words of support yet</p>
                </div>
            </div>

            <!-- Reviews and Comments Section -->
            <div class="reviews-section">
                <h3>Reviews & Comments</h3>
                <p class="reviews-prompt">Share your thoughts and experiences about this fundraiser.</p>

                <!-- Review Form -->
                <div class="review-form-container">
                    <h4>Write a Review</h4>
                    <form class="review-form" id="reviewForm">
                        <div class="rating-container">
                            <label>Your Rating:</label>
                            <div class="star-rating">
                                <input type="radio" name="rating" value="5" id="star5">
                                <label for="star5" class="star">★</label>
                                <input type="radio" name="rating" value="4" id="star4">
                                <label for="star4" class="star">★</label>
                                <input type="radio" name="rating" value="3" id="star3">
                                <label for="star3" class="star">★</label>
                                <input type="radio" name="rating" value="2" id="star2">
                                <label for="star2" class="star">★</label>
                                <input type="radio" name="rating" value="1" id="star1">
                                <label for="star1" class="star">★</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reviewTitle">Review Title:</label>
                            <input type="text" id="reviewTitle" name="title" placeholder="Give your review a title"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="reviewContent">Your Review:</label>
                            <textarea id="reviewContent" name="content" rows="4"
                                placeholder="Share your experience with this fundraiser..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="reviewerName">Your Name:</label>
                            <input type="text" id="reviewerName" name="name" placeholder="Enter your name" required>
                        </div>

                        <button type="submit" class="btn-submit-review">Submit Review</button>
                    </form>
                </div>

                <!-- Reviews Display -->
                <div class="reviews-display">
                    <div class="reviews-header">
                        <h4>Recent Reviews</h4>
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
                        <!-- Sample Reviews -->
                        <div class="review-item" data-rating="5">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">SM</div>
                                    <div class="reviewer-details">
                                        <h5 class="reviewer-name">Sarah Mitchell</h5>
                                        <div class="review-rating">
                                            <span class="stars">★★★★★</span>
                                            <span class="rating-text">5.0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-date">2 days ago</div>
                            </div>
                            <h6 class="review-title">Amazing cause, well organized!</h6>
                            <p class="review-content">This fundraiser is incredibly well-organized and the cause is
                                truly worthy. Ron Holloway has been such an important part of the music community. I'm
                                glad to be able to support his medical expenses.</p>
                            <div class="review-actions">
                                <button class="btn-like-review">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span>12</span>
                                </button>
                                <button class="btn-reply-review">
                                    <i class="fas fa-reply"></i>
                                    Reply
                                </button>
                            </div>
                        </div>

                        <div class="review-item" data-rating="4">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">MJ</div>
                                    <div class="reviewer-details">
                                        <h5 class="reviewer-name">Mike Johnson</h5>
                                        <div class="review-rating">
                                            <span class="stars">★★★★☆</span>
                                            <span class="rating-text">4.0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-date">1 week ago</div>
                            </div>
                            <h6 class="review-title">Great initiative for a local legend</h6>
                            <p class="review-content">Ron has been a fixture in the DC music scene for decades. This
                                fundraiser is a great way to give back to someone who has given so much to our community
                                through music.</p>
                            <div class="review-actions">
                                <button class="btn-like-review">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span>8</span>
                                </button>
                                <button class="btn-reply-review">
                                    <i class="fas fa-reply"></i>
                                    Reply
                                </button>
                            </div>
                        </div>

                        <div class="review-item" data-rating="5">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">AL</div>
                                    <div class="reviewer-details">
                                        <h5 class="reviewer-name">Amy Lewis</h5>
                                        <div class="review-rating">
                                            <span class="stars">★★★★★</span>
                                            <span class="rating-text">5.0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-date">2 weeks ago</div>
                            </div>
                            <h6 class="review-title">Supporting a true artist</h6>
                            <p class="review-content">I've been following Ron's music for years. His talent and
                                dedication to the craft are unmatched. This fundraiser is helping a true artist in need,
                                and I'm proud to be part of it.</p>
                            <div class="review-actions">
                                <button class="btn-like-review">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span>15</span>
                                </button>
                                <button class="btn-reply-review">
                                    <i class="fas fa-reply"></i>
                                    Reply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fundraiser Details -->
            <div class="fundraiser-details">
                <div class="detail-item">
                    <span>Created 4 d ago</span>
                    <span>Medical</span>
                </div>
                <a href="#" class="report-link">
                    <i class="fas fa-flag"></i>
                    Report fundraiser
                </a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="fundraiser-sidebar">
            <!-- Progress Card -->
            <div class="sidebar-card mt-5">
                <div class="progress-section">
                    <div class="progress-text">
                        <h3 class="progress-title">{{ $setting->cur_sym . showAmount(@$campaignData->raised_amount) }} raised</h3>
                        <div class="progress-subtitle align-items-center">
                            <span>{{ $percentage . '%' }} of goal</span>
                            <span>{{ showAmount(@$campaignData->goal_amount).$setting->cur_sym }} goal</span>
                        </div>
                    </div>
                    <div class="progress-circle">
                        <svg viewBox="0 0 100 100">
                            <circle class="progress-circle-bg" cx="50" cy="50" r="45"></circle>
                            <circle class="progress-circle-fill" cx="50" cy="50" r="45"></circle>
                        </svg>
                        <span class="progress-percentage">{{ $percentage . '%' }}</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="#" class="btn-share-card">Share</a>
                    <a href="{{ url('campaign/' . @$campaignData->slug . '/contribute') }}" class="btn-donate-card">Contribute now</a>
                </div>

                <div class="donation-stats">
                    <i class="fas fa-chart-line"></i>
                    <span>807 people just donated</span>
                </div>

                <div class="recent-donations">
                    <div class="donation-item">
                        <div class="donation-info">
                            <i class="fas fa-heart" style="color: #666; font-size: 0.9rem;"></i>
                            <div class="donation-details">
                                <span class="donation-name">Lisa Glassman</span>
                                <span class="donation-amount">$36</span>
                            </div>
                        </div>
                        <span class="donation-type">Recent donation</span>
                    </div>
                    <div class="donation-item">
                        <div class="donation-info">
                            <i class="fas fa-heart" style="color: #666; font-size: 0.9rem;"></i>
                            <div class="donation-details">
                                <span class="donation-name">David Earl</span>
                                <span class="donation-amount">$1,000</span>
                            </div>
                        </div>
                        <span class="donation-type">Top donation</span>
                    </div>
                    <div class="donation-actions">
                        <a href="#" class="donation-action-btn">See all</a>
                        <a href="#" class="donation-action-btn">
                            <i class="fas fa-star"></i>
                            See top
                        </a>
                    </div>
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
    </style>
@endpush

@push('page-script-lib')
    <script src="{{ asset($activeThemeTrue . 'js/select2.js') }}"></script>
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

            $('.select-2').select2({
                containerCssClass: ':all:',
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
        })(jQuery)
    </script>
@endpush
