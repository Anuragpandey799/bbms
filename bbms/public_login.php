<?php
session_start();
require_once 'db.php';
// require_once '../includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'user'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];  // <- make sure this is set!
            $_SESSION['blood_group'] = $user['blood_group'];


            header("Location: public_dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No user found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Blood Bank</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: Arial, sans-serif;
    }

    video.bg-video {
      position: fixed;
      right: 0;
      bottom: 0;
      min-width: 100%;
      min-height: 100%;
      object-fit: cover;
      z-index: -2;
    }

    .overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(5px);
      z-index: -1;
    }

    .login-popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 30px;
      border-radius: 15px;
      width: 100%;
      max-width: 400px;
      z-index: 2;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .dark-theme .login-popup {
      background: #1e1e1e;
      color: #fff;
    }

    .login-popup h2 {
      color: #dc3545;
    }

    .dark-theme .form-control {
      background-color: #2c2c2c;
      color: #fff;
      border-color: #555;
    }

    .dark-theme label {
      color: #ddd;
    }

    .login-popup a {
      color: #dc3545;
    }

    .theme-toggle {
      position: fixed;
      top: 15px;
      right: 20px;
      z-index: 3;
    }
  </style>
</head>
<body>
  <?php require 'header.php'; ?>

<!-- Video Background -->
<video autoplay muted loop class="bg-video">
  <source src="210207_small.mp4" type="video/mp4">
  Your browser does not support HTML5 video.
</video>

<!-- Overlay Blur -->
<div class="overlay"></div>

<!-- Login Modal Popup -->
<div class="login-popup mt-5">
  <h2 class="text-center mb-4">Public User Login</h2>
  
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required />
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required />
    </div>
    <button type="submit" class="btn btn-danger w-100">Login</button>
  </form>

  <div class="mt-3 text-center">
    <a href="admin_login.php">Login as Admin</a> |
    <a href="public_signup.php">New User? Signup</a>
  </div>
</div>

</body>
</html>
