<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us | Blood Bank Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <style>
    :root {
      --primary-color: #dc3545;
      --light-bg: #f8f9fa;
      --lighter-bg: #fff0f0;
      --dark-bg: #212529;
      --dark-card-bg: #2c2f33;
      --dark-text: #f1f1f1;
    }

    body {
      transition: background-color 0.3s ease;
    }

    .about-hero {
      background: linear-gradient(to right, var(--primary-color), #ff6b6b);
      color: white;
      padding: 80px 20px;
      text-align: center;
    }

    .creator-img {
      width: 150px;
      aspect-ratio: 1/1;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid var(--primary-color);
      display: block;
      margin: 0 auto 20px;
    }

    section {
      padding: 60px 20px;
    }

    .why-donate, .blood-info, .certifications, .join-cta {
      background-color: var(--light-bg);
    }

    .timeline-section {
      background-color: var(--lighter-bg);
    }

    .testimonial-card {
      border-left: 5px solid var(--primary-color);
      background: white;
      padding: 20px;
      margin: 10px 0;
      border-radius: 8px;
    }

    .dark-theme {
      background-color: var(--dark-bg);
      color: var(--dark-text);
    }

    .dark-theme .about-hero {
      background: linear-gradient(to right, #8e0e14, #c44545);
    }

    .dark-theme .card,
.dark-theme .testimonial-card,
.dark-theme .why-donate,
.dark-theme .blood-info,
.dark-theme .certifications,
.dark-theme .join-cta,
.dark-theme .timeline-section {
  background-color: var(--dark-card-bg);
  color: var(--dark-text);
}


    .blood-graphic {
      max-width: 100%;
      height: auto;
    }

    .badge-icon {
      width: 80px;
      margin-bottom: 10px;
    }

    .hero-cta {
      background: var(--primary-color);
      color: white;
      padding: 20px 40px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }

    .hero-cta:hover {
      background: #c82333;
    }
  </style>
</head>
<body >

  <?php
    require_once 'header.php';
    if (isset($_SESSION['user'])) {
     require_once 'public_sidebar.php';
    }
  ?>

<div class="about-hero" data-aos="fade-down">
  <h1>About Our Blood Bank System</h1>
  <p>Our mission is to make blood donation and request systems faster, more transparent, and accessible through technology. Together, we save lives.</p>
</div>

<!-- Creators Section -->
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-5 mb-4" data-aos="zoom-in">
      <div class="card text-center shadow p-4">
        <img src="assets/images/anurag.jpg" alt="Anurag Pandey" class="creator-img">
        <h4>Anurag Pandey</h4>
        <p class="data-bs-theme">Full-Stack Developer</p>
        <p>Built core functionalities and user experience with modern design principles.</p>
      </div>
    </div>
    <div class="col-md-5 mb-4" data-aos="zoom-in">
      <div class="card text-center shadow p-4">
        <img src="assets/images/jaydeep.jpg" alt="Jaydeep Pradhan" class="creator-img">
        <h4>Jaydeep Pradhan</h4>
        <p class="data-bs-theme">Co-Developer & System Designer</p>
        <p>Handled UI/UX, system planning, and made sure the project meets real-life needs.</p>
      </div>
    </div>
  </div>
</div>

<!-- Why Donate Section -->
<section class="why-donate" data-aos="fade-up">
  <div class="container text-center">
    <h2>Why Donate Blood?</h2>
    <p>Every donation can save up to 3 lives. Be the reason someone gets a second chance at life.</p>
    <div class="row mt-4">
      <div class="col-md-4">
        <img src="assets/icons/heart.png" class="badge-icon" alt="Save lives">
        <p>Help people in critical need.</p>
      </div>
      <div class="col-md-4">
        <img src="assets/icons/community.png" class="badge-icon" alt="Community">
        <p>Support your community with a life-saving gesture.</p>
      </div>
      <div class="col-md-4">
        <img src="assets/icons/health.png" class="badge-icon" alt="Health">
        <p>Improve your health with regular donation.</p>
      </div>
    </div>
  </div>
</section>

<!-- Blood Type Graphic Section -->
<section class="blood-info" data-aos="fade-up">
  <div class="container text-center">
    <h2>Blood Compatibility</h2>
    <p>Who can donate to whom? Understand the types before you donate.</p>
    <img src="assets/images/blood_chart.png" class="blood-graphic mt-3" alt="Blood Type Compatibility">
  </div>
</section>

<!-- Timeline -->
<section class="timeline-section" data-aos="fade-up">
  <div class="container">
    <h2 class="text-center">Our Journey</h2>
    <div class="timeline-item">
      <h5><strong>2024</strong> - Project Ideation</h5>
      <p>We recognized the need for a digital blood bank management platform after community outreach.</p>
    </div>
    <div class="timeline-item">
      <h5><strong>Early 2025</strong> - Development Phase</h5>
      <p>Designing user-friendly interfaces and robust database systems began.</p>
    </div>
    <div class="timeline-item">
      <h5><strong>Now</strong> - Live System</h5>
      <p>The system is currently operational and helping real users in need.</p>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section" data-aos="fade-up">
  <div class="container">
    <h2 class="text-center">What People Say</h2>
    <div class="testimonial-card">
      <p><em>"Thanks to this system, I got blood for my father during an emergency within minutes."</em> - Rina D.</p>
    </div>
    <div class="testimonial-card">
      <p><em>"A great initiative to bring transparency and speed in blood donation."</em> - Rahul S.</p>
    </div>
  </div>
</section>

<!-- Certifications -->
<section class="certifications" data-aos="fade-up">
  <div class="container text-center">
    <h2>Our Recognitions</h2>
    <p>We are trusted and certified by major health organizations.</p>
    <img src="assets/images/certifications.png" class="img-fluid mt-3" alt="Certifications">
  </div>
</section>

<!-- Call to Action -->
<?php if (!isset($_SESSION['user'])): ?>
<section class="join-cta text-center" data-aos="zoom-in">
  <div class="container">
    <h2>Become a Hero</h2>
    <p>Register as a donor today and become someone’s real-life superhero.</p>
    <a href="public_signup.php" class="hero-cta mt-3 d-inline-block">Register Now</a>
  </div>
</section>
<?php endif ?>


<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init();</script>

</body>
</html>
