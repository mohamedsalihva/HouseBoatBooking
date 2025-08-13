<?php
session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">HouseBoatBooking</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
  <li class="nav-item"><a class="nav-link" href="boats.php">Boats</a></li>
  <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
  <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      </ul>

      <ul class="navbar-nav">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="backend/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="frontend/login/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="frontend/signup/signup.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
