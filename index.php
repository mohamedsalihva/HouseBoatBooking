<?php include 'frontend/includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kerala Cruises</title>
  
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

<div class="container-fluid my-4">
  <!-- Hero Section -->
  <div class="text-center mb-4">
    <h1 class="fw-bold display-5">Welcome to <span class="text-primary">Kerala Cruises</span></h1>
    <p class="lead text-muted">Book your dream houseboat today and enjoy a memorable journey on the water</p>
  </div>

  <!-- Swiper Slider -->
  <div class="swiper mySwiper shadow-lg rounded-4 overflow-hidden">
    <div class="swiper-wrapper">
      <div class="swiper-slide position-relative">
        <img src="img/b7.jpg" class="w-100 d-block" alt="Luxury Houseboat" />
        <div class="position-absolute bottom-0 start-0 w-100 text-white p-3" 
             style="background: rgba(0,0,0,0.5);">
          <h4 class="mb-0">Luxury Houseboats</h4>
          <small>Experience comfort with elegance</small>
        </div>
      </div>

      <div class="swiper-slide position-relative">
        <img src="img/img9.avif" class="w-100 d-block" alt="Backwater View" />
        <div class="position-absolute bottom-0 start-0 w-100 text-white p-3" 
             style="background: rgba(0,0,0,0.5);">
          <h4 class="mb-0">Scenic Backwaters</h4>
          <small>Unwind with breathtaking views</small>
        </div>
      </div>

      <div class="swiper-slide position-relative">
        <img src="img/img10.avif" class="w-100 d-block" alt="Romantic Cruise" />
        <div class="position-absolute bottom-0 start-0 w-100 text-white p-3" 
             style="background: rgba(0,0,0,0.5);">
          <h4 class="mb-0">Romantic Cruises</h4>
          <small>Perfect getaway for couples</small>
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
