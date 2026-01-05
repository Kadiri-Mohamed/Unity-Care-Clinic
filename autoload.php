<?php

spl_autoload_register(function ($className) {
    // First check root directory
    $rootFile = __DIR__ . "/$className.php";
    if (file_exists($rootFile)) {
        require_once $rootFile;
        return;
    }

    $folders = [
        'services',
        'repositories',
        'models',
        'config',
        'view',
        'utils',
    ];

    foreach ($folders as $folder) {
        $file = __DIR__ . "/$folder/$className.php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});