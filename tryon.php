<?php 
session_start();
include 'header.php';
include 'config.php';

// URL se Product ID aur Image get karein
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product_image = isset($_GET['image']) ? htmlspecialchars($_GET['image']) : '';
$product_name = '';
$product_category = '';
$product_price = '';

// Fetch product list for dropdown if user wants to change or select product
$all_products_query = "SELECT id, name, images, category, price FROM products ORDER BY id DESC";
$all_products_result = mysqli_query($conn, $all_products_query);
$all_products = [];
if ($all_products_result && mysqli_num_rows($all_products_result) > 0) {
    while ($p = mysqli_fetch_assoc($all_products_result)) {
        $all_products[] = $p;
    }
}

// Agar ID maujood hai, database se specific product details fetch karein
if ($product_id > 0) {
    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $prod = mysqli_fetch_assoc($result);
        if (empty($product_image)) {
            $product_image = $prod['images'];
        }
        $product_name = $prod['name'];
        $product_category = isset($prod['category']) ? $prod['category'] : '';
        $product_price = isset($prod['price']) ? $prod['price'] : '';
    }
} elseif (!empty($all_products)) {
    // Default to first product if none specified
    $product_id = $all_products[0]['id'];
    $product_image = $all_products[0]['images'];
    $product_name = $all_products[0]['name'];
    $product_category = $all_products[0]['category'];
    $product_price = $all_products[0]['price'];
}

// Handle error messages from session if any
$error_message = isset($_SESSION['tryon_error']) ? $_SESSION['tryon_error'] : '';
unset($_SESSION['tryon_error']);
?>

<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-2" style="font-family: 'Cinzel', serif; color: var(--primary-maroon);">Virtual AI Fitting Room</h2>
        <p class="text-muted small">Experience AI Virtual Try-On: Upload your photo and see how this outfit looks on you realistically!</p>
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-md-10">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>Try-On Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4 justify-content-center">
        <!-- Selected Product Preview & Selection Section -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white text-center fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-tshirt me-2"></i> Selected Product</span>
                    <span class="badge bg-danger" style="background-color: var(--primary-maroon) !important;">AI Ready</span>
                </div>
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-between">
                    <!-- Product Selector Dropdown -->
                    <?php if (!empty($all_products)): ?>
                        <div class="w-100 mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase me-2" style="font-size: 0.75rem;">Change Selected Outfit:</label>
                            <select id="product_select" class="form-select form-select-sm border-secondary shadow-none" onchange="changeProduct(this)">
                                <?php foreach ($all_products as $item): ?>
                                    <option value="<?php echo $item['id']; ?>" 
                                            data-image="<?php echo htmlspecialchars($item['images']); ?>" 
                                            data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                            data-price="<?php echo htmlspecialchars($item['price']); ?>"
                                            data-category="<?php echo htmlspecialchars($item['category']); ?>"
                                            <?php echo ($item['id'] == $product_id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($item['name']); ?> (Rs. <?php echo htmlspecialchars($item['price']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="p-3 bg-light rounded w-100 d-flex flex-column align-items-center justify-content-center my-auto" style="min-height: 280px;">
                        <img id="product_preview_img" src="assets/images/<?php echo htmlspecialchars($product_image); ?>" alt="Product Image" class="img-fluid rounded shadow-sm mb-3" style="max-height: 260px; object-fit: contain;">
                        <h6 id="product_preview_name" class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($product_name); ?></h6>
                        <?php if (!empty($product_price)): ?>
                            <span id="product_preview_price" class="fw-bold text-danger small">Rs. <?php echo htmlspecialchars($product_price); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Image Upload Section -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white text-center fw-bold">
                    <i class="fas fa-camera me-2"></i> Upload Your Photo
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <form id="tryon_form" action="process_upload.php" method="POST" enctype="multipart/form-data" onsubmit="showProcessingState()">
                        <!-- Hidden fields to pass selected product info -->
                        <input type="hidden" id="form_product_id" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="hidden" id="form_product_image" name="product_image" value="<?php echo htmlspecialchars($product_image); ?>">

                        <div class="mb-3">
                            <label for="user_image" class="form-label small fw-bold">Choose your clear full-body photo:</label>
                            <input class="form-control" type="file" id="user_image" name="user_image" accept="image/jpeg,image/png,image/webp,image/jpg" required onchange="previewUserImage(event)">
                            <div class="form-text text-muted small" style="font-size: 0.75rem;">Supported formats: JPG, PNG, WEBP (Max 10MB)</div>
                        </div>

                        <!-- Live User Image Preview Box -->
                        <div id="user_preview_box" class="text-center my-3 p-2 border rounded bg-light d-none">
                            <p class="small fw-bold text-muted mb-2">Photo Preview:</p>
                            <img id="user_preview_img" src="#" alt="User Photo Preview" class="img-fluid rounded shadow-sm" style="max-height: 180px; object-fit: contain;">
                        </div>

                        <div class="alert alert-light border small text-muted my-3">
                            <i class="fas fa-lightbulb text-warning me-1"></i> <strong>Pro Tip:</strong> For best results, use a well-lit, front-facing photo standing straight.
                        </div>

                        <button type="submit" name="submit_tryon" id="submit_btn" class="btn btn-dark w-100 rounded-pill fw-bold py-2.5 mt-2" style="background-color: var(--primary-maroon); border: none; letter-spacing: 0.5px;">
                            Generate AI Try-On <i class="fas fa-magic ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= AI PROCESSING LOADING OVERLAY ================= -->
<div id="processing_overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(5px); z-index: 99999; justify-content: center; align-items: center; text-align: center; color: white;">
    <div class="p-4 rounded-4 shadow-lg text-center" style="max-width: 450px; background: rgba(33, 37, 41, 0.95); border: 1px solid rgba(255,255,255,0.15);">
        <div class="spinner-border text-light mb-3" style="width: 3.5rem; height: 3.5rem; border-width: 4px; color: var(--primary-maroon) !important;" role="status">
            <span class="visually-hidden">Processing...</span>
        </div>
        <h4 class="fw-bold mb-2" style="font-family: 'Cinzel', serif;">AI Virtual Fitting Room</h4>
        <p id="processing_status_text" class="text-light small mb-3">Uploading photo and preparing garment model...</p>
        
        <div class="progress mb-3" style="height: 6px; background-color: rgba(255,255,255,0.2);">
            <div id="processing_progress_bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 25%; background-color: #800020;"></div>
        </div>
        
        <p class="text-white-50 small mb-0" style="font-size: 0.78rem;">
            <i class="fas fa-sparkles me-1 text-warning"></i> AI is realistically styling the selected outfit on your photo. Please wait a moment...
        </p>
    </div>
</div>

<script>
function previewUserImage(event) {
    const file = event.target.files[0];
    const previewBox = document.getElementById('user_preview_box');
    const previewImg = document.getElementById('user_preview_img');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewBox.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        previewBox.classList.add('d-none');
    }
}

function changeProduct(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const prodId = selectedOption.value;
    const prodImg = selectedOption.getAttribute('data-image');
    const prodName = selectedOption.getAttribute('data-name');
    const prodPrice = selectedOption.getAttribute('data-price');

    document.getElementById('form_product_id').value = prodId;
    document.getElementById('form_product_image').value = prodImg;
    document.getElementById('product_preview_img').src = 'assets/images/' + prodImg;
    document.getElementById('product_preview_name').textContent = prodName;
    
    const priceElem = document.getElementById('product_preview_price');
    if (priceElem && prodPrice) {
        priceElem.textContent = 'Rs. ' + prodPrice;
    }
}

function showProcessingState() {
    const fileInput = document.getElementById('user_image');
    if (!fileInput.files || fileInput.files.length === 0) {
        return;
    }

    const overlay = document.getElementById('processing_overlay');
    const statusText = document.getElementById('processing_status_text');
    const progressBar = document.getElementById('processing_progress_bar');

    overlay.style.display = 'flex';

    let progress = 25;
    const interval = setInterval(function() {
        if (progress < 85) {
            progress += 15;
            progressBar.style.width = progress + '%';

            if (progress >= 40 && progress < 60) {
                statusText.textContent = "Analyzing body posture & outfit fit...";
            } else if (progress >= 60 && progress < 80) {
                statusText.textContent = "AI model rendering clothing try-on...";
            } else if (progress >= 80) {
                statusText.textContent = "Finalizing high-quality realistic output...";
            }
        }
    }, 1200);
}
</script>

<?php include 'footer.php'; ?>