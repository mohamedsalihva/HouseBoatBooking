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
include '../../backend/inc/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Store the current URL to redirect back after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login/login.php");
    exit();
}

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Houseboats - Kerala Cruises</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="../../css/custom.css">
</head>
<body>
    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Our Houseboats</h1>
                <p class="text-center text-muted mb-5">Discover our fleet of luxury houseboats perfect for your Kerala backwater experience</p>
            </div>
        </div>

        <div class="row">
            <?php
            // Fetch all boats from database
            $result = $conn->query("SELECT * FROM boats WHERE status = 'available' ORDER BY boat_name");
            
            if (!$result) {
                echo '<div class="col-12"><div class="alert alert-danger">Database query failed: ' . $conn->error . '</div></div>';
            } else if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Decode JSON images if they exist
                    $images = array();
                    if (!empty($row['image'])) {
                        // Try to decode as JSON first
                        $decoded = json_decode($row['image'], true);
                        if (is_array($decoded)) {
                            $images = $decoded;
                        } else {
                            // If not JSON, treat as single image path
                            $images = array($row['image']);
                        }
                    }
                    
                    // Use first image as main image or fallback
                    $mainImage = !empty($images[0]) ? "/HouseBoatBooking/uploads/boats/" . $images[0] : "/HouseBoatBooking/img/b1.jpg";
                    
                    echo '<div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="position-relative">
                                <img src="' . $mainImage . '" class="card-img-top" alt="' . htmlspecialchars($row['boat_name']) . '" style="height: 250px; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-dark bg-opacity-75 fs-6">₹' . number_format($row['price']) . '/night</span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">' . htmlspecialchars($row['boat_name']) . '</h5>
                                <p class="card-text flex-grow-1">
                                    <strong>Type:</strong> ' . ucfirst(htmlspecialchars($row['boat_type'])) . '<br>
                                    <strong>Capacity:</strong> ' . htmlspecialchars($row['capacity']) . ' guests<br>
                                </p>
                                <div class="mt-auto">
                                    <a href="boat_details.php?id=' . $row['id'] . '" class="btn btn-primary w-100">
                                        <i class="bi bi-info-circle"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="col-12">
                    <div class="card text-center p-5">
                        <h3>No boats available at the moment</h3>
                        <p class="text-muted">Please check back later for new additions to our fleet.</p>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
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