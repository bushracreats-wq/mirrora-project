<?php
session_start();
include 'header.php';
include 'config.php';
?>

<div class="container my-4 my-md-5 text-center px-3">
    <h2 class="fw-bold mb-4 fs-3 fs-md-2" style="font-family: 'Cinzel', serif; color: var(--primary-maroon);">AI Try-On Result</h2>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-3 p-md-4">
                <div class="row g-3">
                    <!-- User Uploaded Image -->
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h6 class="fw-bold text-muted fs-6">Your Photo</h6>
                        <?php if (isset($_SESSION['tryon_user_img'])): ?>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="<?php echo $_SESSION['tryon_user_img']; ?>" class="img-fluid rounded shadow-sm w-100" style="max-height: 350px; object-fit: cover;" alt="User Image">
                            </div>
                        <?php else: ?>
                            <p class="text-danger small">No image found.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Selected Product Image -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted fs-6">Selected Outfit</h6>
                        <?php if (isset($_SESSION['tryon_prod_img'])): ?>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="assets/images/<?php echo $_SESSION['tryon_prod_img']; ?>" class="img-fluid rounded shadow-sm w-100" style="max-height: 350px; object-fit: cover;" alt="Product Image">
                            </div>
                        <?php else: ?>
                            <p class="text-danger small">No product found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <h4 class="text-success fw-bold mb-2 fs-5"><i class="fas fa-check-circle"></i> Try-On Successful!</h4>
                    <p class="text-muted small mb-3">Here is how the outfit looks on you.</p>
                    <a href="index.php" class="btn btn-dark rounded-pill px-4 py-2 mt-1 fw-bold text-uppercase" style="background-color: var(--primary-maroon); border: none; font-size: 0.85rem;">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>