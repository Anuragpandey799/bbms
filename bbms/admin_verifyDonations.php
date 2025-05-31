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
        SELECT * FROM donation_cards
        WHERE (
            id LIKE '%$search%' OR
            donation_id LIKE '%$search%' OR
            username LIKE '%$search%' OR
            donor_name LIKE '%$search%' OR
            blood_group LIKE '%$search%' OR
            status LIKE '%$search%'
        )
        ORDER BY created_at DESC
    ";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $searchResults[] = $row;
        }
    } else {
        $message = "No donation card found matching your search.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fulfill_id'])) {
    $cardId = intval($_POST['fulfill_id']);
    $adminUsername = $_SESSION['username'];
    $res = mysqli_query($conn, "SELECT donation_id, status FROM donation_cards WHERE id = $cardId");
    $data = mysqli_fetch_assoc($res);
    $requestId = $data['donation_id'] ?? null;
    $cardStatus = $data['status'] ?? null;

    if (
        $cardId > 0 &&
        $requestId > 0 &&
        isset($_SESSION['username']) &&
        in_array($cardStatus, ['Pending', 'Approved'])
    ) {
        $verifiedAt = date("Y-m-d H:i:s");

        mysqli_query($conn, "
            UPDATE donation_cards 
            SET status='Fulfilled', verified_by='$adminUsername', verified_at='$verifiedAt' 
            WHERE id=$cardId");

        mysqli_query($conn, "
            UPDATE donations 
            SET status='Fulfilled' 
            WHERE id=$requestId");

        $message = "Donation successfully marked as fulfilled.";
    }

    header("Location: admin_verifyDonations.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Donations - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --text: #000000;
            --card-bg: #f9f9f9;
            --card-border: #dee2e6;
            --input-bg: #ffffff;
        }

        body.dark {
            --bg: #1e1e2f;
            --text: #f8f9fa;
            --card-bg: #2c2f3f;
            --card-border: #444;
            --input-bg: #343a40;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            transition: background-color 0.3s, color 0.3s;
        }

        .card-box {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.05);
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
            padding: 0.4em 1em;
        }

        #qrcode {
            margin-top: 20px;
        }

        /* Responsive stacking for donor info */
        @media (max-width: 768px) {
            .card-box p {
                font-size: 0.95rem;
                margin-bottom: 0.6rem;
            }

            .card-box h5 {
                font-size: 1.1rem;
                margin-bottom: 1rem;
            }

            #qrcode {
                text-align: center;
            }
        }
    </style>
</head>
<body class="<?= $bodyClass ?>">

<?php include 'admin_header.php'; ?>

<div class="container py-5">
    <h2 class="mb-4 text-center">Verify Donations</h2>

    <form method="GET" class="input-group mb-4 shadow-sm">
        <input type="text" name="search" class="form-control" placeholder="Search by ID, Request ID, Username, Donor Name, Status..." required>
        <button class="btn btn-primary">Search</button>
    </form>

    <?php if ($message): ?>
        <div class="alert alert-warning text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php foreach ($searchResults as $card): ?>
        <div class="card-box">
            <h5><?= htmlspecialchars($card['donor_name']) ?>'s Donation Identity Card</h5>
            <p><strong>Card ID:</strong> <?= htmlspecialchars($card['id']) ?></p>
            <p><strong>Donor Username:</strong> <?= htmlspecialchars($card['username']) ?></p>
            <p><strong>Age:</strong> <?= htmlspecialchars($card['donor_age']) ?></p>
            <p><strong>Blood Group:</strong> <?= htmlspecialchars($card['blood_group']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($card['location']) ?></p>
            <p><strong>Donor Contact:</strong> <?= htmlspecialchars($card['contact']) ?></p>
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

            <?php if ($card['status'] === 'Pending' || $card['status'] === 'Approved'): ?>
                <form method="POST" class="mt-3">
                    <input type="hidden" name="fulfill_id" value="<?= $card['id'] ?>">
                    <button class="btn btn-success w-100" onclick="return confirm('Mark this donation as fulfilled?')">Mark Fulfilled</button>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary mt-2 w-100" disabled><?= ucfirst($card['status']) ?></button>
            <?php endif; ?>
        </div>

        <script>
            new QRCode("qrcode<?= $card['id'] ?>", {
                text: "DONOR ID: <?= $card['donation_id'] ?>, Username: <?= $card['username'] ?>",
                width: 100,
                height: 100,
                colorDark : "<?= $theme === 'dark' ? '#ffffff' : '#000000' ?>",
                colorLight : "<?= $theme === 'dark' ? '#1e1e2f' : '#ffffff' ?>"
            });
        </script>
    <?php endforeach; ?>
</div>

</body>
</html>
