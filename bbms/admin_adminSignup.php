<?php
session_start();
require_once 'db.php';

$error = '';
$adminCode = "ADMIN123";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $blood_group = trim($_POST['blood_group']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_plain = trim($_POST['password']);
    $code = trim($_POST['admin_code']);
    $password = password_hash($password_plain, PASSWORD_DEFAULT);

    if ($code !== $adminCode) {
        $error = "Invalid Admin Code.";
    } else {
        // Check if username or email already exists
        $check = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, blood_group, username, email, password, role) VALUES (?, ?, ?, ?, ?, 'admin')");
            $stmt->bind_param("sssss", $name, $blood_group, $username, $email, $password);
            if ($stmt->execute()) {
                $_SESSION['admin_id'] = $stmt->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['name'] = $name;
                $_SESSION['blood_group'] = $blood_group;
                $_SESSION['role'] = 'admin';
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Signup failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Signup - Blood Bank</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .signup-container {
            max-width: 500px;
            background: #ffffff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
        }
    </style>
    <script>
        function checkRole(role) {
            if (role === 'user') {
                window.location.href = 'public_signup.php';
            }
        }
        // Clear form after load
        window.onload = () => {
            if (!window.location.search.includes('error')) {
                document.querySelector('form').reset();
            }
        };
    </script>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="signup-container">
            <h2 class="mb-4 text-center text-dark">Create Admin Account</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="d-flex justify-content-center mb-3">
                    <div class="form-check me-4">
                        <input id="public" class="form-check-input" type="radio" name="role" value="user" onchange="checkRole('user')">
                        <label for="public" class="form-check-label">Public</label>
                    </div>
                    <div class="form-check">
                        <input id="admin" class="form-check-input" type="radio" name="role" value="admin" checked onchange="checkRole('admin')">
                        <label for="admin" class="form-check-label">Admin</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Full Name" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-select" required>
                        <option value="">Select Group</option>
                        <option>A+</option><option>A-</option>
                        <option>B+</option><option>B-</option>
                        <option>AB+</option><option>AB-</option>
                        <option>O+</option><option>O-</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Username123" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="sample@gmail.com" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="number" name="phone" class="form-control" placeholder="Phone (optional)" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Password" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Admin Code</label>
                    <input type="text" name="admin_code" class="form-control" required placeholder="Admin Code Required" />
                </div>

                <button type="submit" class="btn btn-dark w-100">Signup as Admin</button>
            </form>

            <div class="mt-3 text-center">
                <a href="admin_login.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
