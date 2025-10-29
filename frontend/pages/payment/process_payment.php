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

// Validate and format dates properly - ensure we're getting dates from session data
$checkin_date = isset($booking_data['checkin_date']) ? $booking_data['checkin_date'] : '';
$checkout_date = isset($booking_data['checkout_date']) ? $booking_data['checkout_date'] : '';

// More robust date validation and formatting
// Handle check-in date
if (!empty($checkin_date) && $checkin_date !== '0000-00-00' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin_date)) {
    // Validate that it's a real date
    $timestamp = strtotime($checkin_date);
    if ($timestamp === false || date('Y-m-d', $timestamp) !== $checkin_date) {
        // If conversion fails, use today's date as fallback
        $checkin_date = date('Y-m-d');
    }
} else {
    // If checkin_date is invalid, use today's date as fallback
    $checkin_date = date('Y-m-d');
}

// Handle check-out date
if (!empty($checkout_date) && $checkout_date !== '0000-00-00' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout_date)) {
    // Validate that it's a real date
    $timestamp = strtotime($checkout_date);
    if ($timestamp === false || date('Y-m-d', $timestamp) !== $checkout_date) {
        // If conversion fails, use tomorrow's date as fallback
        $checkout_date = date('Y-m-d', strtotime('+1 day'));
    }
} else {
    // If checkout_date is invalid, use tomorrow's date as fallback
    $checkout_date = date('Y-m-d', strtotime('+1 day'));
}

// ... existing code ...

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

// Validate dates before database insertion
if (empty($checkin_date) || $checkin_date === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin_date)) {
    $checkin_date = date('Y-m-d'); // Use today as fallback
}

if (empty($checkout_date) || $checkout_date === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout_date)) {
    $checkout_date = date('Y-m-d', strtotime('+1 day')); // Use tomorrow as fallback
}

// Log before database insertion
PaymentLogger::log("Attempting database insertion", [
    'transaction_id' => $transaction_id,
    'payment_status' => $payment_status,
    'payment_method' => $booking_data['payment_method'],
    'payment_details_provided' => !empty($payment_details),
    'checkin_date' => $checkin_date,
    'checkout_date' => $checkout_date
]);

// Validate dates one more time before database insertion
$validated_checkin = $checkin_date;
$validated_checkout = $checkout_date;

// ... existing code ...

// Ensure checkin date is valid
if (empty($validated_checkin) || $validated_checkin === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $validated_checkin)) {
    $validated_checkin = date('Y-m-d');
}

// Ensure checkout date is valid
if (empty($validated_checkout) || $validated_checkout === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $validated_checkout)) {
    $validated_checkout = date('Y-m-d', strtotime('+1 day'));
}

// Ensure checkout is after checkin
if ($validated_checkout <= $validated_checkin) {
    $validated_checkout = date('Y-m-d', strtotime($validated_checkin . ' +1 day'));
}

// ... existing code ...

// Insert booking into database with payment information
// Fixed the bind_param to match the correct data types and order
error_log("About to insert booking - user_id: " . $booking_data['user_id'] . ", boat_id: " . $booking_data['boat_id'] . ", checkin_date: $validated_checkin, checkout_date: $validated_checkout, guests: " . $booking_data['guests'] . ", total_price: " . $booking_data['total_price'] . ", payment_method: " . $booking_data['payment_method'] . ", payment_status: $payment_status, transaction_id: $transaction_id, status: " . $booking_data['status']);

// Use direct SQL insertion for dates to avoid prepared statement issues
$sql = "INSERT INTO bookings (user_id, boat_id, checkin_date, checkout_date, guests, total_price, payment_method, payment_status, transaction_id, status) VALUES (?, ?, '$validated_checkin', '$validated_checkout', ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Make sure we're binding the parameters correctly
// user_id (int), boat_id (int), guests (int), 
// total_price (double), payment_method (string), payment_status (string), transaction_id (string), status (string)
// Note: checkin_date and checkout_date are included directly in the SQL
$stmt->bind_param("iisdssss", 
    $booking_data['user_id'], 
    $booking_data['boat_id'], 
    $booking_data['guests'], 
    $booking_data['total_price'], 
    $booking_data['payment_method'], 
    $payment_status, 
    $transaction_id, 
    $booking_data['status']
);

if ($stmt->execute()) {
    $booking_id = $conn->insert_id;
    
    // Log successful insertion
    PaymentLogger::logPaymentSuccess($booking_id, $transaction_id);
    
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
    header("Location: ../booking/success.php");
    exit();
} else {
    $error_message = "Database error: " . $conn->error;
    PaymentLogger::logPaymentError($error_message, $booking_data);
    $_SESSION['error'] = "There was an error processing your booking. Please try again. Error: " . $conn->error;
    header("Location: ../booking/booking.php?boat_id=" . $booking_data['boat_id']);
    exit();
}

exit();
?>