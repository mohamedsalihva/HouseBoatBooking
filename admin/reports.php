<?php
// Check if user is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}

include '../backend/inc/db_connect.php';
include 'includes/sidebar.php';

// Get filter parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'monthly';
$boat_id = isset($_GET['boat_id']) ? intval($_GET['boat_id']) : 0;
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'revenue';

// Fetch boats for filter dropdown
$boats_stmt = $conn->prepare("SELECT id, boat_name FROM boats ORDER BY boat_name");
$boats_stmt->execute();
$boats_result = $boats_stmt->get_result();
$boats = [];
while ($boat = $boats_result->fetch_assoc()) {
    $boats[] = $boat;
}

// Fetch revenue data based on filter
switch ($filter) {
    case 'daily':
        $group_by = "DATE(b.created_at)";
        $format = "%Y-%m-%d";
        $label = "Daily";
        break;
    case 'weekly':
        $group_by = "YEARWEEK(b.created_at)";
        $format = "%Y-W%u";
        $label = "Weekly";
        break;
    case 'yearly':
        $group_by = "YEAR(b.created_at)";
        $format = "%Y";
        $label = "Yearly";
        break;
    case 'monthly':
    default:
        $group_by = "DATE_FORMAT(b.created_at, '%Y-%m')";
        $format = "%Y-%m";
        $label = "Monthly";
        break;
}

// Build query with boat filter
$where_clause = "b.status = 'confirmed'";
$params = [];
$types = "";

if ($boat_id > 0) {
    $where_clause .= " AND b.boat_id = ?";
    $params[] = $boat_id;
    $types .= "i";
}

// Revenue Report Data
$query = "SELECT 
            DATE_FORMAT(b.created_at, '$format') as period,
            COUNT(*) as bookings,
            SUM(b.total_price) as revenue
          FROM bookings b
          WHERE $where_clause
          GROUP BY $group_by
          ORDER BY $group_by DESC
          LIMIT 12";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$report_data = [];
while ($row = $result->fetch_assoc()) {
    $report_data[] = $row;
}

// Fetch overall statistics
$overall_query = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(total_price) as total_revenue,
                    AVG(total_price) as avg_booking_value
                  FROM bookings 
                  WHERE status = 'confirmed'";

if ($boat_id > 0) {
    $overall_query .= " AND boat_id = ?";
    $overall_stmt = $conn->prepare($overall_query);
    $overall_stmt->bind_param("i", $boat_id);
} else {
    $overall_stmt = $conn->prepare($overall_query);
}

$overall_stmt->execute();
$overall_result = $overall_stmt->get_result();
$overall_stats = $overall_result->fetch_assoc();

// User Report Data
$user_report_query = "SELECT 
                        u.id,
                        u.name,
                        u.email,
                        COUNT(b.id) as total_bookings,
                        COALESCE(SUM(b.total_price), 0) as total_spent,
                        MAX(b.created_at) as last_booking
                      FROM users u
                      LEFT JOIN bookings b ON u.id = b.user_id AND b.status = 'confirmed'
                      GROUP BY u.id, u.name, u.email
                      ORDER BY total_spent DESC
                      LIMIT 10";

$user_report_stmt = $conn->prepare($user_report_query);
$user_report_stmt->execute();
$user_report_result = $user_report_stmt->get_result();
$user_report_data = [];
while ($row = $user_report_result->fetch_assoc()) {
    $user_report_data[] = $row;
}

// Booking Details Report Data
$booking_details_query = "SELECT 
                            b.id,
                            u.name as user_name,
                            bt.boat_name,
                            b.checkin_date,
                            b.checkout_date,
                            b.guests,
                            b.total_price,
                            b.payment_method,
                            b.status,
                            b.created_at
                          FROM bookings b
                          JOIN users u ON b.user_id = u.id
                          JOIN boats bt ON b.boat_id = bt.id
                          WHERE b.status = 'confirmed'";

$booking_params = [];
$booking_types = "";

if ($boat_id > 0) {
    $booking_details_query .= " AND b.boat_id = ?";
    $booking_params[] = $boat_id;
    $booking_types .= "i";
}

$booking_details_query .= " ORDER BY b.created_at DESC LIMIT 50";

$booking_details_stmt = $conn->prepare($booking_details_query);
if (!empty($booking_params)) {
    $booking_details_stmt->bind_param($booking_types, ...$booking_params);
}
$booking_details_stmt->execute();
$booking_details_result = $booking_details_stmt->get_result();
$booking_details_data = [];
while ($row = $booking_details_result->fetch_assoc()) {
    $booking_details_data[] = $row;
}

// Payment Method Analysis
$payment_query = "SELECT 
                    payment_method,
                    COUNT(*) as count,
                    SUM(total_price) as revenue
                  FROM bookings 
                  WHERE status = 'confirmed'";

$payment_params = [];
$payment_types = "";

if ($boat_id > 0) {
    $payment_query .= " AND boat_id = ?";
    $payment_params[] = $boat_id;
    $payment_types .= "i";
}

$payment_query .= " GROUP BY payment_method";

$payment_stmt = $conn->prepare($payment_query);
if (!empty($payment_params)) {
    $payment_stmt->bind_param($payment_types, ...$payment_params);
}
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();
$payment_data = [];
while ($row = $payment_result->fetch_assoc()) {
    $payment_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/HouseBoatBooking/admin/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Comprehensive Reports</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Reports</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Report Type Selector -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Report Type</h5>
            </div>
            <div class="card-body">
                <div class="btn-group" role="group">
                    <a href="?report_type=revenue&filter=<?php echo $filter; ?>&boat_id=<?php echo $boat_id; ?>" 
                       class="btn <?php echo $report_type == 'revenue' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="bi bi-currency-rupee"></i> Revenue Reports
                    </a>
                    <a href="?report_type=users&filter=<?php echo $filter; ?>&boat_id=<?php echo $boat_id; ?>" 
                       class="btn <?php echo $report_type == 'users' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="bi bi-people"></i> User Reports
                    </a>
                    <a href="?report_type=bookings&filter=<?php echo $filter; ?>&boat_id=<?php echo $boat_id; ?>" 
                       class="btn <?php echo $report_type == 'bookings' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="bi bi-calendar-check"></i> Booking Details
                    </a>
                    <a href="?report_type=payment&filter=<?php echo $filter; ?>&boat_id=<?php echo $boat_id; ?>" 
                       class="btn <?php echo $report_type == 'payment' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="bi bi-credit-card"></i> Payment Methods
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Reports</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="report_type" value="<?php echo $report_type; ?>">
                    <div class="col-md-4">
                        <label class="form-label">Time Period</label>
                        <select name="filter" class="form-select">
                            <option value="daily" <?php echo $filter == 'daily' ? 'selected' : ''; ?>>Daily</option>
                            <option value="weekly" <?php echo $filter == 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                            <option value="monthly" <?php echo $filter == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                            <option value="yearly" <?php echo $filter == 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Boat</label>
                        <select name="boat_id" class="form-select">
                            <option value="0">All Boats</option>
                            <?php foreach ($boats as $boat): ?>
                                <option value="<?php echo $boat['id']; ?>" <?php echo $boat_id == $boat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($boat['boat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Apply Filters</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($report_type == 'revenue'): ?>
        <!-- Revenue Reports -->
        <!-- Overall Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Bookings</h6>
                                <h3><?php echo $overall_stats['total_bookings'] ?? 0; ?></h3>
                            </div>
                            <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Revenue</h6>
                                <h3>₹<?php echo number_format($overall_stats['total_revenue'] ?? 0); ?></h3>
                            </div>
                            <i class="bi bi-currency-rupee" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Avg. Booking Value</h6>
                                <h3>₹<?php echo number_format($overall_stats['avg_booking_value'] ?? 0); ?></h3>
                            </div>
                            <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> <?php echo $label; ?> Revenue Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>

        <!-- Revenue Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-table"></i> <?php echo $label; ?> Revenue Details</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($report_data)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                    <th>Avg. Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data as $data): ?>
                                    <tr>
                                        <td><?php echo $data['period']; ?></td>
                                        <td><?php echo $data['bookings']; ?></td>
                                        <td>₹<?php echo number_format($data['revenue'] ?? 0); ?></td>
                                        <td>₹<?php echo number_format(($data['revenue'] ?? 0) / max(1, $data['bookings'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-bar-chart" style="font-size: 3rem; color: #ccc;"></i>
                        <h4 class="mt-3">No data available</h4>
                        <p class="text-muted">There is no revenue data for the selected period.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($report_type == 'users'): ?>
        <!-- User Reports -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-people"></i> Top Users by Spending</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($user_report_data)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Total Bookings</th>
                                    <th>Total Spent</th>
                                    <th>Last Booking</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_report_data as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo $user['total_bookings']; ?></td>
                                        <td>₹<?php echo number_format($user['total_spent']); ?></td>
                                        <td><?php echo $user['last_booking'] ? date('M j, Y', strtotime($user['last_booking'])) : 'N/A'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                        <h4 class="mt-3">No user data available</h4>
                        <p class="text-muted">There is no user data for the selected period.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($report_type == 'bookings'): ?>
        <!-- Booking Details Report -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Recent Bookings</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($booking_details_data)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>User</th>
                                    <th>Boat</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Guests</th>
                                    <th>Total Price</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Booked On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($booking_details_data as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['id']; ?></td>
                                        <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['boat_name']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($booking['checkin_date'])); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($booking['checkout_date'])); ?></td>
                                        <td><?php echo $booking['guests']; ?></td>
                                        <td>₹<?php echo number_format($booking['total_price']); ?></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $booking['payment_method'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'cancelled' ? 'danger' : 'warning'); ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-check" style="font-size: 3rem; color: #ccc;"></i>
                        <h4 class="mt-3">No booking data available</h4>
                        <p class="text-muted">There are no bookings for the selected period.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($report_type == 'payment'): ?>
        <!-- Payment Method Analysis -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-credit-card"></i> Payment Method Analysis</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($payment_data)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th>Number of Transactions</th>
                                    <th>Total Revenue</th>
                                    <th>Avg. Transaction Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payment_data as $payment): ?>
                                    <tr>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                                        <td><?php echo $payment['count']; ?></td>
                                        <td>₹<?php echo number_format($payment['revenue'] ?? 0); ?></td>
                                        <td>₹<?php echo number_format(($payment['revenue'] ?? 0) / max(1, $payment['count'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-credit-card" style="font-size: 3rem; color: #ccc;"></i>
                        <h4 class="mt-3">No payment data available</h4>
                        <p class="text-muted">There is no payment data for the selected period.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize chart for revenue report
        <?php if ($report_type == 'revenue' && !empty($report_data)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('revenueChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [<?php 
                        $labels = array();
                        foreach (array_reverse($report_data) as $data) {
                            $labels[] = "'{$data['period']}'";
                        }
                        echo implode(',', $labels);
                    ?>],
                    datasets: [{
                        label: 'Revenue (₹)',
                        data: [<?php 
                            $values = array();
                            foreach (array_reverse($report_data) as $data) {
                                $values[] = $data['revenue'] ?? 0;
                            }
                            echo implode(',', $values);
                        ?>],
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>