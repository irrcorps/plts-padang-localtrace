<?php
require __DIR__ . '/core/bootstrap.php';

Auth::requireLogin();

$role = Auth::role();

$viewMap = [
    'admin'       => __DIR__ . '/views/dashboard/admin.php',
    'producer'    => __DIR__ . '/views/dashboard/producer.php',
    'distributor' => __DIR__ . '/views/dashboard/distributor.php',
    'retailer'    => __DIR__ . '/views/dashboard/retailer.php',
];

$view = $viewMap[$role] ?? null;

if (!$view) {
    Auth::logout();
    redirect('login.php');
}

require $view;
