<div class="sidebar">
    <div class="logo">
        <h3>HouseBoat Admin</h3>
    </div>
    <a href="/HouseBoatBooking/admin/dashboard.php" <?php if(basename($_SERVER['PHP_SELF']) == 'dashboard.php') echo 'class="active"'; ?>><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="/HouseBoatBooking/admin/boats.php" <?php if(basename($_SERVER['PHP_SELF']) == 'boats.php') echo 'class="active"'; ?>><i class="bi bi-boat"></i> Manage Boats</a>
    <a href="/HouseBoatBooking/admin/bookings.php" <?php if(basename($_SERVER['PHP_SELF']) == 'bookings.php') echo 'class="active"'; ?>><i class="bi bi-calendar-check"></i> Bookings</a>
    <a href="/HouseBoatBooking/admin/users.php" <?php if(basename($_SERVER['PHP_SELF']) == 'users.php') echo 'class="active"'; ?>><i class="bi bi-people"></i> Users</a>
    <a href="/HouseBoatBooking/admin/reports.php" <?php if(basename($_SERVER['PHP_SELF']) == 'reports.php') echo 'class="active"'; ?>><i class="bi bi-bar-chart"></i> Reports</a>
    <a href="/HouseBoatBooking/backend/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>