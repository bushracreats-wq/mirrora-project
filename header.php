<?php 
// Start session if not already started (needed for cart count)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cats = ['Men', 'Women', 'Kids', 'Jewelry', 'Shoes']; 
$selected_region = isset($_GET['region']) ? htmlspecialchars($_GET['region']) : 'global';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIRRÓRA - Virtual Fitting Room</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- Top Alert Bar -->
<div class="maroon-alert py-2 overflow-hidden shadow-sm">
    <marquee behavior="scroll" direction="left" scrollamount="5">
        ✨ <b>MIRRORA20</b> - Use this code for 20% OFF your first Virtual Try-On! &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp; 🚚 Free Delivery on Orders Over Rs. 3000!
    </marquee>
</div>

<!-- Centered Brand & Slogan Header -->
<div class="container text-center py-3 px-3">
    <a href="index.php" class="text-decoration-none">
        <h1 class="brand-title m-0">MIRRÓRA</h1>
    </a>
    <p class="brand-slogan mt-1 mb-0">fit your style, Ab Aap Ki Screen Par</p>
</div>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg custom-navbar sticky-top shadow-sm py-2 py-lg-3">
    <div class="container px-3">

        <button class="navbar-toggler border-0 shadow-none" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between align-items-center" id="navContent">

            <div class="d-none d-lg-block"></div>

            <ul class="navbar-nav mx-auto align-items-lg-center mb-2 mb-lg-0 text-center text-lg-start">

                <li class="nav-item px-2 py-1 py-lg-0">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <?php foreach($cats as $cat):
                    $link = strtolower($cat).'.php';
                ?>

                <li class="nav-item px-2 py-1 py-lg-0">
                    <a class="nav-link" href="<?php echo $link; ?>">
                        <?php echo $cat; ?>
                    </a>
                </li>

                <?php endforeach; ?>

                <li class="nav-item px-2 py-1 py-lg-0">
                    <a class="nav-link" href="sale.php">Sale</a>
                </li>

                <li class="nav-item px-2 py-1 py-lg-0">
                    <a class="nav-link" href="tryon.php">Try-On</a>
                </li>

                <li class="nav-item px-2 py-1 py-lg-0">
                    <a class="nav-link" href="about.php">About</a>
                </li>
<li class="nav-item">
    <a class="nav-link" href="add_review.php">REVIEWS</a>
</li>
                <li class="nav-item px-2 py-1 py-lg-0">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>

            </ul>

            <div class="d-flex align-items-center justify-content-center gap-3 mt-3 mt-lg-0 pb-2 pb-lg-0">

                <!-- Wishlist Icon Navbar -->
                <a href="wishlist.php" class="text-dark position-relative text-decoration-none" title="Wishlist">
                    <i class="far fa-heart fa-lg"></i>
                </a>

                <!-- Cart Icon Navbar -->
                <a href="cart.php" class="text-dark position-relative text-decoration-none">
                    <i class="fas fa-shopping-bag fa-lg"></i>

                    <?php
                    $cart_count=0;

                    if(isset($_SESSION['cart']) && !empty($_SESSION['cart']))
                    {
                        foreach($_SESSION['cart'] as $item)
                        {
                            $cart_count += $item['quantity'];
                        }
                    }

                    if($cart_count>0):
                    ?>

                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                        <?php echo $cart_count; ?>
                    </span>

                    <?php endif; ?>
                </a>

                <div class="social-nav-icons d-flex align-items-center gap-2 ms-2">
                    <a href="https://instagram.com" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://facebook.com" target="_blank">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                </div>

            </div>

        </div>

    </div>
</nav>