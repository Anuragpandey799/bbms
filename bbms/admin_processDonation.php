<?php
require_once 'auth.php';
requireLogin('admin');
require_once 'db.php';

if (isset($_GET['id'], $_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    $validActions = ['approve' => 'Approved', 'reject' => 'Rejected'];

    if (array_key_exists($action, $validActions)) {
        $newStatus = $validActions[$action];

        // Update status in donations table
        $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();
        $stmt->close();

        // Update status in identity_cards table
        $stmt2 = $conn->prepare("UPDATE donation_cards SET status = ? WHERE donation_id = ?");
        $stmt2->bind_param("ss", $newStatus, $id);
        $stmt2->execute();
        $stmt2->close();
    }
}

header("Location: admin_manageDonation.php");
exit;



