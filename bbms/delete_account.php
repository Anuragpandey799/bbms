<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: public_login.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user'];
$message = "";
$success = false;

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_account') {
    $password = $_POST['password'];

    // Fetch the user's details including the password
    $stmt = $conn->prepare("SELECT id, name, username, email, phone, blood_group, password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Copy the user's data to deleted_accounts table
        $stmt = $conn->prepare("INSERT INTO deleted_accounts (user_id, name, username, email, phone, blood_group) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "isssss",
            $user_id,
            $user['name'],
            $user['username'],
            $user['email'],
            $user['phone'],
            $user['blood_group']
        );
        $stmt->execute();

        // Delete the user from users table
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            session_destroy();
            $success = true;
        } else {
            $message = "⚠️ Failed to delete your account. Please try again.";
        }
    } else {
        $message = "❌ Incorrect password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Account - Account Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .success-message {
            display: none;
            text-align: center;
            margin-top: 50px;
            animation: fadeIn 1s forwards;
        }
        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-block;
            border: 4px solid #28a745;
            position: relative;
        }
        .checkmark::after {
            content: '';
            position: absolute;
            left: 22px;
            top: 10px;
            width: 20px;
            height: 40px;
            border-right: 4px solid #28a745;
            border-bottom: 4px solid #28a745;
            transform: rotate(45deg);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-light">

<?php include 'header.php'; ?>
<?php include 'public_sidebar.php'; ?>

<div class="container mt-5 mb-5">
    <h3 class="text-center text-danger mb-4">Delete Your Account</h3>

    <?php if ($success): ?>
        <div class="success-message">
            <div class="checkmark mb-3"></div>
            <h4 class="text-success">Your account has been successfully deleted.</h4>
            <p class="text-muted">You are being redirected to the Home page...</p>
        </div>

        <script>
            document.querySelector('.success-message').style.display = 'block';
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 5000);
        </script>
    <?php else: ?>
        <div class="text-center text-muted mb-4 bg-white">
            Are you sure you want to permanently delete your account? This action cannot be undone.
        </div>

        <?php if ($message): ?>
            <div class="alert alert-info text-center"><?= $message ?></div>
        <?php endif; ?>

        <!-- Account Deletion Form -->
        <form method="POST" onsubmit="return confirmDeletion();" class="mx-auto" style="max-width: 600px;">
            <input type="hidden" name="action" value="delete_account">

            <div class="mb-3">
                <label class="form-label fw-normal">Enter your password to confirm deletion.</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your current password" required>
            </div>

            <button type="submit" class="btn btn-outline-danger w-100 mt-3">Delete My Account</button>
        </form>

        <div class="text-center mt-4">
            <a href="public_settings.php" class="btn btn-outline-secondary">← Back to Settings</a>
        </div>
    <?php endif; ?>
</div>

<script>
    function confirmDeletion() {
        return confirm("⚠️ Are you sure you want to permanently delete your account? This action cannot be undone.");
    }
</script>

</body>
</html>
