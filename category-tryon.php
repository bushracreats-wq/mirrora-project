<?php 
include 'header.php'; 
include 'config.php'; 
?>

<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        
        <div class="col-lg-8 col-md-7">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h3 class="fw-bold m-0" style="color: #800020;">Women's Collection</h3>
                <span class="text-muted small">Select any item to preview on the right</span>
            </div>

            <div class="row g-3">
                <?php 
                // Consider increasing LIMIT to 9 if you want 3 perfect rows of 3
                $query = "SELECT * FROM products WHERE category = 'Women' LIMIT 9"; 
                $result = mysqli_query($conn, $query);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)): 
                        $original_price = $row['price'];
                        $discount_percent = isset($row['discount_percent']) ? $row['discount_percent'] : 0;
                        $final_price = $original_price;
                        
                        if ($discount_percent > 0) {
                            $discount_amount = ($original_price * $discount_percent) / 100;
                            $final_price = $original_price - $discount_amount;
                        }
                        
                        $img_path = "assets/images/" . $row['images'];
                        $prod_name = htmlspecialchars($row['name']);
                ?>
                    <div class="col-lg-4 col-md-6 col-6">
                        <div class="card h-100 shadow-sm border-0 product-card">
                            
                            <div class="position-relative overflow-hidden product-img-wrapper">
                                <?php if ($discount_percent > 0): ?>
                                    <span class="badge" style="position: absolute; top: 10px; left: 10px; background-color: #800020; color: #fff; padding: 5px 8px; font-size: 11px; z-index: 2;">
                                        <?php echo $discount_percent; ?>% OFF
                                    </span>
                                <?php endif; ?>

                                <a href="product-details.php?id=<?php echo $row['id']; ?>">
                                    <img src="<?php echo $img_path; ?>" class="card-img-top product-img" alt="<?php echo $prod_name; ?>" style="height: 280px; object-fit: cover;">
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-dark btn-sm w-100 rounded-0 fw-bold py-2 border-0" 
                                        style="background-color: #111;"
                                        onclick="applyTryOn('<?php echo addslashes($prod_name); ?>', '<?php echo $img_path; ?>', '<?php echo $final_price; ?>')">
                                    <i class="fas fa-magic me-1"></i> TRY ON HERE
                                </button>
                            </div>

                            <div class="card-body text-center d-flex flex-column p-3">
                                <h6 class="fw-bold mb-1 text-truncate">
                                    <a href="product-details.php?id=<?php echo $row['id']; ?>" class="text-dark text-decoration-none"><?php echo $prod_name; ?></a>
                                </h6>
                                
                                <div class="price-box mb-3 mt-auto">
                                    <?php if ($discount_percent > 0): ?>
                                        <span class="text-muted text-decoration-line-through me-1 small">Rs. <?php echo $original_price; ?></span>
                                        <span class="fw-bold" style="color: #800020;">Rs. <?php echo $final_price; ?></span>
                                    <?php else: ?>
                                        <span class="fw-bold text-dark">Rs. <?php echo $original_price; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <a href="cart.php?action=add&id=<?php echo $row['id']; ?>" class="btn btn-outline-dark btn-sm w-100 fw-bold">Add to Cart</a>
                            </div>

                        </div>
                    </div>
                <?php 
                    endwhile; 
                } else {
                    echo "<div class='col-12'><p class='text-muted small italic ps-2'>No products found in this category.</p></div>";
                }
                ?>
            </div>
        </div>

        <div class="col-lg-4 col-md-5">
            <div class="card shadow border-0 sticky-top" style="top: 20px; z-index: 10; border-radius: 12px; overflow: hidden;">
                
                <div class="card-header text-white text-center py-3" style="background-color: #800020;">
                    <h5 class="m-0 fw-bold"><i class="fas fa-user-circle me-2"></i>Fitting Room Preview</h5>
                </div>
                
                <div class="card-body text-center p-3 bg-white">
                    
                    <div id="selected-info" class="alert alert-light border mb-3 p-2 text-start small" style="display: none; border-left: 4px solid #800020 !important;">
                        <span class="text-muted d-block">Currently Trying:</span>
                        <strong id="panel-title" class="text-dark">-</strong>
                        <div class="fw-bold mt-1" style="color: #800020;">Rs. <span id="panel-price">-</span></div>
                    </div>

                    <div id="tryon-canvas" class="position-relative border rounded p-2 mb-3 bg-light d-flex align-items-center justify-content-center flex-column" style="min-height: 380px;">
                        
                        <img id="model-avatar" src="https://dummyimage.com/300x400/e0e0e0/000000.png&text=Default+Model" class="img-fluid rounded" alt="Model" style="max-height: 360px; object-fit: contain;">
                        
                        <img id="outfit-overlay" src="" class="position-absolute" style="max-height: 220px; display: none; top: 28%; left: 50%; transform: translateX(-50%); z-index: 5; filter: drop-shadow(0px 5px 5px rgba(0,0,0,0.3)); transition: transform 0.2s, top 0.2s;">

                        <div id="default-help" class="position-absolute text-muted small px-3 py-2 bg-white rounded shadow-sm border" style="bottom: 15px;">
                            👈 Click <strong>TRY ON HERE</strong> on any item
                        </div>
                    </div>

                    <div id="tryon-controls" class="d-flex justify-content-center gap-2 mb-3" style="display: none !important;">
                        <button class="btn btn-sm btn-outline-dark" onclick="adjustOutfit('up')" title="Move Up"><i class="fas fa-arrow-up"></i></button>
                        <button class="btn btn-sm btn-outline-dark" onclick="adjustOutfit('down')" title="Move Down"><i class="fas fa-arrow-down"></i></button>
                        <button class="btn btn-sm btn-outline-dark" onclick="adjustOutfit('zoom-in')" title="Scale Up"><i class="fas fa-search-plus"></i></button>
                        <button class="btn btn-sm btn-outline-dark" onclick="adjustOutfit('zoom-out')" title="Scale Down"><i class="fas fa-search-minus"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="resetTryOn()" title="Clear Fitting Room"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-dark btn-sm fw-bold py-2" onclick="alert('Photo Upload dialog box feature here')">
                            <i class="fas fa-camera me-1"></i> Upload Your Photo / Model
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Global variables to track outfit position and scale
let currentScale = 1;
let currentTop = 28; // starting top position in percentage

function applyTryOn(productName, imageSrc, price) {
    // 1. Panel Info Update
    document.getElementById('panel-title').innerText = productName;
    document.getElementById('panel-price').innerText = price;
    document.getElementById('selected-info').style.display = 'block';

    // 2. Overlay Image Update & Reset Position
    const overlay = document.getElementById('outfit-overlay');
    overlay.src = imageSrc;
    overlay.style.display = 'block';
    
    // Reset controls to default every time a new product is selected
    currentScale = 1;
    currentTop = 28;
    updateOverlayStyle();

    // 3. UI Toggles
    const helpMsg = document.getElementById('default-help');
    if (helpMsg) helpMsg.style.display = 'none';
    
    // Show Adjustment Controls
    const controls = document.getElementById('tryon-controls');
    controls.style.setProperty('display', 'flex', 'important');
}

// Function to handle interactive fitting
function adjustOutfit(action) {
    if (action === 'up') currentTop -= 2;
    if (action === 'down') currentTop += 2;
    if (action === 'zoom-in') currentScale += 0.05;
    if (action === 'zoom-out') currentScale -= 0.05;
    
    updateOverlayStyle();
}

// Function to apply styles to the overlay
function updateOverlayStyle() {
    const overlay = document.getElementById('outfit-overlay');
    overlay.style.top = currentTop + '%';
    overlay.style.transform = `translateX(-50%) scale(${currentScale})`;
}

// Function to clear the fitting room back to default
function resetTryOn() {
    document.getElementById('outfit-overlay').style.display = 'none';
    document.getElementById('selected-info').style.display = 'none';
    document.getElementById('tryon-controls').style.setProperty('display', 'none', 'important');
    
    const helpMsg = document.getElementById('default-help');
    if (helpMsg) helpMsg.style.display = 'block';
}
</script>

<?php include 'footer.php'; ?>