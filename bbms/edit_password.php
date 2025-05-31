<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: public_login.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user'];
$message = "";

// Fetch current password (not needed for display, just to check user in the database)
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the current password is correct
    if (!password_verify($current_password, $user['password'])) {
        $message = "⚠️ Current password is incorrect. Please try again.";
    } elseif ($new_password !== $confirm_password) {
        // Check if the new password and confirm password match
        $message = "⚠️ New passwords do not match. Please ensure both passwords are the same.";
    } elseif (strlen($new_password) < 8) {
        // Check password length (you can adjust the password strength requirements as needed)
        $message = "⚠️ Your password must be at least 8 characters long.";
    } else {
        // Hash the new password and update it in the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $message = "✅ Your password has been updated successfully.";
        } else {
            $message = "⚠️ Failed to update password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Password - Account Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/header.php'; ?>
<?php include '../includes/public_sidebar.php'; ?>

<div class="container mt-5 mb-5">
    <h3 class="text-center text-danger mb-4">Change Your Password</h3>

    <div class="text-center text-muted mb-4">
        Update your password below. Make sure to choose a strong and secure password. 
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
    <?php endif; ?>

    <!-- Password Update Form -->
    <form method="POST" class="mx-auto" style="max-width: 600px;">
        <input type="hidden" name="action" value="update_password">

        <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" placeholder="Enter your current password" required>
        </div>

        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" placeholder="Create a new password" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm your new password" required>
        </div>

        <button type="submit" class="btn btn-danger w-100 mt-3">Update Password</button>
    </form>

    <div class="text-center mt-4">
        <a href="public_settings.php" class="btn btn-outline-secondary">← Back to Settings</a>
    </div>
</div>

</body>
</html>
