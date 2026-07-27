<?php
session_start();
include 'header.php';
include 'config.php';
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold" style="font-family: 'Cinzel', serif; color: #800020;">Hot Sale & Best Sellers</h2>
        <p class="text-muted">Grab our most popular outfits loved by customers, complete with genuine reviews.</p>
    </div>

    <div class="row">
        <?php
        // Query fetch all products (both discounted and non-discounted)
      // Is query se database se mix products uth kar aayein ge (random order mein)
$query = "SELECT * FROM products ORDER BY RAND() LIMIT 6";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $image = isset($row['images']) ? $row['images'] : '';
                $name = $row['name'];
                $price = $row['price'];
                $discount_percent = isset($row['discount_percent']) ? $row['discount_percent'] : 0;
                $id = $row['id'];

                // Calculate discounted price if discount exists
                if ($discount_percent > 0) {
                    $discount_amount = ($price * $discount_percent) / 100;
                    $final_price = $price - $discount_amount;
                }
        ?>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <!-- Product Image & Badge -->
                        <div class="position-relative overflow-hidden" style="position: relative !important;">
                            <img src="assets/images/<?php echo $image; ?>" class="card-img-top" alt="<?php echo $name; ?>" style="height: 350px; object-fit: cover;">
                            
                            <!-- Custom Wine Red Discount Badge (Shows only if discount exists) -->
                            <?php if ($discount_percent > 0): ?>
                                <span class="badge position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill" style="background-color: #800020 !important; color: #fff !important; z-index: 99 !important;"><?php echo $discount_percent; ?>% OFF</span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold mb-2"><?php echo $name; ?></h5>
                            
                            <!-- Price Box with Strikethrough for Discounted Items -->
                            <div class="price-box mb-3">
                                <?php if ($discount_percent > 0): ?>
                                    <span class="old-price text-muted text-decoration-line-through me-2" style="font-size: 14px;">Rs. <?php echo number_format($price); ?></span>
                                    <span class="new-price fw-bold" style="color: #800020; font-size: 16px;">Rs. <?php echo number_format($final_price); ?></span>
                                <?php else: ?>
                                    <span class="fw-bold" style="color: #800020;">Rs. <?php echo number_format($price); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Customer Comments / Reviews Section -->
                            <div class="bg-light p-3 rounded mb-3 mt-auto border-start border-3" style="border-color: #800020 !important;">
                                <p class="small text-muted mb-1 fst-italic">"Absolutely love the fabric and stitching! Received so many compliments at the event."</p>
                                <span class="small fw-bold text-dark">- Ayesha Khan <span class="text-warning">★★★★★</span></span>
                            </div>

                            <!-- Add to Cart Button -->
                            <a href="cart.php?action=add&id=<?php echo $id; ?>" class="btn btn-dark w-100 rounded-pill py-2 fw-bold" style="background-color: #800020; border: none;">
                                Add to Bag <id class="fas fa-shopping-bag ms-1"></id>
                            </a>
                        </div>
                    </div>
                </div>
        <?php 
            }
        } else {
            echo '<div class="col-12 text-center py-5"><p class="text-muted">No items available right now.</p></div>';
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>