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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Reports</title>
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
                    <h2>Revenue Reports</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Reports</li>
                        </ol>
                    </nav>
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
                                        <td>₹<?php echo number_format($data['revenue']); ?></td>
                                        <td>₹<?php echo number_format($data['revenue'] / max(1, $data['bookings'])); ?></td>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize chart
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('revenueChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [<?php 
                        $labels = array();
                        foreach ($report_data as $data) {
                            $labels[] = "'{$data['period']}'";
                        }
                        echo implode(',', $labels);
                    ?>],
                    datasets: [{
                        label: 'Revenue (₹)',
                        data: [<?php 
                            $values = array();
                            foreach ($report_data as $data) {
                                $values[] = $data['revenue'];
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
    </script>
</body>
</html>