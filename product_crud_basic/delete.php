<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT image FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    deleteImageFile($product['image']);

    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);

    if (isAjaxRequest()) {
        jsonResponse([
            'success' => true,
            'message' => 'Product deleted successfully',
            'id' => $id
        ]);
    }
} elseif (isAjaxRequest()) {
    jsonResponse([
        'success' => false,
        'message' => 'Product not found.'
    ], 404);
}

header('Location: index.php?message=Product deleted successfully');
exit;
?>
