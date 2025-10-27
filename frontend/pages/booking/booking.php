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

// Check if user is admin - if so, show message instead of booking form
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// Check if boat_id is provided
if (!isset($_GET['boat_id']) || empty($_GET['boat_id'])) {
    header("Location: ../boats.php");
    exit();
}

$boat_id = intval($_GET['boat_id']);

// Fetch boat details
$stmt = $conn->prepare("SELECT * FROM boats WHERE id = ? AND status = 'available'");
$stmt->bind_param("i", $boat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: ../boats.php");
    exit();
}

$boat = $result->fetch_assoc();

// Decode JSON images if they exist
$images = array();
if (!empty($boat['image'])) {
    // Try to decode as JSON first
    $decoded = json_decode($boat['image'], true);
    if (is_array($decoded)) {
        $images = $decoded;
    } else {
        // If not JSON, treat as single image path
        $images = array($boat['image']);
    }
}

// Use first image as main image or fallback
$mainImage = !empty($images[0]) ? "/HouseBoatBooking/uploads/boats/" . $images[0] : "/HouseBoatBooking/img/b1.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($boat['boat_name']); ?> - Kerala Cruises</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Booking</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow">
                        <div class="card-header bg-warning text-white">
                            <h4 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Access Denied</h4>
                        </div>
                        <div class="card-body">
                            <p>Admin users cannot book boats. Please use the admin panel to manage boats and bookings.</p>
                            <a href="/HouseBoatBooking/admin/dashboard.php" class="btn btn-primary">Go to Admin Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="bi bi-calendar-check"></i> Book <?php echo htmlspecialchars($boat['boat_name']); ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Boat Details</h5>
                                    <ul class="list-unstyled">
                                        <li><strong>Name:</strong> <?php echo htmlspecialchars($boat['boat_name']); ?></li>
                                        <li><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($boat['boat_type'])); ?></li>
                                        <li><strong>Capacity:</strong> <?php echo htmlspecialchars($boat['capacity']); ?> guests</li>
                                        <li><strong>Price:</strong> ₹<?php echo number_format($boat['price']); ?> per night</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $mainImage)) {
                                        $mainImage = "/HouseBoatBooking/img/b1.jpg";
                                    }
                                    ?>
                                    <img src="<?php echo $mainImage; ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($boat['boat_name']); ?>" style="height: 150px; object-fit: cover;">
                                </div>
                            </div>

                            <form method="POST" action="process_booking.php">
                                <input type="hidden" name="boat_id" value="<?php echo $boat['id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Check-in Date</label>
                                        <input type="date" name="checkin_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Check-out Date</label>
                                        <input type="date" name="checkout_date" class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of Guests</label>
                                        <input type="number" name="guests" class="form-control" min="1" max="<?php echo $boat['capacity']; ?>" value="2" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Special Requests</label>
                                        <textarea name="requests" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Payment Method</label>
                                        <select name="payment_method" class="form-select" required>
                                            <option value="">Select Payment Method</option>
                                            <option value="credit_card">Credit Card</option>
                                            <option value="debit_card">Debit Card</option>
                                            <option value="upi">UPI</option>
                                            <option value="net_banking">Net Banking</option>
                                            <option value="paypal">PayPal</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="../boats.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Boats</a>
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Confirm Booking & Pay</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.min = today;
    });
    
    // Ensure checkout date is after checkin date
    document.querySelector('input[name="checkin_date"]').addEventListener('change', function() {
        const checkoutInput = document.querySelector('input[name="checkout_date"]');
        if (this.value) {
            checkoutInput.min = this.value;
            if (checkoutInput.value && checkoutInput.value <= this.value) {
                checkoutInput.value = '';
            }
        }
    });
    
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