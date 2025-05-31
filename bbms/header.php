<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

<style>
  body.dark-theme {
    background-color: rgb(20, 1, 1);
    color: white;
  }

  body.dark-theme .form-wrapper {
    background-color: rgba(118, 117, 117, 0.43);
    color: white;
  }

  body.dark-theme .navbar-toggler {
    background: rgba(194, 192, 192, 0.64);
    border: 1px solid white;
  }

  .dark-theme .navbar {
    background: rgba(4, 4, 4, 0.72) !important;
    border-bottom: 1px solid red;
    box-shadow: 0px 5px 20px black;
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
  }

  .dark-theme .nav-link,
  .dark-theme .navbar-brand,
  .dark-theme .dropdown-item {
    color: #f8f9fa !important;
  }

  .dark-theme .dropdown-menu {
    background-color: #2c2c2c;
    border: none;
  }

  .dark-theme .dropdown-item:hover {
    background-color: #444;
  }

  .navbar {
    background: linear-gradient(135deg, #dc3545, #a71d2a);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: all 0.4s ease-in-out;
    z-index: 1030;
  }

  .navbar .nav-link {
    position: relative;
    margin: 0 0.5rem;
    font-weight: 500;
    color: white !important;
    transition: all 0.3s ease;
  }

  .navbar .nav-link::after {
    content: '';
    position: absolute;
    width: 0%;
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: white;
    transition: width 0.3s ease-in-out;
  }

  .navbar .nav-link:hover::after,
  .navbar .nav-link.active::after {
    width: 100%;
  }

  .navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
    letter-spacing: 0.5px;
  }

  .dropdown-menu {
    border-radius: 0.5rem;
    border: none;
    margin-top: 0.5rem;
    animation: fadeIn 0.3s ease-in-out;
  }

  .dropdown-item {
    transition: all 0.3s ease;
  }

  .dropdown-item:hover {
    background-color: #f1f1f1;
    color: #dc3545 !important;
    font-weight: 500;
  }

  .logo {
    max-width: 3rem;
    transition: transform 0.3s ease-in-out;
  }

  #themeToggle {
    transform: scale(0.9);
    border: 1px solid white;
    border-radius: 50%;
    font-size: 1.2rem;
    background-color: transparent;
    transition: transform 0.3s ease-in-out;
  }

  #themeToggle:hover {
    transform: scale(1.1);
    color: #ffc107;
    transition: transform 0.2s ease, color 0.2s ease;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    25% { transform: rotate(45deg); }
    50% { transform: rotate(0deg); }
    75% { transform: rotate(-45deg); }
    100% { transform: rotate(0deg); }
  }

  .logo:hover {
    animation: spin .7s linear infinite;
  }

  .navbar-brand:hover {
    animation: spin 1s linear;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @media screen and (max-width: 992px) {
    .dark-btn {
      position: fixed;
      top: 20px;
      right: 80px;
    }
  }
</style>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid">
    <img src="blood_logo.png" alt="Logo" class="logo rounded-circle m-1">
    <a class="navbar-brand text-white" href="index.php">Blood Bank</a>
    
    <button class="navbar-toggler text-white <?php echo isset($_SESSION['user']) ? 'invisible' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" id="navbarToggler">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $current_page == (isset($_SESSION['user']) ? 'public_dashboard.php' : 'index.php') ? 'active' : '' ?>" 
            href="<?= isset($_SESSION['user']) ? 'public_dashboard.php' : 'index.php' ?>">
            Home
          </a>
        </li>
        <li class="nav-item"><a class="nav-link <?= $current_page == 'about.php' ? 'active' : '' ?>" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link <?= $current_page == 'gallery.php' ? 'active' : '' ?>" href="gallery.php">Gallery</a></li>
        <?php if (!isset($_SESSION['user'])): ?>
        <li class="nav-item">
          <a class="nav-link <?= $current_page == 'public_donateBlood.php' ? 'active' : '' ?>" 
             href="<?= isset($_SESSION['user']) ? 'public_donateBlood.php' : 'public_signup.php' ?>">
             Donate Now
          </a>
        </li>
        <?php endif ?>
        <li class="nav-item"><a class="nav-link <?= $current_page == 'contact.php' ? 'active' : '' ?>" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link <?= $current_page == 'review.php' ? 'active' : '' ?>" href="review.php">Review</a></li>
      </ul>

      <ul class="navbar-nav mb-2 mb-lg-0">
        <?php if (!isset($_SESSION['user'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">Login</a>
            <ul class="dropdown-menu" aria-labelledby="loginDropdown">
              <li><a class="dropdown-item" href="admin_login.php">Login as Admin</a></li>
              <li><a class="dropdown-item" href="public_login.php">Login as Public</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $current_page == 'public_signup.php' ? 'active' : '' ?>" href="public_signup.php">Signup</a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link fw-bold"> Welcome, <?= htmlspecialchars($_SESSION['name']) ?></a></li>
        <?php endif; ?>
        <li class="nav-item">
          <button class="btn text-white" id="themeToggle" title="Toggle Theme">🌙</button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script>
  const toggleBtn = document.getElementById('themeToggle');
  const currentTheme = localStorage.getItem('theme');
  if (currentTheme === 'dark') {
    document.body.classList.add('dark-theme');
    toggleBtn.textContent = '☀️';
  }
  toggleBtn.addEventListener('click', () => {
    document.body.classList.toggle('dark-theme');
    const isDark = document.body.classList.contains('dark-theme');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    toggleBtn.textContent = isDark ? '☀️' : '🌙';
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
