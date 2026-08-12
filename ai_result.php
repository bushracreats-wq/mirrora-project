<?php
session_start();
include 'header.php';
include 'config.php';

$user_img = isset($_SESSION['tryon_user_img']) ? $_SESSION['tryon_user_img'] : '';
$prod_img = isset($_SESSION['tryon_prod_img']) ? $_SESSION['tryon_prod_img'] : '';
$prod_id  = isset($_SESSION['tryon_prod_id']) ? $_SESSION['tryon_prod_id'] : 0;
$prod_name= isset($_SESSION['tryon_prod_name']) ? $_SESSION['tryon_prod_name'] : '';
$ai_result= isset($_SESSION['tryon_result']) ? $_SESSION['tryon_result'] : null;

// Determine status & result image
$has_result = !empty($ai_result) && isset($ai_result['success']) && $ai_result['success'] === true;
$result_img_path = $has_result && !empty($ai_result['result_image']) ? $ai_result['result_image'] : '';
$error_msg = (!empty($ai_result) && isset($ai_result['error'])) ? $ai_result['error'] : '';
?>

<div class="container my-4 my-md-5 px-3">
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-2 fs-3 fs-md-2" style="font-family: 'Cinzel', serif; color: var(--primary-maroon);">
            Your Virtual Try-On Result
        </h2>
        <p class="text-muted small">AI generated realistic clothing fitting</p>
    </div>

    <!-- Error Handling Panel (Only shown if generation fails) -->
    <?php if (!$has_result): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm p-4 text-center rounded-3">
                    <div class="mb-3 text-danger">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Virtual Try-On could not be generated. Please try again.</h4>
                    <p class="text-muted small mb-4 max-w-600 mx-auto">
                        <?php 
                        if (!empty($error_msg)) {
                            echo htmlspecialchars($error_msg);
                        } else {
                            echo "No AI Virtual Try-On result available. Please select a clothing product and upload your photo.";
                        }
                        ?>
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                        <a href="tryon.php<?php echo ($prod_id > 0) ? '?id='.$prod_id : ''; ?>" class="btn btn-dark rounded-pill px-4 py-2 fw-bold" style="background-color: var(--primary-maroon); border: none;">
                            <i class="fas fa-redo me-1"></i> Try Again
                        </a>
                        <a href="tryon.php" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold">
                            <i class="fas fa-tshirt me-1"></i> Choose Another Product
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold">
                            <i class="fas fa-home me-1"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- REAL AI GENERATED VIRTUAL TRY-ON RESULT SHOWCASE -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg p-3 p-md-4 mb-4" style="border-radius: 16px;">
                    
                    <div class="text-center p-2 p-md-3">
                        <div class="d-inline-block mb-3">
                            <span class="badge px-4 py-2 rounded-pill bg-success fw-bold text-uppercase shadow-sm" style="letter-spacing: 1.5px; font-size: 0.82rem;">
                                <i class="fas fa-sparkles me-1"></i> AI Virtual Fitting Result
                            </span>
                        </div>

                        <!-- BRAND NEW GENERATED IMAGE OF PERSON WEARING SELECTED CLOTHING -->
                        <div class="d-block position-relative shadow rounded-4 overflow-hidden mx-auto my-2" style="max-width: 520px; background: #f8f9fa;">
                            <img src="<?php echo htmlspecialchars($result_img_path); ?>" class="img-fluid rounded-4 w-100" alt="AI Virtual Try-On Result" style="max-height: 580px; object-fit: contain;">
                        </div>

                        <?php if (!empty($prod_name)): ?>
                            <h5 class="fw-bold text-dark mt-3 mb-1" style="font-family: 'Cinzel', serif;">
                                Outfit: <?php echo htmlspecialchars($prod_name); ?>
                            </h5>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="mt-4 pt-2 d-flex flex-wrap justify-content-center gap-2">
                            <a href="<?php echo htmlspecialchars($result_img_path); ?>" download="mirrora_ai_tryon_result.png" class="btn btn-outline-dark rounded-pill px-4 py-2.5 fw-bold">
                                <i class="fas fa-download me-1"></i> Download Try-On Image
                            </a>
                            <?php if ($prod_id > 0): ?>
                                <a href="cart.php?action=add&id=<?php echo $prod_id; ?>" class="btn btn-dark rounded-pill px-4 py-2.5 fw-bold" style="background-color: var(--primary-maroon); border: none;">
                                    <i class="fas fa-shopping-bag me-1"></i> Buy This Outfit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Navigation Action Buttons -->
                    <div class="mt-4 pt-3 text-center border-top d-flex flex-wrap justify-content-center gap-2">
                        <a href="tryon.php<?php echo ($prod_id > 0) ? '?id='.$prod_id : ''; ?>" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold text-uppercase" style="font-size: 0.8rem;">
                            <i class="fas fa-camera me-1"></i> Upload Another Photo
                        </a>

                        <a href="tryon.php" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold text-uppercase" style="font-size: 0.8rem;">
                            <i class="fas fa-tshirt me-1"></i> Choose Another Product
                        </a>

                        <a href="index.php" class="btn btn-dark rounded-pill px-4 py-2 fw-bold text-uppercase" style="background-color: var(--primary-maroon); border: none; font-size: 0.8rem;">
                            <i class="fas fa-home me-1"></i> Back to Catalog
                        </a>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>