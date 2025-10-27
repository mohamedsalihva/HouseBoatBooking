<?php
session_start();
include '../../../backend/inc/db_connect.php';
include '../../../backend/inc/logger.php';

// Log the payment attempt
PaymentLogger::log("Process payment started", [
    'session_user_id' => $_SESSION['user_id'] ?? null,
    'post_params' => $_POST,
    'get_params' => $_GET,
    'session_booking_payment' => isset($_SESSION['booking_payment']) ? 'exists' : 'missing'
]);

// Add debugging output
error_log("Process payment started - User ID: " . ($_SESSION['user_id'] ?? 'null'));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    PaymentLogger::logPaymentError("User not logged in");
    error_log("User not logged in, redirecting to login");
    header("Location: ../../login/login.php");
    exit();
}

// Check if booking payment details exist
if (!isset($_SESSION['booking_payment'])) {
    PaymentLogger::logPaymentError("No booking payment data in session");
    error_log("No booking payment data in session, redirecting to boats");
    header("Location: ../boats.php");
    exit();
}

$booking_data = $_SESSION['booking_payment'];

// Log the booking data
PaymentLogger::logPaymentAttempt($booking_data, $_POST);
error_log("Booking data: " . json_encode($booking_data));

// Extract payment details based on payment method
$payment_details = [];
if ($booking_data['payment_method'] === 'credit_card' || $booking_data['payment_method'] === 'debit_card') {
    $payment_details = [
        'card_number' => isset($_POST['card_number']) ? $_POST['card_number'] : '',
        'card_expiry' => isset($_POST['card_expiry']) ? $_POST['card_expiry'] : '',
        'card_cvv' => isset($_POST['card_cvv']) ? $_POST['card_cvv'] : '',
        'cardholder_name' => isset($_POST['cardholder_name']) ? $_POST['cardholder_name'] : ''
    ];
} elseif ($booking_data['payment_method'] === 'upi') {
    $payment_details = [
        'upi_id' => isset($_POST['upi_id']) ? $_POST['upi_id'] : ''
    ];
} elseif ($booking_data['payment_method'] === 'net_banking') {
    $payment_details = [
        'bank_name' => isset($_POST['bank_name']) ? $_POST['bank_name'] : '',
        'account_number' => isset($_POST['account_number']) ? $_POST['account_number'] : ''
    ];
} elseif ($booking_data['payment_method'] === 'paypal') {
    $payment_details = [
        'paypal_email' => isset($_POST['paypal_email']) ? $_POST['paypal_email'] : ''
    ];
}

// For this simplified payment system, we'll assume payment is successful
$payment_status = 'completed';
$transaction_id = 'TXN' . time() . rand(1000, 9999);

// Log before database insertion
PaymentLogger::log("Attempting database insertion", [
    'transaction_id' => $transaction_id,
    'payment_status' => $payment_status,
    'payment_method' => $booking_data['payment_method'],
    'payment_details_provided' => !empty($payment_details)
]);
error_log("Attempting database insertion with transaction ID: " . $transaction_id);

// Insert booking into database with payment information
$stmt = $conn->prepare("INSERT INTO bookings (user_id, boat_id, checkin_date, checkout_date, guests, total_price, special_requests, payment_method, payment_status, transaction_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiissdsssss", 
    $booking_data['user_id'], 
    $booking_data['boat_id'], 
    $booking_data['checkin_date'], 
    $booking_data['checkout_date'], 
    $booking_data['guests'], 
    $booking_data['total_price'], 
    $booking_data['requests'], 
    $booking_data['payment_method'], 
    $payment_status, 
    $transaction_id, 
    $booking_data['status']
);

if ($stmt->execute()) {
    $booking_id = $conn->insert_id;
    
    // Log successful insertion
    PaymentLogger::logPaymentSuccess($booking_id, $transaction_id);
    error_log("Database insertion successful, booking ID: " . $booking_id);
    
    // Add booking ID to session data
    $booking_data['booking_id'] = $booking_id;
    $booking_data['transaction_id'] = $transaction_id;
    $booking_data['payment_status'] = $payment_status;
    $booking_data['payment_details'] = $payment_details; // Store payment details for reference
    
    $_SESSION['booking_details'] = $booking_data;
    
    // Clear payment data from session
    unset($_SESSION['booking_payment']);
    
    // Redirect to success page
    PaymentLogger::log("Redirecting to booking success", ['booking_id' => $booking_id]);
    error_log("Redirecting to booking_success.php with booking ID: " . $booking_id);
    header("Location: ../booking/success.php");
    exit();
} else {
    $error_message = "Database error: " . $conn->error;
    PaymentLogger::logPaymentError($error_message, $booking_data);
    error_log("Database error: " . $conn->error);
    $_SESSION['error'] = "There was an error processing your booking. Please try again. Error: " . $conn->error;
    header("Location: ../booking/booking.php?boat_id=" . $booking_data['boat_id']);
    exit();
}

exit();
?>