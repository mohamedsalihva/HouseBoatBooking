<?php
// Ensure session is properly started with correct parameters
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
include '../../../backend/inc/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Store the current URL to redirect back after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ../../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Kerala Cruises</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include '../../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">My Bookings</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/HouseBoatBooking/index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Bookings</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Booking History</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Fetch user's bookings
                        $stmt = $conn->prepare("SELECT b.*, bt.boat_name, bt.boat_type FROM bookings b JOIN boats bt ON b.boat_id = bt.id WHERE b.user_id = ? ORDER BY b.created_at DESC");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            echo '<div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Booking ID</th>
                                                <th>Boat</th>
                                                <th>Dates</th>
                                                <th>Guests</th>
                                                <th>Total Price</th>
                                                <th>Payment</th>
                                                <th>Status</th>
                                                <th>Booked On</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                            
                            while ($row = $result->fetch_assoc()) {
                                // Debug output to see what we're getting from the database
                                error_log("Displaying booking ID: " . $row['id'] . " - Checkin: " . $row['checkin_date'] . " - Checkout: " . $row['checkout_date']);
                                
                                // Format dates properly
                                $checkin_date = $row['checkin_date'];
                                $checkout_date = $row['checkout_date'];
                                
                                // Format the dates for display with more robust checking
                                if (!empty($checkin_date) && $checkin_date !== '0000-00-00' && $checkin_date !== '1970-01-01' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin_date)) {
                                    // Try to format the date, if it fails, show the raw value
                                    $formatted_checkin = @date('M j, Y', strtotime($checkin_date));
                                    if ($formatted_checkin === false) {
                                        $formatted_checkin = $checkin_date;
                                    }
                                } else {
                                    $formatted_checkin = 'Not specified';
                                }
                                
                                if (!empty($checkout_date) && $checkout_date !== '0000-00-00' && $checkout_date !== '1970-01-01' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout_date)) {
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
                                if (!empty($checkin_date) && $checkin_date !== '0000-00-00' && $checkin_date !== '1970-01-01' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin_date) &&
                                    !empty($checkout_date) && $checkout_date !== '0000-00-00' && $checkout_date !== '1970-01-01' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout_date)) {
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
                                            <strong>' . htmlspecialchars($row['boat_name']) . '</strong><br>
                                            <small class="text-muted">' . ucfirst(htmlspecialchars($row['boat_type'])) . '</small>
                                        </td>
                                        <td>
                                            <strong>' . $formatted_checkin . '</strong> to <strong>' . $formatted_checkout . '</strong><br>
                                            <small class="text-muted">' . $nights . ' nights</small>
                                        </td>
                                        <td>' . $row['guests'] . '</td>
                                        <td>₹' . number_format($row['total_price']) . '</td>
                                        <td>
                                            <strong>' . ucfirst(str_replace('_', ' ', $row['payment_method'] ?? 'N/A')) . '</strong><br>
                                            <small class="text-muted">TXN: ' . ($row['transaction_id'] ?? 'N/A') . '</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-' . ($row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'cancelled' ? 'danger' : 'warning')) . '">
                                                ' . ucfirst($row['status']) . '
                                            </span>
                                        </td>
                                        <td>' . date('M j, Y', strtotime($row['created_at'] ?? 'now')) . '</td>
                                      </tr>';
                            }
                            
                            echo '</tbody>
                                  </table>
                                </div>';
                        } else {
                            echo '<div class="text-center py-5">
                                    <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                                    <h4 class="mt-3">No bookings found</h4>
                                    <p class="text-muted">You haven\'t made any bookings yet.</p>
                                    <a href="../boats.php" class="btn btn-primary">
                                        <i class="bi bi-boat"></i> Book a Houseboat
                                    </a>
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Initialize dropdowns -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
    });
    </script>
</body>
</html>