<?php
/**
 * 🚀 PUSH TO SERVER - ApnaFund
 * Manual file upload script for live server
 */

echo "<h1>🚀 ApnaFund - Push to Server</h1>";

echo "<h2>📋 Files to Upload to Live Server</h2>";

$filesToPush = [
    // Core Social Login Files
    'app/Http/Controllers/User/Auth/SocialLoginController.php' => 'Social Login Controller',
    'app/Http/Controllers/Admin/SocialLoginSettingController.php' => 'Admin Social Login Settings',
    'config/services.php' => 'Services Configuration',
    'routes/user.php' => 'User Routes',
    'routes/admin.php' => 'Admin Routes',
    'app/Models/User.php' => 'User Model',
    'resources/views/themes/apnafund/user/auth/login.blade.php' => 'Login View',
    'resources/views/admin/setting/social_login.blade.php' => 'Admin Social Login View',
    'resources/views/admin/partials/sidebar.blade.php' => 'Admin Sidebar',
    'database/migrations/2024_01_15_000000_add_social_login_fields_to_users_table.php' => 'Database Migration',
    
    // Admin User Management Files
    'app/Http/Controllers/Admin/UserController.php' => 'Admin User Controller',
    'resources/views/admin/user/delete-all-users.blade.php' => 'Delete Users View',
    
    // Optional Files
    'fix_social_login_live.php' => 'Live Server Fix Script',
    'test_social_final.php' => 'Social Login Test',
    'SERVER_PUSH_CHECKLIST.md' => 'Push Checklist'
];

echo "<table border='1' cellpadding='10' style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th>File Path</th><th>Description</th><th>Status</th><th>Size</th>";
echo "</tr>";

$totalSize = 0;
$existingFiles = 0;

foreach ($filesToPush as $file => $description) {
    $status = file_exists($file) ? '✅ Exists' : '❌ Missing';
    $size = file_exists($file) ? filesize($file) : 0;
    $sizeFormatted = $size > 0 ? number_format($size) . ' bytes' : 'N/A';
    
    if (file_exists($file)) {
        $existingFiles++;
        $totalSize += $size;
    }
    
    $rowColor = file_exists($file) ? '#d4edda' : '#f8d7da';
    
    echo "<tr style='background: $rowColor;'>";
    echo "<td><code>$file</code></td>";
    echo "<td>$description</td>";
    echo "<td>$status</td>";
    echo "<td>$sizeFormatted</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>📊 Upload Summary</h2>";
echo "<div style='background: #e9ecef; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
echo "<p><strong>Total Files:</strong> " . count($filesToPush) . "</p>";
echo "<p><strong>Existing Files:</strong> $existingFiles</p>";
echo "<p><strong>Missing Files:</strong> " . (count($filesToPush) - $existingFiles) . "</p>";
echo "<p><strong>Total Size:</strong> " . number_format($totalSize) . " bytes (" . round($totalSize/1024, 2) . " KB)</p>";
echo "</div>";

echo "<h2>🚀 Upload Methods</h2>";

echo "<h3>Method 1: FTP/SFTP Upload</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<p><strong>Steps:</strong></p>";
echo "<ol>";
echo "<li>Connect to your server via FTP/SFTP</li>";
echo "<li>Upload all files maintaining directory structure</li>";
echo "<li>Set proper file permissions (644 for files, 755 for directories)</li>";
echo "<li>Run commands on server (see below)</li>";
echo "</ol>";
echo "</div>";

echo "<h3>Method 2: cPanel File Manager</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<p><strong>Steps:</strong></p>";
echo "<ol>";
echo "<li>Login to cPanel</li>";
echo "<li>Open File Manager</li>";
echo "<li>Navigate to your website directory</li>";
echo "<li>Upload files maintaining directory structure</li>";
echo "<li>Run commands via Terminal (if available)</li>";
echo "</ol>";
echo "</div>";

echo "<h3>Method 3: Git (if repository exists)</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<pre style='background: #000; color: #0f0; padding: 10px; border-radius: 5px;'>";
echo "git add .\n";
echo "git commit -m 'Add social login and admin features'\n";
echo "git push origin main";
echo "</pre>";
echo "</div>";

echo "<h2>⚡ After Upload - Run These Commands</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<pre style='background: #000; color: #0f0; padding: 10px; border-radius: 5px;'>";
echo "# Run these commands on your live server:\n";
echo "php artisan migrate\n";
echo "php artisan config:clear\n";
echo "php artisan cache:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear";
echo "</pre>";
echo "</div>";

echo "<h2>🔧 Environment Variables to Add</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<p><strong>Add these to your .env file on live server:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
echo "# Social Login (Optional - can be disabled)\n";
echo "FACEBOOK_CLIENT_ID=your_facebook_app_id\n";
echo "FACEBOOK_CLIENT_SECRET=your_facebook_app_secret\n";
echo "FACEBOOK_REDIRECT_URI=https://yourdomain.com/user/auth/facebook/callback\n";
echo "GOOGLE_CLIENT_ID=your_google_client_id\n";
echo "GOOGLE_CLIENT_SECRET=your_google_client_secret\n";
echo "GOOGLE_REDIRECT_URI=https://yourdomain.com/user/auth/google/callback";
echo "</pre>";
echo "</div>";

echo "<h2>🧪 Testing After Upload</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<ol>";
echo "<li><strong>Test Login Page:</strong> Visit /login - should work without errors</li>";
echo "<li><strong>Test Admin Panel:</strong> Visit /admin - should work normally</li>";
echo "<li><strong>Test Social Login:</strong> Buttons should be hidden if credentials not set</li>";
echo "<li><strong>Test User Deletion:</strong> Admin → Users → Delete All Users</li>";
echo "<li><strong>Check Error Logs:</strong> Look for any PHP errors</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🚨 Important Notes</h2>";
echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<ul>";
echo "<li><strong>Backup:</strong> Always backup your live server before uploading</li>";
echo "<li><strong>Permissions:</strong> Set correct file permissions (644 for files, 755 for directories)</li>";
echo "<li><strong>Database:</strong> Run migration to add social login fields</li>";
echo "<li><strong>Cache:</strong> Clear all caches after upload</li>";
echo "<li><strong>Testing:</strong> Test all functionality before going live</li>";
echo "</ul>";
echo "</div>";

echo "<h2>📞 Support</h2>";
echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<p>If you encounter any issues:</p>";
echo "<ul>";
echo "<li>Check <code>fix_social_login_live.php</code> for solutions</li>";
echo "<li>Verify all files are uploaded correctly</li>";
echo "<li>Check server error logs</li>";
echo "<li>Test functionality step by step</li>";
echo "</ul>";
echo "</div>";

echo "<p style='background: #28a745; color: white; padding: 15px; border-radius: 8px; text-align: center; font-size: 18px; font-weight: bold;'>";
echo "🚀 Ready to Push! All files are prepared for upload.";
echo "</p>";
?>
