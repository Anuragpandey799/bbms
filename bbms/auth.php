<?php
session_start();

// Call this with 'user' or 'admin' to check session
function requireLogin($role = 'user') {
    if (!isset($_SESSION[$role])) {
        if ($role === 'admin') {
            header("Location: admin_login.php");
        } else {
            header("Location: login.php");
        }
        exit();
    }
}
?>
