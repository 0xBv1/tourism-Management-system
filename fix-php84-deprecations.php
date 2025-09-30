<?php

/**
 * PHP 8.4 Deprecation Fixes
 * 
 * This script patches vendor files to fix nullable parameter deprecation warnings
 * that occur when using PHP 8.4 with older packages.
 */

echo "🔧 Applying PHP 8.4 deprecation fixes...\n";

$fixes = [
    // Fix Symfony Polyfill PHP83
    [
        'file' => 'vendor/symfony/polyfill-php83/bootstrap.php',
        'search' => 'function ldap_exop_sync($ldap, $request_oid, $request_data = null, $controls = null, &$response_data = null, &$response_oids = null)',
        'replace' => 'function ldap_exop_sync($ldap, $request_oid, ?string $request_data = null, ?array $controls = null, &$response_data = null, &$response_oids = null)'
    ],
    
    // Fix Termwind functions
    [
        'file' => 'vendor/nunomaduro/termwind/src/Functions.php',
        'search' => 'function style(string $name, $callback = null)',
        'replace' => 'function style(string $name, ?callable $callback = null)'
    ],
    [
        'file' => 'vendor/nunomaduro/termwind/src/Functions.php',
        'search' => 'function ask(string $question, $autocomplete = null)',
        'replace' => 'function ask(string $question, ?callable $autocomplete = null)'
    ],
    
    // Fix Laravel helpers
    [
        'file' => 'vendor/laravel/framework/src/Illuminate/Support/helpers.php',
        'search' => 'function optional($value, $callback = null)',
        'replace' => 'function optional($value, ?callable $callback = null)'
    ],
    [
        'file' => 'vendor/laravel/framework/src/Illuminate/Support/helpers.php',
        'search' => 'function with($value, $callback = null)',
        'replace' => 'function with($value, ?callable $callback = null)'
    ],
    
    // Fix Opis Closure
    [
        'file' => 'vendor/opis/closure/functions.php',
        'search' => 'function unserialize($data, $options = null)',
        'replace' => 'function unserialize($data, ?array $options = null)'
    ],
    
    // Fix Spatie Activity Log
    [
        'file' => 'vendor/spatie/laravel-activitylog/src/helpers.php',
        'search' => 'function activity($logName = null)',
        'replace' => 'function activity(?string $logName = null)'
    ]
];

$appliedFixes = 0;
$skippedFixes = 0;

foreach ($fixes as $fix) {
    $filePath = $fix['file'];
    
    if (!file_exists($filePath)) {
        echo "⚠️  File not found: {$filePath}\n";
        $skippedFixes++;
        continue;
    }
    
    $content = file_get_contents($filePath);
    
    if (strpos($content, $fix['search']) !== false) {
        $newContent = str_replace($fix['search'], $fix['replace'], $content);
        
        if (file_put_contents($filePath, $newContent)) {
            echo "✅ Fixed: " . basename($filePath) . "\n";
            $appliedFixes++;
        } else {
            echo "❌ Failed to write: {$filePath}\n";
            $skippedFixes++;
        }
    } else {
        echo "ℹ️  Pattern not found in: " . basename($filePath) . "\n";
        $skippedFixes++;
    }
}

echo "\n📊 Summary:\n";
echo "   Applied fixes: {$appliedFixes}\n";
echo "   Skipped fixes: {$skippedFixes}\n";

if ($appliedFixes > 0) {
    echo "\n🎉 PHP 8.4 deprecation fixes applied successfully!\n";
    echo "   You may need to run 'composer dump-autoload' to refresh the autoloader.\n";
} else {
    echo "\n⚠️  No fixes were applied. This might mean:\n";
    echo "   - The packages have already been updated\n";
    echo "   - The file paths have changed\n";
    echo "   - The patterns don't match the current code\n";
}

echo "\n";

