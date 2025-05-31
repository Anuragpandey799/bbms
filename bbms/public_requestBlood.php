<?php 
require_once 'auth.php';
requireLogin('user');

require_once 'db.php';

$success = '';                                  
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user'];
    $patient_name = trim($_POST['patient_name']);
    $age = intval($_POST['age']);
    $blood_group = $_POST['blood_group'];
    $reason = trim($_POST['reason']);
    $contact = trim($_POST['contact']);
    $hospital = trim($_POST['hospital']);

    $stmt = $conn->prepare("INSERT INTO requests (user_id, patient_name, age, blood_group, reason, hospital, contact, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("isissss", $user_id, $patient_name, $age, $blood_group, $reason, $hospital, $contact);

    if ($stmt->execute()) {
        $request_id = $stmt->insert_id;
        header("Location: requestCard_save.php?request_id=$request_id");
        exit();
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Blood</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
            position: relative;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .bg-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: -1;
            filter: brightness(0.5);
        }

        .form-wrapper {
            backdrop-filter: blur(6px);
            background-color: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            margin-top: 60px;
            margin-bottom: 40px;
        }

        .form-wrapper h2 {
            font-weight: 700;
            color: #dc3545;
        }

        @media (max-width: 576px) {
            .form-wrapper {
                padding: 20px;
            }
            .form-wrapper h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<!-- Background Video -->
<video autoplay muted loop playsinline class="bg-video">
    <source src="210207_small.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<?php include 'header.php'; ?>
<?php include 'public_sidebar.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-10 col-md-8 col-lg-6">
            <div class="form-wrapper">
                <h2 class="text-center mb-4">🩸 Request Blood</h2>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Patient Name</label>
                        <input type="text" name="patient_name" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-control" min="0" max="130" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select" required>
                            <option value="">Select Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Request</label>
                        <input type="text" name="reason" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact" class="form-control" pattern="\d{10}" title="Enter a valid 10-digit number" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hospital Name</label>
                        <input type="text" name="hospital" class="form-control" required />
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to apply dark mode -->
<script>
    function applyTheme() {
        const theme = localStorage.getItem('theme');
        document.body.classList.toggle('dark-mode', theme === 'dark');
    }

    document.addEventListener('DOMContentLoaded', applyTheme);
    window.addEventListener('storage', applyTheme);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
