<?php
include '../../backend/inc/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['boat_name'];
    $type     = $_POST['boat_type'];
    $capacity = $_POST['capacity'];
    $price    = $_POST['price'];

    // Handle image upload
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../../uploads/boats/" . $imageName);
    }

    $stmt = $conn->prepare("INSERT INTO boats (boat_name, boat_type, capacity, price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssids", $name, $type, $capacity, $price, $imageName);
    $stmt->execute();

    header("Location: ../boats.php"); // redirect to manage boats
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Boat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Add New Boat</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/boats.php">Boats</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Boat</li>
                        </ol>
                    </nav>
                </div>
                <a href="/HouseBoatBooking/admin/boats.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Boats</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-boat"></i> Boat Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Boat Name</label>
                            <input type="text" name="boat_name" class="form-control" placeholder="Enter boat name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Boat Type</label>
                            <select name="boat_type" class="form-select" required>
                                <option value="">Select boat type</option>
                                <option value="Standard">Standard</option>
                                <option value="Premium">Premium</option>
                                <option value="Luxury">Luxury</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" placeholder="Enter capacity" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="Enter price" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Boat Image</label>
                            <input type="file" name="image" class="form-control">
                            <div class="form-text">Upload a high-quality image of the boat (JPG, PNG, GIF)</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="bi bi-check-circle"></i> Add Boat</button>
                        <a href="/HouseBoatBooking/admin/boats.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>