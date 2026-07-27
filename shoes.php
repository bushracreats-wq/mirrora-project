<?php 
include 'header.php'; 
include 'config.php'; 
?>

<!-- =================== Shoes Collection Page =================== -->
<div class="container-fluid px-4 mt-4 mb-5">
    
    <!-- Page Header -->
    <div class="d-flex align-items-center mb-4 mt-4 border-bottom pb-2">
        <h3 class="m-0 fw-bold ps-2 category-heading">Shoes Collection</h3>
    </div>

    <!-- Products Row -->
    <div class="row g-3 mb-5">
        <?php 
        $query = "SELECT * FROM products WHERE category = 'Shoes'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)): 
        ?>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="card h-100 shadow-sm product-card">
                    
                    <!-- Image Wrapper with Relative Positioning -->
                    <div class="position-relative overflow-hidden product-img-wrapper" style="position: relative !important;">
                        
                        <!-- Discount Badge -->
                        <?php 
                        if (isset($row['discount_percent']) && $row['discount_percent'] > 0) {
                            echo '<span class="custom-discount-badge" style="position: absolute !important; top: 10px !important; left: 10px !important; background-color: #800020 !important; color: #fff !important; padding: 3px 8px !important; font-size: 11px !important; font-weight: bold !important; border-radius: 4px !important; z-index: 99 !important;">' . $row['discount_percent'] . '% OFF</span>';
                        }
                        ?>

                        <!-- Product Image -->
                        <a href="product-details.php?id=<?php echo $row['id']; ?>">
                            <img src="assets/images/<?php echo $row['images']; ?>" class="card-img-top product-img" alt="Product">
                        </a>
                        
                        <!-- TRY ON Overlay -->
                        <div class="try-on-overlay">
                            <a href="tryon.php?id=<?php echo $row['id']; ?>" class="btn btn-dark btn-sm w-100 rounded-0 fw-bold text-decoration-none py-2" style="letter-spacing: 1px; font-size: 0.80rem;">
                                <i class="fas fa-user-circle me-1"><span> TRY ON</span></i>
                            </a>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body text-center d-flex flex-column">
                        <h6 class="fw-bold mb-1">
                            <a href="product-details.php?id=<?php echo $row['id']; ?>" class="text-dark text-decoration-none"><?php echo $row['name']; ?></a>
                        </h6>
                        
                        <!-- Price Box -->
                        <div class="price-box mb-3 mt-auto">
                            <?php 
                            $original_price = $row['price'];
                            $discount_percent = isset($row['discount_percent']) ? $row['discount_percent'] : 0;
                            
                            if ($discount_percent > 0) {
                                $discount_amount = ($original_price * $discount_percent) / 100;
                                $final_price = $original_price - $discount_amount;
                                echo '<span class="old-price" style="text-decoration: line-through; color: #888; font-size: 12px; margin-right: 5px;">Rs. ' . $original_price . '</span>';
                                echo '<span class="new-price fw-bold" style="color: #800020;">Rs. ' . $final_price . '</span>';
                            } else {
                                echo '<span class="fw-bold text-dark">Rs. ' . $original_price . '</span>';
                            }
                            ?>
                        </div>
                        
                        <!-- Add to Cart Button -->
                        <a href="cart.php?action=add&id=<?php echo $row['id']; ?>" class="btn btn-outline-dark btn-sm w-100 text-decoration-none">Add to Cart</a>
                    </div>

                </div>
            </div>
        <?php 
            endwhile; 
        } else {
            echo "<div class='col-12'><p class='text-muted small fst-italic ps-2'>No products found in Shoes collection.</p></div>";
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>