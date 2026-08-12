<?php
session_start();
include 'config.php'; 
include 'header.php'; 

$success_msg = '';
$error_msg = '';

if (isset($_POST['submit_review'])) {
    $product_id = intval($_POST['product_id']);
    $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $review_text = mysqli_real_escape_string($conn, $_POST['review_text']);
    $rating = intval($_POST['rating']);

    $query = "INSERT INTO reviews (product_id, user_name, review_text, rating, status) VALUES ('$product_id', '$user_name', '$review_text', '$rating', 'pending')";
    
    if (mysqli_query($conn, $query)) {
        $success_msg = "Aapka review successfully submit ho gaya hai! Admin approval ke baad show hoga.";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews & Feedback - MIRRÓRA</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            background-color: #fdfbfb;
            font-family: 'Poppins', sans-serif;
            color: #000000;
        }
        .review-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0 !important;
        }
        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(128, 0, 32, 0.1) !important;
        }
        .review-img-container {
            height: 280px; 
            width: 100%;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        .review-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            transition: transform 0.5s ease;
        }
        .review-card:hover .review-img-container img {
            transform: scale(1.03);
        }
        .form-container {
            background: #ffffff;
            border-top: 4px solid #800020;
            border: 1px solid #cbd5e1;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-radius: 0 !important;
        }
        .form-control, .form-select {
            border-radius: 0 !important;
            border: 1px solid #cbd5e1;
            color: #000000;
        }
        .form-control:focus, .form-select:focus {
            border-color: #800020;
            box-shadow: 0 0 0 0.25rem rgba(128, 0, 32, 0.15);
            background-color: #ffffff;
            color: #000000;
        }
        .btn-maroon {
            background-color: #800020;
            color: #ffffff;
            border-radius: 0 !important;
            transition: background-color 0.3s ease;
        }
        .btn-maroon:hover {
            background-color: #600018;
            color: #ffffff;
        }
    </style>
</head>
<body>

<!-- Customer Reviews Display Section -->
<div class="container my-4 my-md-5 py-2">
    <div class="text-center mb-4 mb-md-5 px-3">
        <h2 class="fw-bold mb-2 fs-3 fs-md-2" style="color: #800020; font-family: 'Cinzel', serif; letter-spacing: 1px;">What Our Customers Say</h2>
        <div style="width: 60px; height: 3px; background-color: #800020; margin: 0 auto 15px auto;"></div>
        <p class="text-muted small">Explore genuine feedback and looks shared by our valued customers.</p>
    </div>
    
    <div class="row g-4">
        <?php
        $all_reviews = mysqli_query($conn, "SELECT r.*, p.name as prod_name, p.images as prod_image FROM reviews r JOIN products p ON r.product_id = p.id WHERE r.status = 'approved' ORDER BY r.id DESC");
        
        if ($all_reviews && mysqli_num_rows($all_reviews) > 0) {
            while ($row = mysqli_fetch_assoc($all_reviews)) {
                $stars = str_repeat("★", intval($row['rating']));
                
                echo '<div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="card review-card shadow-sm h-100 bg-white">';
                
                if (!empty($row['prod_image'])) {
                    echo '<div class="review-img-container">
                            <img src="assets/images/' . htmlspecialchars($row['prod_image']) . '" alt="' . htmlspecialchars($row['prod_name']) . '">
                          </div>';
                }

                echo '<div class="card-body d-flex flex-column p-3 p-md-4">
                            <span class="badge mb-3 align-self-start px-2 py-1 text-uppercase text-white" style="font-size: 0.7rem; letter-spacing: 1px; background-color: #555 !important;">' . htmlspecialchars($row['prod_name']) . '</span>
                            <p class="fst-italic text-secondary mb-4" style="font-size: 0.9rem; line-height: 1.6;">"' . htmlspecialchars($row['review_text']) . '"</p>
                            
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">- ' . htmlspecialchars($row['user_name']) . '</h6>
                                <span class="text-warning fs-6">' . $stars . '</span>
                            </div>
                        </div>
                      </div>
                      </div>';
            }
        } else {
            echo '<div class="col-12 text-center py-5"><p class="text-muted fst-italic">No approved reviews displayed yet.</p></div>';
        }
        ?>
    </div>
</div>

<!-- Review Submission Form Section -->
<div class="container my-4 my-md-5 py-2">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card form-container border-0 p-3 p-md-4 p-lg-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-2 fs-4" style="color: #800020; font-family: 'Cinzel', serif;">Share Your Experience</h3>
                    <p class="text-muted small">Apna keemti review share karein taaki doosre customers ki madad ho sake.</p>
                </div>

                <?php if(!empty($success_msg)): ?>
                    <div class="alert alert-success border-0 shadow-sm rounded-0 small"><?php echo $success_msg; ?></div>
                <?php endif; ?>

                <?php if(!empty($error_msg)): ?>
                    <div class="alert alert-danger border-0 shadow-sm rounded-0 small"><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <form action="add_review.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary" style="letter-spacing: 1px;">Select Product</label>
                        <select name="product_id" class="form-select py-2 shadow-none" required>
                            <option value="">-- Product Select Karein --</option>
                            <?php
                            $prod_res = mysqli_query($conn, "SELECT id, name FROM products");
                            while($p = mysqli_fetch_assoc($prod_res)) {
                                echo "<option value='{$p['id']}'>{$p['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary" style="letter-spacing: 1px;">Aapka Naam</label>
                        <input type="text" name="user_name" class="form-control py-2 shadow-none" placeholder="e.g. Ayesha Khan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary" style="letter-spacing: 1px;">Rating</label>
                        <select name="rating" class="form-select py-2 shadow-none" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5 Stars - Excellent)</option>
                            <option value="4">⭐⭐⭐⭐ (4 Stars - Good)</option>
                            <option value="3">⭐⭐⭐ (3 Stars - Average)</option>
                            <option value="2">⭐⭐ (2 Stars - Poor)</option>
                            <option value="1">⭐ (1 Star - Very Bad)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-secondary" style="letter-spacing: 1px;">Aapka Review / Comments</label>
                        <textarea name="review_text" rows="4" class="form-control shadow-none" placeholder="Fabric aur stitching kaisi lagi yahan likhein..." required></textarea>
                    </div>

                    <button type="submit" name="submit_review" class="btn btn-maroon w-100 fw-bold py-2.5 shadow-sm text-uppercase mb-2" style="letter-spacing: 1px; font-size: 0.85rem;">Submit Review</button>
                    <a href="index.php" class="btn btn-outline-dark w-100 py-2 text-uppercase fw-bold rounded-0" style="font-size: 0.8rem; letter-spacing: 1px;">Back to Home</a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>

</body>
</html>