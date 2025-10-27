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

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: boats.php");
    exit();
}

$boat_id = intval($_GET['id']);

// Fetch boat details
$stmt = $conn->prepare("SELECT * FROM boats WHERE id = ? AND status = 'available'");
$stmt->bind_param("i", $boat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: boats.php");
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

// Use fallback image if no images
if (empty($images)) {
    $images = ['b1.jpg'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($boat['boat_name']); ?> - Kerala Cruises</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="../../css/custom.css">
    <style>
        .boat-image-slider {
            height: 500px;
        }
        .boat-image-slider img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-type-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="boats.php">Houseboats</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($boat['boat_name']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h1 class="mb-3"><?php echo htmlspecialchars($boat['boat_name']); ?></h1>
            </div>
        </div>

        <div class="row">
            <!-- Image Slider -->
            <div class="col-lg-8 mb-4">
                <div class="swiper boatImageSwiper">
                    <div class="swiper-wrapper">
                        <?php for ($i = 0; $i < count($images); $i++): ?>
                            <div class="swiper-slide position-relative">
                                <div class="image-type-badge">Image <?php echo ($i + 1); ?></div>
                                <img src="/HouseBoatBooking/uploads/boats/<?php echo htmlspecialchars($images[$i]); ?>" alt="<?php echo htmlspecialchars($boat['boat_name'] . ' Image ' . ($i + 1)); ?>">
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>

            <!-- Boat Details -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h2 class="card-title mb-0">₹<?php echo number_format($boat['price']); ?><small class="text-muted">/night</small></h2>
                        </div>
                        
                        <div class="mb-3">
                            <h5 class="text-muted">Boat Details</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-people-fill me-2 text-primary"></i> <strong>Capacity:</strong> <?php echo htmlspecialchars($boat['capacity']); ?> guests</li>
                                <li class="mb-2"><i class="bi bi-tag-fill me-2 text-primary"></i> <strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($boat['boat_type'])); ?></li>
                            </ul>
                        </div>

                        <?php if (!empty($boat['description'])): ?>
                            <div class="mb-3">
                                <h5 class="text-muted">Description</h5>
                                <p><?php echo nl2br(htmlspecialchars($boat['description'])); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="bi bi-calendar-x"></i> Admin Cannot Book
                                </button>
                            <?php else: ?>
                                <a href="booking/booking.php?boat_id=<?php echo $boat['id']; ?>" class="btn btn-primary btn-lg">
                                    <i class="bi bi-calendar-check"></i> Book Now
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- Initialize Swiper -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var swiper = new Swiper(".boatImageSwiper", {
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
            });

            // Initialize dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
</body>
</html>