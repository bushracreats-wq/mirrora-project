<?php 
include 'header.php'; 
include 'config.php'; 

// Agar wishlist mein add karne ki request aaye
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $product_id = intval($_GET['id']);
    
    $check_query = "SELECT * FROM wishlist WHERE product_id = $product_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        $insert_query = "INSERT INTO wishlist (product_id) VALUES ($product_id)";
        mysqli_query($conn, $insert_query);
    }
    echo "<script>window.location.href='wishlist.php';</script>";
}

// Item remove karne ke liye
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $wishlist_id = intval($_GET['id']);
    mysqli_query($conn, "DELETE FROM wishlist WHERE id = $wishlist_id");
    echo "<script>window.location.href='wishlist.php';</script>";
}
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="fw-bold m-0" style="font-family: 'Cinzel', serif; color: #333;">My Wishlist</h2>
        
        <!-- Continue Shopping / Back Button -->
        <a href="index.php" class="btn btn-outline-dark btn-sm rounded-0 px-3 py-2 text-uppercase fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">
            <i class="fas fa-arrow-left me-1"></i> Continue Shopping
        </a>
    </div>
    
    <div class="row">
        <?php
        $query = "SELECT wishlist.id as wishlist_id, products.* FROM wishlist JOIN products ON wishlist.product_id = products.id";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm rounded-0">
                        <img src="assets/images/<?php echo $row['images']; ?>" class="card-img-top rounded-0" style="height: 250px; object-fit: contain; background: #f8f9fa;" alt="...">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold" style="font-size: 1rem;"><?php echo $row['name']; ?></h5>
                            <p class="text-danger fw-bold mb-3">Rs. <?php echo $row['price']; ?></p>
                            
                            <div class="d-flex justify-content-center gap-2">
                                <a href="product-details.php?id=<?php echo $row['id']; ?>" class="btn btn-dark btn-sm rounded-0 px-3">View</a>
                                <a href="wishlist.php?action=remove&id=<?php echo $row['wishlist_id']; ?>" class="btn btn-outline-danger btn-sm rounded-0 px-2"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
        <?php 
            }
        } else {
            echo "<div class='col-12 text-center py-5'><p class='text-muted'>Your wishlist is empty.</p><a href='index.php' class='btn btn-dark rounded-0 px-4 text-uppercase fw-bold' style='font-size: 0.8rem; letter-spacing: 1px;'>Explore Products</a></div>";
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>