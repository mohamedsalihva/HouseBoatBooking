<?php
// Check if user is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}

include '../backend/inc/db_connect.php';
include 'includes/sidebar.php';

// Handle booking confirmation/cancellation actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $booking_id = intval($_GET['id']);
    
    if ($action == 'confirm') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        if ($stmt->execute()) {
            $message = "Booking confirmed successfully!";
        } else {
            $error = "Error confirming booking.";
        }
    } elseif ($action == 'cancel') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        if ($stmt->execute()) {
            $message = "Booking cancelled successfully!";
        } else {
            $error = "Error cancelling booking.";
        }
    }
    
    // Redirect to avoid resubmission
    header("Location: /HouseBoatBooking/admin/bookings.php" . (isset($message) || isset($error) ? "?" . (isset($message) ? "message=" . urlencode($message) : "error=" . urlencode($error)) : ""));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
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
                    <h2>Manage Bookings</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Bookings</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php 
        if (isset($_GET['message'])): 
            $message = $_GET['message'];
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php 
        if (isset($_GET['error'])): 
            $error = $_GET['error'];
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Revenue Summary -->
        <?php
        // Fetch revenue statistics
        $total_stmt = $conn->prepare("SELECT COUNT(*) as total_bookings, SUM(total_price) as total_revenue FROM bookings WHERE status = 'confirmed'");
        $total_stmt->execute();
        $total_result = $total_stmt->get_result();
        $total_stats = $total_result->fetch_assoc();
        
        $monthly_stmt = $conn->prepare("SELECT COUNT(*) as monthly_bookings, SUM(total_price) as monthly_revenue FROM bookings WHERE status = 'confirmed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $monthly_stmt->execute();
        $monthly_result = $monthly_stmt->get_result();
        $monthly_stats = $monthly_result->fetch_assoc();
        ?>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h2>₹<?php echo number_format($total_stats['total_revenue'] ?? 0); ?></h2>
                        <p class="mb-0"><?php echo $total_stats['total_bookings'] ?? 0; ?> confirmed bookings</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">30-Day Revenue</h5>
                        <h2>₹<?php echo number_format($monthly_stats['monthly_revenue'] ?? 0); ?></h2>
                        <p class="mb-0"><?php echo $monthly_stats['monthly_bookings'] ?? 0; ?> confirmed bookings</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Booking List</h5>
            </div>
            <div class="card-body">
                <?php
                // Fetch all bookings with user and boat details
                $stmt = $conn->prepare("SELECT b.*, u.name as username, u.email, bt.boat_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN boats bt ON b.boat_id = bt.id ORDER BY b.created_at DESC");
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    echo '<div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>User</th>
                                        <th>Boat</th>
                                        <th>Dates</th>
                                        <th>Guests</th>
                                        <th>Total Price</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Booked On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    
                    while ($row = $result->fetch_assoc()) {
                        // Format dates properly
                        $checkin_date = $row['checkin_date'];
                        $checkout_date = $row['checkout_date'];
                        
                        // Debug output to see what we're getting from the database
                        error_log("Admin Booking ID: " . $row['id'] . " - Checkin: " . $checkin_date . " - Checkout: " . $checkout_date);
                        
                        // Format the dates for display with more robust checking
                        if (!empty($checkin_date) && $checkin_date !== '0000-00-00' && $checkin_date !== '1970-01-01') {
                            // Try to format the date, if it fails, show the raw value
                            $formatted_checkin = @date('M j, Y', strtotime($checkin_date));
                            if ($formatted_checkin === false) {
                                $formatted_checkin = $checkin_date;
                            }
                        } else {
                            $formatted_checkin = 'Not specified';
                        }
                        
                        if (!empty($checkout_date) && $checkout_date !== '0000-00-00' && $checkout_date !== '1970-01-01') {
                            // Try to format the date, if it fails, show the raw value
                            $formatted_checkout = @date('M j, Y', strtotime($checkout_date));
                            if ($formatted_checkout === false) {
                                $formatted_checkout = $checkout_date;
                            }
                        } else {
                            $formatted_checkout = 'Not specified';
                        }
                        
                        // Calculate nights only if both dates are valid
                        $nights = 'N/A';
                        if (!empty($checkin_date) && $checkin_date !== '0000-00-00' && 
                            !empty($checkout_date) && $checkout_date !== '0000-00-00') {
                            $checkin_timestamp = strtotime($checkin_date);
                            $checkout_timestamp = strtotime($checkout_date);
                            
                            if ($checkin_timestamp !== false && $checkout_timestamp !== false) {
                                $nights = ceil(($checkout_timestamp - $checkin_timestamp) / (60 * 60 * 24));
                                // Ensure we don't have negative nights
                                if ($nights < 0) $nights = 0;
                            }
                        }
                        
                        echo '<tr>
                                <td>' . $row['id'] . '</td>
                                <td>
                                    <strong>' . htmlspecialchars($row['username']) . '</strong><br>
                                    <small class="text-muted">' . htmlspecialchars($row['email']) . '</small>
                                </td>
                                <td>' . htmlspecialchars($row['boat_name']) . '</td>
                                <td>
                                    ' . $formatted_checkin . ' - ' . $formatted_checkout . '<br>
                                    <small class="text-muted">' . $nights . ' nights</small>
                                </td>
                                <td>' . $row['guests'] . '</td>
                                <td>₹' . number_format($row['total_price']) . '</td>
                                <td>
                                    <strong>' . ucfirst(str_replace('_', ' ', $row['payment_method'] ?? 'N/A')) . '</strong><br>
                                    <small class="text-muted">' . ucfirst($row['payment_status'] ?? 'N/A') . '</small><br>
                                    <small class="text-muted">' . ($row['transaction_id'] ?? 'N/A') . '</small>
                                </td>
                                <td>
                                    <span class="badge bg-' . ($row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'cancelled' ? 'danger' : 'warning')) . '">
                                        ' . ucfirst($row['status']) . '
                                    </span>
                                </td>
                                <td>' . date('M j, Y', strtotime($row['created_at'] ?? 'now')) . '</td>
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <a href="?action=confirm&id=' . $row['id'] . '" class="btn btn-sm btn-success ' . ($row['status'] == 'confirmed' ? 'disabled' : '') . '">
                                            <i class="bi bi-check-circle"></i> Confirm
                                        </a>
                                        <a href="?action=cancel&id=' . $row['id'] . '" class="btn btn-sm btn-danger ' . ($row['status'] == 'cancelled' ? 'disabled' : '') . '" onclick="return confirm(\'Are you sure you want to cancel this booking?\')">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </a>
                                    </div>
                                </td>
                              </tr>';
                    }
                    
                    echo '</tbody>
                          </table>
                        </div>';
                } else {
                    echo '<div class="text-center py-5">
                            <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                            <h4 class="mt-3">No bookings found</h4>
                            <p class="text-muted">There are no bookings in the system yet.</p>
                          </div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>