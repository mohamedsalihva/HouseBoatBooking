<?php
session_start();
include '../../../backend/inc/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Check if user is admin - if so, redirect to admin dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: /HouseBoatBooking/admin/dashboard.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../boats.php");
    exit();
}

// Debug output to see what we're receiving
error_log("Booking form data received: " . json_encode($_POST));

$user_id = $_SESSION['user_id'];
$boat_id = intval($_POST['boat_id']);
$checkin_date = $_POST['checkin_date'];
$checkout_date = $_POST['checkout_date'];
$guests = intval($_POST['guests']);
$requests = isset($_POST['requests']) ? trim($_POST['requests']) : '';
$payment_method = $_POST['payment_method'];

// Debug output to see what we've extracted
error_log("Extracted data - Checkin: " . $checkin_date . ", Checkout: " . $checkout_date . ", Boat ID: " . $boat_id);

// Validate payment method
$valid_payment_methods = ['credit_card', 'debit_card', 'upi', 'net_banking', 'paypal'];
if (!in_array($payment_method, $valid_payment_methods)) {
    $_SESSION['error'] = "Invalid payment method selected.";
    header("Location: booking.php?boat_id=" . $boat_id);
    exit();
}

// Validate dates
if (empty($checkin_date) || empty($checkout_date)) {
    $_SESSION['error'] = "Please select both check-in and check-out dates.";
    header("Location: booking.php?boat_id=" . $boat_id);
    exit();
}

// More robust date validation and formatting
// Handle check-in date
if (!empty($checkin_date)) {
    // If it's already in the correct format, validate it
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin_date)) {
        // Validate that it's a real date
        $timestamp = strtotime($checkin_date);
        if ($timestamp === false || date('Y-m-d', $timestamp) !== $checkin_date) {
            $_SESSION['error'] = "Invalid check-in date.";
            header("Location: booking.php?boat_id=" . $boat_id);
            exit();
        }
    } else {
        // Try to convert to proper format
        $timestamp = strtotime($checkin_date);
        if ($timestamp !== false) {
            $checkin_date = date('Y-m-d', $timestamp);
        } else {
            // If conversion fails, show error
            $_SESSION['error'] = "Invalid check-in date format.";
            header("Location: booking.php?boat_id=" . $boat_id);
            exit();
        }
    }
} else {
    $_SESSION['error'] = "Please select a check-in date.";
    header("Location: booking.php?boat_id=" . $boat_id);
    exit();
}

// Handle check-out date
if (!empty($checkout_date)) {
    // If it's already in the correct format, validate it
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout_date)) {
        // Validate that it's a real date
        $timestamp = strtotime($checkout_date);
        if ($timestamp === false || date('Y-m-d', $timestamp) !== $checkout_date) {
            $_SESSION['error'] = "Invalid check-out date.";
            header("Location: booking.php?boat_id=" . $boat_id);
            exit();
        }
    } else {
        // Try to convert to proper format
        $timestamp = strtotime($checkout_date);
        if ($timestamp !== false) {
            $checkout_date = date('Y-m-d', $timestamp);
        } else {
            // If conversion fails, show error
            $_SESSION['error'] = "Invalid check-out date format.";
            header("Location: booking.php?boat_id=" . $boat_id);
            exit();
        }
    }
} else {
    $_SESSION['error'] = "Please select a check-out date.";
    header("Location: booking.php?boat_id=" . $boat_id);
    exit();
}

// ... existing code ...

// Validate and ensure dates are properly formatted
$validated_checkin = $checkin_date;
$validated_checkout = $checkout_date;

// Ensure checkin date is valid
if (empty($validated_checkin) || $validated_checkin === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $validated_checkin)) {
    $validated_checkin = date('Y-m-d');
}

// Ensure checkout date is valid
if (empty($validated_checkout) || $validated_checkout === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $validated_checkout)) {
    $validated_checkout = date('Y-m-d', strtotime('+1 day'));
}

// Check if check-out date is after check-in date
if ($validated_checkout <= $validated_checkin) {
    $validated_checkout = date('Y-m-d', strtotime($validated_checkin . ' +1 day'));
}

// Update the variables to use validated dates
$checkin_date = $validated_checkin;
$checkout_date = $validated_checkout;

// Validate guests count
if ($guests <= 0) {
    $_SESSION['error'] = "Please enter a valid number of guests.";
    header("Location: booking.php?boat_id=" . $boat_id);
    exit();
}

// Fetch boat details to verify availability and get price
$stmt = $conn->prepare("SELECT * FROM boats WHERE id = ? AND status = 'available'");
$stmt->bind_param("i", $boat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "The selected boat is not available.";
    header("Location: ../boats.php");
    exit();
}

$boat = $result->fetch_assoc();

// Check if guest count exceeds boat capacity
if ($guests > $boat['capacity']) {
    $_SESSION['error'] = "The number of guests exceeds the boat's capacity of " . $boat['capacity'] . " people.";
    header("Location: booking.php?boat_id=" . $boat_id);
    exit();
}

// Calculate number of nights using validated dates
$checkin = new DateTime($checkin_date);
$checkout = new DateTime($checkout_date);
$interval = $checkin->diff($checkout);
$nights = $interval->days;

// Ensure we have a positive number of nights
if ($nights <= 0) {
    $nights = 1;
}

// This validation is now handled above, so we can remove it or keep it as a backup
if ($nights <= 0) {
    $_SESSION['error'] = "Invalid date range.";
    header("Location: booking.php?boat_id=" . $boat_id);
    exit();
}

// Calculate total price using validated nights
$total_price = $boat['price'] * $nights;

// Ensure we have a positive total price
if ($total_price <= 0) {
    $total_price = $boat['price']; // At least one night
}

// For higher value bookings, we might want manual confirmation
$auto_confirm = $total_price < 50000; // Auto-confirm bookings under ₹50,000
$status = $auto_confirm ? 'confirmed' : 'pending';

// Validate dates before storing in session
$validated_checkin = $checkin_date;
$validated_checkout = $checkout_date;

// Ensure checkin date is valid
if (empty($validated_checkin) || $validated_checkin === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $validated_checkin)) {
    $validated_checkin = date('Y-m-d');
}

// Ensure checkout date is valid
if (empty($validated_checkout) || $validated_checkout === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $validated_checkout)) {
    $validated_checkout = date('Y-m-d', strtotime('+1 day'));
}

// Store booking details in session for payment processing
$_SESSION['booking_payment'] = [
    'user_id' => $user_id,
    'boat_id' => $boat_id,
    'checkin_date' => $checkin_date, // Use the corrected date
    'checkout_date' => $checkout_date, // Use the corrected date
    'guests' => $guests,
    'total_price' => $total_price,
    'requests' => $requests,
    'payment_method' => $payment_method,
    'boat_name' => $boat['boat_name'],
    'status' => $status,
    'auto_confirm' => $auto_confirm
];

// ... existing code ...

// ... existing code ...

// Redirect to payment page
header("Location: ../payment/payment.php");
exit();
?>