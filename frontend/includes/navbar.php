<?php
session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top py-2">
  <div class="container">
    <!-- Brand / Logo -->
    <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
      <div class="d-flex align-items-center overflow-hidden" style="height:50px;">
        <img src="img/logo2.png" alt="HouseBoat Logo" class="me-2 mt-3" style="height:90px; width:auto;">
      </div>
      Kerala<span class="text-primary">Cruises</span>
    </a>

    <!-- Toggler for Mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="boats.php">Boats</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
      </ul>

      <!-- Right Side (Auth Section) -->
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item d-flex align-items-center text-white me-2">
            <i class="bi bi-person-circle me-1"></i> 
            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-light btn-sm" href="backend/logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn btn-outline-light btn-sm me-2" href="frontend/login/login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-primary btn-sm" href="frontend/signup/signup.php">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
