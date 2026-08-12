<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$success_msg = '';
$error_msg = '';

// 1. PRODUCT ADD KARNE KI LOGIC
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']);
    $discount = intval($_POST['discount_percent']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);

    $image = '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $image_name = time() . '_' . basename($_FILES['image_file']['name']);
        $target_dir = "assets/images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
            $image = $image_name;
        }
    }

    $query = "INSERT INTO products (name, category, price, description, size, color, images, discount_percent) 
              VALUES ('$name', '$category', '$price', '$desc', '$size', '$color', '$image', '$discount')";
    if (mysqli_query($conn, $query)) {
        $success_msg = "Product successfully add ho gaya!";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}

// 2. PRODUCT UPDATE KARNE KI LOGIC
if (isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']);
    $discount = intval($_POST['discount_percent']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    
    $image_query_part = "";
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $image_name = time() . '_' . basename($_FILES['image_file']['name']);
        $target_dir = "assets/images/";
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_dir . $image_name)) {
            $image_query_part = ", images='$image_name'";
        }
    }

    $query = "UPDATE products SET name='$name', category='$category', price='$price', description='$desc', size='$size', color='$color', discount_percent='$discount' $image_query_part WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        $success_msg = "Product successfully update ho gaya!";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}

// 3. PRODUCT DELETE
if (isset($_GET['delete_product'])) {
    $id = intval($_GET['delete_product']);
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header("Location: admin.php?tab=products");
    exit();
}

// 4. MESSAGE DELETE
if (isset($_GET['delete_msg'])) {
    $msg_id = intval($_GET['delete_msg']);
    mysqli_query($conn, "DELETE FROM messages WHERE id = $msg_id");
    header("Location: admin.php?tab=messages");
    exit();
}

// 5. REVIEW APPROVE OR DELETE
if (isset($_GET['action']) && isset($_GET['id'])) {
    $rev_id = intval($_GET['id']);
    if ($_GET['action'] == 'approve') {
        mysqli_query($conn, "UPDATE reviews SET status = 'approved' WHERE id = $rev_id");
    } elseif ($_GET['action'] == 'delete') {
        mysqli_query($conn, "DELETE FROM reviews WHERE id = $rev_id");
    }
    header("Location: admin.php?tab=reviews");
    exit();
}

// 6. ORDER STATUS UPDATE
if (isset($_POST['update_order_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = $order_id");
    header("Location: admin.php?tab=orders");
    exit();
}

// Stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'] ?? 0;
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'] ?? 0;
$total_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM messages"))['count'] ?? 0;
$total_wishlist = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM wishlist"))['count'] ?? 0;
$total_reviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reviews"))['count'] ?? 0;

$revenue_query = mysqli_query($conn, "SELECT SUM(total_amount) as total_rev FROM orders");
$revenue_row = mysqli_fetch_assoc($revenue_query);
$total_revenue = $revenue_row['total_rev'] ? $revenue_row['total_rev'] : 0;

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
$edit_mode = isset($_GET['edit_product']) ? intval($_GET['edit_product']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mirrora Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Yahan <style> tag lagana zaroori hai taake CSS render ho -->
    <style>
        :root {
--bg-main: #f4f6f9;
    --bg-card: #ffffff;
    --border-color: #cbd5e1;
    --accent-maroon: #6b1d2f;
    --accent-maroon-hover: #521523;
    --text-main: #000000;
    --text-muted: #333333;
}

body { 
    background-color: var(--bg-main); 
    color: var(--text-main);
    font-family: 'Inter', sans-serif; 
    font-size: 0.85rem; 
    margin: 0;
}

/* --- Sidebar Styling --- */
.sidebar { 
    min-height: 100vh; 
    background: #ffffff; 
    border-right: 1px solid var(--border-color);
    display: flex; 
    flex-direction: column; 
    justify-content: space-between; 
    padding: 20px 0;
}

.sidebar .nav, 
.sidebar .menu-list,
.sidebar div {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.sidebar a { 
    color: var(--text-muted); 
    text-decoration: none; 
    padding: 10px 16px; 
    margin: 4px 12px;
    border-radius: 0px; 
    display: flex;
    align-items: center;
    gap: 10px;
    transition: 0.2s ease; 
    font-size: 0.85rem; 
    font-weight: 500;
}

.sidebar a:hover, 
.sidebar a.active, 
.sidebar a:hover *, 
.sidebar a.active * { 
    color: #ffffff !important; 
    background: var(--accent-maroon); 
}

/* --- Cards & Containers --- */
.card, .card-custom {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 0px; 
    color: var(--text-main);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 20px;
    margin-bottom: 20px;
}

.card-stat { 
    border-left: 4px solid var(--accent-maroon); 
    padding: 16px !important;
}

.card-stat .stat-title {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    margin-bottom: 6px;
    font-weight: 600;
}

.card-stat .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-main);
    line-height: 1.2;
}

/* --- Form Controls --- */
.form-control, .form-select { 
    background-color: #ffffff;
    border: 1px solid var(--border-color);
    color: var(--text-main);
    font-size: 0.85rem; 
    padding: 0.55rem 0.75rem;
    border-radius: 0px !important; 
}

.form-control:focus, .form-select:focus {
    background-color: #ffffff; 
    border-color: var(--accent-maroon);
    color: var(--text-main);
    box-shadow: none;
}

label, .form-label {
    font-weight: 600;
    color: var(--text-main);
    margin-bottom: 6px;
    font-size: 0.8rem;
}

/* --- Tables & Customer Info Fix --- */
.table { 
    color: #000000 !important; 
    font-size: 0.85rem; 
    border-color: var(--border-color); 
}

.table > :not(caption) > * > * { 
    background-color: transparent; 
    color: #000000 !important; 
    border-bottom-color: var(--border-color); 
    padding: 12px;
}

/* Table ke andar ke sabhi texts (Name, Email, Phone, Products) ko pure black karne ke liye */
.table td, .table th, .table div, .table span, .table strong {
    color: #000000 !important;
}

.table-hover > tbody > tr:hover > * { 
    background-color: #fdeded !important; 
    color: #000000 !important; 
}

/* --- Status Badges --- */
.status-Pending { background-color: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fde68a !important; font-weight: 600; padding: 4px 8px; border-radius: 0px; }
.status-Processing { background-color: #e0f2fe !important; color: #0369a1 !important; border: 1px solid #bae6fd !important; font-weight: 600; padding: 4px 8px; border-radius: 0px; }
.status-Delivered { background-color: #dcfce7 !important; color: #15803d !important; border: 1px solid #bbf7d0 !important; font-weight: 600; padding: 4px 8px; border-radius: 0px; }
.status-Cancelled { background-color: #fee2e2 !important; color: #b91c1c !important; border: 1px solid #fecaca !important; font-weight: 600; padding: 4px 8px; border-radius: 0px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
            <div>
                <div class="p-3 text-center border-bottom border-secondary border-opacity-25">
                  <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <h2 class="fw-bold text-dark mb-0" style="color: #800020 !important;">MIRRORA</h2>
     
    </div>
   
</div>
                    <small class="text-muted" style="font-size: 0.72rem;">Admin Control Center</small>
                </div>
                <div class="py-3">
                    <a href="admin.php?tab=dashboard" class="<?php echo ($active_tab == 'dashboard') ? 'active' : ''; ?>"><i class="fas fa-home me-2"></i> Overview</a>
                    <a href="admin.php?tab=products" class="<?php echo ($active_tab == 'products') ? 'active' : ''; ?>"><i class="fas fa-box me-2"></i> Products (CRUD)</a>
                    <a href="admin.php?tab=orders" class="<?php echo ($active_tab == 'orders') ? 'active' : ''; ?>"><i class="fas fa-shopping-cart me-2"></i> Orders</a>
                    <a href="admin.php?tab=reviews" class="<?php echo ($active_tab == 'reviews') ? 'active' : ''; ?>"><i class="fas fa-star me-2"></i> Reviews</a>
                    <a href="admin.php?tab=messages" class="<?php echo ($active_tab == 'messages') ? 'active' : ''; ?>"><i class="fas fa-envelope me-2"></i> Messages</a>
                    <a href="admin.php?tab=wishlist" class="<?php echo ($active_tab == 'wishlist') ? 'active' : ''; ?>"><i class="fas fa-heart me-2"></i> Wishlist</a>
                </div>
            </div>
            
            <div class="p-3 border-top border-secondary border-opacity-25">
                <a href="index.php" target="_blank" class="text-info mb-1 d-block" style="font-size: 0.82rem;"><i class="fas fa-external-link-alt me-2"></i> View Site</a>
                <a href="admin.php?logout=true" class="text-danger d-block" style="font-size: 0.82rem;" onclick="return confirm('Aap waqai logout karna chahti hain?');"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-3">
            
            <!-- Top Bar (Matching Figma Image header style) -->
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-secondary border-opacity-25">
                <div>
                  <h3 class="fw-bold m-0" style="color:#7A1E2C;">
    Welcome Back, Admin 👋
</h3>
<p class="text-muted mb-0">
    Manage your MIRRORA store from one place.
</p>
                    <small class="text-muted" style="font-size: 0.75rem;">Here is what's happening with your store today.</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="card-custom px-3 py-1.5 d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                        <i class="far fa-calendar-alt text-muted"></i> 
                        <span>Jan 20, 2026 - Feb 09, 2026</span>
                    </div>
                    <span class="badge p-2 text-white fw-medium shadow-sm" style="background-color: var(--accent-red); font-size: 0.75rem;"><i class="fas fa-database me-1"></i> mirrora_db</span>
                </div>
            </div>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 mb-3 bg-dark border-success text-success" role="alert" style="font-size: 0.83rem;">
                    <?php echo $success_msg; ?>
                    <button type="button" class="btn-close btn-close-white py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 mb-3 bg-dark border-danger text-danger" role="alert" style="font-size: 0.83rem;">
                    <?php echo $error_msg; ?>
                    <button type="button" class="btn-close btn-close-white py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
<?php if($active_tab == 'dashboard'): ?>
                <div class="row g-2 mb-3">
                    <div class="col"><div class="card card-custom card-stat"><div class="stat-title">Products</div><div class="stat-value"><?php echo $total_products; ?></div></div></div>
                    <div class="col"><div class="card card-custom card-stat" style="border-left-color: #0dcaf0;"><div class="stat-title">Orders</div><div class="stat-value"><?php echo $total_orders; ?></div></div></div>
                    <div class="col"><div class="card card-custom card-stat" style="border-left-color: #ffc107;"><div class="stat-title">Messages</div><div class="stat-value"><?php echo $total_messages; ?></div></div></div>
                    <div class="col"><div class="card card-custom card-stat" style="border-left-color: #198754;"><div class="stat-title">Wishlist</div><div class="stat-value"><?php echo $total_wishlist; ?></div></div></div>
                    <div class="col"><div class="card card-custom card-stat" style="border-left-color: #fd7e14;"><div class="stat-title">Reviews</div><div class="stat-value"><?php echo $total_reviews; ?></div></div></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-custom p-3" style="border-left: 3px solid #198754 !important;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><div class="stat-title">Total Revenue</div><h3 class="fw-bold mb-0 text-success" style="font-size: 1.3rem;">Rs. <?php echo number_format($total_revenue); ?></h3></div>
                                <div class="fs-3 text-success opacity-50"><i class="fas fa-wallet"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-custom p-3">
                            <h6 class="fw-bold mb-2 text-white" style="font-size: 0.82rem;"><i class="fas fa-chart-line me-2 text-danger"></i> Revenue Analytics Overview</h6>
                            <div style="position: relative; height: 220px; width: 100%;"><canvas id="revenueChart"></canvas></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- TAB 1: DASHBOARD -->
            <?php if($active_tab == 'dashboard'): ?>
                <div class="row g-3 mb-3">
                    <div class="col-md-4 col-lg-2"><div class="card card-custom card-stat p-3"><h6 class="text-muted fw-semibold mb-1" style="font-size: 0.72rem;">Products</h6><h4 class="fw-bold text-white m-0"><?php echo $total_products; ?></h4></div></div>
                    <div class="col-md-4 col-lg-2"><div class="card card-custom card-stat p-3" style="border-left-color: #0dcaf0;"><h6 class="text-muted fw-semibold mb-1" style="font-size: 0.72rem;">Orders</h6><h4 class="fw-bold text-white m-0"><?php echo $total_orders; ?></h4></div></div>
                    <div class="col-md-4 col-lg-2"><div class="card card-custom card-stat p-3" style="border-left-color: #ffc107;"><h6 class="text-muted fw-semibold mb-1" style="font-size: 0.72rem;">Messages</h6><h4 class="fw-bold text-white m-0"><?php echo $total_messages; ?></h4></div></div>
                    <div class="col-md-4 col-lg-3"><div class="card card-custom card-stat p-3" style="border-left-color: #198754;"><h6 class="text-muted fw-semibold mb-1" style="font-size: 0.72rem;">Wishlist</h6><h4 class="fw-bold text-white m-0"><?php echo $total_wishlist; ?></h4></div></div>
                    <div class="col-md-4 col-lg-3"><div class="card card-custom card-stat p-3" style="border-left-color: #fd7e14;"><h6 class="text-muted fw-semibold mb-1" style="font-size: 0.72rem;">Reviews</h6><h4 class="fw-bold text-white m-0"><?php echo $total_reviews; ?></h4></div></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-custom p-3" style="border-left: 4px solid #198754 !important;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.72rem;">Total Revenue</h6><h3 class="fw-bold mb-0 text-success">Rs. <?php echo number_format($total_revenue); ?></h3></div>
                                <div class="fs-2 text-success opacity-50"><i class="fas fa-wallet"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-custom p-3">
                            <h6 class="fw-bold mb-3 text-white" style="font-size: 0.88rem;"><i class="fas fa-chart-line me-2 text-danger"></i> Revenue Analytics Overview</h6>
                            <div style="position: relative; height: 260px; width: 100%;"><canvas id="revenueChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <script>
                    const ctx = document.getElementById('revenueChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August'],
                            datasets: [{
                                label: 'Revenue (Rs)',
                                data: [0, 15000, 28000, 42000, 35000, 60000, 75000, <?php echo $total_revenue; ?>],
                                backgroundColor: 'rgba(128, 0, 32, 0.15)',
                                borderColor: '#800020',
                                borderWidth: 2.5,
                                tension: 0.35,
                                fill: true,
                                pointBackgroundColor: '#800020',
                                pointRadius: 4
                            }]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false, 
                            plugins: { 
                                legend: { display: true, position: 'top', labels: { color: '#fff', font: { size: 11 } } } 
                            }, 
                            scales: { 
                                y: { beginAtZero: true, grid: { color: 'rgba(249, 239, 239, 0.05)' }, ticks: { color: '#9898a0' } }, 
                                x: { grid: { display: false }, ticks: { color: '#e5e5e5' } } 
                            } 
                        }
                    });
                </script>
            <?php endif; ?>

            <!-- TAB 2: PRODUCTS MANAGEMENT -->
            <?php if($active_tab == 'products'): ?>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card card-custom p-3">
                            <?php 
                            if($edit_mode > 0) {
                                $edit_query = mysqli_query($conn, "SELECT * FROM products WHERE id = $edit_mode");
                                $p_data = mysqli_fetch_assoc($edit_query);
                            }
                            ?>
                            <h6 class="fw-bold mb-3 border-bottom border-secondary border-opacity-25 pb-2 text-danger" style="font-size: 0.9rem;"><?php echo ($edit_mode > 0) ? 'Update Product Details' : 'Add New Product'; ?></h6>
                            
                            <form action="admin.php?tab=products" method="POST" enctype="multipart/form-data">
                                <?php if($edit_mode > 0): ?>
                                    <input type="hidden" name="product_id" value="<?php echo $p_data['id']; ?>">
                                <?php endif; ?>

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem;">Product Name</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo ($edit_mode > 0) ? $p_data['name'] : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem;">Category</label>
                                        <select name="category" class="form-select" required>
                                            <?php 
                                            $cats = ['Men', 'Women', 'Kids', 'Jewelry', 'Shoes'];
                                            foreach($cats as $c) {
                                                $selected = ($edit_mode > 0 && $p_data['category'] == $c) ? 'selected' : '';
                                                echo "<option value='$c' $selected>$c</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem;">Price (Rs)</label>
                                        <input type="number" step="0.01" name="price" class="form-control" value="<?php echo ($edit_mode > 0) ? $p_data['price'] : ''; ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem;">Discount %</label>
                                        <input type="number" name="discount_percent" class="form-control" value="<?php echo ($edit_mode > 0) ? $p_data['discount_percent'] : '0'; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem;">Product Image</label>
                                        <input type="file" name="image_file" class="form-control" <?php echo ($edit_mode == 0) ? 'required' : ''; ?>>
                                        <?php if($edit_mode > 0 && !empty($p_data['images'])): ?>
                                            <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;">Current: <?php echo $p_data['images']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem;">Size</label>
                                        <input type="text" name="size" class="form-control" placeholder="e.g. S, M, L, XL" value="<?php echo ($edit_mode > 0) ? ($p_data['size'] ?? '') : ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem;">Color</label>
                                        <input type="text" name="color" class="form-control" placeholder="e.g. Red, Black" value="<?php echo ($edit_mode > 0) ? ($p_data['color'] ?? '') : ''; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem;">Description</label>
                                        <textarea name="description" class="form-control" rows="1" placeholder="Enter description..."><?php echo ($edit_mode > 0) ? ($p_data['description'] ?? '') : ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex gap-2">
                                    <?php if($edit_mode > 0): ?>
                                        <button type="submit" name="update_product" class="btn btn-success btn-sm px-3 fw-semibold py-1">Update Product</button>
                                        <a href="admin.php?tab=products" class="btn btn-outline-secondary btn-sm px-3 py-1">Cancel Edit</a>
                                    <?php else: ?>
                                        <button type="submit" name="add_product" class="btn btn-sm px-3 fw-semibold py-1 text-white" style="background-color: var(--accent-red);">Add Product</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card card-custom p-3">
                            <h6 class="fw-bold mb-2 text-white" style="font-size: 0.9rem;">Products Inventory</h6>
                            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="sticky-top" style="background-color: var(--bg-card); font-size: 0.8rem;">
                                        <tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th class="text-end">Actions</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $p_result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                                        if(mysqli_num_rows($p_result) > 0) {
                                            while($row = mysqli_fetch_assoc($p_result)) {
                                                echo "<tr>
                                                    <td class='fw-semibold'>#{$row['id']}</td>
                                                    <td><img src='assets/images/{$row['images']}' width='35' height='35' style='object-fit:cover; border-radius:4px;'></td>
                                                    <td class='fw-medium text-white'>{$row['name']}</td>
                                                    <td><span class='badge bg-secondary bg-opacity-25 text-light fw-normal px-2 py-1' style='font-size: 0.72rem;'>{$row['category']}</span></td>
                                                    <td class='fw-bold text-success'>Rs. " . number_format($row['price']) . "</td>
                                                    <td class='text-end'>
                                                        <a href='admin.php?tab=products&edit_product={$row['id']}' class='btn btn-outline-info btn-sm px-2 py-0' style='font-size: 0.75rem;'><i class='fas fa-edit'></i></a>
                                                        <a href='admin.php?tab=products&delete_product={$row['id']}' class='btn btn-outline-danger btn-sm px-2 py-0' style='font-size: 0.75rem;' onclick='return confirm(\"Are you sure you want to delete this product?\");'><i class='fas fa-trash'></i></a>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center text-muted py-3'>No products found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- TAB 3: ORDERS -->
            <?php if($active_tab == 'orders'): ?>
                <div class="card card-custom p-3">
                    <h6 class="fw-bold mb-3 text-danger" style="font-size: 0.9rem;"><i class="fas fa-shopping-cart me-2"></i> Customer Orders</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead style="font-size: 0.8rem;">
                                <tr><th>Order ID</th><th>Customer Info</th><th>Shipping Address</th><th>Products</th><th>Total (Rs)</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $o_res = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
                                if($o_res && mysqli_num_rows($o_res) > 0) {
                                    while($ord = mysqli_fetch_assoc($o_res)) {
                                        $current_status = $ord['status'];
                                        echo "<tr>
                                            <td class='fw-bold'>#{$ord['id']}</td>
                                            <td><span class='fw-semibold text-white'>{$ord['customer_name']}</span><br><small class='text-muted' style='font-size: 0.72rem;'><i class='fas fa-envelope'></i> {$ord['email']}</small><br><small class='text-muted' style='font-size: 0.72rem;'><i class='fas fa-phone'></i> {$ord['phone']}</small></td>
                                            <td>{$ord['address']}<br><span class='badge bg-dark border border-secondary fw-normal text-light' style='font-size: 0.7rem;'>{$ord['city']}</span></td>
                                            <td><small style='white-space: pre-wrap; font-size: 0.78rem;' class='text-muted'>{$ord['order_products']}</small></td>
                                            <td class='fw-bold text-success'>Rs. " . number_format($ord['total_amount'], 2) . "</td>
                                            <td>
                                                <form action='admin.php?tab=orders' method='POST' class='d-flex align-items-center gap-1'>
                                                    <input type='hidden' name='order_id' value='{$ord['id']}'>
                                                    <select name='status' class='form-select form-select-sm status-select status-{$current_status}' id='status_{$ord['id']}' onchange='updateDropdownColor(this)' style='width: 110px; font-size: 0.75rem; padding: 0.2rem 0.4rem;'>
                                                        <option value='Pending' " . ($current_status == 'Pending' ? 'selected' : '') . ">Pending</option>
                                                        <option value='Processing' " . ($current_status == 'Processing' ? 'selected' : '') . ">Processing</option>
                                                        <option value='Delivered' " . ($current_status == 'Delivered' ? 'selected' : '') . ">Delivered</option>
                                                        <option value='Cancelled' " . ($current_status == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>
                                                    </select>
                                                    <button type='submit' name='update_order_status' class='btn btn-outline-light btn-sm py-0 px-2' title='Update Status'><i class='fas fa-check' style='font-size: 0.7rem;'></i></button>
                                                </form>
                                            </td>
                                            <td><small class='text-muted' style='font-size: 0.72rem;'>{$ord['order_date']}</small></td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center text-muted py-3'>No orders found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <script>function updateDropdownColor(el) { el.className = "form-select form-select-sm status-select status-" + el.value; }</script>
            <?php endif; ?>

            <!-- TAB 4: REVIEWS CONTROL -->
            <?php if($active_tab == 'reviews'): ?>
                <div class="card card-custom p-3">
                    <h6 class="fw-bold mb-3 text-danger" style="font-size: 0.9rem;"><i class="fas fa-star me-2"></i> Manage Customer Reviews</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead style="font-size: 0.8rem;"><tr><th>ID</th><th>Product ID</th><th>User Name</th><th>Review</th><th>Rating</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php
                                $review_result = mysqli_query($conn, "SELECT * FROM reviews ORDER BY id DESC");
                                if ($review_result && mysqli_num_rows($review_result) > 0) {
                                    while ($rev = mysqli_fetch_assoc($review_result)) {
                                        $status_badge = ($rev['status'] == 'approved') ? '<span class="badge bg-success bg-opacity-25 text-success fw-normal">Approved</span>' : '<span class="badge bg-warning bg-opacity-25 text-warning fw-normal">Pending</span>';
                                        echo '<tr>
                                            <td>' . $rev['id'] . '</td><td>' . $rev['product_id'] . '</td><td class="text-white">' . htmlspecialchars($rev['user_name']) . '</td>
                                            <td class="text-muted">' . htmlspecialchars($rev['review_text']) . '</td><td>' . intval($rev['rating']) . ' ★</td><td>' . $status_badge . '</td>
                                            <td>';
                                        if ($rev['status'] != 'approved') {
                                            echo '<a href="admin.php?tab=reviews&action=approve&id=' . $rev['id'] . '" class="btn btn-outline-success btn-sm py-0 px-2 me-1" style="font-size: 0.75rem;">Approve</a>';
                                        }
                                        echo '<a href="admin.php?tab=reviews&action=delete&id=' . $rev['id'] . '" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 0.75rem;" onclick=\'return confirm("Delete review?");\'>Delete</a></td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center text-muted py-3">No reviews available.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- TAB 5: MESSAGES CONTROL -->
            <?php if($active_tab == 'messages'): ?>
                <div class="card card-custom p-3">
                    <h6 class="fw-bold mb-3 text-danger" style="font-size: 0.9rem;"><i class="fas fa-envelope me-2"></i> User Messages / Inquiries</h6>
                    <div class="table-responsive">
                        <table class="table hover align-middle">
                            <thead style="font-size: 0.8rem;">
                                <tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $msg_result = mysqli_query($conn, "SELECT * FROM messages ORDER BY id DESC");
                                if ($msg_result && mysqli_num_rows($msg_result) > 0) {
                                    while ($msg = mysqli_fetch_assoc($msg_result)) {
                                        echo "<tr>
                                            <td class='fw-semibold'>#{$msg['id']}</td>
                                            <td class='text-white'>" . htmlspecialchars($msg['name']) . "</td>
                                            <td>" . htmlspecialchars($msg['email']) . "</td>
                                            <td><small style='white-space: pre-wrap;' class='text-muted'>" . htmlspecialchars($msg['message']) . "</small></td>
                                            <td><small class='text-muted'>" . ($msg['created_at'] ?? 'N/A') . "</small></td>
                                            <td>
                                                <a href='admin.php?tab=messages&delete_msg={$msg['id']}' class='btn btn-outline-danger btn-sm py-0 px-2' style='font-size: 0.75rem;' onclick='return confirm(\"Are you sure you want to delete this message?\");'><i class='fas fa-trash'></i> Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center text-muted py-3'>No messages found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- TAB 6: WISHLIST -->
            <?php if($active_tab == 'wishlist'): ?>
                <div class="card card-custom p-3">
                    <h6 class="fw-bold mb-3 text-danger" style="font-size: 0.9rem;"><i class="fas fa-heart me-2"></i> Customer Wishlist Items</h6>
                    <div class="table-responsive">
                        <table class="table hover align-middle">
                            <thead style="font-size: 0.8rem;">
                                <tr><th>ID</th><th>User ID / Session</th><th>Product ID</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $wish_result = mysqli_query($conn, "SELECT * FROM wishlist ORDER BY id DESC");
                                if ($wish_result && mysqli_num_rows($wish_result) > 0) {
                                    while ($w = mysqli_fetch_assoc($wish_result)) {
                                        echo "<tr>
                                            <td class='fw-semibold'>#{$w['id']}</td>
                                            <td class='text-muted'>" . htmlspecialchars($w['user_id'] ?? 'Guest') . "</td>
                                            <td class='text-white'>#{$w['product_id']}</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center text-muted py-3'>No wishlist items found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>