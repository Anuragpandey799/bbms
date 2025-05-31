<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gallery - Blood Bank Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    body.dark-theme {
      background-color: #121212;
      color: white;
    }

    .gallery-header {
      background: linear-gradient(90deg, #dc3545, #ff6b6b);
      color: white;
      padding: 60px 20px;
      text-align: center;
    }

    .gallery-header h1 {
      font-size: 3rem;
      font-weight: bold;
    }

    .gallery-header p {
      font-size: 1.2rem;
      max-width: 750px;
      margin: 15px auto;
    }

    .gallery-img {
      height: 230px;
      object-fit: cover;
      border-radius: 16px;
      transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .gallery-img:hover {
      transform: scale(1.07);
      box-shadow: 0 0 20px rgba(220, 53, 69, 0.6);
    }

    .img-card {
      border: none;
      padding: 10px;
      background: none;
    }

    .img-caption {
      font-weight: 600;
      margin-top: 12px;
      font-size: 1.05rem;
    }

    .section-title {
      font-weight: bold;
      color: #dc3545;
      margin-top: 50px;
      margin-bottom: 25px;
    }

    .img-wrapper {
      overflow: hidden;
      border-radius: 16px;
    }
  </style>
</head>
<body>

<?php include("header.php"); ?>
<?php if (isset($_SESSION['user'])): ?>
<?php include('public_sidebar.php') ?>
<?php endif ?>


<div class="gallery-header" data-aos="fade-down">
  <h1>Gallery of Gratitude</h1>
  <p>Snapshots of unity, kindness, and the moments that remind us of the strength in community.</p>
</div>

<div class="container mt-5 mb-5">
  <h3 class="text-center section-title" data-aos="fade-up">Captured Moments</h3>
  <div class="row g-4 justify-content-center">
    <?php
      $images = [
        ["file" => "blood_logo.png", "caption" => "A moment of kindness"],
        ["file" => "gallery_img.jpg", "caption" => "Together we heal"],
        ["file" => "bloodBank.png", "caption" => "Every drop counts"],
        ["file" => "bloodBank.png", "caption" => "United for a cause"],
        ["file" => "gallery_img.jpg", "caption" => "Smiles that matter"],
        ["file" => "blood_logo.png", "caption" => "Hope delivered"],
        ["file" => "blood_logo.png", "caption" => "Hope delivered"],
        ["file" => "bloodBank.png", "caption" => "Together we heal"],
        ["file" => "gallery_img.jpg", "caption" => "Hope delivered"],
        ["file" => "bloodBank.png", "caption" => "Together we heal"],
        ["file" => "gallery_img.jpg", "caption" => "Hope delivered"],

      ];
      foreach ($images as $index => $img):
    ?>
    <div class="col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="<?= $index * 1 ?>">
      <div class="card img-card text-center">
        <div class="img-wrapper">
          <img src="<?= $img['file'] ?>" alt="Gallery Image <?= $index + 1 ?>" class="gallery-img w-100">
        </div>
        <div class="img-caption"><?= $img['caption'] ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="text-center mt-5" data-aos="fade-up">
    <p class="text-muted">This gallery honors the spirit of humanity and resilience. Let’s continue spreading hope and compassion.</p>
  </div>
</div>


<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>

