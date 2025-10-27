<?php
include '../../backend/inc/db_connect.php';
include '../includes/sidebar.php';

// Check if user is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $boat_name = $_POST['boat_name'];
    $boat_type = $_POST['boat_type'];
    $capacity = $_POST['capacity'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Handle file upload
    $image_paths = array();
    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = '../../uploads/boats/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_count = count($_FILES['images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $file_name = time() . '_' . rand(1000, 9999) . '_' . basename($_FILES['images']['name'][$i]);
                $target_file = $upload_dir . $file_name;
                
                // Check file type
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'avif'])) {
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file)) {
                        $image_paths[] = $file_name;
                    }
                }
            }
        }
    }

    // Convert image paths array to JSON
    $images_json = !empty($image_paths) ? json_encode($image_paths) : null;

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO boats (boat_name, boat_type, capacity, price, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $boat_name, $boat_type, $capacity, $price, $description, $images_json, $status);

    if ($stmt->execute()) {
        header("Location: /HouseBoatBooking/admin/boats.php?success=1"); // redirect to manage boats
        exit();
    } else {
        $error = "Error adding boat: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Boat - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    <style>
        /* Added padding to prevent content from hiding behind sidebar */
        .content {
            padding: 25px;
            margin-left: 260px;
        }
        .content-header {
            margin-bottom: 25px;
        }
        @media (max-width: 992px) {
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>

    <div class="content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-plus-circle"></i> Add New Boat</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/boats.php">Boats</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add New Boat</li>
                        </ol>
                    </nav>
                </div>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="/HouseBoatBooking/backend/auth/logout.php" class="btn btn-outline-primary btn-sm ms-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-boat"></i> Boat Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="boat_name" class="form-label">Boat Name *</label>
                                <input type="text" class="form-control" id="boat_name" name="boat_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="boat_type" class="form-label">Boat Type *</label>
                                <select class="form-select" id="boat_type" name="boat_type" required>
                                    <option value="">Select Boat Type</option>
                                    <option value="luxury">Luxury</option>
                                    <option value="deluxe">Deluxe</option>
                                    <option value="standard">Standard</option>
                                    <option value="economy">Economy</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="capacity" class="form-label">Capacity (Guests) *</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="50" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price per Night (₹) *</label>
                                <input type="number" class="form-control" id="price" name="price" min="1000" step="100" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Boat Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">You can select multiple images. Supported formats: JPG, JPEG, PNG, GIF, AVIF</div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available">Available</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="booked">Booked</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/HouseBoatBooking/admin/boats.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Boats</a>
                            <div>
                                <a href="/HouseBoatBooking/admin/boats.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Boat</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>