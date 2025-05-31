<?php
session_start();
require_once 'db.php';

$error = '';
$adminCode = "ADMIN123";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $code = $_POST['admin_code'];

    if ($code !== $adminCode) {
        $error = "Invalid Admin Code.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $admin = $res->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin'] = $admin['id'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Admin not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Blood Bank</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Background Video Styling */
        #bg-video {
            position: fixed;
            top: 0;
            left: 0;
            min-width: 100%;
            min-height: 100%;
            object-fit: cover;
            z-index: -1;
            filter: brightness(0.4);
        }

        .container {
            z-index: 1;
        }

        .login-container {
            animation: fadeInUp 1s ease;
        }

        .card {
            border: none;
            border-radius: 1rem;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.4s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .form-control:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
            transition: 0.3s ease;
        }

        .btn-dark {
            background-color: #e74c3c;
            border: none;
            transition: all 0.3s ease-in-out;
        }

        .btn-dark:hover {
            background-color: #c0392b;
            box-shadow: 0 0 15px rgba(231, 76, 60, 0.6);
        }

        .back-link a {
            color: #444;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #e74c3c;
            text-decoration: underline;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 60px, 0);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }
    </style>
</head>
<body>

<!-- Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="210207_small.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<!-- Main Content -->
<div class="container py-2">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 login-container">
            <div class="card p-5 animate__animated animate__fadeInUp">
                <h2 class="text-center mb-4 fw-bold">Admin Login</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required />
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Admin Code</label>
                        <input type="text" name="admin_code" class="form-control" required />
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-2">Login as Admin</button>
                </form>

                <div class="mt-3 text-center back-link">
                    <a href="index.php">← Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scroll Reveal -->
<!-- <script src="https://unpkg.com/scrollreveal"></script>
<script>
    ScrollReveal().reveal('.login-container', {
        delay: 300,
        distance: '50px',
        duration: 1000,
        easing: 'ease-out',
        origin: 'bottom'
    });
</script> -->

</body>
</html>
