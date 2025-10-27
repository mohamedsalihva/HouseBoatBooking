<?php
// Check if user is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}

include '../backend/inc/db_connect.php';
include 'includes/sidebar.php';

// Fetch statistics
$boats_stmt = $conn->prepare("SELECT COUNT(*) as total_boats FROM boats");
$boats_stmt->execute();
$boats_result = $boats_stmt->get_result();
$boats_count = $boats_result->fetch_assoc()['total_boats'];

$bookings_stmt = $conn->prepare("SELECT COUNT(*) as total_bookings FROM bookings");
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();
$bookings_count = $bookings_result->fetch_assoc()['total_bookings'];

$revenue_stmt = $conn->prepare("SELECT SUM(total_price) as total_revenue FROM bookings WHERE status = 'confirmed'");
$revenue_stmt->execute();
$revenue_result = $revenue_stmt->get_result();
$total_revenue = $revenue_result->fetch_assoc()['total_revenue'] ?? 0;

$users_stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM users");
$users_stmt->execute();
$users_result = $users_stmt->get_result();
$users_count = $users_result->fetch_assoc()['total_users'];

// Fetch recent bookings
$recent_stmt = $conn->prepare("SELECT b.*, u.name as username, bt.boat_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN boats bt ON b.boat_id = bt.id ORDER BY b.created_at DESC LIMIT 5");
$recent_stmt->execute();
$recent_result = $recent_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/HouseBoatBooking/admin/css/dashboard.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Dashboard</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Boats</h5>
                                <h2><?php echo $boats_count; ?></h2>
                            </div>
                            <i class="bi bi-boat" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Bookings</h5>
                                <h2><?php echo $bookings_count; ?></h2>
                            </div>
                            <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Revenue</h5>
                                <h2>₹<?php echo number_format($total_revenue); ?></h2>
                            </div>
                            <i class="bi bi-currency-rupee" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Total Users</h5>
                                <h2><?php echo $users_count; ?></h2>
                            </div>
                            <i class="bi bi-people" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Bookings</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>User</th>
                                            <th>Boat</th>
                                            <th>Dates</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th>Booked On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $recent_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo htmlspecialchars($row['boat_name']); ?></td>
                                                <td>
                                                    <?php echo date('M j', strtotime($row['checkin_date'])); ?> - <?php echo date('M j', strtotime($row['checkout_date'])); ?>
                                                </td>
                                                <td>₹<?php echo number_format($row['total_price']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo ($row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'cancelled' ? 'danger' : 'warning')); ?>">
                                                        <?php echo ucfirst($row['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                                <h4 class="mt-3">No bookings found</h4>
                                <p class="text-muted">There are no bookings in the system yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>