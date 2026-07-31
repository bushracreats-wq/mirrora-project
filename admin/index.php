<?php
session_start();
// Root folder ki config file ko include kar rahe hain database connection ke liye
require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT * FROM admins WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);

    // Plain text password match logic
    if ($admin && $pass === $admin['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Mirrora</title>
    <!-- Google Fonts: Cinzel & Plus Jakarta Sans for Professional Look -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f9;
            height: 100vh;
        }
        .admin-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            overflow: hidden;
        }
        .admin-title {
            font-family: 'Cinzel', serif;
            color: #800020;
            letter-spacing: 0.5px;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #800020;
            box-shadow: 0 0 0 0.2rem rgba(128, 0, 32, 0.15);
        }
        .btn-mirrora {
            background-color: #800020;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #ffffff;
            transition: background-color 0.3s ease;
        }
        .btn-mirrora:hover {
            background-color: #600018;
            color: #ffffff;
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #6c757d;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
        .input-group:focus-within .input-group-text {
            border-color: #800020;
            color: #800020;
        }
    </style>
</head>
<body class="d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card admin-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="admin-title fw-bold mb-1">MIRRORA</h2>
                        <p class="text-muted small text-uppercase tracking-wider">Admin Portal</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small rounded-3 text-center mb-4">
                            <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control" autocomplete="off" required placeholder="Enter username">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" autocomplete="new-password" required placeholder="Enter password">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-mirrora w-100 text-uppercase shadow-sm">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                    </form>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">&copy; Mirrora E-Commerce Suite. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>