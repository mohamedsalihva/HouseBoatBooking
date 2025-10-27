<?php
include '../../../backend/inc/db_connect.php';
include '../../includes/sidebar.php';

// Check if user is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}

// Handle search
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM boats WHERE boat_name LIKE ? OR boat_type LIKE ? ORDER BY id DESC");
    $search_term = '%' . $search . '%';
    $stmt->bind_param("ss", $search_term, $search_term);
} else {
    $stmt = $conn->prepare("SELECT * FROM boats ORDER BY id DESC");
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Boats - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/HouseBoatBooking/admin/css/dashboard.css" rel="stylesheet">
    <style>
        .boat-card {
            transition: all 0.3s ease;
            height: 100%;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }
        .boat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }
        .boat-image-container {
            height: 220px;
            overflow: hidden;
            position: relative;
        }
        .boat-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .boat-card:hover .boat-image {
            transform: scale(1.05);
        }
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: #333;
        }
        .boat-info {
            margin-bottom: 15px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: 500;
            color: #666;
        }
        .info-value {
            font-weight: 600;
            color: #333;
        }
        .price-highlight {
            color: #28a745;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .action-buttons .btn {
            flex: 1;
            margin: 0 2px;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 6px;
        }
        .action-buttons .btn:first-child {
            margin-left: 0;
        }
        .action-buttons .btn:last-child {
            margin-right: 0;
        }
        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 10px 15px;
            font-size: 0.85rem;
        }
        .search-container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .page-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .empty-state {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            margin-top: 30px;
        }
        .empty-state i {
            font-size: 4rem;
            color: #ced4da;
            margin-bottom: 20px;
        }
        .empty-state h4 {
            color: #6c757d;
            margin-bottom: 15px;
        }
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
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
        
        /* Enhanced card design */
        .boat-card .card-body {
            padding: 20px;
        }
        .boat-card .card-footer {
            padding: 12px 20px;
        }
        .action-buttons {
            margin-top: 15px;
        }
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    </style>
</head>
<body>
    <?php include '../../includes/sidebar.php'; ?>

    <div class="content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-boat"></i> Manage Boats</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Boats</li>
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
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>Boat added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>Boat updated successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>Boat deleted successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>Error performing operation. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="page-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="bi bi-boat me-2"></i>Boat Management</h3>
                <a href="add.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add New Boat
                </a>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form method="GET" class="mb-0">
                    <div class="row">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search boats by name or type..." value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-outline-primary" type="submit">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        // Process images
                        $images = array();
                        if (!empty($row['image'])) {
                            $decoded = json_decode($row['image'], true);
                            if (is_array($decoded)) {
                                $images = $decoded;
                            } else {
                                $images = array($row['image']);
                            }
                        }
                        
                        // Get first image or default
                        $image_src = "/HouseBoatBooking/img/b1.jpg";
                        if (!empty($images[0])) {
                            $image_path = '/HouseBoatBooking/uploads/boats/' . htmlspecialchars($images[0]);
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/HouseBoatBooking/uploads/boats/' . $images[0])) {
                                $image_src = $image_path;
                            }
                        }
                        
                        // Status badge class
                        $status_class = '';
                        $status_text = ucfirst(htmlspecialchars($row['status']));
                        switch ($row['status']) {
                            case 'available':
                                $status_class = 'bg-success';
                                break;
                            case 'maintenance':
                                $status_class = 'bg-warning text-dark';
                                break;
                            case 'booked':
                                $status_class = 'bg-info';
                                break;
                            default:
                                $status_class = 'bg-secondary';
                        }
                        
                        // Count total images
                        $image_count = count($images);
                        ?>
                        <div class="col">
                            <div class="card boat-card h-100">
                                <div class="boat-image-container">
                                    <img src="<?php echo $image_src; ?>" class="boat-image" alt="<?php echo htmlspecialchars($row['boat_name']); ?>">
                                    <span class="badge <?php echo $status_class; ?> status-badge"><?php echo $status_text; ?></span>
                                    <?php if ($image_count > 1): ?>
                                        <span class="badge bg-primary notification-badge" title="<?php echo $image_count; ?> images"><?php echo $image_count; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['boat_name']); ?></h5>
                                    
                                    <div class="boat-info">
                                        <div class="info-item">
                                            <span class="info-label">Type:</span>
                                            <span class="info-value"><?php echo ucfirst(htmlspecialchars($row['boat_type'])); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Capacity:</span>
                                            <span class="info-value"><?php echo htmlspecialchars($row['capacity']); ?> guests</span>
                                        </div>
                                        <div class="info-item mb-0">
                                            <span class="info-label">Price:</span>
                                            <span class="price-highlight">₹<?php echo number_format($row['price']); ?><small class="text-muted">/night</small></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto action-buttons">
                                        <div class="btn-group w-100" role="group">
                                            <a href="../edit.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary">
                                                <i class="bi bi-pencil me-1"></i> Edit
                                            </a>
                                            <a href="../delete.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this boat?\n\nBoat: <?php echo htmlspecialchars($row['boat_name']); ?>\nID: <?php echo $row['id']; ?>\n\nThis action cannot be undone.')">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    <small><i class="bi bi-hash me-1"></i>ID: <?php echo $row['id']; ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-boat"></i>
                    <h4>No Boats Found</h4>
                    <p class="text-muted mb-4">
                        <?php if (!empty($search)): ?>
                            No boats match your search criteria. 
                            <a href="index.php" class="btn btn-link">View all boats</a>
                        <?php else: ?>
                            You haven't added any boats yet.
                        <?php endif; ?>
                    </p>
                    <a href="add.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Your First Boat
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>