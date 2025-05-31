<?php
require_once 'auth.php';
requireLogin('user');
require_once 'db.php';

if (!isset($_GET['request_id'])) {
    die("Invalid access");
}

$request_id = $_GET['request_id'];

$stmt = $conn->prepare("SELECT r.*, u.username FROM requests r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
$stmt->bind_param("s", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Request not found.");
}

$data = $result->fetch_assoc();
$admin_contact = '7991845638';

// Insert into request_cards if not already
$check = $conn->prepare("SELECT id FROM request_cards WHERE request_id = ?");
$check->bind_param("s", $request_id);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows === 0) {
    $insert = $conn->prepare("INSERT INTO request_cards 
        (request_id, username, patient_name, patient_age, blood_group, reason, hospital, requester_contact, admin_contact, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $insert->bind_param("sssisssss", 
        $request_id,
        $data['username'],
        $data['patient_name'],
        $data['age'],
        $data['blood_group'],
        $data['reason'],
        $data['hospital'],
        $data['contact'],
        $admin_contact
    );
    
    $insert->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Identity Card</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff3f3;
            font-family: 'Segoe UI', sans-serif;
        }

        .card-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border: 3px dashed #dc3545;
            padding: 30px 25px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .card-header {
            text-align: center;
            font-weight: bold;
            font-size: 26px;
            color: #dc3545;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #333;
        }

        .signature {
            font-family: 'Brush Script MT', cursive;
            font-size: 20px;
            text-align: right;
            margin-top: 40px;
            color: #6c757d;
        }

        #qrcode {
            margin: 30px auto;
            /* text-align: center; */
        }

        #print-btn {
            display: block;
            margin: 30px auto;
            padding: 10px 30px;
            border: none;
            font-size: 16px;
            border-radius: 30px;
            background-color: #dc3545;
            color: white;
            transition: background 0.3s ease;
        }

        #print-btn:hover {
            background-color: #b02a37;
        }

        @media print {
            #print-btn,
            .header {
                display: none !important;
            }

            .card-container {
                box-shadow: none !important;
                margin: 2px;
            }

            body {
                background: white !important;
            }
        }

        @media (max-width: 576px) {
            .card-header {
                font-size: 22px;
            }

            .card-container {
                padding: 20px;
            }

            .signature {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>

<!-- Header (hidden on print) -->
<div class="header d-print-none">
    <?php include 'header.php'; ?>
    <?php include 'public_sidebar.php'; ?>

</div>

<!-- Main Card -->
<div class="card-container" id="card">
    <div class="card-header"><?= htmlspecialchars($data['username']) ?>'s Request Card</div>
    <p class="text-center text-muted small">Created at: <code><?= date("d/m/y H:i:s") ?></code></p>
    <hr>

    <p><span class="info-label">Patient Name:</span> <?= htmlspecialchars($data['patient_name']) ?></p>
    <p><span class="info-label">Age:</span> <?= intval($data['age']) ?></p>
    <p><span class="info-label">Blood Group:</span> <?= htmlspecialchars($data['blood_group']) ?></p>
    <p><span class="info-label">Contact Number:</span> <?= htmlspecialchars($data['contact']) ?></p>
    <p><span class="info-label">Hospital:</span> <?= htmlspecialchars($data['hospital']) ?></p>
    <p><span class="info-label">Reason:</span> <?= htmlspecialchars($data['reason']) ?></p>
    <p><span class="info-label">Admin Contact:</span> <?= $admin_contact ?></p>

    <div id="qrcode"></div>

    <div class="signature">Verified By<br>- P. Anurag & BBMS team</div>
</div>

<!-- Print Button -->
<button id="print-btn">🖨️ Print / Save Card</button>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "REQUEST ID: <?= htmlspecialchars($request_id) ?> | Username: <?= htmlspecialchars($data['username']) ?>",
        width: 100,
        height: 100
    });

    document.getElementById('print-btn').addEventListener('click', function () {
        window.print();
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
