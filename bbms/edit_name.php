<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: public_login.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user'];
$message = "";

// Fetch current name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['name']);
    if (!empty($new_name)) {
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $new_name, $user_id);
        if ($stmt->execute()) {
            $_SESSION['name'] = $new_name;
            $message = "✅ Name updated successfully.";
            $user['name'] = $new_name;
        } else {
            $message = "⚠️ Failed to update name. Please try again.";
        }
    } else {
        $message = "⚠️ Name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Name - Account Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../includes/header.php'; ?>
<?php include '../includes/public_sidebar.php'; ?>

<div class="container mt-5 mb-5">
    <h3 class="text-center text-danger mb-4">Change Your Name</h3>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto" style="max-width: 500px;">
        <div class="mb-3">
            <label class="form-label">New Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter your new name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <button type="submit" class="btn btn-danger w-100">Update Name</button>
    </form>

    <div class="text-center mt-4">
        <a href="public_settings.php" class="btn btn-outline-secondary">← Back to Settings</a>
    </div>
</div>

</body>
</html>
