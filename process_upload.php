<?php
session_start();
include 'config.php'; // Database connection

// 1. Handling Products Bulk Upload
if (isset($_POST['upload_all'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category']; 

    $targetDir = "uploads/"; 
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $uploaded_count = 0;

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $val) {
            if ($_FILES['images']['error'][$key] == 0) {
                $fileName = time() . '_' . basename($_FILES['images']['name'][$key]);
                $targetFilePath = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFilePath)) {
                    $sql = "INSERT INTO products (name, price, category, image) VALUES ('$name', '$price', '$category', '$fileName')";
                    $run_query = mysqli_query($conn, $sql);

                    if ($run_query) {
                        $uploaded_count++;
                    }
                }
            }
        }
        
        if ($uploaded_count > 0) {
            header("Location: jewelry.php?status=success");
            exit();
        } else {
            echo "Error: Failed to upload images.";
        }
    } else {
        echo "Baraye meharbani kam az kam aik image select karein!";
    }
}

// 2. Handling Virtual Try-On User Image & Redirecting to AI Result Page
if (isset($_POST['submit_tryon'])) {
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
    $product_image = isset($_POST['product_image']) ? $_POST['product_image'] : '';

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
        $image_name = time() . '_' . $_FILES['user_image']['name'];
        $target_file = $target_dir . basename($image_name);

        if (move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file)) {
            // Session mein values save karein
            $_SESSION['tryon_user_img'] = $target_file;
            $_SESSION['tryon_prod_img'] = $product_image;

            // Ab yeh seedha AI result page par jaye ga!
            header("Location: ai_result.php");
            exit();
        } else {
            echo "Error: Failed to move uploaded user image.";
        }
    } else {
        echo "Baraye meharbani apni tasveer (user image) select karein!";
    }
}
?>