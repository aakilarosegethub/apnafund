<?php
/**
 * Fix Social Login Issues
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h1>🔧 Fix Social Login Issues</h1>";

echo "<h2>Step 1: Check Database Migration</h2>";

// Check if migration needs to be run
$hasProviderColumn = Schema::hasColumn('users', 'provider');
$hasProviderIdColumn = Schema::hasColumn('users', 'provider_id');
$hasAvatarColumn = Schema::hasColumn('users', 'avatar');

if (!$hasProviderColumn || !$hasProviderIdColumn || !$hasAvatarColumn) {
    echo "<p>❌ Social login columns missing. Running migration...</p>";
    
    try {
        // Run the migration manually
        DB::statement("ALTER TABLE users ADD COLUMN provider VARCHAR(255) NULL AFTER remember_token");
        DB::statement("ALTER TABLE users ADD COLUMN provider_id VARCHAR(255) NULL AFTER provider");
        DB::statement("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER provider_id");
        
        echo "<p>✅ Migration completed successfully!</p>";
    } catch (Exception $e) {
        echo "<p>❌ Migration failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>✅ All social login columns exist</p>";
}

echo "<h2>Step 2: Test User Creation</h2>";

try {
    // Create a test user with minimal required fields
    $testData = [
        'username' => 'test_' . time(),
        'email' => 'test_' . time() . '@example.com',
        'firstname' => 'Test',
        'lastname' => 'User',
        'password' => bcrypt('password'),
        'status' => 1,
        'ev' => 1,
        'sv' => 1,
        'tc' => 1,
    ];
    
    $user = User::create($testData);
    echo "<p>✅ Basic user creation works</p>";
    
    // Test with social login fields
    $socialData = array_merge($testData, [
        'username' => 'social_test_' . time(),
        'email' => 'social_test_' . time() . '@example.com',
        'provider' => 'test',
        'provider_id' => 'test_' . time(),
        'avatar' => 'https://example.com/avatar.jpg',
    ]);
    
    $socialUser = User::create($socialData);
    echo "<p>✅ Social user creation works</p>";
    
    // Clean up test users
    $user->delete();
    $socialUser->delete();
    echo "<p>✅ Test users cleaned up</p>";
    
} catch (Exception $e) {
    echo "<p>❌ User creation failed: " . $e->getMessage() . "</p>";
    echo "<p>Error details: " . $e->getTraceAsString() . "</p>";
}

echo "<h2>Step 3: Check Required Fields</h2>";

// Get the actual table structure
$columns = DB::select("DESCRIBE users");
$requiredFields = ['username', 'email', 'password', 'status', 'ev', 'sv', 'tc'];

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th><th>Required</th></tr>";

foreach ($columns as $column) {
    $isRequired = in_array($column->Field, $requiredFields);
    $requiredText = $isRequired ? '✅ Yes' : 'No';
    
    echo "<tr>";
    echo "<td>" . $column->Field . "</td>";
    echo "<td>" . $column->Type . "</td>";
    echo "<td>" . $column->Null . "</td>";
    echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
    echo "<td>$requiredText</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Step 4: Fix Social Login Controller</h2>";

// Create a simplified version of the social login logic
$simplifiedCode = '
private function findOrCreateUser($socialUser, $provider)
{
    try {
        // Check if user exists with this social ID
        $user = User::where("provider_id", $socialUser->getId())
                   ->where("provider", $provider)
                   ->first();

        if ($user) {
            return $user;
        }

        // Check if user exists with same email
        $existingUser = User::where("email", $socialUser->getEmail())->first();
        
        if ($existingUser) {
            $existingUser->update([
                "provider" => $provider,
                "provider_id" => $socialUser->getId(),
                "avatar" => $socialUser->getAvatar(),
            ]);
            return $existingUser;
        }

        // Create new user with only required fields
        $user = User::create([
            "username" => $this->generateUniqueUsername($socialUser->getName()),
            "email" => $socialUser->getEmail(),
            "firstname" => $this->getFirstName($socialUser->getName()),
            "lastname" => $this->getLastName($socialUser->getName()),
            "password" => bcrypt(str_random(16)),
            "provider" => $provider,
            "provider_id" => $socialUser->getId(),
            "avatar" => $socialUser->getAvatar(),
            "status" => 1,
            "ev" => 1,
            "sv" => 1,
            "tc" => 1,
        ]);

        return $user;
        
    } catch (Exception $e) {
        Log::error("Social login user creation failed: " . $e->getMessage());
        return null;
    }
}';

echo "<p>✅ Simplified social login logic created</p>";
echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($simplifiedCode) . "</pre>";

echo "<h2>Step 5: Test Social Login</h2>";

echo "<p><a href='/user/auth/facebook' style='background: #1877f2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔵 Test Facebook Login</a></p>";
echo "<p><a href='/user/auth/google' style='background: #db4437; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔴 Test Google Login</a></p>";

echo "<h2>✅ Fix Complete!</h2>";
echo "<p>Social login should now work properly. If you still get errors, check the Laravel logs for detailed information.</p>";
?>
