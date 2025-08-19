<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Kerala Cruises</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/signup.css">
</head>
<body>

  <div class="login-container">
    <h3> Kerala Cruises</h3>
    <p>Create your account and start your journey</p>

    <form action="../../backend/signup_process.php" method="POST">
      <div class="mb-3 text-start">
        <label class="form-label">Full Name</label>
        <input type="text" class="form-control" name="name" placeholder="Enter your name" required>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" placeholder="Enter password" required>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label">Confirm Password</label>
        <input type="password" class="form-control" name="confirm_password" placeholder="Re-enter password" required>
      </div>

      <button type="submit" class="btn-login">Sign Up</button>
    </form>

    <p class="mt-3">
      Already a member? 
      <a href="../login/login.php">Login</a>
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
