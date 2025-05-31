<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<!-- Sidebar -->
<div class="sidebar pb-5 mb-5" id="sidebar">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="text-danger mb-0">Menu</h5>
        <!-- Minimize/Maximize Button with Bootstrap Icon -->
        <button class="minimize-btn btn btn-sm btn-outline-danger" id="minimizeSidebarBtn">
            <i class="bi bi-chevron-up"></i>
        </button>
    </div>

    <div class="sidebar-links mt-3">
    <a href="public_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <!-- <a href="public_dashboard.php"><i class="bi bi-star me-2"></i> Benefits</a>
    <a href="public_dashboard.php"><i class="bi bi-people me-2"></i> Community</a> -->

    <!-- Only visible on small/medium devices -->
    <div class="d-lg-none">
        <a href="about.php"><i class="bi bi-info-circle me-2"></i> About</a>
        <a href="contact.php"><i class="bi bi-envelope me-2"></i> Contact</a>
        <a href="gallery.php"><i class="bi bi-images me-2"></i> Gallery</a>
    </div>

    <a href="public_donateBlood.php"><i class="bi bi-droplet me-2"></i> Donate Blood</a>
    <a href="public_requestBlood.php"><i class="bi bi-telephone-inbound me-2"></i> Request Blood</a>
    <a href="public_searchBlood.php"><i class="bi bi-search me-2"></i> Search Donors</a>
    <a href="public_history.php"><i class="bi bi-clock-history me-2"></i> History</a>
    <a href="public_settings.php"><i class="bi bi-gear me-2"></i> Settings</a>
    <a href="review.php"><i class="bi bi-star me-2"></i> review</a>


    <!-- Profile section -->
    <a href="public_profile.php"><i class="bi bi-person-circle me-2"></i> Profile</a>

    <a href="logout.php" class="text-bg-danger mb-5"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

</div>

<!-- Overlay -->
<div class="overlay" id="overlay"></div>

<!-- Menu Toggle Button -->
<button class="menu-toggle" id="menuToggle">☰</button>

<!-- Sidebar Toggle Script -->
<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const minimizeBtn = document.getElementById('minimizeSidebarBtn');
    const minimizeIcon = minimizeBtn.querySelector('i');

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        menuToggle.textContent = sidebar.classList.contains('active') ? '✖' : '☰';
    }

    function toggleMinimizeSidebar() {
        sidebar.classList.toggle('minimized');
        // Change the icon between chevron-up and chevron-down
        if (sidebar.classList.contains('minimized')) {
            minimizeIcon.classList.remove('bi-chevron-up');
            minimizeIcon.classList.add('bi-chevron-down');
        } else {
            minimizeIcon.classList.remove('bi-chevron-down');
            minimizeIcon.classList.add('bi-chevron-up');
        }
    }

    menuToggle.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
    minimizeBtn.addEventListener('click', toggleMinimizeSidebar);
</script>

<style>
    .sidebar {
    position: fixed;
    top: 1;
    left: -250px;
    height: 100%;
    width: 250px;
    background-color: rgba(255, 255, 255, 0.8); /* Transparent background */
    border-right: 2px solid blue; /* Red border line */
    padding: 1rem;
    transition: left 0.3s ease, height 0.3s ease, padding 0.3s ease;
    z-index: 1051;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
    overflow-y: auto;
    overflow-x: hidden;
    backdrop-filter: blur(1px); /* Frosted glass effect */

    /* Hide scrollbar */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none;  /* IE and Edge */
}
.sidebar::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}


    /* Minimized sidebar: shrink the sidebar vertically */
    .sidebar.minimized {
        height: 50px;
        padding: 0.5rem 1rem;
        border-radius: 5px;
    }

    .sidebar.minimized .sidebar-links {
        display: none;
    }

    .sidebar.active {
        left: 0;
    }

    .sidebar h5 {
        margin-bottom: 1.5rem;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        margin-bottom: 10px;
        color: #333;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: background-color 0.2s, color 0.2s;
    }

    .sidebar a:hover {
        background-color: rgb(240, 12, 35);
        color: #fff;
        border: 1px solid black;
    }

    .menu-toggle {
        position: fixed;
        top: 15px;
        right: 20px;
        font-size: 28px;
        z-index: 1052;
        border: 1px solid #dc3545;
        color: #dc3545;
        border-radius: 5px;
        padding: 4px 10px;
        background: white;
        cursor: pointer;
    }

    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
    }

    .overlay.active {
        display: block;
    }

    .minimize-btn {
        font-size: 14px;
        line-height: 1;
        padding: 0 8px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (min-width: 992px) {
        .sidebar {
            left: 0;
        }

        .menu-toggle {
            display: none;
        }

        .main-content {
            margin-left: 250px;
        }

        .overlay {
            display: none !important;
        }
    }
</style>
