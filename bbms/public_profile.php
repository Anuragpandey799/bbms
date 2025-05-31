<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: public_login.php");
    exit();
}

$user_id = $_SESSION['user'];

try {
    $stmt = $conn->prepare("SELECT id, username, name, email, blood_group, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        header("Location: public_login.php");
        exit();
    }

    $donation_stmt = $conn->prepare("SELECT COUNT(*) as total_donations FROM donations WHERE user_id = ?");
    $donation_stmt->bind_param("i", $user_id);
    $donation_stmt->execute();
    $donation_result = $donation_stmt->get_result();
    $donation_data = $donation_result->fetch_assoc();
    $donation_count = $donation_data['total_donations'];

    if ($donation_count >= 20) {
        $badge = ["🏆 Eternal Giver", "Your sacrifice echoes through generations. You're a legend among heroes."];
    } elseif ($donation_count >= 10) {
        $badge = ["🌟 Life Guardian", "You're an unwavering source of life and light for those in need."];
    } elseif ($donation_count >= 5) {
        $badge = ["💖 Heart Warrior", "Your repeated kindness makes you a beacon of strength and compassion."];
    } elseif ($donation_count >= 2) {
        $badge = ["🔥 Pulse Keeper", "Every drop fuels hope. You're keeping hearts beating."];
    } elseif ($donation_count == 1) {
        $badge = ["🌱 Hope Giver", "You've taken your first step as a life-saver. Welcome to the mission!"];
    } else {
        $badge = ["🔘 No Badge Yet", "Donate to start your journey of saving lives!"];
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile | Blood Bank</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body >


<?php include 'header.php'; ?>
<?php include 'public_sidebar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow p-4 text-center">
                <!-- Profile Picture -->
                <?php if (!empty($user['profile_pic'])): ?>
                    <img src="<?= '../uploads/profile_pics/' . $user['profile_pic'] ?>" alt="User" class="rounded-circle border border-primary mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                <?php else: ?>
                    <div class="mb-3 text-primary">
                        <i class="bi bi-person-circle" style="font-size: 120px;"></i>
                    </div>
                <?php endif; ?>

                <h3 class="mb-1"><?= htmlspecialchars($user['name']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>

                <hr class="my-4">

                <div class="row text-start mb-3">
                    <div class="col-sm-6 mb-3">
                        <p class="mb-1"><strong class="text-primary">Username:</strong> <?= htmlspecialchars($user['username'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong class="text-primary">Blood Group:</strong> <?= htmlspecialchars($user['blood_group'] ?? 'N/A') ?></p>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <p class="mb-1"><strong class="text-primary">Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'N/A') ?></p>
                        <p class="mb-0"><strong class="text-primary">Total Donations:</strong> <?= $donation_count ?></p>
                    </div>
                </div>

                <!-- Badge Card -->
                <!-- Badge Ladder -->
<div class="mt-4 text-start">
    <h5 class="mb-3">🎖️ Your Badge Level: <span class="text-primary fw-bold"><?= $badge[0] ?></span></h5>
    
    <div class="list-group">
        <?php
        $badge_levels = [
            1 => ["🌱 Hope Giver", "You've taken your first step as a life-saver."],
            2 => ["🔥 Pulse Keeper", "You're keeping hearts beating."],
            5 => ["💖 Heart Warrior", "A beacon of strength and compassion."],
            10 => ["🌟 Life Guardian", "An unwavering source of life."],
            20 => ["🏆 Eternal Giver", "You're a legend among heroes."]
        ];

        foreach ($badge_levels as $min_donations => $info) {
            $is_current = $donation_count >= $min_donations &&
                ($min_donations === max(array_keys(array_filter($badge_levels, fn($v, $k) => $donation_count >= $k, ARRAY_FILTER_USE_BOTH))));
            
            echo '<div class="list-group-item d-flex justify-content-between align-items-center ' . ($is_current ? 'bg-primary text-white' : '') . '">';
            echo '<div><strong>' . $info[0] . '</strong><br><small>' . $info[1] . '</small></div>';
            echo '<span class="badge rounded-pill ' . ($is_current ? 'bg-light text-primary fw-bold' : 'bg-secondary') . '">';
            echo ($is_current ? 'Your Level' : $min_donations . '+');
            echo '</span>';
            echo '</div>';
        }

        if ($donation_count < 1) {
            echo '<div class="list-group-item d-flex justify-content-between align-items-center bg-warning-subtle">';
            echo '<div><strong>🔘 No Badge Yet</strong><br><small>Donate to start your journey of saving lives!</small></div>';
            echo '<span class="badge bg-warning text-dark rounded-pill">Get Started</span>';
            echo '</div>';
        }
        ?>
    </div>
</div>


                <!-- Action Buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-2">
                    <a href="public_donateBlood.php" class="btn btn-outline-primary">
                        <i class="bi bi-droplet me-1"></i> Donate
                    </a>
                    <a href="public_settings.php" class="btn btn-outline-secondary">
                        <i class="bi bi-gear me-1"></i> Settings
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
