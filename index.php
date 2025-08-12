<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HouseBoatBooking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="css/swiper.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">🏝 HouseBoatBooking</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
                        aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active text-primary" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="#">Boats</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="#">Contact Us</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="#">About</a></li>
            </ul>

            <form class="d-flex me-3">
                <input class="form-control me-2" type="search" placeholder="Search boats...">
                <button class="btn btn-light text-primary" type="submit">Search</button>
            </form>

            <div class="d-flex">
                <a href="frontend/login/login.php" class="btn btn-outline-primary me-2">Login</a>
                <a href="frontend/signup/signup.php" class="btn btn-warning text-primary">Register</a>
            </div>
        </div>
    </div>
</nav>

  
<!---swiper--->
<div class="container">
  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <img src="img/b7.jpg" class="w-100 d-block" />
      </div>
      <div class="swiper-slide">
        <img src="img/img9.avif"  class="w-100 d-block" />
      </div>
      <div class="swiper-slide">
        <img src="img/img10.avif"  class="w-100 d-block" />
      </div>
    </div>
  </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="js/swiper.js"></script>

</body>
</html>
