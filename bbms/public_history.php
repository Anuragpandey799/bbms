<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: public_login.php");
    exit();
}

require_once 'db.php';

// User Information
$user_id = $_SESSION['user'];
$username = $_SESSION['username'];
$name = $_SESSION['name'] ?? $username;

$created_at = $conn->query("SELECT created_at FROM users WHERE id = $user_id")->fetch_assoc()['created_at'] ?? 'N/A';
$donationCount = $conn->query("SELECT COUNT(*) AS count FROM donations WHERE user_id = $user_id")->fetch_assoc()['count'];
$requestCount = $conn->query("SELECT COUNT(*) AS count FROM requests WHERE user_id = $user_id")->fetch_assoc()['count'];

$donationCards = $conn->query("SELECT dc.* FROM donation_cards dc JOIN donations d ON dc.donation_id = d.id WHERE d.user_id = $user_id ORDER BY dc.created_at DESC");
$requestCards = $conn->query("SELECT rc.* FROM request_cards rc JOIN requests r ON rc.request_id = r.id WHERE r.user_id = $user_id ORDER BY rc.created_at DESC");

// Handle delete actions
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && isset($_GET['id'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['card_type'])) {
        $card_id = (int) $_GET['id'];
        $card_type = $_GET['card_type'];

        if ($card_type === 'donation') {
            $conn->query("DELETE FROM donation_cards WHERE id = $card_id");
        } elseif ($card_type === 'request') {
            $conn->query("DELETE FROM request_cards WHERE id = $card_id");
        }

        header("Location: history.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .light-mode {
            background-color: #f8f9fa;
            color: #212529;
        }
        .dark-mode {
            background-color: #343a40;
            color: #f8f9fa;
        }
        .main-wrapper {
            display: flex;
        }
        .sidebar {
            border-right: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .content {
            flex-grow: 1;
            padding: 2rem;
            overflow: hidden;
            transition: margin-left 0.3s ease;
        }
        .section-card {
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .section-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .info-box {
            border-radius: 12px;
            padding: 1rem;
            background-color: orange;
            transition: background-color 0.3s ease;
        }
        .info-box:hover {
            background-color: rgb(246, 113, 24);
        }
        @media (max-width: 768px) {
            .main-wrapper {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                position: relative;
                border-right: none;
            }
        }
    </style>
</head>
<body class="light-mode" id="body">

<?php include 'header.php'; ?>

<div class="main-wrapper">
    <?php include 'public_sidebar.php'; ?>

    <div class="content container">
        <h2 class="mb-4 text-center text-danger">Activity Overview</h2>

        <div class="row text-center mb-5">
            <div class="col-md-4 mb-3">
                <div class="info-box">
                    <h6 class="text-muted">Account Created</h6>
                    <p><?= htmlspecialchars(date("F j, Y", strtotime($created_at))) ?></p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="info-box">
                    <h6 class="text-muted">Total Donations</h6>
                    <p><?= $donationCount ?></p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="info-box">
                    <h6 class="text-muted">Total Requests</h6>
                    <p><?= $requestCount ?></p>
                </div>
            </div>
        </div>

        <!-- Side-by-side layout -->
        <div class="row">
            <!-- Donations -->
            <div class="col-md-6 pe-md-4">
                <h4 class="text-danger mb-3">Donation Records</h4>
                <hr>
                <div class="row">
                    <?php if ($donationCards->num_rows > 0): ?>
                        <?php while ($donation = $donationCards->fetch_assoc()): ?>
                            <div class="col-12 mb-3">
                                <div class="card section-card" id="donation-card-<?= $donation['id'] ?>">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">Donor: <?= htmlspecialchars($donation['donor_name']) ?></h5>
                                        <ul class="list-unstyled">
                                            <li><strong>Age:</strong> <?= $donation['donor_age'] ?></li>
                                            <li><strong>Blood Group:</strong> <?= $donation['blood_group'] ?></li>
                                            <li><strong>Contact:</strong> <?= $donation['contact'] ?></li>
                                            <li><strong>Location:</strong> <?= $donation['location'] ?></li>
                                            <li><strong>Status:</strong> <?= $donation['status'] ?></li>
                                            <li><strong>Verified By:</strong> <?= $donation['verified_by'] ?? 'N/A' ?></li>
                                            <li><strong>Verified At:</strong> <?= $donation['verified_at'] ?? 'N/A' ?></li>
                                            <li><strong>Date:</strong> <?= date('F j, Y', strtotime($donation['created_at'])) ?></li>
                                        </ul>
                                        <div class="mt-3 d-flex justify-content-between">
                                            <button onclick="printCard('donation-card-<?= $donation['id'] ?>')" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-download"></i> Print
                                            </button>
                                            <a href="?action=delete&id=<?= $donation['id'] ?>&card_type=donation" class="btn btn-outline-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this donation card?');">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center fw-bold">No donation records found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Requests -->
            <div class="col-md-6 ps-md-4 mt-5 mt-md-0">
                <h4 class="text-primary mb-3">Request Records</h4>
                <hr>
                <div class="row">
                    <?php if ($requestCards->num_rows > 0): ?>
                        <?php while ($request = $requestCards->fetch_assoc()): ?>
                            <div class="col-12 mb-3">
                                <div class="card section-card" id="request-card-<?= $request['id'] ?>">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">Patient: <?= htmlspecialchars($request['patient_name']) ?></h5>
                                        <ul class="list-unstyled">
                                            <li><strong>Age:</strong> <?= $request['patient_age'] ?></li>
                                            <li><strong>Blood Group:</strong> <?= $request['blood_group'] ?></li>
                                            <li><strong>Reason:</strong> <?= $request['reason'] ?></li>
                                            <li><strong>Hospital:</strong> <?= $request['hospital'] ?></li>
                                            <li><strong>Contact:</strong> <?= $request['requester_contact'] ?></li>
                                            <li><strong>Status:</strong> <?= $request['status'] ?></li>
                                            <li><strong>Verified By:</strong> <?= $request['verified_by'] ?? 'N/A' ?></li>
                                            <li><strong>Verified At:</strong> <?= $request['verified_at'] ?? 'N/A' ?></li>
                                            <li><strong>Date:</strong> <?= date('F j, Y', strtotime($request['created_at'])) ?></li>
                                        </ul>
                                        <div class="mt-3 d-flex justify-content-between">
                                            <button onclick="printCard('request-card-<?= $request['id'] ?>')" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-download"></i> Print
                                            </button>
                                            <a href="?action=delete&id=<?= $request['id'] ?>&card_type=request" class="btn btn-outline-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this request card?');">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center fw-bold">No request records found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle between light and dark mode
    function toggleMode() {
        var body = document.getElementById('body');
        if (body.classList.contains('light-mode')) {
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
        } else {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
        }
    }

    // Print card function
    function printCard(cardId) {
        var cardContent = document.getElementById(cardId).innerHTML;
        var printWindow = window.open('', '', 'height=500, width=800');

        printWindow.document.write('<html><head><title>Print Card</title>');
        printWindow.document.write(`
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f8f9fa;
                }
                .container {
                    max-width: 800px;
                    margin: 20px auto;
                    padding: 20px;
                }
                .card {
                    border-radius: 15px;
                    border: 1px solid #ddd;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    padding: 20px;
                    background-color: #ffffff;
                    font-size: 16px;
                    line-height: 1.5;
                }
                .card-header {
                    font-size: 24px;
                    font-weight: bold;
                    color: #4e73df;
                    text-align: center;
                }
                .card ul {
                    list-style: none;
                    padding: 0;
                }
                .card ul li {
                    margin-bottom: 8px;
                }
                .card ul li strong {
                    font-weight: 600;
                    color: #333;
                }
                .btn {
                    display: none;
                }
                @media print {
                    .btn {
                        display: none;
                    }
                }
            </style>
        `);
        printWindow.document.write('</head><body>');
        printWindow.document.write('<div class="container"><div class="card">');
        printWindow.document.write('<div class="card-header">Donation/Request Card</div>');
        printWindow.document.write('<div class="card-body">');
        printWindow.document.write(cardContent);
        printWindow.document.write('</div></div></div>');
        printWindow.document.write('</body></html>');

        printWindow.document.close();
        printWindow.print();
    }
</script>
</body>
</html>
