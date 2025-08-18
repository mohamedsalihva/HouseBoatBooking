<?php
include '../backend/inc/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['boat_name'];
    $type     = $_POST['boat_type'];
    $capacity = $_POST['capacity'];
    $price    = $_POST['price'];

    // Handle image upload
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/boats/" . $imageName);
    }

    $stmt = $conn->prepare("INSERT INTO boats (boat_name, boat_type, capacity, price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssids", $name, $type, $capacity, $price, $imageName);
    $stmt->execute();

    header("Location: boats.php"); // redirect to manage boats
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
    <link rel="stylesheet" href="css/dashboard.css"> <!-- Use same dashboard CSS -->
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center py-3">Admin Panel</h4>
        <a href="dashboard.php">Dashboard</a>
        <a href="boats.php" class="active">Manage Boats</a>
        <a href="bookings.php">Bookings</a>
        <a href="users.php">Users</a>
        <a href="reports.php">Reports</a>
        <a href="../logout.php" class="text-danger">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Add New Boat</h2>
            <a href="boats.php" class="btn btn-secondary">Back to Boats</a>
        </div>

        <div class="card shadow-sm p-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Boat Name</label>
                    <input type="text" name="boat_name" class="form-control" placeholder="Enter boat name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Boat Type</label>
                    <input type="text" name="boat_type" class="form-control" placeholder="Enter boat type" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" placeholder="Enter capacity" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="Enter price" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Boat Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">Add Boat</button>
                    <a href="boats.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
