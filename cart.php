<?php
session_start();
include 'config.php';

// Handle Cart Actions (Add, Update, Remove) BEFORE any HTML/header is outputted
if (isset($_GET['action'])) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    // 1. Add to Cart Logic
    if ($_GET['action'] == 'add' && $id > 0) {
        $query = "SELECT * FROM products WHERE id = $id";
        $res = mysqli_query($conn, $query);
        
        if ($res && mysqli_num_rows($res) > 0) {
            $product = mysqli_fetch_assoc($res);
            
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            // Get Color & Size from URL / GET parameters
            $selected_color = isset($_GET['color']) && !empty($_GET['color']) ? trim($_GET['color']) : 'N/A';
            $selected_size = isset($_GET['size']) && !empty($_GET['size']) ? trim($_GET['size']) : 'N/A';

            // Discount percent calculation
            $original_price = floatval($product['price']);
            $discount_percent = isset($product['discount_percent']) ? floatval($product['discount_percent']) : 0;
            
            $final_price = $original_price;
            
            if ($discount_percent > 0) {
                $final_price = $original_price - ($original_price * $discount_percent / 100);
            }
            
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity'] += 1;
                // Update selected color and size if re-added
                $_SESSION['cart'][$id]['color'] = $selected_color;
                $_SESSION['cart'][$id]['size']  = $selected_size;
            } else {
                $image_col = isset($product['images']) ? $product['images'] : '';
                $_SESSION['cart'][$id] = [
                    'name'            => $product['name'],
                    'price'           => $final_price,         // Calculated Discounted Price
                    'original_price'  => $original_price,      // Original Price for cut-off view
                    'discount_percent'=> $discount_percent,    // Percentage for reference
                    'image'           => $image_col,
                    'quantity'        => 1,
                    'color'           => $selected_color,      // Saved selected Color
                    'size'            => $selected_size        // Saved selected Size
                ];
            }
        }
        header("Location: cart.php");
        exit;
    }
    
    // 2. Remove from Cart Logic
    if ($_GET['action'] == 'remove' && $id > 0) {
        unset($_SESSION['cart'][$id]);
        header("Location: cart.php");
        exit;
    } 
    
    // 3. Update Quantity Logic
    elseif ($_GET['action'] == 'update' && $id > 0 && isset($_POST['quantity'])) {
        $qty = intval($_POST['quantity']);
        if ($qty > 0) {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        } else {
            unset($_SESSION['cart'][$id]);
        }
        header("Location: cart.php");
        exit;
    }
}

// Ab yahan header include hoga kyunki redirection ka kaam pehle ho chuka hai
include 'header.php';
?>

<div class="container my-4 my-md-5 px-3">
    <h2 class="fw-bold mb-4 fs-3 fs-md-2" style="font-family: 'Cinzel', serif; color: var(--primary-maroon);">Shopping Bag</h2>

    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <div class="row g-4">
            <!-- Cart Items Table -->
            <div class="col-lg-8">
                <div class="table-responsive shadow-sm rounded-3 border bg-white">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="py-3 ps-3">Product</th>
                                <th scope="col" class="py-3">Price</th>
                                <th scope="col" class="py-3" style="width: 110px;">Quantity</th>
                                <th scope="col" class="py-3">Total</th>
                                <th scope="col" class="py-3 pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_total = 0;
                            foreach ($_SESSION['cart'] as $id => $item): 
                                $total = $item['price'] * $item['quantity'];
                                $grand_total += $total;
                            ?>
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/images/<?php echo $item['image']; ?>" alt="" class="img-fluid rounded me-2 me-md-3 flex-shrink-0" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fw-bold fs-6 text-wrap" style="font-size: 0.9rem;"><?php echo $item['name']; ?></h6>
                                            
                                            <!-- Color & Size Display in Cart -->
                                            <?php if ((isset($item['color']) && $item['color'] !== 'N/A') || (isset($item['size']) && $item['size'] !== 'N/A')): ?>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                                    <?php echo isset($item['color']) && $item['color'] !== 'N/A' ? '<strong>Color:</strong> ' . htmlspecialchars($item['color']) : ''; ?>
                                                    <?php echo (isset($item['color']) && $item['color'] !== 'N/A' && isset($item['size']) && $item['size'] !== 'N/A') ? ' | ' : ''; ?>
                                                    <?php echo isset($item['size']) && $item['size'] !== 'N/A' ? '<strong>Size:</strong> ' . htmlspecialchars($item['size']) : ''; ?>
                                                </small>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Price Column with Cut-off Effect -->
                                <td class="text-nowrap" style="font-size: 0.9rem;">
                                    <?php if (isset($item['original_price']) && isset($item['discount_percent']) && $item['discount_percent'] > 0): ?>
                                        <span class="fw-bold text-danger">Rs. <?php echo number_format($item['price']); ?></span>
                                        <span class="badge bg-danger ms-1" style="font-size: 0.7rem;"><?php echo $item['discount_percent']; ?>% OFF</span>
                                        <br>
                                        <del class="text-muted small">Rs. <?php echo number_format($item['original_price']); ?></del>
                                    <?php else: ?>
                                        Rs. <?php echo number_format($item['price']); ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <form action="cart.php?action=update&id=<?php echo $id; ?>" method="POST" class="d-flex">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control form-control-sm text-center" style="width: 55px;" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="fw-bold text-nowrap" style="font-size: 0.9rem;">Rs. <?php echo number_format($total); ?></td>
                                <td class="pe-3">
                                    <a href="cart.php?action=remove&id=<?php echo $id; ?>" class="text-danger text-decoration-none small text-nowrap"><i class="fas fa-trash-alt"></i> <span class="d-none d-sm-inline">Remove</span></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="index.php" class="btn btn-outline-dark btn-sm rounded-0 mt-3"><i class="fas fa-arrow-left me-1"></i> Continue Shopping</a>
            </div>

            <!-- Cart Summary Box -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm bg-light p-3 p-md-4">
                    <h5 class="fw-bold mb-3 fs-5" style="font-family: 'Cinzel', serif;">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2 fs-6">
                        <span>Subtotal</span>
                        <span class="fw-bold">Rs. <?php echo number_format($grand_total); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3 fs-6">
                        <span>Shipping</span>
                        <span class="text-success fw-bold">Free</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5" style="color: var(--primary-maroon);">Rs. <?php echo number_format($grand_total); ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-dark w-100 rounded-pill py-2.5 fw-bold text-uppercase" style="background-color: var(--primary-maroon); border: none; font-size: 0.9rem;">
                        Proceed to Checkout <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
            <h4>Your shopping bag is empty.</h4>
            <p class="text-muted">Explore our collections and add products to your cart.</p>
            <a href="index.php" class="btn btn-dark mt-3 rounded-0 px-4" style="background-color: var(--primary-maroon); border: none;">Shop Now</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>