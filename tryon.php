

<?php 
session_start();
include 'header.php';
include 'config.php';

// URL se Product ID aur Image get karein
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product_image = isset($_GET['image']) ? htmlspecialchars($_GET['image']) : '';
$product_name = '';

// Agar URL mein image ya naam nahi hai lekin ID maujood hai, toh database se fetch karein
if ($product_id > 0) {
    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $prod = mysqli_fetch_assoc($result);
        if (empty($product_image)) {
            $product_image = $prod['images'];
        }
        $product_name = $prod['name'];
    }
}
?>

<div class="container my-5">
    <h2 class="text-center fw-bold mb-4" style="font-family: 'Cinzel', serif; color: var(--primary-maroon);">Virtual AI Fitting Room</h2>
    
    <div class="row g-4 justify-content-center">
        <!-- Selected Product Preview Section -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white text-center fw-bold">Selected Product</div>
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <?php if (!empty($product_image)): ?>
                        <img src="assets/images/<?php echo $product_image; ?>" alt="Product Image" class="img-fluid rounded mb-3" style="max-height: 280px; object-fit: contain;">
                        <?php if (!empty($product_name)): ?>
                            <h5 class="fw-bold text-dark mt-2"><?php echo $product_name; ?></h5>
                        <?php endif; ?>
                        <input type="hidden" name="selected_product_image" value="<?php echo $product_image; ?>">
                    <?php else: ?>
                        <p class="text-muted">No product selected yet.</p>
                        <a href="index.php" class="btn btn-outline-dark btn-sm mt-2">Choose Product from Home</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- User Image Upload Section -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white text-center fw-bold">Upload Your Photo</div>
                <div class="card-body">
                    <form action="process_upload.php" method="POST" enctype="multipart/form-data">
                        <!-- Hidden fields to pass product info -->
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="hidden" name="product_image" value="<?php echo $product_image; ?>">

                        <div class="mb-3">
                            <label for="user_image" class="form-label small fw-bold">Choose your full-body picture:</label>
                            <input class="form-control" type="file" id="user_image" name="user_image" accept="image/*" required>
                        </div>

                        <div class="alert alert-light border small text-muted">
                            <i class="fas fa-info-circle me-1"></i> Tip: Upload a clear front-facing picture for the best Virtual Try-On results.
                        </div>

                        <button type="submit" name="submit_tryon" class="btn btn-dark w-100 rounded-pill fw-bold py-2 mt-3" style="background-color: #660014; border: none;">
                            Generate AI Try-On <i class="fas fa-magic ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>