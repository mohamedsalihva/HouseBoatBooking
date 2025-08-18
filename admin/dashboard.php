<?php include '../backend/inc/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<!-- Sidebar -->
<div class="col-md-2 sidebar">
    <h4 class="text-center py-3">Admin</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="boats.php">View/Edit Boats</a> <!-- New -->
    <a href="bookings.php">Manage Bookings</a>
    <a href="users.php">Manage Users</a>
    <a href="reports.php">Reports</a>
    <a href="../logout.php" class="text-danger">Logout</a>
</div>


<!-- Main Content -->
<div class="content">
    <h2 class="fw-bold">Dashboard Overview</h2>
    <p class="text-muted">Quick summary of the system's status.</p>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center">
                <i class="bi bi-calendar-check"></i>
                <h5 class="mt-3">Total Bookings</h5>
                <h3 class="fw-bold">
                    <?php
                    // Uncomment once bookings table is created
                    // $result_bookings = $conn->query("SELECT COUNT(*) AS total FROM bookings");
                    // echo $result_bookings ? $result_bookings->fetch_assoc()['total'] : 0;
                    echo 0;
                    ?>
                </h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center">
                <i class="bi bi-people"></i>
                <h5 class="mt-3">Total Users</h5>
                <h3 class="fw-bold">
                    <?php
                    $result_users = $conn->query("SELECT COUNT(*) AS total FROM users");
                    echo $result_users ? $result_users->fetch_assoc()['total'] : 0;
                    ?>
                </h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center">
                <i class="bi bi-graph-up"></i>
                <h5 class="mt-3">Reports</h5>
                <h3 class="fw-bold">5</h3>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
