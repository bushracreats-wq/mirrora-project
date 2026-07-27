

<?php if (isset($_GET['success']) && $_GET['success'] == 'bulk_uploaded'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> Aapke saare products kamyabi ke sath upload ho gaye hain. Aap mazeed products bhi upload kar sakti hain!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="container mt-5">


    <h2>Upload New Products (Bulk Upload)</h2>
    <form action="process_upload.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Common Product Name/Prefix (e.g. Luxury Suit)" class="form-control mb-2" required>
        <input type="number" name="price" placeholder="Price" class="form-control mb-2" required>
        <select name="category" class="form-control mb-2">
            <option value="Men">Men</option>
            <option value="Women">Women</option>
            <option value="Kids">Kids</option>
            <option value="Jewelry">Jewelry</option>
            <option value="Shoes">Shoes</option>
        </select>
        
        <!-- Yahan 'multiple' attribute ki wajah se aap aik sath 24 ya jitni marzi images select kar sakti hain -->
        <input type="file" name="images[]" class="form-control mb-2" multiple required>
        
        <button type="submit" name="upload_all" class="btn btn-primary">Upload All Products</button>
    </form>
</div>