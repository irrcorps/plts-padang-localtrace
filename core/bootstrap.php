<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/helpers.php';

if (APP_ENV === 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

Auth::start();

spl_autoload_register(function (string $class) {
    $paths = [
        __DIR__ . '/../models/' . $class . '.php',
        __DIR__ . '/../controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});
