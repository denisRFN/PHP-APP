<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Product not found.');
}

$errors = [];
$name = $product['name'];
$description = $product['description'];
$price = $product['price'];
$availabilityDate = $product['availability_date'];
$inStock = (int)$product['in_stock'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name'] ?? '');
    $description = cleanInput($_POST['description'] ?? '');
    $price = cleanInput($_POST['price'] ?? '');
    $availabilityDate = cleanInput($_POST['availability_date'] ?? '');
    $inStock = isset($_POST['in_stock']) ? 1 : 0;

    $errors = validateProduct($name, $price, $_FILES['image'], false);

    if (empty($errors)) {
        $imageName = $product['image'];
        $newImageName = uploadImage($_FILES['image']);

        if ($newImageName !== null) {
            deleteImageFile($product['image']);
            $imageName = $newImageName;
        }

        $stmt = $pdo->prepare('UPDATE products SET name = :name, description = :description, price = :price, image = :image, availability_date = :availability_date, in_stock = :in_stock WHERE id = :id');
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image' => $imageName,
            'availability_date' => $availabilityDate !== '' ? $availabilityDate : null,
            'in_stock' => $inStock,
            'id' => $id
        ]);

        $redirectUrl = 'index.php?message=Product updated successfully';

        if (isAjaxRequest()) {
            jsonResponse([
                'success' => true,
                'message' => 'Product updated successfully',
                'redirect' => $redirectUrl
            ]);
        }

        header('Location: ' . $redirectUrl);
        exit;
    } elseif (isAjaxRequest()) {
        jsonResponse([
            'success' => false,
            'errors' => $errors
        ], 422);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container form-page">
        <h1>Edit Product</h1>

        <?php include 'form_errors.php'; ?>

        <form method="POST" enctype="multipart/form-data" class="product-form ajax-product-form">
            <label>Name *</label>
            <input type="text" name="name" value="<?php echo showValue($name); ?>">

            <label>Description</label>
            <textarea name="description"><?php echo showValue($description); ?></textarea>

            <label>Price *</label>
            <input type="number" step="0.01" name="price" value="<?php echo showValue($price); ?>">

            <label>Image</label>
            <?php if ($product['image']): ?>
                <img class="current-image" src="<?php echo UPLOAD_URL . showValue($product['image']); ?>" alt="Current image">
            <?php endif; ?>
            <input type="file" name="image" accept="image/*">

            <label>Availability Date</label>
            <input type="date" name="availability_date" value="<?php echo showValue($availabilityDate); ?>">

            <label class="checkbox-label">
                <input type="checkbox" name="in_stock" <?php echo $inStock ? 'checked' : ''; ?>> In Stock
            </label>

            <div class="form-actions">
                <button type="submit" class="button">Save Changes</button>
                <a href="index.php" class="button secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
