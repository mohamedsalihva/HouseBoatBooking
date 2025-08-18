<?php include 'frontend/includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HouseBoatBooking</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="css/swiper.css" />

  <style>
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body>

<!-- Navbar included from navbar.php -->

<div class="container my-5">
  <h1 class="text-center">Welcome to HouseBoatBooking</h1>
  <p class="text-center">Book your dream houseboat today!</p>


  <!-- Swiper Slider -->
  <div class="container">
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <img src="img/b7.jpg" class="w-100 d-block" alt="Slide 1" />
        </div>
        <div class="swiper-slide">
          <img src="img/img9.avif" class="w-100 d-block" alt="Slide 2" />
        </div>
        <div class="swiper-slide">
          <img src="img/img10.avif" class="w-100 d-block" alt="Slide 3" />
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Sample Houseboats Section -->
<div class="container my-5">
  <h2 class="text-center mb-4">Our Popular Houseboats</h2>
  <div class="row g-4">

    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="img/1.webp" class="card-img-top" alt="Houseboat Serenity" style="height: 220px; object-fit: cover;">
        <div class="card-body text-center">
          <h5 class="card-title">Houseboat Serenity</h5>
          <p class="card-text">Relax and enjoy a calm backwater ride with our cozy Serenity boat.</p>
          <a href="boats.php" class="btn btn-primary">View All Houseboats</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="img/2.avif" class="card-img-top" alt="Houseboat Paradise" style="height: 220px; object-fit: cover;">
        <div class="card-body text-center">
          <h5 class="card-title">Houseboat Paradise</h5>
          <p class="card-text">Enjoy premium comfort and scenic views with our Paradise boat.</p>
          <a href="boats.php" class="btn btn-primary">View All Houseboats</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="img/b1.jpg" class="card-img-top" alt="Houseboat Royal" style="height: 220px; object-fit: cover;">
        <div class="card-body text-center">
          <h5 class="card-title">Houseboat Royal</h5>
          <p class="card-text">Travel like royalty in our fully-equipped Royal houseboat.</p>
          <a href="boats.php" class="btn btn-primary">View All Houseboats</a>
        </div>
      </div>
    </div>

  </div>
</div>


<?php include "frontend/includes/about.php";?>
<?php include "frontend/includes/contact.php";?>
<?php include "frontend/includes/footer.php";?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="js/swiper.js"></script>

</body>
</html>
