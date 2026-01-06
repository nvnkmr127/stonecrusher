<?php
/**
 * Stone Crusher ERP - Automated Deployment Webhook
 * 
 * Usage: Set DEPLOY_KEY in your .env file
 * Call: https://your-domain.com/deploy.php?key=YOUR_DEPLOY_KEY
 */

// 1. Security Configuration
$authorized = false;
$headers = getallheaders();
$envFile = __DIR__ . '/../.env';
$deployKey = '';

// 2. Load Secret Key from .env
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue; // Skip comments

        if (strpos(trim($line), 'DEPLOY_KEY=') === 0) {
            $deployKey = trim(substr(trim($line), 11));
            // Remove quotes if present
            $deployKey = trim($deployKey, '"\'');
            break;
        }
    }
}

if (empty($deployKey)) {
    http_response_code(500);
    die("❌ Configuration Error: DEPLOY_KEY not set in .env file.");
}

// 3. Verify Request
$paramKey = $_GET['key'] ?? '';
if ($paramKey === $deployKey) {
    $authorized = true;
}

if (!$authorized) {
    http_response_code(403);
    die("⛔ Access Denied: Invalid Authentication Key.");
}

// 4. Execution
header('Content-Type: text/plain');
echo "🚀 Starting Automated Deployment at " . date('Y-m-d H:i:s') . "\n";
echo "--------------------------------------------------------\n";

// Ensure we are in the project root
chdir(__DIR__ . '/../');

// Script Path
$script = './cpanel_deploy.sh';

if (!file_exists($script)) {
    http_response_code(500);
    die("❌ Error: Deployment script ($script) not found.");
}

// Ensure executable
if (!is_executable($script)) {
    chmod($script, 0755);
}

// Run the script
// 2>&1 redirects stderr to stdout so we capture errors too
$command = "bash $script 2>&1";
$output = [];
$returnCode = 0;

exec($command, $output, $returnCode);

// 5. Output Results
echo implode("\n", $output);

echo "\n--------------------------------------------------------\n";
if ($returnCode === 0) {
    echo "✅ Deployment Completed Successfully.\n";
} else {
    http_response_code(500);
    echo "❌ Deployment Failed (Exit Code: $returnCode).\n";
}
