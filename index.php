<?php 
include 'header.php'; 
include 'config.php'; 
$cats = ['Men', 'Women', 'Kids', 'Jewelry', 'Shoes']; 
?>

<div id="mainCarousel" class="carousel slide carousel-fade mb-5">

  <div class="carousel-indicators">
    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
</div>

    <div class="carousel-inner">
        
        <div class="carousel-item active">
            <video id="bannerVideo" src="assets/images/banervideo.mp4" class="d-block w-100 banner-img" autoplay muted playsinline style="object-fit: cover; height: 500px;"></video>
            <div class="carousel-caption text-start">
                <span class="badge bg-light text-dark mb-3">VIRTUAL FITTING ROOM</span>
                <h1>Fitting Room, Ab Aapki Screen Par!</h1>
                <p>Experience the future of online shopping with AI Virtual Try-On.</p>
                <a href="tryon.php" class="btn btn-light">Try AI Room</a>
            </div>
        </div>

        <div class="carousel-item">
            <img src="assets/images/bannew9.png" class="d-block w-100 banner-img" alt="Banner 2" style="object-fit: cover; height: 500px;">
        </div>

        <div class="carousel-item">
            <img src="assets/images/bannew10.png" class="d-block w-100 banner-img" alt="Banner 3" style="object-fit: cover; height: 500px;">
        </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<div class="container-fluid px-4 mt-4 mb-5">
    <h2 class="text-center mb-5 fw-bold section-title">Explore Our Collections</h2>
    
    <?php foreach($cats as $cat): ?>
        <div class="d-flex align-items-center mb-4 mt-4 border-bottom pb-2">
            <h4 class="m-0 fw-bold ps-2 category-heading"><?php echo $cat; ?></h4>
            <a href="<?php echo strtolower($cat); ?>.php" class="ms-auto text-decoration-none small fw-bold view-all-link">
                VIEW ALL <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3 mb-5">
            <?php 
            $query = "SELECT * FROM products WHERE category = '$cat' LIMIT 6";
            $result = mysqli_query($conn, $query);
            
            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)): 
            ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="card h-100 shadow-sm product-card">
                        
                        <div class="position-relative overflow-hidden product-img-wrapper">
                            
                            <?php if (isset($row['discount_percent']) && $row['discount_percent'] > 0): ?>
                                <span class="custom-discount-badge" style="position: absolute; top: 10px; left: 10px; background-color: #800020; color: #fff; padding: 3px 8px; font-size: 11px; font-weight: bold; border-radius: 4px; z-index: 99;">
                                    <?php echo $row['discount_percent']; ?>% OFF
                                </span>
                            <?php endif; ?>

                            <a href="product-details.php?id=<?php echo $row['id']; ?>">
                                <img src="assets/images/<?php echo $row['images']; ?>" class="card-img-top product-img" alt="Product">
                            </a>
                            
                            <div class="try-on-overlay">
                                <a href="tryon.php?id=<?php echo $row['id']; ?>" class="btn btn-dark btn-sm w-100 rounded-0 fw-bold text-decoration-none py-2" style="letter-spacing: 1px; font-size: 0.80rem;">
                                    <i class="fas fa-user-circle me-1"><span> TRY ON</span></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body text-center d-flex flex-column">
                            <h6 class="fw-bold mb-1">
                                <a href="product-details.php?id=<?php echo $row['id']; ?>" class="text-dark text-decoration-none"><?php echo $row['name']; ?></a>
                            </h6>
                            
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
                            
                            <a href="cart.php?action=add&id=<?php echo $row['id']; ?>" class="btn btn-outline-dark btn-sm w-100 text-decoration-none">Add to Cart</a>
                        </div>

                    </div>
                </div>
            <?php 
                endwhile; 
            } else {
                echo "<div class='col-12'><p class='text-muted small fst-italic ps-2'>No products found in " . $cat . " collection.</p></div>";
            }
            ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>