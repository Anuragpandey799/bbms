<?php
require_once 'auth.php';
requireLogin('user');
require_once 'db.php';

if (!isset($_GET['donation_id'])) die("Invalid access");

$donation_id = intval($_GET['donation_id']);

$stmt = $conn->prepare("SELECT d.*, u.username FROM donations d JOIN users u ON d.user_id = u.id WHERE d.id = ?");
$stmt->bind_param("i", $donation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) die("Donation not found.");

$data = $result->fetch_assoc();
$admin_contact = '7991845638';

// Check and insert into donation_cards if not already present
$check = $conn->prepare("SELECT id FROM donation_cards WHERE donation_id = ?");
$check->bind_param("i", $donation_id);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows === 0) {
    $insert = $conn->prepare("INSERT INTO donation_cards 
        (donation_id, username, donor_name, donor_age, blood_group, location, contact, admin_contact, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $insert->bind_param("ississss",
        $donation_id,
        $data['username'],
        $data['name'],
        $data['age'],
        $data['blood_group'],
        $data['location'],
        $data['contact'],
        $admin_contact
    );
    $insert->execute();
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Donation Identity Card</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .card-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 20px;
            padding: 35px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 0.6s ease;
        }

        .card-header {
            font-size: 26px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            color: #d63384;
        }

        .card-detail {
            margin: 15px 0;
            font-size: 18px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .signature {
            font-family: 'Brush Script MT', cursive;
            font-size: 22px;
            text-align: right;
            margin-top: 40px;
            color: #333;
        }

        #qrcode {
            text-align: center;
            margin: 30px 0;
        }

        #print-btn {
            display: block;
            margin: 30px auto;
            padding: 12px 40px;
            font-size: 16px;
            border-radius: 30px;
            background-color: #0d6efd;
            color: white;
            border: none;
            transition: background-color 0.3s ease;
        }

        #print-btn:hover {
            background-color: #084298;
        }

        .note-text {
            text-align: center;
            font-size: 15px;
            margin-top: 10px;
        }

        @media (max-width: 576px) {
            .card-container {
                padding: 25px 20px;
            }

            .card-header {
                font-size: 22px;
            }

            .card-detail {
                font-size: 16px;
            }
        }

        @media print {
            #print-btn,
            .header,
            .note-text {
                display: none !important;
            }

            .card-container {
                box-shadow: none !important;
                margin: 0;
                padding: 0;
            }

            body {
                background: white !important;
            }
        }
    </style>
</head>
<body>

<!-- Optional Header Include -->
<div class="header d-print-none">
    <?php include 'header.php'; ?>
    <?php include 'public_sidebar.php'; ?>

</div>

<div class="container">
    <div class="card-container bg-light text-dark" id="card">
        <div class="card-header"><?= htmlspecialchars($data['username']) ?>'s Donation Card</div>
        <p class="text-center text-muted small">
            Created at: <code><?= date("d/m/y H:i:s") ?></code>
        </p>
        <hr>
        <div class="card-detail"><span class="info-label">Donor Name:</span> <?= htmlspecialchars($data['name']) ?></div>
        <div class="card-detail"><span class="info-label">Age:</span> <?= intval($data['age']) ?></div>
        <div class="card-detail"><span class="info-label">Blood Group:</span> <?= htmlspecialchars($data['blood_group']) ?></div>
        <div class="card-detail"><span class="info-label">Contact Number:</span> <?= htmlspecialchars($data['contact']) ?></div>
        <div class="card-detail"><span class="info-label">Location:</span> <?= htmlspecialchars($data['location']) ?></div>
        <div class="card-detail"><span class="info-label">Admin Contact:</span> <?= $admin_contact ?></div>

        <div id="qrcode"></div>

        <div class="signature">Verified By<br>- P. Anurag & BBMS team</div>
    </div>

    <p class="note-text d-print-none">Note: Please save or print this card for future hospital visits.</p>
    <button id="print-btn" class="btn d-print-none">🖨️ Print / Save Card</button>
</div>

<!-- QR Code -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "DONOR ID: <?= $donation_id ?> | Username: <?= htmlspecialchars($data['username']) ?>",
        width: 120,
        height: 120
    });

    document.getElementById('print-btn').addEventListener('click', function () {
        window.print();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
