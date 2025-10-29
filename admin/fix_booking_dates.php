<?php
// Admin script to fix invalid booking dates in the database
session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('Access denied. Admin only.');
}

include '../backend/inc/db_connect.php';

function fixBookingDates($conn) {
    $results = [];
    
    // Find bookings with invalid checkin dates
    $stmt = $conn->prepare("SELECT id, checkout_date FROM bookings WHERE checkin_date = '0000-00-00' OR checkin_date IS NULL");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $fixed_count = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $booking_id = $row['id'];
            $checkout_date = $row['checkout_date'];
            
            // Set checkin date to one day before checkout date
            $checkin_date = date('Y-m-d', strtotime($checkout_date . ' -1 day'));
            
            // Update the booking
            $update_stmt = $conn->prepare("UPDATE bookings SET checkin_date = ? WHERE id = ?");
            $update_stmt->bind_param("si", $checkin_date, $booking_id);
            
            if ($update_stmt->execute()) {
                $results[] = "Fixed booking ID $booking_id: set checkin date to $checkin_date";
                $fixed_count++;
            } else {
                $results[] = "Error fixing booking ID $booking_id: " . $conn->error;
            }
            
            $update_stmt->close();
        }
    }
    
    // Check for bookings where checkin date is after or equal to checkout date
    $stmt = $conn->prepare("SELECT id, checkin_date, checkout_date FROM bookings WHERE checkin_date >= checkout_date");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $booking_id = $row['id'];
            $checkout_date = $row['checkout_date'];
            
            // Set checkin date to one day before checkout date
            $checkin_date = date('Y-m-d', strtotime($checkout_date . ' -1 day'));
            
            // Update the booking
            $update_stmt = $conn->prepare("UPDATE bookings SET checkin_date = ? WHERE id = ?");
            $update_stmt->bind_param("si", $checkin_date, $booking_id);
            
            if ($update_stmt->execute()) {
                $results[] = "Fixed booking ID $booking_id: set checkin date to $checkin_date";
                $fixed_count++;
            } else {
                $results[] = "Error fixing booking ID $booking_id: " . $conn->error;
            }
            
            $update_stmt->close();
        }
    }
    
    if ($fixed_count == 0) {
        $results[] = "No bookings with invalid dates found.";
    } else {
        $results[] = "Fixed $fixed_count bookings with invalid dates.";
    }
    
    return $results;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_dates'])) {
    $results = fixBookingDates($conn);
    $message = "Date fixing process completed.";
} else {
    $results = [];
    $message = "";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Booking Dates - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Fix Booking Dates</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="bookings.php">Bookings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Fix Booking Dates</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Fix Invalid Booking Dates</h5>
                    </div>
                    <div class="card-body">
                        <p>This tool will fix bookings with invalid dates in the database:</p>
                        <ul>
                            <li>Bookings with checkin_date = '0000-00-00'</li>
                            <li>Bookings with checkin_date IS NULL</li>
                            <li>Bookings where checkin_date is after or equal to checkout_date</li>
                        </ul>
                        <p>For each invalid booking, the system will set the checkin_date to one day before the checkout_date.</p>
                        
                        <form method="POST">
                            <button type="submit" name="fix_dates" class="btn btn-primary">
                                <i class="bi bi-tools"></i> Fix Booking Dates
                            </button>
                        </form>
                        
                        <?php if (!empty($message)): ?>
                            <div class="mt-4">
                                <h5><?php echo htmlspecialchars($message); ?></h5>
                                <?php if (!empty($results)): ?>
                                    <ul class="list-group">
                                        <?php foreach ($results as $result): ?>
                                            <li class="list-group-item"><?php echo htmlspecialchars($result); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>