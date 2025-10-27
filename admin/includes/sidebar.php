<nav class="sidebar">
    <div class="sidebar-header">
        <h3><i class="bi bi-speedometer2"></i> Admin Panel</h3>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="/HouseBoatBooking/admin/dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['REQUEST_URI'], '/boats/') !== false) ? 'active' : ''; ?>" href="/HouseBoatBooking/admin/boats/manage/index.php">
                <i class="bi bi-boat"></i> Manage Boats
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>" href="/HouseBoatBooking/admin/bookings.php">
                <i class="bi bi-calendar-check"></i> Bookings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['REQUEST_URI'], '/users/') !== false) ? 'active' : (basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''); ?>" href="/HouseBoatBooking/admin/users/index.php">
                <i class="bi bi-people"></i> Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="/HouseBoatBooking/admin/reports.php">
                <i class="bi bi-bar-chart"></i> Reports
            </a>
        </li>
        <li class="nav-item mt-3">
            <a class="nav-link" href="/HouseBoatBooking/backend/auth/logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</nav>