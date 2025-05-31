<?php 
require_once 'auth.php';
requireLogin('admin');
require_once 'db.php';

// Get unread contact message count
$unreadQuery = mysqli_query($conn, "SELECT COUNT(*) AS unread FROM contact_messages WHERE status = 'unread'");
$unreadCount = mysqli_fetch_assoc($unreadQuery)['unread'];

// Total donors (users with role = 'user')
$donors = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'user'");
$donorCount = mysqli_fetch_assoc($donors)['total'];

// Total users
$users = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$userCount = mysqli_fetch_assoc($users)['total'];

// Total requests
$requests = mysqli_query($conn, "SELECT COUNT(*) AS total FROM requests");
$requestCount = mysqli_fetch_assoc($requests)['total'];

// Blood group availability
$bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$groupStats = [];
foreach ($bloodGroups as $group) {
    $query = mysqli_query($conn, "SELECT COUNT(*) AS count FROM donations WHERE blood_group = '$group' AND status = 'approved'");
    $groupStats[$group] = mysqli_fetch_assoc($query)['count'];
}

// Recent blood requests
$recentRequests = mysqli_query($conn, "
    SELECT patient_name, blood_group, hospital, contact, status
    FROM requests
    ORDER BY id DESC
    LIMIT 5
");

// Recent donations
$recentDonations = mysqli_query($conn, "
    SELECT name, blood_group, location, contact, status
    FROM donations
    ORDER BY id DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Blood Bank</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"> -->
    
    <style>
        .card {
            border-radius: 1rem;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .badge {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<div class="container py-5">
    <h2 class="text-center mb-5 text-primary">Admin Dashboard</h2>

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card text-white bg-danger text-center p-4 shadow-sm">
                <div class="fs-2 fw-bold"><?= $donorCount ?></div>
                <div>Total Donors</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card text-white bg-primary text-center p-4 shadow-sm">
                <div class="fs-2 fw-bold"><?= $requestCount ?></div>
                <div>Total Requests</div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-4">
            <div class="card text-white bg-success text-center p-4 shadow-sm">
                <div class="fs-2 fw-bold"><?= $userCount ?></div>
                <div>Total Users</div>
            </div>
        </div>
    </div>

    <!-- Blood Group Availability -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3 text-center text-danger">Blood Group Availability</h5>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Blood Group</th>
                                <th>Units Available</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupStats as $group => $count): ?>
                            <tr>
                                <td><?= $group ?></td>
                                <td><?= $count ?></td>
                                <td>
                                    <?php
                                        echo $group === 'O-' ? "Universal Donor" :
                                             ($group === 'AB+' ? "Universal Acceptor" : "Regular");
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Blood Requests -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3 text-center text-primary">Recent Blood Requests</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Patient</th>
                                <th>Blood Group</th>
                                <th>Hospital</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($req = mysqli_fetch_assoc($recentRequests)): ?>
                            <tr class="<?= $req['status'] === 'Rejected' ? 'table-danger' : '' ?>">
                                <td><?= htmlspecialchars($req['patient_name']) ?></td>
                                <td><?= $req['blood_group'] ?></td>
                                <td><?= htmlspecialchars($req['hospital']) ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $req['status'] === 'Approved' ? 'bg-success' : 
                                            ($req['status'] === 'Pending' ? 'bg-warning text-dark' : 
                                            ($req['status'] === 'Fulfilled' ? 'bg-secondary' : 'bg-danger')) ?>">
                                        <?= ucfirst($req['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Donations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3 text-center text-success">Recent Donations</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Donor</th>
                                <th>Blood Group</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($donation = mysqli_fetch_assoc($recentDonations)): ?>
                            <tr class="<?= $donation['status'] === 'Rejected' ? 'table-danger' : '' ?>">
                                <td><?= htmlspecialchars($donation['name']) ?></td>
                                <td><?= $donation['blood_group'] ?></td>
                                <td><?= htmlspecialchars($donation['location']) ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $donation['status'] === 'Approved' ? 'bg-success' : 
                                            ($donation['status'] === 'Pending' ? 'bg-warning text-dark' : 
                                            ($donation['status'] === 'Fulfilled' ? 'bg-secondary' : 'bg-danger')) ?>">
                                        <?= ucfirst($donation['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
