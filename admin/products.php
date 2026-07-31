<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once '../config.php';

$msg = "";
$edit_mode = false;
$edit_id = "";
$edit_name = "";
$edit_category = "";
$edit_price = "";
$edit_discount = "";
$edit_image = "";

// 1. Edit Fetch Logic
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $edit_id = $row['id'];
        $edit_name = $row['name'];
        $edit_category = $row['category'];
        $edit_price = $row['price'];
        $edit_discount = $row['discount_percent'];
        $edit_image = $row['images'];
    }
}

// 2. Add or Update Product Logic
if (isset($_POST['save_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']);
    $discount_percent = intval($_POST['discount_percent']);
    
    // Image Upload Handling
    $image_name = $_POST['old_image'];
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $file_tmp = $_FILES['product_image']['tmp_name'];
        $file_name = time() . '_' . $_FILES['product_image']['name'];
        $upload_dir = '../assets/images/';
        
        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            $image_name = $file_name;
        }
    }

    // Capture ID from URL query string or hidden form field safely
    $product_id = 0;
    if (isset($_GET['edit']) && !empty($_GET['edit'])) {
        $product_id = intval($_GET['edit']);
    } elseif (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
    }

    if ($product_id > 0) {
        // UPDATE QUERY
        $sql = "UPDATE products SET name='$name', category='$category', price='$price', discount_percent='$discount_percent', images='$image_name' WHERE id=$product_id";
        if (mysqli_query($conn, $sql)) {
            header("Location: products.php?msg=updated");
            exit();
        } else {
            $msg = "<div class='alert alert-danger'>Error updating product: " . mysqli_error($conn) . "</div>";
        }
    } else {
        // INSERT QUERY
        $sql = "INSERT INTO products (name, category, price, discount_percent, images) VALUES ('$name', '$category', '$price', '$discount_percent', '$image_name')";
        if (mysqli_query($conn, $sql)) {
            header("Location: products.php?msg=added");
            exit();
        } else {
            $msg = "<div class='alert alert-danger'>Error adding product: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Success Messages Handler
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'updated') {
        $msg = "<div class='alert alert-success'>Product updated successfully!</div>";
    } elseif ($_GET['msg'] == 'added') {
        $msg = "<div class='alert alert-success'>Product added successfully!</div>";
    }
}

// 3. Delete Product Logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header("Location: products.php");
    exit();
}

// 4. Category Filter Logic
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'All';
if ($selected_category !== 'All') {
    $safe_cat = mysqli_real_escape_string($conn, $selected_category);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE category = '$safe_cat' ORDER BY id DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Mirrora Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f9;
        }
        .admin-title {
            font-family: 'Cinzel', serif;
            color: #800020;
        }
        .btn-mirrora {
            background-color: #800020;
            border: none;
            color: white;
        }
        .btn-mirrora:hover {
            background-color: #600018;
            color: white;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 py-3 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php" style="font-family: 'Cinzel', serif;">MIRRORA ADMIN</a>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold admin-title">Products Inventory & Categories</h3>
            <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
        </div>

        <?php echo $msg; ?>

        <div class="card border-0 shadow-sm p-4 mb-5 bg-white rounded-4">
            <h5 class="fw-bold mb-3"><?php echo $edit_mode ? 'Edit Product ID #' . $edit_id : 'Add New Product'; ?></h5>
            <form method="POST" action="products.php<?php echo $edit_mode ? '?edit=' . $edit_id : ''; ?>" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo $edit_id; ?>">
                <input type="hidden" name="old_image" value="<?php echo $edit_image; ?>">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Product Name</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($edit_name); ?>" placeholder="Luxury Linen Suit">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Category</label>
                        <select name="category" class="form-control" required>
                            <option value="Men" <?php if($edit_category=='Men') echo 'selected'; ?>>Men</option>
                            <option value="Women" <?php if($edit_category=='Women') echo 'selected'; ?>>Women</option>
                            <option value="Kids" <?php if($edit_category=='Kids') echo 'selected'; ?>>Kids</option>
                            <option value="Jewelry" <?php if($edit_category=='Jewelry') echo 'selected'; ?>>Jewelry</option>
                            <option value="Shoes" <?php if($edit_category=='Shoes') echo 'selected'; ?>>Shoes</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Price (Rs.)</label>
                        <input type="number" name="price" class="form-control" required value="<?php echo $edit_price; ?>" placeholder="4500">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Discount (%)</label>
                        <input type="number" name="discount_percent" class="form-control" value="<?php echo $edit_discount !== '' ? $edit_discount : 0; ?>" placeholder="20">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Select Image File</label>
                        <input type="file" name="product_image" class="form-control" <?php echo $edit_mode ? '' : 'required'; ?>>
                        <?php if($edit_mode && !empty($edit_image)): ?>
                            <small class="text-muted">Current: <?php echo $edit_image; ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="save_product" class="btn btn-mirrora px-4 fw-bold">
                            <?php echo $edit_mode ? 'Update Product' : 'Add Product'; ?>
                        </button>
                        <?php if($edit_mode): ?>
                            <a href="products.php" class="btn btn-secondary px-3 ms-2">Cancel</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <div id="product-table" class="mb-3 d-flex flex-wrap gap-2">
            <a href="products.php?category=All#product-table" class="btn btn-sm <?php echo $selected_category=='All' ? 'btn-dark' : 'btn-outline-dark'; ?>">All Products</a>
            <a href="products.php?category=Women#product-table" class="btn btn-sm <?php echo $selected_category=='Women' ? 'btn-dark' : 'btn-outline-dark'; ?>">Women</a>
            <a href="products.php?category=Men#product-table" class="btn btn-sm <?php echo $selected_category=='Men' ? 'btn-dark' : 'btn-outline-dark'; ?>">Men</a>
            <a href="products.php?category=Kids#product-table" class="btn btn-sm <?php echo $selected_category=='Kids' ? 'btn-dark' : 'btn-outline-dark'; ?>">Kids</a>
            <a href="products.php?category=Jewelry#product-table" class="btn btn-sm <?php echo $selected_category=='Jewelry' ? 'btn-dark' : 'btn-outline-dark'; ?>">Jewelry</a>
            <a href="products.php?category=Shoes#product-table" class="btn btn-sm <?php echo $selected_category=='Shoes' ? 'btn-dark' : 'btn-outline-dark'; ?>">Shoes</a>
        </div>

        <div class="card border-0 shadow-sm p-3 bg-white rounded-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><img src="../assets/images/<?php echo $row['images']; ?>" class="rounded" style="width: 45px; height: 45px; object-fit: cover;" alt=""></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo $row['category']; ?></span></td>
                                <td>Rs. <?php echo number_format($row['price']); ?></td>
                                <td class="text-danger fw-bold"><?php echo $row['discount_percent']; ?>%</td>
                                <td>
                                    <a href="products.php?edit=<?php echo $row['id']; ?>#product-table" class="btn btn-primary btn-sm me-1"><i class="fas fa-edit"></i></a>
                                    <a href="products.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No products found in this category.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>