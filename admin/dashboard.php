<?php
session_start();

// Restrict access to only admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="dashboard.css" rel="stylesheet">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="p-3 border-bottom">Admin Panel</h4>
        <a href="dashboard.php">Dashboard</a>
        <a href="#">Manage Users</a>
        <a href="#">Manage Bookings</a>
        <a href="#">Reports</a>
        <a href="/HouseBoatBooking/backend/logout.php">Logout</a>
    </div>

    <!-- Main content -->
    <div class="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-custom mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Welcome, <?php echo $_SESSION['username']; ?></a>
            </div>
        </nav>

        <!-- Dashboard Cards -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text fs-4">120</p>
                        <a href="#" class="btn btn-primary btn-sm">View</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Total Bookings</h5>
                        <p class="card-text fs-4">58</p>
                        <a href="#" class="btn btn-success btn-sm">View</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Pending Requests</h5>
                        <p class="card-text fs-4">6</p>
                        <a href="#" class="btn btn-warning btn-sm">View</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Example -->
        <div class="mt-5">
            <h4>Recent Bookings</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Houseboat</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>Luxury Boat</td>
                            <td>2025-08-10</td>
                            <td><span class="badge bg-success">Confirmed</span></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>Sunset Cruise</td>
                            <td>2025-08-12</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Michael</td>
                            <td>Royal Yacht</td>
                            <td>2025-08-15</td>
                            <td><span class="badge bg-danger">Cancelled</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
