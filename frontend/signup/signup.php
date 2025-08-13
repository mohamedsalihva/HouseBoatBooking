<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - HouseBoatBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow-sm" style="max-width: 450px; width: 100%;">
    <div class="card-body p-4">
        <h3 class="text-center text-primary mb-4">🏝 HouseBoatBooking - Sign Up</h3>

        <form action="../../backend/signup_process.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary">Sign Up</button>
        </form>

        <p class="mt-3 text-center">
            Already have an account? <a href="../login/login.php">Login here</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
