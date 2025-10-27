<?php
include '../../backend/inc/db_connect.php';
include '../includes/sidebar.php';

// Check if user is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}

// Check if boat ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: /HouseBoatBooking/admin/boats.php");
    exit();
}

$boat_id = intval($_GET['id']);

// Fetch boat details
$stmt = $conn->prepare("SELECT * FROM boats WHERE id = ?");
$stmt->bind_param("i", $boat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: /HouseBoatBooking/admin/boats.php");
    exit();
}

$boat = $result->fetch_assoc();

// Decode JSON images if they exist
$images = array();
if (!empty($boat['image'])) {
    $decoded = json_decode($boat['image'], true);
    if (is_array($decoded)) {
        $images = $decoded;
    } else {
        $images = array($boat['image']);
    }
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
    $image_paths = $images; // Start with existing images
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

    // Handle image removal
    if (isset($_POST['remove_images'])) {
        $images_to_remove = $_POST['remove_images'];
        foreach ($images_to_remove as $image_to_remove) {
            // Remove from array
            $image_paths = array_diff($image_paths, [$image_to_remove]);
            // Delete file from server
            $file_path = '../../uploads/boats/' . $image_to_remove;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    // Convert image paths array to JSON
    $images_json = !empty($image_paths) ? json_encode(array_values($image_paths)) : null;

    // Update database
    $stmt = $conn->prepare("UPDATE boats SET boat_name = ?, boat_type = ?, capacity = ?, price = ?, description = ?, image = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $boat_name, $boat_type, $capacity, $price, $description, $images_json, $status, $boat_id);

    if ($stmt->execute()) {
        header("Location: /HouseBoatBooking/admin/boats.php?updated=1");
        exit();
    } else {
        $error = "Error updating boat: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Boat - Admin Panel</title>
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
                    <h2><i class="bi bi-pencil-square"></i> Edit Boat</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/boats.php">Boats</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Boat</li>
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
                    <h5 class="mb-0"><i class="bi bi-boat"></i> Edit Boat Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="boat_name" class="form-label">Boat Name *</label>
                                <input type="text" class="form-control" id="boat_name" name="boat_name" value="<?php echo htmlspecialchars($boat['boat_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="boat_type" class="form-label">Boat Type *</label>
                                <select class="form-select" id="boat_type" name="boat_type" required>
                                    <option value="">Select Boat Type</option>
                                    <option value="luxury" <?php echo $boat['boat_type'] === 'luxury' ? 'selected' : ''; ?>>Luxury</option>
                                    <option value="deluxe" <?php echo $boat['boat_type'] === 'deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                                    <option value="standard" <?php echo $boat['boat_type'] === 'standard' ? 'selected' : ''; ?>>Standard</option>
                                    <option value="economy" <?php echo $boat['boat_type'] === 'economy' ? 'selected' : ''; ?>>Economy</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="capacity" class="form-label">Capacity (Guests) *</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="50" value="<?php echo htmlspecialchars($boat['capacity']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price per Night (₹) *</label>
                                <input type="number" class="form-control" id="price" name="price" min="1000" step="100" value="<?php echo htmlspecialchars($boat['price']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($boat['description']); ?></textarea>
                        </div>

                        <?php if (!empty($images)): ?>
                            <div class="mb-3">
                                <label class="form-label">Current Images</label>
                                <div class="row">
                                    <?php foreach ($images as $image): ?>
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                <img src="/HouseBoatBooking/uploads/boats/<?php echo htmlspecialchars($image); ?>" class="card-img-top" alt="Boat Image" style="height: 150px; object-fit: cover;">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remove_images[]" value="<?php echo htmlspecialchars($image); ?>" id="remove_<?php echo htmlspecialchars($image); ?>">
                                                        <label class="form-check-label" for="remove_<?php echo htmlspecialchars($image); ?>">Remove</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="images" class="form-label">Add More Images</label>
                            <input class="form-control" type="file" id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">Select one or more images (JPG, JPEG, PNG, GIF, AVIF)</div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="available" <?php echo $boat['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                <option value="maintenance" <?php echo $boat['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                <option value="booked" <?php echo $boat['status'] === 'booked' ? 'selected' : ''; ?>>Booked</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/HouseBoatBooking/admin/boats.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back to Boats
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update Boat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>