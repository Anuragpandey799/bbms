<?php
require_once 'auth.php';
requireLogin('user');
require_once 'db.php';

$results = [];
$blood_group = '';
$location = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blood_group = $_POST['blood_group'];
    $location = trim($_POST['location']);

    $query = "SELECT * FROM donations WHERE blood_group = ? AND location LIKE ? AND status = 'Approved'";
    $stmt = $conn->prepare($query);
    $likeLocation = "%" . $location . "%";
    $stmt->bind_param("ss", $blood_group, $likeLocation);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Blood</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .form-wrapper {
            background-color: #ffffffd9;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .table th, .table td {
            vertical-align: middle;
        }

        @media (max-width: 576px) {
            .form-wrapper {
                padding: 1rem;
            }

            .table th, .table td {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'public_sidebar.php'; ?>

<div class="container my-5">
    <div class="form-wrapper mx-auto" style="max-width: 900px;">
        <h2 class="text-center text-danger fw-bold mb-4">Search for Blood</h2>

        <form method="POST" class="row g-3 justify-content-center mb-4">
            <div class="col-12 col-md-4">
                <select name="blood_group" class="form-select" required>
                    <option value="">Select Blood Group</option>
                    <?php
                    $groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                    foreach ($groups as $group) {
                        $selected = ($blood_group == $group) ? 'selected' : '';
                        echo "<option value=\"$group\" $selected>$group</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <input type="text" name="location" class="form-control" placeholder="Enter Location" required value="<?= htmlspecialchars($location) ?>" />
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-danger w-100">Search</button>
            </div>
        </form>

        <?php if (!empty($results) && $results->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Donor Name</th>
                            <th>Blood Group</th>
                            <th>Location</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['blood_group']) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td><?= htmlspecialchars($row['contact']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <div class="alert alert-warning text-center">No donors found for the selected criteria.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Theme toggle support (optional) -->
<script>
    function applyTheme() {
        const theme = localStorage.getItem('theme');
        document.body.classList.toggle('dark-mode', theme === 'dark');
    }

    document.addEventListener('DOMContentLoaded', applyTheme);
    window.addEventListener('storage', applyTheme);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
