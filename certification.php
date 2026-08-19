<?php
require __DIR__ . '/core/bootstrap.php';

Auth::requireRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products.php');
}

$controller = new CertificationController();
$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);

switch ($action) {
    case 'verify':
        $controller->verify($id);
        break;
    case 'reject':
        $controller->reject($id);
        break;
    case 'certify':
        $controller->certify($id);
        break;
    default:
        redirect('products.php');
}
