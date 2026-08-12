<?php
include 'config.php';
session_start();

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Admin table ya hardcoded credentials check (Aap apne admin table ke mutabiq query adjust kar sakti hain)
    $query = "SELECT * FROM admins WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        // Password verify (Agar password plain text hai toh direct comparison bhi kar sakti hain: $password == $admin['password'])
        if (password_verify($password, $admin['password']) || $password == $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin.php");
            exit();
        } else {
            $error = "Ghalat password darj kiya gaya hai!";
        }
    } else {
        $error = "Aisa koi admin username mojood nahi hai!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Mirrora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-custom { background-color: #800020; color: #fff; }
        .btn-custom:hover { background-color: #600018; color: #fff; }
    </style>
</head>
<body>

<div class="card login-card p-4 bg-white">
    <div class="text-center mb-4">
        <h3 class="fw-bold" style="color: #800020;">MIRRORA</h3>
        <p class="text-muted small">Admin Panel Login</p>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">Username</label>
            <input type="text" name="username" class="form-control form-control-sm" required>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">Password</label>
            <input type="password" name="password" class="form-control form-control-sm" required>
        </div>
        <button type="submit" name="login" class="btn btn-custom btn-sm w-100 py-2 fw-semibold">Login to Dashboard</button>
    </form>
</div>

</body>
</html>