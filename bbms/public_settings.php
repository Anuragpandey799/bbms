<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: public_login.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user'];
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Settings - Blood Bank</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f9f9f9;
        }

        .settings-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            border: none;
            border-radius: 16px;
        }

        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #dc3545;
        }

        /* Dark Theme Support */
        body.dark-theme {
            background-color: #121212;
            color: #f1f1f1;
        }

        body.dark-theme .settings-card {
            background-color: #1f1f1f;
            color: #f1f1f1;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.05);
        }

        body.dark-theme .settings-card:hover {
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.1);
        }

        body.dark-theme .settings-card.bg-light {
            background-color: #2c2c2c !important;
        }

        body.dark-theme .text-muted {
            color: #ccc !important;
        }

        body.dark-theme .btn-outline-secondary {
            color: #f1f1f1;
            border-color: #f1f1f1;
        }

        body.dark-theme .btn-outline-secondary:hover {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'public_sidebar.php'; ?>

<div class="container mt-5 mb-5">
    <h3 class="text-center text-danger mb-4">Account Settings</h3>
    <p class="text-center fw-bold mb-4">Manage your account settings below. Click and proceed to edit your informations.</p>

    <div class="row g-4">
        <!-- Name -->
        <div class="col-md-6">
            <div onclick="window.location='edit_name.php'" class="card settings-card p-4 text-center">
                <div class="icon">📝</div>
                <h5>Update Name</h5>
                <p class="text-muted">Your current name: <strong><?= htmlspecialchars($user['name']) ?></strong></p>
            </div>
        </div>

        <!-- Email -->
        <div class="col-md-6">
            <div onclick="window.location='edit_email.php'" class="card settings-card p-4 text-center">
                <div class="icon">📧</div>
                <h5>Update Email</h5>
                <p class="text-muted">Your email: <strong><?= htmlspecialchars($user['email']) ?></strong></p>
            </div>
        </div>

        <!-- Password -->
        <div class="col-md-6">
            <div onclick="window.location='edit_password.php'" class="card settings-card p-4 text-center">
                <div class="icon">🔒</div>
                <h5>Reset Password</h5>
                <p class="text-muted">Keep your account secure with a strong password.</p>
            </div>
        </div>

        <!-- Profile Picture -->
        <div class="col-md-6">
            <div onclick="window.location='edit_profile_picture.php'" class="card settings-card p-4 text-center">
                <div class="icon">🖼️</div>
                <h5>Profile Picture</h5>
                <p class="text-muted">Upload or change your profile picture.</p>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="col-12">
            <div onclick="window.location='/bbms/delete_account.php'" class="card settings-card p-4 text-center bg-light border-danger">
                <div class="icon text-danger">⚠️</div>
                <h5 class="text-danger">Delete My Account</h5>
                <p class="text-muted">Permanently remove your account and data.</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="public_dashboard.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
    </div>
</div>

<script>
    // Apply theme if already set
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
