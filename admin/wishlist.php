<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once '../config.php';

// Delete Wishlist Item Logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM wishlist WHERE id = $id");
    header("Location: wishlist.php");
    exit();
}

// Fetch wishlist data joined with products table to get Name and Image
$query = "SELECT wishlist.id as wishlist_id, wishlist.*, products.name as product_name, products.images as product_image, products.price as product_price 
          FROM wishlist 
          LEFT JOIN products ON wishlist.product_id = products.id 
          ORDER BY wishlist.id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Wishlist - Mirrora Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 py-3 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php">MIRRORA ADMIN</a>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold" style="color: #800020;">Customer Wishlist Items</h3>
            <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $row['wishlist_id']; ?></td>
                                <td>
                                    <?php if(!empty($row['product_image'])): ?>
                                        <img src="../assets/images/<?php echo $row['product_image']; ?>" class="rounded" style="width: 45px; height: 45px; object-fit: cover;" alt="">
                                    <?php else: ?>
                                        <span class="text-muted small">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold">
                                    <?php echo htmlspecialchars($row['product_name'] ?? 'Product Deleted / Not Found'); ?>
                                </td>
                                <td class="text-success fw-bold">
                                    <?php echo isset($row['product_price']) ? 'Rs. ' . number_format($row['product_price']) : 'N/A'; ?>
                                </td>
                                <td>
                                    <a href="wishlist.php?delete=<?php echo $row['wishlist_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove this item from wishlist?');"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No items in wishlist yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>