<?php
session_start();
include '../../../backend/inc/db_connect.php';

error_log("Booking success page started");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in, redirecting to login");
    header("Location: ../../login/login.php");
    exit();
}

// Check if booking details exist
if (!isset($_SESSION['booking_details'])) {
    error_log("No booking details in session, redirecting to boats");
    header("Location: ../boats.php");
    exit();
}

$booking_details = $_SESSION['booking_details'];

error_log("Booking details retrieved: " . json_encode($booking_details));

// Clear booking details from session
unset($_SESSION['booking_details']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Kerala Cruises</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include '../../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../../index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="../boats.php">Boats</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Booking Confirmation</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header <?php echo $booking_details['status'] == 'confirmed' ? 'bg-success' : 'bg-warning'; ?> text-white text-center">
                        <h4 class="mb-0">
                            <i class="bi bi-<?php echo $booking_details['status'] == 'confirmed' ? 'check-circle' : 'info-circle'; ?>"></i> 
                            Booking <?php echo ucfirst($booking_details['status']); ?>!
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-<?php echo $booking_details['status'] == 'confirmed' ? 'check-circle' : 'info-circle'; ?>" 
                               style="font-size: 4rem; color: <?php echo $booking_details['status'] == 'confirmed' ? '#28a745' : '#ffc107'; ?>;"></i>
                            <h5 class="mt-3">
                                <?php if ($booking_details['status'] == 'confirmed'): ?>
                                    Thank you for your booking!
                                <?php else: ?>
                                    Booking Submitted for Review
                                <?php endif; ?>
                            </h5>
                            <p class="text-muted">
                                <?php if ($booking_details['status'] == 'confirmed'): ?>
                                    Your booking is confirmed and payment has been processed successfully.
                                <?php else: ?>
                                    We will contact you shortly to confirm the details.
                                <?php endif; ?>
                            </p>
                        </div>

                        <h5 class="mb-3">Booking Confirmation</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Confirmation Number</th>
                                        <td><strong>BKG-<?php echo htmlspecialchars($booking_details['booking_id']); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Boat Name</th>
                                        <td><?php echo htmlspecialchars($booking_details['boat_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Check-in Date</th>
                                        <td><?php echo htmlspecialchars($booking_details['checkin_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Check-out Date</th>
                                        <td><?php echo htmlspecialchars($booking_details['checkout_date']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Number of Guests</th>
                                        <td><?php echo htmlspecialchars($booking_details['guests']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Total Price</th>
                                        <td><strong>₹<?php echo number_format($booking_details['total_price']); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method</th>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $booking_details['payment_method'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <td><?php echo htmlspecialchars($booking_details['transaction_id']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Booking Status</th>
                                        <td>
                                            <span class="badge bg-<?php echo $booking_details['status'] == 'confirmed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($booking_details['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if (!empty($booking_details['requests'])): ?>
                                    <tr>
                                        <th>Special Requests</th>
                                        <td><?php echo nl2br(htmlspecialchars($booking_details['requests'])); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($booking_details['status'] == 'confirmed'): ?>
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle"></i> Booking Confirmed</h6>
                            <p class="mb-0">Your booking is confirmed and payment has been successfully processed. A confirmation email has been sent to your registered email address.</p>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Next Steps</h6>
                            <ul class="mb-0">
                                <li>Check your email for booking confirmation and details</li>
                                <li>Arrive at the designated time on your check-in date</li>
                                <li>Bring a copy of this confirmation and a valid ID</li>
                            </ul>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-info-circle"></i> Pending Confirmation</h6>
                            <p class="mb-0">Your booking has been submitted and is pending confirmation. Our team will review your booking and contact you within 24 hours. You will receive a confirmation email once your booking is confirmed.</p>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between">
                            <a href="../boats.php" class="btn btn-primary"><i class="bi bi-boat"></i> Browse More Boats</a>
                            <a href="../user/bookings.php" class="btn btn-secondary"><i class="bi bi-list"></i> My Bookings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Initialize dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
    });
    </script>
</body>
</html>