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

        // Update status in requests table
        $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();
        $stmt->close();

        // Update status in identity_cards table for request cards
        $stmt2 = $conn->prepare("UPDATE request_cards SET status = ? WHERE request_id = ?");
        $stmt2->bind_param("si", $newStatus, $id);
        $stmt2->execute();
        $stmt2->close();
    }
}

header("Location: admin_manageRequest.php");
exit;
?>
