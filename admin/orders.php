<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once '../config.php';

// Update Order Status Logic
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = $order_id");
    header("Location: orders.php");
    exit();
}

// Delete Order Logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM orders WHERE id = $id");
    header("Location: orders.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - Mirrora Admin</title>
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
            <h3 class="fw-bold" style="color: #800020;">Customer Orders, Size & Color</h3>
            <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date & Time</th>
                            <th>Customer Details</th>
                            <th>Order Details</th>
                            <th>Color & Size</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Update</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): 
                                $status = isset($row['status']) ? $row['status'] : 'Pending';
                                $badge_bg = 'bg-warning text-dark';
                                if($status == 'Delivered') $badge_bg = 'bg-success';
                                elseif($status == 'Processing') $badge_bg = 'bg-info text-dark';
                                elseif($status == 'Cancelled') $badge_bg = 'bg-danger';
                            ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $row['id']; ?></td>
                                
                                <!-- Order Date & Time Column -->
                                <td>
                                    <?php if (!empty($row['order_date'])): ?>
                                        <small class="fw-bold text-dark d-block"><?php echo date('d M Y', strtotime($row['order_date'])); ?></small>
                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="far fa-clock me-1"></i><?php echo date('h:i A', strtotime($row['order_date'])); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted small">N/A</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <strong><?php echo htmlspecialchars($row['customer_name'] ?? 'N/A'); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['phone'] ?? ''); ?></small><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['city'] ?? ''); ?>, <?php echo htmlspecialchars($row['address'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <div class="p-2 bg-light rounded border" style="max-width: 200px; font-size: 12px;">
                                        <?php echo nl2br(htmlspecialchars($row['order_products'] ?? 'N/A')); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary mb-1">Color: <?php echo htmlspecialchars($row['product_color'] ?? 'N/A'); ?></span><br>
                                    <span class="badge bg-dark">Size: <?php echo htmlspecialchars($row['product_size'] ?? 'N/A'); ?></span>
                                </td>
                                <td class="fw-bold text-success">
                                    Rs. <?php echo isset($row['total_amount']) ? number_format($row['total_amount']) : '0'; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $badge_bg; ?> px-2 py-1"><?php echo $status; ?></span>
                                </td>
                                <td>
                                    <form method="POST" class="d-flex gap-1">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <select name="status" class="form-select form-select-sm" style="width: 110px;">
                                            <option value="Pending" <?php if($status=='Pending') echo 'selected'; ?>>Pending</option>
                                            <option value="Processing" <?php if($status=='Processing') echo 'selected'; ?>>Processing</option>
                                            <option value="Delivered" <?php if($status=='Delivered') echo 'selected'; ?>>Delivered</option>
                                            <option value="Cancelled" <?php if($status=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-dark btn-sm"><i class="fas fa-check"></i></button>
                                    </form>
                                </td>
                                <td>
                                    <a href="orders.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this order?');"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>