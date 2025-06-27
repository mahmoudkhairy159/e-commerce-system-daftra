<?php

// Simple script to fix module paths case sensitivity issues
$baseDir = __DIR__;
$modulesDir = $baseDir . '/Modules';

if (!is_dir($modulesDir)) {
    echo "Modules directory not found.\n";
    exit(1);
}

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
    fixRouteFiles($modulePath . '/Routes');
    fixRouteFiles($modulePath . '/routes');
}

function createSymlinkIfNeeded($basePath, $sourceDirName, $targetDirName) {
    $sourceDir = $basePath . '/' . $sourceDirName;
    $targetDir = $basePath . '/' . $targetDirName;

    if (is_dir($sourceDir) && !file_exists($targetDir)) {
        echo "  Creating symlink: $targetDirName -> $sourceDirName\n";
        symlink($sourceDir, $targetDir);
    }
}

function fixRouteFiles($routesDir) {
    if (!is_dir($routesDir)) {
        return;
    }

    $routeFiles = ['api.php', 'web.php'];
    
    foreach ($routeFiles as $expectedFile) {
        // If the exact file exists, no action needed
        if (file_exists($routesDir . '/' . $expectedFile)) {
            continue;
        }
        
        // Check for case variations
        $files = scandir($routesDir);
        foreach ($files as $file) {
            if (strtolower($file) === $expectedFile && $file !== $expectedFile) {
                echo "  Creating route file symlink: $expectedFile -> $file\n";
                symlink($routesDir . '/' . $file, $routesDir . '/' . $expectedFile);
                break;
            }
        }
        
        // If no variation found, create an empty file
        if (!file_exists($routesDir . '/' . $expectedFile)) {
            echo "  Creating empty route file: $expectedFile\n";
            file_put_contents($routesDir . '/' . $expectedFile, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n// Routes for this module\n");
        }
    }
}

echo "Path fixing completed!\n";