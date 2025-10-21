<?php include '../backend/inc/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Boats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


    <link rel="stylesheet" href="css/dashboard.css"> <!-- Use the same CSS -->

    <link rel="stylesheet" href="css/dashboard.css">


    <link rel="stylesheet" href="css/dashboard.css"> <!-- Use the same CSS -->

</head>

<body>
    <!-- Sidebar -->

    <div class="sidebar">
        <h4 class="text-center py-3">Admin Panel</h4>
        <a href="dashboard.php">Dashboard</a>
        <a href="boats.php" class="active">Manage Boats</a> <!-- Active page highlighted -->
        <a href="bookings.php">Bookings</a>
        <a href="users.php">Users</a>
        <a href="reports.php">Reports</a>
        <a href="../logout.php" class="text-danger">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Boats</h2>
            <a href="add_boats.php" class="btn btn-primary">+ Add Boat</a>


    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Manage Boats</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Boats</li>
                        </ol>
                    </nav>
                </div>
                <a href="/HouseBoatBooking/admin/boats/add.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Boat</a>
            </div>


        </div>

        <div class="row g-4">
            <?php
            $result = $conn->query("SELECT * FROM boats");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagePath = "/HouseBoatBooking/uploads/boats/" . $row['image'];
                    $imageTag = (!empty($row['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/HouseBoatBooking/uploads/boats/' . $row['image']))
                        ? "<img src='$imagePath' class='card-img-top' alt='{$row['boat_name']}' style='height:200px; object-fit:cover;'>"
                        : "<div class='bg-secondary text-white d-flex align-items-center justify-content-center' style='height:200px;'>No Image</div>";

                    echo "<div class='col-md-3'>
                        <div class='card shadow-sm h-100'>
                            $imageTag
                            <div class='card-body'>
                                <h5 class='card-title'>{$row['boat_name']}</h5>
                                <p class='card-text'>
                                    <strong>Type:</strong> {$row['boat_type']}<br>

                                    <strong>Capacity:</strong> {$row['capacity']}<br>
                                    <strong>Price:</strong> {$row['price']}
                                </p>
                            </div>
                            <div class='card-footer d-flex justify-content-between'>
                                <a href='edit_boat.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='delete_boat.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>


                                    <strong>Capacity:</strong> {$row['capacity']} guests<br>
                                    <strong>Price:</strong> ₹{$row['price']} per night
                                </p>
                            </div>
                            <div class='card-footer d-flex justify-content-between'>
                                <a href='boats/edit.php?id={$row['id']}' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i> Edit</a>
                                <a href='boats/delete.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this boat?\")'><i class='bi bi-trash'></i> Delete</a>


                            </div>
                        </div>
                    </div>";
                }
            } else {


                echo "<div class='col-12 text-center'><p>No boats found.</p></div>";

                echo "<div class='col-12'>
                    <div class='card text-center'>
                        <div class='card-body'>
                            <i class='bi bi-boat' style='font-size: 3rem; color: #ccc;'></i>
                            <h4 class='mt-3'>No boats found</h4>
                            <p class='text-muted'>There are no boats in the system yet.</p>
                            <a href='boats/add.php' class='btn btn-primary'><i class='bi bi-plus-circle'></i> Add Your First Boat</a>
                        </div>
                    </div>
                </div>";


                echo "<div class='col-12 text-center'><p>No boats found.</p></div>";

            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>

</html>

</html>
