<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Kerala Cruises</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/login.css">
</head>
<body>

  <div class="login-container">
    <h4>Welcome Back</h4>
    <p class="text-muted">Login to continue your Kerala Cruises journey</p>

    <form action="../../backend/auth/login.php" method="POST">
      <div class="mb-3 text-start">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="example@mail.com" required>
      </div>

      <div class="mb-3 text-start">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
      </div>

      <button type="submit" class="btn-login">Login</button>
    </form>

    <p class="mt-3 mb-0">
      Don't have an account? 
      <a href="../signup/signup.php">Sign Up</a>
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>