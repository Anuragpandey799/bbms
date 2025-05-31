<?php
session_start();
require_once 'header.php'; // adjust path if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Bank Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
        }

        video.background-video {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            filter: brightness(0.8);
            display: none; /* Hide initially */
        }

        .main-content {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 3rem 1rem;
        }

        .main-content h1 {
            font-size: 3rem;
            font-weight: 700;
            animation: fadeInUp 1s ease-out;
        }

        .main-content p {
            font-size: 1.3rem;
            margin-top: 1rem;
            animation: fadeInUp 1.2s ease-out;
        }

        .cta-buttons {
            margin-top: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            animation: fadeInUp 1.4s ease-out;
        }

        .cta-buttons a {
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary:hover,
        .btn-secondary:hover,
        .btn-success:hover,
        .btn-outline-danger:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .quote-box {
            margin-top: 3rem;
            font-size: 1.2rem;
            font-style: italic;
            max-width: 800px;
            animation: fadeIn 2s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0 }
            to { opacity: 1 }
        }

        ::selection {
            background: #dc3545;
            color: white;
        }

        .no-video {
            background-color: #121212 !important;
            color: #ffffff;
        }

        .no-video .main-content {
            color: #ffffff;
        }

        @media (max-width: 576px) {
            .main-content h1 {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body class="bg-dark">

<!-- 🔴 Background video -->
<video autoplay muted loop class="background-video" id="bgVideo">
    <source src="210207_small.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
</video>

<!-- 🌟 Main Content -->
<div class="main-content">
    <h1>Welcome to Blood Bank</h1>
    <p>“You don’t have to be a doctor to save lives — just donate blood.”</p>

    <div class="cta-buttons">
        <a href="public_login.php" class="btn btn-primary">Login as Public</a>
        <a href="admin_login.php" class="btn btn-secondary">Login as Admin</a>
        <a href="public_signup.php" class="btn btn-success">Signup</a>
    </div>

    <div class="quote-box mt-5">
        “A single pint can save three lives, a single gesture can create a million smiles.”
        <br><br>
        <strong>Be a Hero. Be a Donor.</strong>
    </div>

    <!-- 🩸 Education & Awareness Section -->
    <div class="mt-5 text-white px-4 py-5" style="background: rgba(0, 0, 0, 0.6); border-radius: 15px; max-width: 900px;">
        <h2 class="mb-4 text-danger fw-bold">Why Donate Blood?</h2>

        <ul class="list-unstyled">
            <li class="mb-3">
                <strong>🩸 Every 2 seconds, someone needs blood.</strong><br>
                Accidents, surgeries, cancer treatments, and complications during childbirth are just a few examples.
            </li>
            <li class="mb-3">
                <strong>💉 One donation can save up to 3 lives.</strong><br>
                Your 350-450ml of blood is separated into components — red cells, platelets, plasma — benefiting multiple patients.
            </li>
            <li class="mb-3">
                <strong>❤️ It’s safe, quick, and deeply rewarding.</strong><br>
                The entire process takes just 20-30 minutes and helps you give back to humanity in a way nothing else can.
            </li>
            <li class="mb-3">
                <strong>💪 Health benefits for donors too!</strong><br>
                Regular donation helps regulate iron levels, can reduce harmful cholesterol, and stimulates new blood cell production.
            </li>
            <li class="mb-3">
                <strong>🧬 You could be someone’s only hope.</strong><br>
                Rare blood groups are hard to find. Your donation might be a perfect match for someone critically in need.
            </li>
        </ul>

        <h4 class="mt-4 text-light">Your Effort Makes a Lifesaving Impact</h4>
        <p>
            By registering, searching, or donating — you're becoming part of a national movement to ensure that <strong>no life is lost due to blood shortage</strong>.  
            Join the community of heroes who choose compassion, action, and humanity every day.
        </p>

        <h5 class="mt-4 text-warning">💡 Did You Know?</h5>
        <ul class="list-unstyled">
            <li>🔺 Over 12,000 people in India alone need blood daily.</li>
            <li>🔺 Blood can only be stored for 35–42 days. The need is continuous.</li>
            <li>🔺 Less than 1% of the population donates regularly.</li>
        </ul>

        <div class="text-center mt-4">
            <a href="public_signup.php" class="btn btn-lg btn-outline-light me-2">Join Now</a>
            <a class="btn btn-lg btn-danger" href="public_login.php">Find Blood</a>
        </div>
    </div>
</div>

<script>
    const video = document.getElementById("bgVideo");

    // If video can be played successfully
    video.addEventListener('canplaythrough', () => {
        video.style.display = 'block';
        document.body.classList.remove('no-video');
    });

    // If an error occurs while loading the video
    video.addEventListener('error', () => {
        video.style.display = 'none';
        document.body.classList.add('no-video');
    });

    // Fallback: if video doesn't load in 5s, assume failure
    setTimeout(() => {
        if (video.readyState < 3) {
            video.style.display = 'none';
            document.body.classList.add('no-video');
        }
    }, 5000);
</script>

</body>
</html>
