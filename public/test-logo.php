<?php
// Simple test page to check logo files
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logo Test - ApnaCrowdfunding</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
        }
        .logo-test {
            background: white;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .logo-test h3 {
            color: #333;
            margin: 0 0 10px 0;
        }
        .logo-test img {
            max-width: 200px;
            height: auto;
            border: 1px solid #ddd;
            padding: 10px;
            background: white;
        }
        .info {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
        .dark-bg {
            background: #333;
            padding: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>ApnaCrowdfunding Logo Test</h1>
    <p>Current time: <?php echo date('Y-m-d H:i:s'); ?></p>
    
    <div class="logo-test">
        <h3>Logo Light (For Dark Backgrounds)</h3>
        <div class="dark-bg">
            <img src="/assets/universal/images/logoFavicon/logo_light.png?v=<?php echo time(); ?>" alt="Logo Light">
        </div>
        <div class="info">
            <?php 
            $lightPath = __DIR__ . '/assets/universal/images/logoFavicon/logo_light.png';
            if (file_exists($lightPath)) {
                echo "✅ File exists | Size: " . filesize($lightPath) . " bytes | Modified: " . date('Y-m-d H:i:s', filemtime($lightPath));
            } else {
                echo "❌ File not found";
            }
            ?>
        </div>
    </div>
    
    <div class="logo-test">
        <h3>Logo Dark (For Light Backgrounds)</h3>
        <img src="/assets/universal/images/logoFavicon/logo_dark.png?v=<?php echo time(); ?>" alt="Logo Dark">
        <div class="info">
            <?php 
            $darkPath = __DIR__ . '/assets/universal/images/logoFavicon/logo_dark.png';
            if (file_exists($darkPath)) {
                echo "✅ File exists | Size: " . filesize($darkPath) . " bytes | Modified: " . date('Y-m-d H:i:s', filemtime($darkPath));
            } else {
                echo "❌ File not found";
            }
            ?>
        </div>
    </div>
    
    <div class="logo-test">
        <h3>Favicon</h3>
        <img src="/assets/universal/images/logoFavicon/favicon.png?v=<?php echo time(); ?>" alt="Favicon">
        <div class="info">
            <?php 
            $faviconPath = __DIR__ . '/assets/universal/images/logoFavicon/favicon.png';
            if (file_exists($faviconPath)) {
                echo "✅ File exists | Size: " . filesize($faviconPath) . " bytes | Modified: " . date('Y-m-d H:i:s', filemtime($faviconPath));
            } else {
                echo "❌ File not found";
            }
            ?>
        </div>
    </div>
    
    <div class="logo-test">
        <h3>Upload Test</h3>
        <p>To test logo upload:</p>
        <ol>
            <li>Go to Admin → Settings → Basic Settings</li>
            <li>Upload a logo in the "Logo and Favicon Preferences" section</li>
            <li>Refresh this page to see if the logo updated</li>
            <li>Check the file modification time above</li>
        </ol>
    </div>
    
    <div class="logo-test">
        <h3>Debug Info</h3>
        <p><strong>Directory Permissions:</strong></p>
        <ul>
            <li>Logo directory: <?php echo is_writable(__DIR__ . '/assets/universal/images/logoFavicon/') ? '✅ Writable' : '❌ Not writable'; ?></li>
            <li>Assets directory: <?php echo is_writable(__DIR__ . '/assets/') ? '✅ Writable' : '❌ Not writable'; ?></li>
        </ul>
        
        <p><strong>PHP Info:</strong></p>
        <ul>
            <li>Upload max filesize: <?php echo ini_get('upload_max_filesize'); ?></li>
            <li>Post max size: <?php echo ini_get('post_max_size'); ?></li>
            <li>Memory limit: <?php echo ini_get('memory_limit'); ?></li>
        </ul>
    </div>
    
    <style>
        .refresh-btn {
            background: #05ce78;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 0;
        }
        .refresh-btn:hover {
            background: #04b86b;
        }
    </style>
    
    <button class="refresh-btn" onclick="location.reload()">🔄 Refresh Page</button>
    
    <p><small>This test page helps you verify if logos are uploading correctly and being displayed properly.</small></p>
</body>
</html>
