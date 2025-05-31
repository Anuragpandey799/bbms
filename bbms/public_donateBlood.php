<?php
require_once 'auth.php';
requireLogin('user');
require_once 'db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user'];
    $name = trim($_POST['name']);
    $age = intval($_POST['age']);
    $blood_group = $_POST['blood_group'];
    $contact = trim($_POST['contact']);
    $location = trim($_POST['location']);

    $stmt = $conn->prepare("INSERT INTO donations (user_id, name, age, blood_group, contact, location, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("isisss", $user_id, $name, $age, $blood_group, $contact, $location);

    if ($stmt->execute()) {
        $donation_id = $stmt->insert_id;
        header("Location: donationCard_save.php?donation_id=" . $donation_id);
        exit;
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Donate Blood</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      transition: background-color 0.3s ease, color 0.3s ease;
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }

    .bg-video {
      position: fixed;
      top: 0;
      left: 0;
      min-width: 100%;
      min-height: 100%;
      object-fit: cover;
      z-index: -1;
      filter: brightness(0.6);
    }

    .form-wrapper {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
      backdrop-filter: blur(6px);
    }

    .btn-danger {
      transition: all 0.3s ease;
    }

    .btn-danger:hover {
      background-color: #c82333;
      transform: scale(1.02);
    }

    @media (max-width: 767px) {
      .form-wrapper {
        margin-top: 30px;
        padding: 20px;
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

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-12">
      <div class="form-wrapper">
        <h2 class="text-center mb-4 fw-bold text-danger">Become a Blood Donor</h2>

        <?php if ($success): ?>
          <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Blood Group</label>
            <select name="blood_group" class="form-select" required>
              <option value="">Select Blood Group</option>
              <option value="A+">A+</option><option value="A-">A-</option>
              <option value="B+">B+</option><option value="B-">B-</option>
              <option value="AB+">AB+</option><option value="AB-">AB-</option>
              <option value="O+">O+</option><option value="O-">O-</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required />
          </div>
          <button type="submit" class="btn btn-danger w-100">Submit Donation</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
