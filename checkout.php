<?php
session_start();
include 'config.php';

// Agar cart khali hai toh index par bhej dein
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

// Order place hone ki logic
if (isset($_POST['place_order'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // Grand Total aur Products string calculate karein
    $total_amount = 0;
    $products_details_array = [];
    
    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total_amount += $subtotal;
        
        // Product ka naam, quantity aur price aik string mein jama kar rahe hain
        $products_details_array[] = $item['name'] . " (Qty: " . $item['quantity'] . ", Price: " . $item['price'] . ")";
    }
    
    // Saare products ko aik text mein convert kar liya
    $order_products_str = mysqli_real_escape_string($conn, implode(" | ", $products_details_array));

    // Orders table mein data insert karein
    $insert_order = "INSERT INTO orders (customer_name, email, phone, address, city, payment_method, total_amount, order_products, order_date) 
                     VALUES ('$name', '$email', '$phone', '$address', '$city', '$payment_method', '$total_amount', '$order_products_str', NOW())";
    
    if (mysqli_query($conn, $insert_order)) {
        $order_id = mysqli_insert_id($conn);

        // Order successful hone ke baad cart ko clear kar dein
        unset($_SESSION['cart']);
        
        // <-- FIX: Ab yeh direct order_success.php par bhejega Order ID ke sath -->
        echo "<script>window.location.href='order_success.php?order_id=$order_id';</script>";
        exit;
    } else {
        $error = "Something went wrong. Please try again!";
    }
}

include 'header.php';
?>

<div class="container my-4 my-md-5 px-3">
    <h2 class="fw-bold mb-4 fs-3 fs-md-2" style="font-family: 'Cinzel', serif; color: var(--primary-maroon);">Checkout & Secure Payment</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger py-2"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="checkout.php" method="POST">
        <div class="row g-4">
            <!-- Billing Details -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-3 p-md-4 bg-white">
                    <h4 class="fw-bold mb-3 fs-5" style="color: var(--primary-maroon);">Billing Details</h4>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-sm py-2" required placeholder="Ayesha Khan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-sm py-2" required placeholder="example@email.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Phone Number</label>
                            <input type="text" name="phone" class="form-control form-control-sm py-2" required placeholder="0300-1234567">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Street Address</label>
                            <input type="text" name="address" class="form-control form-control-sm py-2" required placeholder="House # 123, Street # 4, Area">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">City</label>
                            <input type="text" name="city" class="form-control form-control-sm py-2" required placeholder="Karachi">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary & Professional Payment Methods -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm bg-light p-3 p-md-4">
                    <h4 class="fw-bold mb-3 fs-5" style="font-family: 'Cinzel', serif;">Your Order Summary</h4>
                    
                    <!-- Added Products List in Checkout -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm align-middle mb-0 bg-transparent">
                            <tbody>
                                <?php 
                                $grand_total = 0;
                                foreach ($_SESSION['cart'] as $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $grand_total += $subtotal;
                                ?>
                                <tr>
                                    <td style="width: 45px;">
                                        <img src="assets/images/<?php echo $item['image']; ?>" alt="" class="img-fluid rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;"><?php echo $item['name']; ?></h6>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                    </td>
                                    <td class="text-end fw-bold text-nowrap" style="font-size: 0.85rem;">Rs. <?php echo number_format($subtotal); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between border-top pt-2 mb-3 fs-6">
                        <span class="fw-bold">Total Amount</span>
                        <span class="fw-bold" style="color: var(--primary-maroon);">Rs. <?php echo number_format($grand_total); ?></span>
                    </div>

                    <!-- Professional Payment Gateway Options -->
                    <h5 class="fw-bold mb-3 mt-3 fs-6" style="color: var(--primary-maroon);">Select Payment Method</h5>
                    
                    <div class="payment-methods mb-3">
                        <div class="form-check mb-2 p-2.5 border rounded bg-white shadow-sm">
                            <input class="form-check-input ms-1" type="radio" name="payment_method" id="cod" value="COD" checked>
                            <label class="form-check-label fw-bold ms-2" for="cod" style="font-size: 0.85rem;">
                                <i class="fas fa-truck text-dark me-1"></i> Cash on Delivery (COD)
                            </label>
                            <div class="text-muted ms-4" style="font-size: 0.75rem;">Pay cash at your doorstep.</div>
                        </div>

                        <div class="form-check mb-2 p-2.5 border rounded bg-white shadow-sm">
                            <input class="form-check-input ms-1" type="radio" name="payment_method" id="bank" value="Bank Transfer">
                            <label class="form-check-label fw-bold ms-2" for="bank" style="font-size: 0.85rem;">
                                <i class="fas fa-university text-dark me-1"></i> Direct Bank Transfer
                            </label>
                            <div class="text-muted ms-4" style="font-size: 0.75rem;">Transfer directly to bank account.</div>
                        </div>

                        <div class="form-check mb-2 p-2.5 border rounded bg-white shadow-sm">
                            <input class="form-check-input ms-1" type="radio" name="payment_method" id="wallet" value="EasyPaisa / JazzCash">
                            <label class="form-check-label fw-bold ms-2" for="wallet" style="font-size: 0.85rem;">
                                <i class="fas fa-mobile-alt text-dark me-1"></i> EasyPaisa / JazzCash
                            </label>
                            <div class="text-muted ms-4" style="font-size: 0.75rem;">Pay via mobile wallet account.</div>
                        </div>

                        <div class="form-check p-2.5 border rounded bg-white shadow-sm">
                            <input class="form-check-input ms-1" type="radio" name="payment_method" id="card" value="Credit/Debit Card">
                            <label class="form-check-label fw-bold ms-2" for="card" style="font-size: 0.85rem;">
                                <i class="fas fa-credit-card text-dark me-1"></i> Credit / Debit Card
                            </label>
                            <div class="text-muted ms-4" style="font-size: 0.75rem;">Visa or Mastercard secure payment.</div>
                        </div>
                    </div>

                    <button type="submit" name="place_order" class="btn btn-dark w-100 rounded-pill py-2.5 fw-bold text-uppercase" style="background-color: var(--primary-maroon); border: none; font-size: 0.85rem;">
                        Place Order <i class="fas fa-check-circle ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>