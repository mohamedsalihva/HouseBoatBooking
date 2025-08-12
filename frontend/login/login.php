<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HouseBoatBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow-sm" style="max-width: 400px; width: 100%;">
    <div class="card-body p-4">
        <h3 class="text-center text-primary mb-4">🏝 HouseBoatBooking</h3>

        <form action="login_process.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>

            <p class="mt-3 text-center">
                Don’t have an account? <a href="../signup/signup.php">Register here</a>
            </p>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
