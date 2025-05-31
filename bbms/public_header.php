<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Blood Bank Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .dark-theme {
      background-color: #121212;
      color: white;
    }
    .dark-theme .navbar {
      background-color: #222 !important;
    }
    .dark-theme .nav-link,
    .dark-theme .navbar-brand {
      color: white !important;
    }
    .dark-theme .dropdown-menu {
      background-color: #333;
    }
    .dark-theme .dropdown-item {
      color: white;
    }
    .dark-theme .dropdown-item:hover {
      background-color: #555;
    }
    .logo {
      max-width: 3rem;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger sticky-top">
  <div class="container-fluid">
    <img src="blood_logo.png" alt="Logo" class="logo rounded-circle m-1">
    <a class="navbar-brand" href="index.php">Blood Bank</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" id="navbarToggler">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= $current_page == 'about.php' ? 'active' : '' ?>" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link <?= $current_page == 'projects.php' ? 'active' : '' ?>" href="projects.php">Projects</a></li>
        <li class="nav-item"><a class="nav-link <?= $current_page == 'gallery.php' ? 'active' : '' ?>" href="gallery.php">Gallery</a></li>
        <?php
$donate_link = isset($_SESSION['user_id']) ? 'public_donateBlood.php' : 'public_login.php';
?>
<li class="nav-item">
  <a class="nav-link <?= $current_page == 'public_donateBlood.php' ? 'active' : '' ?>" href="<?= $donate_link ?>">Donate Now</a>
</li>
        <li class="nav-item"><a class="nav-link <?= $current_page == 'contact.php' ? 'active' : '' ?>" href="contact.php">Contact</a></li>
      </ul>
      <ul class="navbar-nav">
        <?php if (!isset($_SESSION['user'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?= in_array($current_page, ['admin_login.php', 'public_login.php']) ? 'active' : '' ?>" href="#" id="loginDropdown" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">Login</a>
            <ul class="dropdown-menu" aria-labelledby="loginDropdown">
              <li><a class="dropdown-item" href="admin_login.php">Login as Admin</a></li>
              <li><a class="dropdown-item" href="public_login.php">Login as Public</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link <?= $current_page == 'signup.php' ? 'active' : '' ?>" href="public_signup.php">Signup</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php endif; ?>
        <li class="nav-item">
          <button class="btn btn-outline-light ms-2" id="themeToggle">🌙</button>
        </li>
      </ul>
    </div>
  </div>
</nav>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


