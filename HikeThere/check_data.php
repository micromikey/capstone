<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "User Preferences:\n";
$user = App\Models\User::whereNotNull('hiking_preferences')->first();
if ($user) {
    echo "Found user with preferences:\n";
    print_r($user->hiking_preferences);
} else {
    echo "No user preferences found.\n";
}

echo "\nTrail Package Sample:\n";
$package = App\Models\Trail::with('package')->first()?->package;
if ($package) {
    print_r($package->toArray());
} else {
    echo "No trail package found.\n";
}