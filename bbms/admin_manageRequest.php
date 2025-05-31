<?php
require_once 'auth.php';
requireLogin('admin');
require_once 'db.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sql = "SELECT r.*, u.username FROM requests r JOIN users u ON r.user_id = u.id ";
if ($search !== '') {
    $sql .= "WHERE r.patient_name LIKE '%$search%' OR r.blood_group LIKE '%$search%' OR r.hospital LIKE '%$search%' ";
}
$sql .= "ORDER BY r.id DESC";
$requests = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Blood Requests - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <style>
        body {
            transition: background-color 0.3s, color 0.3s;
        }

        body.dark-mode {
            background-color: #212529;
            color: white;
        }

        .dark-mode .table {
            background-color: #343a40;
        }

        .dark-mode .table th,
        .dark-mode .table td {
            color: white;
        }

        .navbar-dark-mode {
            background-color: #343a40;
            color: white;
        }

        body.light-mode {
            background-color: #f8f9fa;
            color: black;
        }

        .navbar-light-mode {
            background-color: #f8f9fa;
            color: black;
        }

        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Responsive stacked table for small screens */
        @media (max-width: 768px) {
            table thead {
                display: none;
            }

            table, tbody, tr, td {
                display: block;
                width: 100%;
            }

            tr {
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
                padding: 0.75rem;
            }

            td {
                text-align: right;
                position: relative;
                padding-left: 50%;
                border: none;
                border-bottom: 1px solid #dee2e6;
            }

            td::before {
                position: absolute;
                top: 0.75rem;
                left: 1rem;
                width: 45%;
                padding-right: 1rem;
                white-space: nowrap;
                font-weight: bold;
                content: attr(data-label);
                text-align: left;
            }

            .table-wrapper {
                overflow: visible;
            }
        }

        #themeToggleBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body id="body" >

<?php include 'admin_header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Blood Requests</h2>

    <!-- Search Bar -->
    <div class="d-flex justify-content-end mb-3">
        <form method="get" class="d-flex w-100 w-md-auto">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by patient, blood group, hospital..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
    </div>

    <?php if ($requests->num_rows > 0): ?>
        <div class="table-wrapper shadow-sm p-2 rounded">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Patient Name</th>
                        <th>Age</th>
                        <th>Blood Group</th>
                        <th>Reason</th>
                        <th>Hospital</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($req = $requests->fetch_assoc()): ?>
                        <tr>
                            <td data-label="User"><?= htmlspecialchars($req['username']) ?></td>
                            <td data-label="Patient Name"><?= htmlspecialchars($req['patient_name']) ?></td>
                            <td data-label="Age"><?= $req['age'] ?></td>
                            <td data-label="Blood Group"><?= $req['blood_group'] ?></td>
                            <td data-label="Reason"><?= htmlspecialchars($req['reason']) ?></td>
                            <td data-label="Hospital"><?= htmlspecialchars($req['hospital']) ?></td>
                            <td data-label="Contact"><?= htmlspecialchars($req['contact']) ?></td>
                            <td data-label="Status">
                                <span class="badge <?= 
                                    $req['status'] === 'Approved' ? 'bg-success' : 
                                    ($req['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark')
                                ?>"><?= $req['status'] ?></span>
                            </td>
                            <td data-label="Action">
                                <?php if ($req['status'] === 'Pending'): ?>
                                    <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="<?= $req['id'] ?>" data-action="approve">Approve</button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="<?= $req['id'] ?>" data-action="reject">Reject</button>
                                <?php else: ?>
                                    <span class="text-muted">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No blood requests found.</div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmLabel">Confirm Action</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to <strong id="modalActionText"></strong> this request?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a id="confirmBtn" class="btn btn-primary">Yes, Proceed</a>
      </div>
    </div>
  </div>
</div>


<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script>
    // Modal logic
    const confirmModal = document.getElementById('confirmModal');
    confirmModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const requestId = button.getAttribute('data-id');
        const action = button.getAttribute('data-action');
        const modalText = document.getElementById('modalActionText');
        const confirmBtn = document.getElementById('confirmBtn');

        modalText.textContent = action.charAt(0).toUpperCase() + action.slice(1);
        confirmBtn.href = `admin_processRequest.php?id=${requestId}&action=${action}`;
    });
</script>

</body>
</html>
