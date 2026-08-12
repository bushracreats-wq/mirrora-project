<?php 
include 'header.php'; 
include 'config.php'; 

// Product ID get karein URL se
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<div class='container my-5 text-center'><h3>Product not found!</h3><a href='index.php' class='btn btn-dark mt-3'>Back to Home</a></div>";
    include 'footer.php';
    exit;
}
?>

<div class="container my-5">
    <div class="row align-items-center mb-5">
        <!-- Product Image Section -->
        <div class="col-lg-5 mb-4 mb-lg-0 text-center">
            <div class="card border-0 shadow-sm p-3 bg-light">
                <img src="assets/images/<?php echo $product['images']; ?>" class="img-fluid rounded" alt="<?php echo $product['name']; ?>" style="max-height: 380px; width: 100%; object-fit: contain;">
            </div>
        </div>

        <!-- Product Details & Options Section -->
        <div class="col-lg-7">
            <h2 class="fw-bold mb-2" style="font-family: 'Cinzel', serif; color: #333;"><?php echo $product['name']; ?></h2>
            <p class="text-muted small mb-3">Category: <span class="fw-semibold text-dark"><?php echo $product['category']; ?></span></p>
            <h4 class="text-danger fw-bold mb-4">Rs. <?php echo $product['price']; ?></h4>

            <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                <?php echo isset($product['description']) ? $product['description'] : ''; ?>
            </p>

            <!-- Add to Cart Form -->
            <form action="cart.php" method="GET">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                <!-- Dynamic Size Selection -->
                <?php if (!empty($product['size'])): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px;">Select Size:</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php 
                        $sizes_array = explode(',', $product['size']);
                        $is_first_size = true;
                        foreach ($sizes_array as $size) {
                            $clean_size = trim($size);
                            if(empty($clean_size)) continue;
                            $checked = $is_first_size ? 'checked' : '';
                            $id_attr = 'size_' . preg_replace('/[^a-zA-Z0-9]/', '', $clean_size);
                        ?>
                            <input type="radio" class="btn-check" name="size" id="<?php echo $id_attr; ?>" value="<?php echo htmlspecialchars($clean_size); ?>" <?php echo $checked; ?>>
                            <label class="btn btn-outline-dark btn-sm rounded-0 px-3" for="<?php echo $id_attr; ?>"><?php echo htmlspecialchars($clean_size); ?></label>
                        <?php 
                            $is_first_size = false;
                        } 
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Dynamic Color Selection -->
                <?php if (!empty($product['color'])): ?>
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px;">Select Color:</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php 
                        $colors_array = explode(',', $product['color']);
                        $is_first_color = true;
                        foreach ($colors_array as $color) {
                            $clean_color = trim($color);
                            if(empty($clean_color)) continue;
                            $checked = $is_first_color ? 'checked' : '';
                            $color_id_attr = 'color_' . preg_replace('/[^a-zA-Z0-9]/', '', $clean_color);
                        ?>
                            <input type="radio" class="btn-check" name="color" id="<?php echo $color_id_attr; ?>" value="<?php echo htmlspecialchars($clean_color); ?>" <?php echo $checked; ?>>
                            <label class="btn btn-outline-dark btn-sm rounded-0 px-3" for="<?php echo $color_id_attr; ?>"><?php echo htmlspecialchars($clean_color); ?></label>
                        <?php 
                            $is_first_color = false;
                        } 
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Action Buttons with Back & Wishlist -->
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <button type="submit" class="btn btn-dark btn-sm px-4 py-2 rounded-0 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">
                        <i class="fas fa-shopping-bag me-1"></i> Add to Cart
                    </button>
                    <a href="tryon.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-danger btn-sm px-4 py-2 rounded-0 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">
                        <i class="fas fa-user-circle me-1"></i> Try On
                    </a>
                    <a href="wishlist.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-outline-dark btn-sm px-3 py-2 rounded-0 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;" title="Add to Wishlist">
                        <i class="far fa-heart"></i>
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-0 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;" title="Go Back">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- CUSTOMER REVIEWS SECTION -->
    <div class="row mt-5 pt-4 border-top">
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4">Customer Reviews</h4>
            
            <?php
            $rev_query = "SELECT * FROM reviews WHERE product_id = $id AND status = 'approved' ORDER BY id DESC";
            $rev_result = mysqli_query($conn, $rev_query);

            if ($rev_result && mysqli_num_rows($rev_result) > 0) {
                while ($rev = mysqli_fetch_assoc($rev_result)) {
                    echo '<div class="card border-0 shadow-sm p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold m-0 text-dark">' . htmlspecialchars($rev['user_name']) . '</h6>
                                <span class="text-warning small">';
                    
                    // Rating stars generation
                    $rating = intval($rev['rating']);
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo '<i class="fas fa-star"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }

                    echo '      </span>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">' . htmlspecialchars($rev['review_text']) . '</p>
                          </div>';
                }
            } else {
                echo '<p class="text-muted fst-italic">Is product par abhi tak koi approved review mojood nahi hai.</p>';
            }
            ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>