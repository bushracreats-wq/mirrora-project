<?php
session_start();
include 'header.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
?>

<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-5 bg-light">
                <!-- Success Icon -->
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>

                <h2 class="fw-bold mb-2" style="font-family: 'Cinzel', serif; color: var(--primary-maroon);">Thank You For Your Order!</h2>
                <p class="text-muted mb-4">Your order has been successfully placed and is being processed.</p>

                <?php if ($order_id > 0): ?>
                    <div class="alert bg-white border mb-4 py-3">
                        <span class="small text-muted d-block">Order Reference Number:</span>
                        <strong class="fs-5 text-dark">#<?php echo $order_id; ?></strong>
                    </div>
                <?php endif; ?>

                <p class="small text-muted mb-4">We will send you an email confirmation with order details and tracking info very soon.</p>

                <div class="d-flex justify-content-center gap-3">
                    <a href="index.php" class="btn btn-dark rounded-pill px-4 py-2 fw-bold" style="background-color: var(--primary-maroon); border: none;">
                        <i class="fas fa-shopping-bag me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>