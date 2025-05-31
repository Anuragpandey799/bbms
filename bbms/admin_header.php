<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once 'auth.php';
requireLogin('admin');
?>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --bg: #ffffff;
        --text: #000000;
        --nav-hover: rgba(226, 69, 69, 0.15);
    }

    body.dark {
        --bg: #1e1e2f;
        --text: #ffffff;
        --nav-hover: rgba(255, 255, 255, 0.1);
        background-color: var(--bg);
        color: var(--text);
    }
    body.dark .navbar-toggler{
        background-color: white;
    }
    body.dark .navbar-brand{
        color: white;
        font-weight: bold;
    }

    .navbar {
        background-color: var(--bg) !important;
        color: var(--text) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
        /* box-shadow: 10px 10px 10px rgba(0, 0, 0, 0.05); */
        border-bottom: 1px solid red;
    }


    .navbar .navbar-nav .nav-link {
        color: var(--text) !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .navbar .navbar-nav .nav-link:hover {
        background-color: var(--nav-hover);
        border-radius: 0.5rem;
    }

    .theme-toggle {
        border: none;
        background: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text);
        transition: transform 0.3s ease;
    }

    .theme-toggle:hover {
        transform: rotate(180deg);
    }

    body {
        transition: background-color 0.5s ease, color 0.3s ease;
    }
</style>

<!-- Admin Navbar -->
<nav class="navbar navbar-expand-lg sticky-top py-2 px-3 ">
    <div class="container-fluid">
        <a class="navbar-brand " href="admin_dashboard.php">Admin Panel</a>

        <!-- Theme toggle icon (always visible) -->
        <button class="theme-toggle order-lg-1 ms-auto me-2" id="toggleTheme" title="Toggle Theme">
            <i class="bi bi-moon-fill" id="themeIcon"></i>
        </button>

        <!-- Mobile menu toggler -->
        <button class="navbar-toggler border border-1" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Menu -->
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 d-flex flex-wrap gap-2 mt-2 mt-lg-0">
                <li class="nav-item"><a class="nav-link" href="admin_manageDonation.php">Manage Donations</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_manageRequest.php">Manage Requests</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_contactMessages.php">Messages</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_verifyRequest.php">Verify Requests</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_verifyDonations.php">Verify Donations</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Theme Toggle Script -->
<script>
    const body = document.body;
    const toggleBtn = document.getElementById('toggleTheme');
    const icon = document.getElementById('themeIcon');

    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark');
        icon.classList.replace('bi-moon-fill', 'bi-brightness-high-fill');
    }

    toggleBtn.addEventListener('click', () => {
        body.classList.toggle('dark');
        const dark = body.classList.contains('dark');
        icon.classList.toggle('bi-moon-fill', !dark);
        icon.classList.toggle('bi-brightness-high-fill', dark);
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    });
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
