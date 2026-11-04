<?php
require 'config.php';

$message = $_GET['message'] ?? '';
$search = cleanInput($_GET['search'] ?? '');
$page = (int)($_GET['page'] ?? 1);
$perPage = 10;

if ($page < 1) {
    $page = 1;
}

$whereSql = '';
$params = [];

if ($search !== '') {
    $whereSql = 'WHERE name LIKE :search';
    $params['search'] = '%' . $search . '%';
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products $whereSql");
$countStmt->execute($params);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalProducts / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

$sql = "SELECT * FROM products $whereSql ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <div>
                <h1>Products</h1>
                <p>Basic PHP, MySQL, HTML, CSS and JavaScript CRUD app.</p>
            </div>
            <a class="button" href="create.php">Create Product</a>
        </div>

        <?php if ($message !== ''): ?>
            <div class="message success js-message"><?php echo showValue($message); ?></div>
        <?php else: ?>
            <div class="message success js-message" style="display: none;"></div>
        <?php endif; ?>

        <form class="search-form" method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search by name" value="<?php echo showValue($search); ?>">
            <button type="submit">Search</button>
            <a class="button secondary" href="index.php">Reset</a>
        </form>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Availability Date</th>
                        <th>Stock Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <tr data-product-id="<?php echo (int)$product['id']; ?>">
                                <td><?php echo showValue($product['name']); ?></td>
                                <td><?php echo showValue($product['description']); ?></td>
                                <td>$<?php echo number_format((float)$product['price'], 2); ?></td>
                                <td>
                                    <?php if ($product['image']): ?>
                                        <img class="product-image" src="<?php echo UPLOAD_URL . showValue($product['image']); ?>" alt="Product image">
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo showValue($product['availability_date']); ?></td>
                                <td><?php echo $product['in_stock'] ? 'In Stock' : 'Out of Stock'; ?></td>
                                <td class="actions">
                                    <a class="button small" href="edit.php?id=<?php echo (int)$product['id']; ?>">Edit</a>
                                    <button class="button small danger js-delete-button" type="button" data-id="<?php echo (int)$product['id']; ?>" data-name="<?php echo showValue($product['name']); ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="<?php echo $i === $page ? 'active' : ''; ?>" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-box">
            <h2>Delete product?</h2>
            <p id="deleteText">This action cannot be undone.</p>
            <form method="POST" action="delete.php" id="deleteForm">
                <input type="hidden" name="id" id="deleteId">
                <div class="modal-actions">
                    <button type="button" class="button secondary js-close-delete">Cancel</button>
                    <button type="submit" class="button danger">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
