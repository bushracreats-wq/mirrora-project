<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once '../config.php';

// Quick Counts from Tables
$products_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$orders_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];
$messages_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM messages"))['total'];
$wishlist_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Mirrora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4 py-3 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php" style="font-family: 'Cinzel', serif;">MIRRORA ADMIN PANEL</a>
        <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
    </nav>

    <div class="container my-5">
        <h2 class="fw-bold mb-4" style="color: #800020;">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
        
        <div class="row g-4">
            <!-- Products Card -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #800020 0%, #860611 100%);">
                    <h5>Products</h5>
                    <h2 class="fw-bold display-6 my-2"><?php echo $products_count; ?></h2>
                    <a href="products.php" class="btn btn-light btn-sm text-dark fw-bold w-100">Manage <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #ba9b00 0%, #b49f00 100%);">
                    <h5>Orders</h5>
                    <h2 class="fw-bold display-6 my-2"><?php echo $orders_count; ?></h2>
                    <a href="orders.php" class="btn btn-light btn-sm text-dark fw-bold w-100">Manage <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <!-- Wishlist Card -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #540303 0%, #5d0202 100%);">
                    <h5>Wishlist</h5>
                    <h2 class="fw-bold display-6 my-2"><?php echo $wishlist_count; ?></h2>
                    <a href="wishlist.php" class="btn btn-light btn-sm text-dark fw-bold w-100">View Items <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <!-- Messages Card -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #4A00E0 0%, #1c0431 100%);">
                    <h5>Messages</h5>
                    <h2 class="fw-bold display-6 my-2"><?php echo $messages_count; ?></h2>
                    <a href="messages.php" class="btn btn-light btn-sm text-dark fw-bold w-100">View Queries <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>