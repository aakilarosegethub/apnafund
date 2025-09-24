<?php
/**
 * Test Delete All Users Functionality
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;

echo "<h1>🗑️ Test Delete All Users</h1>";

echo "<h2>📊 Current User Count:</h2>";
$userCount = User::count();
echo "<p><strong>Total Users:</strong> $userCount</p>";

if ($userCount > 0) {
    echo "<h2>👥 Current Users:</h2>";
    $users = User::select('id', 'username', 'email', 'firstname', 'lastname', 'created_at')
                 ->orderBy('created_at', 'desc')
                 ->limit(10)
                 ->get();
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Created</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user->id . "</td>";
        echo "<td>" . $user->username . "</td>";
        echo "<td>" . $user->email . "</td>";
        echo "<td>" . $user->firstname . " " . $user->lastname . "</td>";
        echo "<td>" . $user->created_at->format('Y-m-d H:i:s') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    if ($userCount > 10) {
        echo "<p>... and " . ($userCount - 10) . " more users</p>";
    }
}

echo "<h2>🔗 Admin Links:</h2>";
echo "<p><a href='/admin/user/delete-all-users' style='background: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; display: inline-block; margin: 10px;'>🗑️ Go to Delete All Users Page</a></p>";
echo "<p><a href='/admin/user' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; display: inline-block; margin: 10px;'>👥 Go to Users List</a></p>";

echo "<h2>⚠️ Important Notes:</h2>";
echo "<ul>";
echo "<li>✅ Admin password validation has been removed</li>";
echo "<li>✅ Confirmation text validation has been removed</li>";
echo "<li>✅ Checkbox is pre-checked and button is always enabled</li>";
echo "<li>⚠️ This action will permanently delete all users</li>";
echo "<li>⚠️ Make sure you have a database backup</li>";
echo "</ul>";

echo "<h2>🧪 Test Steps:</h2>";
echo "<ol>";
echo "<li>Click 'Go to Delete All Users Page'</li>";
echo "<li>Click 'Delete All Users' button (checkbox is pre-checked)</li>";
echo "<li>Confirm the action in the popup</li>";
echo "</ol>";

echo "<h2>📝 What Was Changed:</h2>";
echo "<ul>";
echo "<li>❌ Removed admin password field from form</li>";
echo "<li>❌ Removed admin password validation from controller</li>";
echo "<li>❌ Removed confirmation text field from form</li>";
echo "<li>❌ Removed confirmation text validation from controller</li>";
echo "<li>❌ Removed password and text checks from JavaScript</li>";
echo "<li>✅ Checkbox is pre-checked and button is always enabled</li>";
echo "<li>✅ Kept double confirmation popup</li>";
echo "</ul>";

echo "<p style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; border: 1px solid #bee5eb;'>";
echo "<strong>Status:</strong> All validations have been removed! Button is now always enabled and ready to use.";
echo "</p>";
?>
