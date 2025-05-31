<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $blood_group = trim($_POST['blood_group']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password_plain = trim($_POST['password']);
    $role = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'user';

    $isStrongPassword = preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{7,12}$/', $password_plain);

    if (!$name || !$blood_group || !$username || !$email || !$password_plain) {
        $error = "All required fields must be filled.";
    } elseif (!$isStrongPassword) {
        $error = "Password must be 7–12 characters and include an uppercase letter, lowercase letter, number, and special character.";
    } else {
        $password = password_hash($password_plain, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, blood_group, username, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $name, $blood_group, $username, $email, $phone, $password, $role);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['name'] = $name;
                $_SESSION['blood_group'] = $blood_group;
                $_SESSION['role'] = $role;
                header("Location: public_dashboard.php");
                exit();
            } else {
                $error = "Signup failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Signup - Blood Bank</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow-x: hidden;
      font-family: 'Segoe UI', sans-serif;
    }

    video.bg-video {
      position: fixed;
      right: 0;
      bottom: 0;
      min-width: 100%;
      min-height: 100%;
      object-fit: cover;
      z-index: -2;
    }

    .overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.2);
      backdrop-filter: blur(1px);
      z-index: -1;
    }

    .signup-popup {
      position: relative;
      top: 20px;
      margin: 0 auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 30px 25px;
      border-radius: 20px;
      width: 95%;
      max-width: 500px;
      z-index: 2;
      box-shadow: 0 12px 30px rgba(0,0,0,0.4);
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .form-control:focus {
      border-color: #dc3545;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
    }

    .btn-danger:hover {
      background-color: #c82333;
      border-color: #bd2130;
    }

    body.dark-theme .signup-popup {
      background: rgba(18, 18, 18, 0.95);
      color: #f8f9fa;
    }

    body.dark-theme .form-control,
    body.dark-theme .form-select {
      background-color: #2c2c2c;
      color: #f8f9fa;
      border: 1px solid #444;
    }

    body.dark-theme .form-control:focus {
      border-color: #dc3545;
    }

    body.dark-theme .btn-danger {
      background-color: #c82333;
    }

    .form-text.rules span {
      margin-right: 10px;
      font-size: 0.85em;
      font-weight: 500;
    }

    .rule-valid {
      color: green;
    }

    .rule-invalid {
      color: red;
    }

    @media (max-width: 576px) {
      .signup-popup {
        padding: 25px 15px;
      }
    }
  </style>
</head>
<body>
<?php require 'header.php'; ?>

<video autoplay muted loop class="bg-video">
  <source src="210207_small.mp4" type="video/mp4">
</video>

<div class="overlay"></div>

<div class="container my-5">
  <?php if ($error): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="signup-popup">
    <h2 class="mb-4 text-center text-danger">Create Public Account</h2>

    <form method="POST">
      <div class="d-flex justify-content-center mb-4">
        <div class="form-check me-4">
          <input id="public" class="form-check-input" type="radio" name="role" value="user" checked onchange="checkRole('user')">
          <label for="public" class="form-check-label">Public</label>
        </div>
        <div class="form-check">
          <input id="admin" class="form-check-input" type="radio" name="role" value="admin" onchange="checkRole('admin')">
          <label for="admin" class="form-check-label">Admin</label>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required placeholder="Full Name"/>
      </div>

      <div class="mb-3">
        <label class="form-label">Blood Group</label>
        <select name="blood_group" class="form-select" required>
          <option value="">Select Group</option>
          <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
          <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required placeholder="userName123" />
      </div>

      <div class="mb-3">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control" required placeholder="sample123@gmail.com"/>
      </div>

      <div class="mb-3">
        <label class="form-label">Phone *</label>
        <input type="tel" name="phone" class="form-control" minlength="10" maxlength="10" placeholder="Phone no. (optional)" />
      </div>

      <div class="mb-3">
        <label class="form-label">Password *</label>
        <div class="input-group">
          <input type="password" name="password" id="password" class="form-control" required />
          <span class="input-group-text" id="togglePassword" style="cursor:pointer;" title="Show/Hide Password">👁️</span>
        </div>
        <div class="form-text rules mt-1" id="passwordHelp">
          <span id="length" class="rule-invalid">7–12 chars</span>
          <span id="uppercase" class="rule-invalid">A-Z</span>
          <span id="lowercase" class="rule-invalid">a-z</span>
          <span id="number" class="rule-invalid">0-9</span>
          <span id="special" class="rule-invalid">!@#$</span>
        </div>
      </div>

      <button type="submit" class="btn btn-danger w-100 mt-2" disabled>Signup as Public</button>
    </form>

    <div class="mt-3 text-center">
      <a href="public_login.php">Already have an account? Login</a>
    </div>
  </div>
</div>

<script>
  function checkRole(role) {
    if (role === 'admin') {
      window.location.href = 'admin_adminSignup.php';
    }
  }

  const passwordInput = document.getElementById('password');
  const togglePassword = document.getElementById('togglePassword');
  const submitBtn = document.querySelector('form button[type="submit"]');

  const rules = {
    length: document.getElementById('length'),
    uppercase: document.getElementById('uppercase'),
    lowercase: document.getElementById('lowercase'),
    number: document.getElementById('number'),
    special: document.getElementById('special'),
  };

  togglePassword.addEventListener('click', function () {
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
    this.textContent = type === 'password' ? '👁️' : '🙈';
  });

  passwordInput.addEventListener('input', function () {
    const val = passwordInput.value;

    const validations = {
      length: val.length >= 7 && val.length <= 12,
      uppercase: /[A-Z]/.test(val),
      lowercase: /[a-z]/.test(val),
      number: /[0-9]/.test(val),
      special: /[\W_]/.test(val),
    };

    for (let key in validations) {
      rules[key].className = validations[key] ? 'rule-valid' : 'rule-invalid';
    }

    const allPassed = Object.values(validations).every(Boolean);
    submitBtn.disabled = !allPassed;
  });
</script>

</body>
</html>
