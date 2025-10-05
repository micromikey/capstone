#!/usr/bin/env php
<?php
/**
 * Convert Google Cloud Service Account JSON to base64 for Railway
 * Usage: php encode-gcs-key.php path/to/service-account.json
 */

if ($argc < 2) {
    echo "Usage: php encode-gcs-key.php path/to/service-account.json\n";
    exit(1);
}

$jsonFile = $argv[1];

if (!file_exists($jsonFile)) {
    echo "Error: File not found: $jsonFile\n";
    exit(1);
}

$jsonContent = file_get_contents($jsonFile);
$jsonData = json_decode($jsonContent, true);

if (!$jsonData) {
    echo "Error: Invalid JSON file\n";
    exit(1);
}

echo "\n=== Google Cloud Storage Configuration ===\n\n";
echo "Add these to Railway Variables:\n\n";

echo "GCS_PROJECT_ID=" . $jsonData['project_id'] . "\n";
echo "GCS_BUCKET=hikethere-storage\n";
echo "GCS_KEY_FILE_CONTENT=" . base64_encode($jsonContent) . "\n";
echo "FILESYSTEM_DISK=gcs\n";

echo "\n=== Instructions ===\n";
echo "1. Copy the variables above\n";
echo "2. Add them to Railway Variables\n";
echo "3. Railway will automatically redeploy\n\n";
