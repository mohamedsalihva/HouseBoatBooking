<?php
// Check if user is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /HouseBoatBooking/frontend/login/login.php");
    exit();
}

include '../backend/inc/db_connect.php';
include 'includes/sidebar.php';

// Handle search
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE role != 'admin' AND (name LIKE ? OR email LIKE ?) ORDER BY created_at DESC");
    $search_term = '%' . $search . '%';
    $stmt->bind_param("ss", $search_term, $search_term);
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC");
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
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
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-people"></i> Manage Users</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/HouseBoatBooking/admin/dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Users</li>
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
            <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    User updated successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    User deleted successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error performing operation. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people"></i> User List</h5>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php if ($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo ($row['role'] == 'admin' ? 'primary' : 'secondary'); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($row['role'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="users/edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="users/delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                            <h4 class="mt-3">No users found</h4>
                            <p class="text-muted">
                                <?php if (!empty($search)): ?>
                                    No users match your search criteria. 
                                    <a href="users.php">View all users</a>
                                <?php else: ?>
                                    You haven't added any users yet.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
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