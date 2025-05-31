<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $message = trim($_POST['message']);
    $user_id = $_SESSION['user_id'];

    if (!$message) {
        $error = "Review cannot be empty.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);
        if ($stmt->execute()) {
            $success = "Thank you for your feedback!";
        } else {
            $error = "Failed to submit review.";
        }
    }
}

// Fetch all reviews with user info using JOIN
$reviews = $conn->query("
    SELECT reviews.message, reviews.created_at, users.username 
    FROM reviews 
    JOIN users ON reviews.user_id = users.id 
    ORDER BY reviews.created_at DESC
");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Community Reviews</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" /> -->
  <style>
    /* body {
      background: linear-gradient(135deg, #e6f0ff, #ffffff);
      font-family: 'Segoe UI', sans-serif;
      scroll-behavior: smooth;
    } */

    .review-form {
      background: rgba(255, 255, 255, 0.95);
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
      animation: fadeInUp 1s ease;
    }

    .review-card {
      background: white;
      border: none;
      border-radius: 15px;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .review-card:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(30px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .glass {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(5px);
    }

    .section-title {
      font-size: 2rem;
      font-weight: bold;
      text-align: center;
      margin-bottom: 2rem;
      color: #dc3545;
    }

    .review-meta {
      font-size: 0.9em;
      color: #888;
    }

    textarea.form-control {
      resize: none;
    }
  </style>
</head>
<body>
<?php require 'header.php'; ?>

<div class="container py-5">
  <h1 class="section-title">Community Reviews</h1>

  <?php if (isset($_SESSION['user'])): ?>
  <div class="review-form mx-auto mb-5" style="max-width: 700px;" data-aos="fade-up">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Write your review</label>
        <textarea name="message" class="form-control" rows="4" placeholder="Share your experience..." required></textarea>
      </div>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <button type="submit" class="btn btn-danger w-100">Post Review</button>
    </form>
  </div>
  <?php else: ?>
    <div class="alert alert-warning text-center" data-aos="fade-down">Please <a href="public_login.php" class="text-danger">log in</a> to post a review.</div>
  <?php endif; ?>

  <div class="row g-4">
    <?php while ($row = $reviews->fetch_assoc()): ?>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= rand(50, 300) ?>">
        <div class="card review-card h-100 p-3">
          <div class="card-body">
            <h5 class="card-title text-danger">@<?= htmlspecialchars($row['username']) ?></h5>
            <p class="card-text"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
          </div>
          <div class="card-footer text-end bg-white border-0">
            <span class="review-meta"><?= date("M d, Y H:i", strtotime($row['created_at'])) ?></span>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  AOS.init({ duration: 800, once: true });
</script>
</body>
</html>
