
<!-- not in use admin auth -->

<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['adminUsername'];
    $password = $_POST['adminPassword'];
    $adminCode = $_POST['adminCode'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($admin = $res->fetch_assoc()) {
        if (password_verify($password, $admin['password']) && $admin['admin_code'] === $adminCode) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit();
        }
    }

    echo "<script>alert('Invalid admin credentials'); window.location.href='admin_login.php';</script>";
}
?>
