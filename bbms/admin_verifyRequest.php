<?php
require_once 'auth.php';
requireLogin('admin');
require_once 'db.php';

$theme = $_SESSION['theme'] ?? 'light';
$bodyClass = $theme === 'dark' ? 'dark' : '';

$searchResults = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $query = "
        SELECT * FROM request_cards
        WHERE 
            id LIKE '%$search%' OR
            request_id LIKE '%$search%' OR
            username LIKE '%$search%' OR
            patient_name LIKE '%$search%' OR
            status LIKE '%$search%' OR
            blood_group LIKE '%$search%' OR
            hospital LIKE '%$search%' 
        ORDER BY created_at DESC
    ";

    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $searchResults[] = $row;
        }
    } else {
        $message = "No request card found matching your search.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fulfill_id'])) {
    $cardId = intval($_POST['fulfill_id']);
    $adminUsername = $_SESSION['username'];

    $res = mysqli_query($conn, "SELECT request_id, status FROM request_cards WHERE id = $cardId");
    $data = mysqli_fetch_assoc($res);
    $requestId = $data['request_id'] ?? null;
    $cardStatus = $data['status'] ?? null;

    if (
        $cardId > 0 &&
        $requestId &&
        isset($_SESSION['username']) &&
        in_array($cardStatus, ['Pending', 'Approved'])
    ) {
        $verifiedAt = date("Y-m-d H:i:s");

        mysqli_query($conn, "
            UPDATE request_cards 
            SET status='Fulfilled', verified_by='$adminUsername', verified_at='$verifiedAt' 
            WHERE id=$cardId");

        mysqli_query($conn, "
            UPDATE requests 
            SET status='Fulfilled' 
            WHERE id='$requestId'");

        $message = "Request successfully marked as fulfilled.";
    }

    header("Location: admin_verifyRequest.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Requests - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --bg: #ffffff;
            --text: #000000;
            --card-bg: #f9f9f9;
            --card-border: #e0e0e0;
            --input-bg: #ffffff;
        }

        body.dark {
            --bg: #1e1e2f;
            --text: #f1f1f1;
            --card-bg: #2c2c3e;
            --card-border: #3d3d50;
            --input-bg: #2b2b3a;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .card-box {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .form-control, .btn {
            background-color: var(--input-bg);
            color: var(--text);
            border-color: var(--card-border);
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 0.4em 0.8em;
            border-radius: 0.5rem;
        }

        .btn:disabled {
            opacity: 0.7;
        }

        #qrcode {
            margin-top: 10px;
        }

        @media (max-width: 576px) {
           
            .card-box {
                padding: 1rem;
            }

            h5 {
                font-size: 1.1rem;
            }

            .btn {
                width: 100%;
            }
        }
       

    </style>
</head>
<body class="<?= $bodyClass ?>">

<?php include 'admin_header.php'; ?>

<div class="container py-5">
    <h2 class="mb-4 text-center">Verify Blood Requests</h2>

    <form method="GET" class="row g-2 mb-4" id="search-form">
    <div class="col-12 col-sm-9">
        <input type="text" name="search" class="form-control" placeholder="Search by ID, Request ID, Username, Patient Name or Status" required>
    </div>
    <div class="col-12 col-sm-3">
        <button class="btn btn-primary w-100">Search</button>
    </div>
    </form>


    <?php if ($message): ?>
        <div class="alert alert-warning text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php foreach ($searchResults as $card): ?>
        <div class="card-box">
            <h5><?= htmlspecialchars($card['patient_name']) ?>'s Blood Request Identity Card</h5>
            <p><strong>Card ID:</strong> <?= htmlspecialchars($card['id']) ?></p>
            <p><strong>Requester Username:</strong> <?= htmlspecialchars($card['username']) ?></p>
            <p><strong>Patient Age:</strong> <?= htmlspecialchars($card['patient_age']) ?></p>
            <p><strong>Blood Group:</strong> <?= htmlspecialchars($card['blood_group']) ?></p>
            <p><strong>Hospital:</strong> <?= htmlspecialchars($card['hospital']) ?></p>
            <p><strong>Requester Contact:</strong> <?= htmlspecialchars($card['requester_contact']) ?></p>
            <p><strong>Admin Contact:</strong> <?= htmlspecialchars($card['admin_contact']) ?></p>
            <p><strong>Status:</strong>
                <span class="badge bg-<?= 
                    $card['status'] === 'Pending' ? 'warning text-dark' : 
                    ($card['status'] === 'Approved' ? 'success' : 
                    ($card['status'] === 'Fulfilled' ? 'secondary' : 'danger')) 
                ?> status-badge"><?= ucfirst($card['status']) ?></span>
            </p>

            <?php if (!empty($card['verified_by'])): ?>
                <p><strong>Verified By:</strong> <?= htmlspecialchars($card['verified_by']) ?></p>
                <p><strong>Verified At:</strong> <?= htmlspecialchars($card['verified_at']) ?></p>
            <?php endif; ?>

            <div id="qrcode<?= $card['id'] ?>"></div>

            <?php if (in_array($card['status'], ['Pending', 'Approved'])): ?>
                <form method="POST" class="mt-2">
                    <input type="hidden" name="fulfill_id" value="<?= $card['id'] ?>">
                    <button class="btn btn-success" onclick="return confirm('Mark this request as fulfilled?')">Mark Fulfilled</button>
                </form>
            <?php else: ?>
                <button class="btn btn-<?= $card['status'] === 'Fulfilled' ? 'secondary' : 'danger' ?> mt-2" disabled><?= ucfirst($card['status']) ?></button>
            <?php endif; ?>
        </div>

        <script>
            new QRCode("qrcode<?= $card['id'] ?>", {
                text: "REQUEST ID: <?= $card['request_id'] ?>, Username: <?= $card['username'] ?>",
                width: 100,
                height: 100,
                colorDark: "<?= $theme === 'dark' ? '#ffffff' : '#000000' ?>",
                colorLight: "<?= $theme === 'dark' ? '#1e1e2f' : '#ffffff' ?>"
            });
        </script>
    <?php endforeach; ?>
</div>
</body>
</html>
