<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: public_login.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user'];
$message = "";

// Fetch current email
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle email update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_email') {
    $new_email = trim($_POST['email']);

    // Validate email format
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "⚠️ Invalid email format. Please enter a valid email address.";
    } else {
        // Check if the new email is already taken
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $new_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "⚠️ This email is already in use. Please choose another email address.";
        } else {
            // Update email in the database
            $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->bind_param("si", $new_email, $user_id);
            if ($stmt->execute()) {
                $_SESSION['email'] = $new_email; // Update session email
                $message = "✅ Your email has been updated successfully.";
            } else {
                $message = "⚠️ Failed to update email. Please try again.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Email - Account Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/header.php'; ?>
<?php include '../includes/public_sidebar.php'; ?>

<div class="container mt-5 mb-5">
    <h3 class="text-center text-danger mb-4">Edit Your Email Address</h3>

    <div class="text-center text-muted mb-4">
        Update your email below. Make sure it is a valid email address.
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
    <?php endif; ?>

    <!-- Email Update Form -->
    <form method="POST" class="mx-auto" style="max-width: 600px;">
        <input type="hidden" name="action" value="update_email">

        <div class="mb-3">
            <label class="form-label">Current Email</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">New Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your new email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
        </div>

        <button type="submit" class="btn btn-danger w-100 mt-3">Update Email</button>
    </form>

    <div class="text-center mt-4">
        <a href="public_settings.php" class="btn btn-outline-secondary">← Back to Settings</a>
    </div>
</div>

</body>
</html>
