<?php
require __DIR__ . '/core/bootstrap.php';

Auth::requireRole(['distributor', 'retailer']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    (new TraceabilityController())->store();
    exit;
}

redirect('products.php');
