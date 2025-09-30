<?php
/**
 * Fix Carbon deprecation warning for PHP 8.4 compatibility
 * 
 * This script adds the #[\ReturnTypeWillChange] attribute to the createFromTimestamp
 * method in Carbon's Timestamp trait to suppress the deprecation warning.
 * 
 * Run this script after composer install/update:
 * php fix-carbon-deprecation.php
 */

$carbonTimestampFile = __DIR__ . '/vendor/nesbot/carbon/src/Carbon/Traits/Timestamp.php';

if (!file_exists($carbonTimestampFile)) {
    echo "Carbon Timestamp.php file not found. Make sure Carbon is installed.\n";
    exit(1);
}

$content = file_get_contents($carbonTimestampFile);

// Check if the fix is already applied
if (strpos($content, '#[\ReturnTypeWillChange]') !== false) {
    echo "Carbon deprecation fix is already applied.\n";
    exit(0);
}

// Apply the fix
$pattern = '/public static function createFromTimestamp\(\$timestamp, \$tz = null\)/';
$replacement = '#[\ReturnTypeWillChange]' . "\n    public static function createFromTimestamp(\$timestamp, \$tz = null)";

$newContent = preg_replace($pattern, $replacement, $content);

if ($newContent === $content) {
    echo "Could not find the createFromTimestamp method to patch.\n";
    exit(1);
}

if (file_put_contents($carbonTimestampFile, $newContent)) {
    echo "Successfully applied Carbon deprecation fix.\n";
    echo "The #[\ReturnTypeWillChange] attribute has been added to createFromTimestamp method.\n";
} else {
    echo "Failed to write the patched file.\n";
    exit(1);
}
