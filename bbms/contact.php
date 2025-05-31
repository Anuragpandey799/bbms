<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $phone   = trim($_POST["phone"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

    if ($stmt->execute()) {
        $adminEmail = "funwithdevanu@gmail.com";
        $mailSubject = "New Contact Message from Blood Bank Website";
        $mailBody = "You have received a new message from the contact page:\n\n" .
                    "Name: $name\nEmail: $email\nPhone: $phone\nSubject: $subject\n\nMessage:\n$message";
        mail($adminEmail, $mailSubject, $mailBody);
        $_SESSION['success'] = "Your message has been sent successfully.";
    } else {
        $_SESSION['error'] = "Failed to send your message. Please try again.";
    }

    $stmt->close();
    $conn->close();
    header("Location: contact.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <title>Contact Us - Blood Bank Management System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
      body{
        overflow-x: hidden;
      }
    html {
      scroll-behavior: smooth;
    }

    .contact-hero {
      background: linear-gradient(120deg, #dc3545c7, #ff6b6bc2);
      color: white;
      padding: 80px 20px;
      text-align: center;
    }

    .map-responsive {
      overflow: hidden;
      padding-bottom: 56.25%;
      position: relative;
      height: 0;
      border-radius: 15px;
    }

    .map-responsive iframe {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      border: 0;
    }

    .form-wrapper {
      background-color: white;
    }

    .dark-theme .form-wrapper,
    .dark-theme .shadow {
      background-color: rgba(255, 255, 255, 0.05);
      color: white;
    }

    .dark-theme .form-control {
      background-color: #2c2c2c;
      color: white;
      border: 1px solid #555;
    }

    .dark-theme .form-control::placeholder {
      color: #ccc;
    }

    .dark-theme .form-control:focus {
      background-color: #333;
      border-color: #ff6b6b;
      color: white;
    }

    .dark-theme .alert-success {
      background-color: #1f3d1f;
      color: #c8f7c5;
    }

    .dark-theme .alert-danger {
      background-color: #3d1f1f;
      color: #f7c5c5;
    }

    .dark-theme .bg-white {
      background-color: rgba(255,255,255,0.07)!important;
    }

    a {
      color: inherit;
    }

    .dark-theme a {
      color:rgb(239, 180, 4);
    }
  </style>
</head>

<body>
  <?php
    require_once 'header.php';
    if (isset($_SESSION['user'])) {
      require_once 'public_sidebar.php';
    }
  ?>

  <div class="contact-hero">
    <h1 data-aos="fade-down" class="display-5 fw-bold">Get in Touch</h1>
    <p data-aos="fade-up" class="lead mx-auto" style="max-width: 700px;">
      We're committed to saving lives. Reach out for help, support, or to contribute to our blood donation initiative.
    </p>
  </div>

  <div class="container py-5">
    <div class="row g-5">
      <!-- Contact Form -->
      <div class="col-lg-6" data-aos="fade-right">
        <div class="form-wrapper rounded-4 shadow p-4">
          <h4 class="mb-4 text-danger">Send a Message</h4>

          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
          <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
          <?php endif; ?>

          <form method="post" action="">
            <div class="mb-3">
              <input type="text" class="form-control rounded-3" name="name" placeholder="Your Name" required/>
            </div>
            <div class="mb-3">
              <input type="email" class="form-control rounded-3" name="email" placeholder="Your Email" required/>
            </div>
            <div class="mb-3">
              <input type="tel" class="form-control rounded-3" name="phone" placeholder="Mobile no." required/>
            </div>
            <div class="mb-3">
              <input type="text" class="form-control rounded-3" name="subject" placeholder="Subject" required/>
            </div>
            <div class="mb-3">
              <textarea class="form-control rounded-3" name="message" rows="5" placeholder="Your Message" required></textarea>
            </div>
            <button type="submit" class="btn btn-danger px-4">Send</button>
          </form>
        </div>
      </div>

      <!-- Info + Map -->
      <div class="col-lg-6" data-aos="fade-left">
        <div class="bg-white rounded-4 shadow p-4 mb-3">
          <h5 class="text-danger fw-bold">Our Address</h5>
          <p class="mb-0">Blood Bank HQ, 2nd Floor, Health Plaza,<br>Sector 21, Mumbai, Maharashtra - 400093</p>
        </div>
        <div class="bg-white rounded-4 shadow p-4 mb-3">
          <h5 class="text-danger fw-bold">Call Us</h5>
          <p><a href="tel:+7991845638">+91 79918 45638</a><br><a href="tel:+9619329967">+91 96193 29967</a></p>
        </div>
        <div class="bg-white rounded-4 shadow p-4 mb-3">
          <h5 class="text-danger fw-bold">Email</h5>
          <p><a href="mailto:anuraganitaanil4aaa@gmail.com">anuraganitaanil4aaa@gmail.com</a><br><a href="mailto:anuhackerag799@gmail.com">anuhackerag799@gmail.com</a></p>
        </div>
        <div class="map-responsive mt-4">
          <iframe src="https://maps.app.goo.gl/6NAgJwfofezVA8BZ8" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>

  <?php if (isset($_SESSION['error'])): ?>
  <script>
    const formSection = document.querySelector('.contact-form');
    if (formSection) {
      window.scrollTo({
        top: formSection.offsetTop - 60,
        behavior: 'smooth'
      });
    }
  </script>
  <?php endif; ?>
</body>
</html>
