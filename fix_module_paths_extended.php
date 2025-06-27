<?php

// Extended script to fix module paths case sensitivity and create missing route files
$baseDir = __DIR__;
$modulesDir = $baseDir . '/Modules';

if (!is_dir($modulesDir)) {
    echo "Modules directory not found.\n";
    exit(1);
}

// List of common route files to check for each module
$commonRouteFiles = [
    'api.php',
    'web.php',
    'admin-api.php', // Added the specific file you're missing
    'channels.php',
    'console.php'
];

$modules = scandir($modulesDir);

foreach ($modules as $module) {
    if ($module === '.' || $module === '..') {
        continue;
    }

    $modulePath = $modulesDir . '/' . $module;
    if (!is_dir($modulePath)) {
        continue;
    }

    echo "Fixing module: $module\n";

    // Fix Routes directory
    createSymlinkIfNeeded($modulePath, 'Routes', 'routes');
    
    // Fix Config directory
    createSymlinkIfNeeded($modulePath, 'Config', 'config');
    
    // Fix route files
    fixRouteFiles($modulePath . '/Routes', $commonRouteFiles);
    fixRouteFiles($modulePath . '/routes', $commonRouteFiles);
    
    // Specifically for Admin module
    if ($module === 'Admin') {
        ensureRouteFile($modulePath . '/routes', 'admin-api.php');
    }
}

function createSymlinkIfNeeded($basePath, $sourceDirName, $targetDirName) {
    $sourceDir = $basePath . '/' . $sourceDirName;
    $targetDir = $basePath . '/' . $targetDirName;

    if (is_dir($sourceDir) && !file_exists($targetDir)) {
        echo "  Creating symlink: $targetDirName -> $sourceDirName\n";
        symlink($sourceDir, $targetDir);
    }
}

function ensureRouteFile($routesDir, $fileName) {
    if (!is_dir($routesDir)) {
        mkdir($routesDir, 0775, true);
    }
    
    if (!file_exists($routesDir . '/' . $fileName)) {
        echo "  Creating route file: $fileName\n";
        file_put_contents($routesDir . '/' . $fileName, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n// Routes for this module\n");
    }
}

function fixRouteFiles($routesDir, $expectedFiles) {
    if (!is_dir($routesDir)) {
        mkdir($routesDir, 0775, true);
        echo "  Created directory: $routesDir\n";
    }

    foreach ($expectedFiles as $expectedFile) {
        // If the exact file exists, no action needed
        if (file_exists($routesDir . '/' . $expectedFile)) {
            continue;
        }
        
        // Check for case variations
        $files = scandir($routesDir);
        $foundVariant = false;
        
        foreach ($files as $file) {
            if (strtolower($file) === strtolower($expectedFile) && $file !== $expectedFile) {
                echo "  Creating route file symlink: $expectedFile -> $file\n";
                symlink($routesDir . '/' . $file, $routesDir . '/' . $expectedFile);
                $foundVariant = true;
                break;
            }
        }
        
        // If no variation found, create an empty file
        if (!$foundVariant) {
            echo "  Creating empty route file: $expectedFile\n";
            file_put_contents($routesDir . '/' . $expectedFile, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n// Routes for this module\n");
        }
    }
}

echo "Path fixing completed!\n";