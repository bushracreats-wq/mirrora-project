<?php
session_start();
include 'config.php'; // Database connection
require_once 'ai_service.php'; // AI Virtual Try-On Service

// 1. Handling Products Bulk Upload (Preserved)
if (isset($_POST['upload_all'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $category = mysqli_real_escape_string($conn, $_POST['category']); 

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

// 2. Handling Virtual Try-On User Image & Processing AI Try-On
if (isset($_POST['submit_tryon'])) {
    $aiService = new AiTryOnService();
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $product_image = isset($_POST['product_image']) ? trim($_POST['product_image']) : '';

    $aiService->logDebug("Received Try-On Request via POST", [
        'post_product_id' => $product_id,
        'post_product_image' => $product_image,
        'files' => isset($_FILES['user_image']) ? $_FILES['user_image']['name'] : 'none'
    ]);

    // Query exact product details from database
    $category = 'tops';
    $product_name = '';
    if ($product_id > 0) {
        $p_query = "SELECT * FROM products WHERE id = $product_id LIMIT 1";
        $p_res = mysqli_query($conn, $p_query);
        if ($p_res && mysqli_num_rows($p_res) > 0) {
            $p_row = mysqli_fetch_assoc($p_res);
            if (empty($product_image)) {
                $product_image = $p_row['images'];
            }
            $category = isset($p_row['category']) ? $p_row['category'] : 'tops';
            $product_name = isset($p_row['name']) ? $p_row['name'] : '';
            $aiService->logDebug("Database fetched product row", $p_row);
        } else {
            $aiService->logDebug("Warning: Product ID $product_id not found in DB");
        }
    }

    // Ensure uploads directory exists
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Automatic cleanup of old temporary upload files (> 24 hours old)
    clean_old_temp_files($target_dir);

    // Validate User Uploaded Image
    if (!isset($_FILES['user_image']) || $_FILES['user_image']['error'] !== UPLOAD_ERR_OK) {
        $err_code = isset($_FILES['user_image']['error']) ? $_FILES['user_image']['error'] : UPLOAD_ERR_NO_FILE;
        $errMsg = get_upload_error_message($err_code);
        $aiService->logDebug("Upload error: " . $errMsg);
        $_SESSION['tryon_error'] = $errMsg;
        header("Location: tryon.php?id=" . $product_id);
        exit();
    }

    $file_tmp = $_FILES['user_image']['tmp_name'];
    $file_size = $_FILES['user_image']['size'];
    $raw_filename = $_FILES['user_image']['name'];

    // 1. File Size Validation (Max 10 MB)
    $max_size = 10 * 1024 * 1024;
    if ($file_size > $max_size) {
        $errMsg = "The uploaded file is too large (" . round($file_size / 1024 / 1024, 2) . " MB). Maximum allowed size is 10 MB.";
        $aiService->logDebug("Validation Error: " . $errMsg);
        $_SESSION['tryon_error'] = $errMsg;
        header("Location: tryon.php?id=" . $product_id);
        exit();
    }

    // 2. Extension Validation
    $ext = strtolower(pathinfo($raw_filename, PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed_extensions)) {
        $errMsg = "Unsupported file extension (." . htmlspecialchars($ext) . "). Please upload a JPG, JPEG, PNG, or WEBP image.";
        $aiService->logDebug("Validation Error: " . $errMsg);
        $_SESSION['tryon_error'] = $errMsg;
        header("Location: tryon.php?id=" . $product_id);
        exit();
    }

    // 3. MIME Type / Image Integrity Validation
    $image_info = @getimagesize($file_tmp);
    if ($image_info === false) {
        $errMsg = "Invalid or corrupted image file. Please select a valid photo.";
        $aiService->logDebug("Validation Error: " . $errMsg);
        $_SESSION['tryon_error'] = $errMsg;
        header("Location: tryon.php?id=" . $product_id);
        exit();
    }

    $mime_type = $image_info['mime'];
    $allowed_mimes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'];
    if (!in_array($mime_type, $allowed_mimes)) {
        $errMsg = "Invalid image MIME type (" . htmlspecialchars($mime_type) . ").";
        $aiService->logDebug("Validation Error: " . $errMsg);
        $_SESSION['tryon_error'] = $errMsg;
        header("Location: tryon.php?id=" . $product_id);
        exit();
    }

    // 4. Generate Safe Non-Guessable Filename
    $safe_filename = 'user_tryon_' . uniqid('', true) . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target_file_path = $target_dir . $safe_filename;

    if (!move_uploaded_file($file_tmp, $target_file_path)) {
        $errMsg = "Failed to save uploaded photo to server storage. Please try again.";
        $aiService->logDebug("Storage Error: " . $errMsg);
        $_SESSION['tryon_error'] = $errMsg;
        header("Location: tryon.php?id=" . $product_id);
        exit();
    }

    // Locate product image file on server
    $product_file_path = '';
    if (file_exists('assets/images/' . $product_image)) {
        $product_file_path = 'assets/images/' . $product_image;
    } elseif (file_exists('uploads/' . $product_image)) {
        $product_file_path = 'uploads/' . $product_image;
    } elseif (file_exists($product_image)) {
        $product_file_path = $product_image;
    }

    if (empty($product_file_path) || !file_exists($product_file_path)) {
        $errMsg = "Selected product clothing image ($product_image) was not found on server.";
        $aiService->logDebug("Product Image Error: " . $errMsg);
        $_SESSION['tryon_error'] = $errMsg;
        header("Location: tryon.php?id=" . $product_id);
        exit();
    }

    $aiService->logDebug("Validated input images", [
        'user_file_path' => $target_file_path,
        'user_file_size' => filesize($target_file_path),
        'product_file_path' => $product_file_path,
        'product_file_size' => filesize($product_file_path),
        'category' => $category
    ]);

    // Call AI Virtual Try-On Service
    $ai_result = $aiService->generateTryOn($target_file_path, $product_file_path, $category);

    // Save details in session for result display
    $_SESSION['tryon_user_img'] = $target_file_path;
    $_SESSION['tryon_prod_img'] = $product_image;
    $_SESSION['tryon_prod_id']  = $product_id;
    $_SESSION['tryon_prod_name']= $product_name;
    $_SESSION['tryon_result']   = $ai_result;

    header("Location: ai_result.php");
    exit();
}

/**
 * Helper to translate PHP upload error code to user friendly message
 */
function get_upload_error_message($code) {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return "Uploaded image exceeds maximum allowed file size limit.";
        case UPLOAD_ERR_PARTIAL:
            return "The image was only partially uploaded. Please try again.";
        case UPLOAD_ERR_NO_FILE:
            return "No image was selected. Please choose a photo to upload.";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Missing temporary folder on server.";
        case UPLOAD_ERR_CANT_WRITE:
            return "Failed to write uploaded file to disk.";
        default:
            return "An unknown error occurred during image upload.";
    }
}

/**
 * Helper to clean up old temp tryon images
 */
function clean_old_temp_files($dir) {
    if (!is_dir($dir)) return;
    $files = @glob($dir . '{user_tryon_*,ai_tryon_*}', GLOB_BRACE);
    if (!empty($files)) {
        $now = time();
        foreach ($files as $f) {
            if (is_file($f) && ($now - filemtime($f)) > 86400) { // 24 hours
                @unlink($f);
            }
        }
    }
}
?>