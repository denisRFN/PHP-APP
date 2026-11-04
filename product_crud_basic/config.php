<?php
$host = 'localhost';
$dbName = 'product_crud_basic';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

function cleanInput($value)
{
    return trim($value);
}

function showValue($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function isAjaxRequest()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function validateProduct($name, $price, $imageFile, $imageRequired = false)
{
    $errors = [];

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($price === '') {
        $errors[] = 'Price is required.';
    } elseif (!is_numeric($price) || $price < 0) {
        $errors[] = 'Price must be a valid positive number.';
    }

    if ($imageRequired && (!isset($imageFile) || $imageFile['error'] === UPLOAD_ERR_NO_FILE)) {
        $errors[] = 'Image is required.';
    }

    if (isset($imageFile) && $imageFile['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($imageFile['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'There was a problem uploading the image.';
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $imageInfo = getimagesize($imageFile['tmp_name']);

            if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedTypes, true)) {
                $errors[] = 'Image must be JPG, PNG, GIF, or WEBP.';
            }
        }
    }

    return $errors;
}

function uploadImage($imageFile)
{
    if (!isset($imageFile) || $imageFile['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $extension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
    $safeName = uniqid('product_', true) . '.' . $extension;
    $targetPath = UPLOAD_DIR . $safeName;

    if (move_uploaded_file($imageFile['tmp_name'], $targetPath)) {
        return $safeName;
    }

    return null;
}

function deleteImageFile($imageName)
{
    if ($imageName) {
        $path = UPLOAD_DIR . $imageName;

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
?>
