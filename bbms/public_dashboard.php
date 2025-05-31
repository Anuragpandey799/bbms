<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: public_login.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user'];
$username = $_SESSION['username'];
$name = $_SESSION['name'] ?? $username;

// Fetch statistics securely
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM donations WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$donationCount = $stmt->get_result()->fetch_assoc()['count'];

$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM requests WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$requestCount = $stmt->get_result()->fetch_assoc()['count'];

// Real-world statistics
$totalRequired = 12000000; // Annual units needed
$totalDonated = 9000000;   // Annual donations
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Blood Bank</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .card {
            border-radius: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .main-content {
            padding-top: 80px;
            padding-bottom: 50px;
        }

        h2 {
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .stat-value {
                font-size: 2rem;
            }
        }

        .btn-custom {
            border-radius: 50px;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'public_sidebar.php'; ?>

<!-- Page Content -->
<div class="main-content">
    <div class="container">
        <h2 class="text-center text-danger mb-5">Welcome, <?= htmlspecialchars($name) ?>!</h2>

        <!-- Stats Row -->
        <div class="row g-4 justify-content-center">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card text-center bg-light h-100">
                    <div class="card-body">
                        <h6 class="text-danger fw-bold mb-2">India's Blood Need</h6>
                        <p class="stat-value text-danger" data-count="<?= $totalRequired ?>">0</p>
                        <small>Units/year</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card text-center bg-light h-100">
                    <div class="card-body">
                        <h6 class="text-success fw-bold mb-2">Total Blood Donated</h6>
                        <p class="stat-value text-success" data-count="<?= $totalDonated ?>">0</p>
                        <small>Units/year</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card text-center bg-success text-white h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">Your Donations</h6>
                        <p class="stat-value" data-count="<?= $donationCount ?>">0</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card text-center bg-primary text-white h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">Your Requests</h6>
                        <p class="stat-value" data-count="<?= $requestCount ?>">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Row -->
        <div class="row g-4 mt-5">
            <div class="col-md-6">
                <div class="card bg-warning text-dark text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Donate Blood</h5>
                        <p>Ready to save lives? Register your donation.</p>
                        <a href="public_donateBlood.php" class="btn btn-dark btn-custom">Donate Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-info text-white text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Request Blood</h5>
                        <p>Need blood urgently? Submit your request.</p>
                        <a href="public_requestBlood.php" class="btn btn-light btn-custom">Request Now</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile logout -->
        <div class="text-center mt-5 d-lg-none">
            <a href="logout.php" class="btn btn-outline-danger btn-custom">Logout</a>
        </div>
    </div>
</div>

<!-- Animation Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.stat-value');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-count');
                const current = +counter.innerText.replace(/,/g, '');
                const increment = target / 200;

                if (current < target) {
                    counter.innerText = Math.ceil(current + increment).toLocaleString();
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            };
            updateCount();
        });
    });
</script>

</body>
</html>
