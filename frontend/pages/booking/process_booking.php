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

$user_id = $_SESSION['user_id'];
$boat_id = intval($_POST['boat_id']);
$checkin_date = $_POST['checkin_date'];
$checkout_date = $_POST['checkout_date'];
$guests = intval($_POST['guests']);
$requests = isset($_POST['requests']) ? trim($_POST['requests']) : '';
$payment_method = $_POST['payment_method'];

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

// Check if check-out date is after check-in date
if ($checkout_date <= $checkin_date) {
    $_SESSION['error'] = "Check-out date must be after check-in date.";
    header("Location: booking.php?boat_id=" . $boat_id);
    exit();
}

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

// Calculate number of nights
$checkin = new DateTime($checkin_date);
$checkout = new DateTime($checkout_date);
$interval = $checkin->diff($checkout);
$nights = $interval->days;

if ($nights <= 0) {
    $_SESSION['error'] = "Invalid date range.";
    header("Location: booking.php?boat_id=" . $boat_id);
    exit();
}

// Calculate total price
$total_price = $boat['price'] * $nights;

// For higher value bookings, we might want manual confirmation
$auto_confirm = $total_price < 50000; // Auto-confirm bookings under ₹50,000
$status = $auto_confirm ? 'confirmed' : 'pending';

// Store booking details in session for payment processing
$_SESSION['booking_payment'] = [
    'user_id' => $user_id,
    'boat_id' => $boat_id,
    'checkin_date' => $checkin_date,
    'checkout_date' => $checkout_date,
    'guests' => $guests,
    'total_price' => $total_price,
    'requests' => $requests,
    'payment_method' => $payment_method,
    'boat_name' => $boat['boat_name'],
    'status' => $status,
    'auto_confirm' => $auto_confirm
];

// Redirect to payment page
header("Location: ../payment/payment.php");
exit();
?>