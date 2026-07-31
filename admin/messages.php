<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once '../config.php';

// Delete Message Logic
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM messages WHERE id = $id");
    header("Location: messages.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM messages ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Messages - Mirrora Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 py-3 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php">MIRRORA ADMIN</a>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold" style="color: #800020;">Customer Contact Messages</h3>
            <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Sender Name</th>
                            <th>Email Address</th>
                            <th>Message Content</th>
                            <th>Date / Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $row['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><a href="mailto:<?php echo $row['email']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($row['email']); ?></a></td>
                                <td><p class="mb-0 text-muted" style="max-width: 350px;"><?php echo htmlspecialchars($row['message']); ?></p></td>
                                <td><small class="text-muted"><?php echo isset($row['created_at']) ? $row['created_at'] : 'N/A'; ?></small></td>
                                <td>
                                    <a href="messages.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this message?');"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No messages received yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>