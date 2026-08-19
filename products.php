<?php
require __DIR__ . '/core/bootstrap.php';

Auth::requireRole(['producer', 'admin', 'distributor', 'retailer']);

$controller = new ProductController();
$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $postId = isset($_POST['id']) ? (int) $_POST['id'] : null;

    switch ($postAction) {
        case 'store':
            $controller->store();
            break;
        case 'update':
            $controller->update($postId);
            break;
        case 'delete':
            $controller->delete($postId);
            break;
        case 'submit':
            $controller->submit($postId);
            break;
        default:
            redirect('products.php');
    }
    exit;
}

switch ($action) {
    case 'create':
        $controller->showCreateForm();
        break;
    case 'edit':
        $controller->showEditForm($id);
        break;
    case 'show':
        $controller->show($id);
        break;
    case 'index':
    default:
        $controller->index();
        break;
}
