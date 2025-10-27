<?php
session_start();
include '../../../backend/inc/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Check if booking details exist
if (!isset($_SESSION['booking_payment'])) {
    header("Location: ../boats.php");
    exit();
}

$booking_details = $_SESSION['booking_payment'];

// Generate a unique order ID
$order_id = 'order_' . time() . '_' . rand(1000, 9999);
$amount = $booking_details['total_price'] * 100; // Convert to paise
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing - Kerala Cruises</title>
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
                        <li class="breadcrumb-item"><a href="../booking/booking.php?boat_id=<?php echo $booking_details['boat_id'] ?? ''; ?>">Booking</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Payment</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="bi bi-credit-card"></i> Payment Confirmation</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-check" style="font-size: 3rem; color: #007bff;"></i>
                            <h5 class="mt-3">Confirm Your Booking Payment</h5>
                            <p class="text-muted">Complete your booking by confirming payment</p>
                        </div>

                        <h5 class="mb-3">Booking Details</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Order ID</th>
                                        <td><strong><?php echo htmlspecialchars($order_id); ?></strong></td>
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
                                </tbody>
                            </table>
                        </div>

                        <!-- Payment Details Form -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-credit-card"></i> Payment Information</h6>
                            <p class="mb-3">Please provide your payment details below to complete the booking.</p>
                            
                            <form method="POST" action="process_payment.php" id="payment-form">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
                                <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
                                
                                <!-- Dynamic Payment Fields Based on Selected Method -->
                                <?php if ($booking_details['payment_method'] === 'credit_card' || $booking_details['payment_method'] === 'debit_card'): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Card Number</label>
                                        <input type="text" class="form-control" name="card_number" placeholder="1234 5678 9012 3456" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Expiry Date</label>
                                            <input type="text" class="form-control" name="card_expiry" placeholder="MM/YY" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">CVV</label>
                                            <input type="text" class="form-control" name="card_cvv" placeholder="123" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Cardholder Name</label>
                                        <input type="text" class="form-control" name="cardholder_name" placeholder="John Doe" required>
                                    </div>
                                <?php elseif ($booking_details['payment_method'] === 'upi'): ?>
                                    <div class="mb-3">
                                        <label class="form-label">UPI ID</label>
                                        <input type="text" class="form-control" name="upi_id" placeholder="username@bank" required>
                                    </div>
                                <?php elseif ($booking_details['payment_method'] === 'net_banking'): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Bank Name</label>
                                        <select class="form-select" name="bank_name" required>
                                            <option value="">Select your bank</option>
                                            <option value="sbi">State Bank of India</option>
                                            <option value="hdfc">HDFC Bank</option>
                                            <option value="icici">ICICI Bank</option>
                                            <option value="axis">Axis Bank</option>
                                            <option value="kotak">Kotak Mahindra Bank</option>
                                            <option value="other">Other Bank</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Account Number</label>
                                        <input type="text" class="form-control" name="account_number" placeholder="Enter account number" required>
                                    </div>
                                <?php elseif ($booking_details['payment_method'] === 'paypal'): ?>
                                    <div class="mb-3">
                                        <label class="form-label">PayPal Email</label>
                                        <input type="email" class="form-control" name="paypal_email" placeholder="your.email@example.com" required>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg" id="payment-button">
                                        <i class="bi bi-lock"></i> Confirm Payment of ₹<?php echo number_format($booking_details['total_price']); ?>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <a href="../boats.php" class="btn btn-secondary">Cancel Payment</a>
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
        
        // Add loading state to payment button
        var paymentForm = document.getElementById('payment-form');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function() {
                var paymentButton = document.getElementById('payment-button');
                if (paymentButton) {
                    paymentButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                    paymentButton.disabled = true;
                }
            });
        }
    });
    </script>
</body>
</html>