<?php
require_once 'auth.php';
requireLogin('admin');
require_once 'db.php';

// Handle search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sql = "SELECT d.*, u.username FROM donations d JOIN users u ON d.user_id = u.id ";
if ($search !== '') {
    $sql .= "WHERE u.username LIKE '%$search%' OR d.blood_group LIKE '%$search%' OR d.location LIKE '%$search%' ";
}
$sql .= "ORDER BY d.id DESC";
$donations = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Donations - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->

    <style>
        .pending-row { background-color: #fff3cd; }
        .status-badge { font-size: 0.85rem; }

        /* Dark mode styles */
        body.dark-mode {
            background-color: #212529;
            color: white;
        }
        .dark-mode .table { background-color: #343a40; }
        .dark-mode .table th, .dark-mode .table td { color: white; }
        .dark-mode .table-dark { background-color: #495057; }

        .navbar-dark-mode { background-color: #343a40; color: white; }
        .navbar-light-mode { background-color: #f8f9fa; color: black; }
    </style>
</head>
<body id="body">

<?php include 'admin_header.php'; ?>

<div class="container py-5">
    <div class="row align-items-center justify-content-between mb-4 g-2">
        <div class="col-12 col-md-auto">
            <h3 class="mb-0">Manage Blood Donations</h3>
        </div>
        <div class="col-12 col-md-6">
            <form class="d-flex" method="get">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by username, blood group, location..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </form>
        </div>
    </div>

    <?php if (mysqli_num_rows($donations) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Donor Name</th>
                        <th>Age</th>
                        <th>Blood Group</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($don = mysqli_fetch_assoc($donations)): ?>
                        <tr class="<?= $don['status'] === 'Pending' ? 'pending-row' : '' ?>">
                            <td><?= htmlspecialchars($don['username']) ?></td>
                            <td><?= htmlspecialchars($don['name']) ?></td>
                            <td><?= $don['age'] ?></td>
                            <td><?= $don['blood_group'] ?></td>
                            <td><?= htmlspecialchars($don['location']) ?></td>
                            <td><?= htmlspecialchars($don['contact']) ?></td>
                            <td>
                                <span class="badge <?= 
                                    $don['status'] === 'Approved' ? 'bg-success' : 
                                    ($don['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark')
                                ?> status-badge">
                                    <?= $don['status'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($don['status'] === 'Pending'): ?>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="<?= $don['id'] ?>" data-action="approve">Approve</button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="<?= $don['id'] ?>" data-action="reject">Reject</button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Processed</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No blood donations found.</div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to <span id="modalActionText"></span> this donation?
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="confirmBtn">Yes, proceed</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

<script>
    const modal = document.getElementById('confirmModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const action = button.getAttribute('data-action');

        const actionText = action.charAt(0).toUpperCase() + action.slice(1);
        document.getElementById('modalActionText').textContent = actionText;
        document.getElementById('confirmBtn').href = `admin_processDonation.php?id=${id}&action=${action}`;
    });

    // Theme toggle
    const body = document.getElementById("body");
    const currentTheme = localStorage.getItem("theme");
    const navbar = document.querySelector("nav");

    if (currentTheme) {
        body.classList.add(currentTheme);
        navbar.classList.add(currentTheme === "dark-mode" ? "navbar-dark-mode" : "navbar-light-mode");
    }

    const themeBtn = document.getElementById("themeToggleBtn");
    if (themeBtn) {
        themeBtn.addEventListener("click", () => {
            const newTheme = body.classList.contains("light-mode") ? "dark-mode" : "light-mode";
            body.classList.toggle("light-mode");
            body.classList.toggle("dark-mode");
            navbar.classList.toggle("navbar-light-mode");
            navbar.classList.toggle("navbar-dark-mode");
            localStorage.setItem("theme", newTheme);
        });
    }
</script>

</body>
</html>
