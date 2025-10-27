<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kerala Cruises - Luxury Houseboat Experiences</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="css/swiper.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/custom.css">
</head>
<body>
  <!-- Navbar included from navbar.php -->
  <?php 
  // Ensure session is properly started with correct parameters
  if (session_status() == PHP_SESSION_NONE) {
      session_set_cookie_params([
          'lifetime' => 0,
          'path' => '/',
          'domain' => '',
          'secure' => false,
          'httponly' => true,
          'samesite' => 'Lax'
      ]);
      session_start();
  }
  include 'frontend/includes/navbar.php'; 
  ?>

  <!-- Swiper Carousel Section (replacing the image) -->
  <div class="container-fluid px-0">
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide position-relative">
          <img src="img/b7.jpg" class="w-100 d-block" alt="Luxury Houseboat" />
          <div class="position-absolute bottom-0 start-0 w-100 text-white p-4" 
               style="background: rgba(0,0,0,0.6);">
            <div class="container">
              <div class="row">
                <div class="col-lg-8">
                  <h2 class="display-5 fw-bold mb-3">Luxury Houseboats</h2>
                  <p class="lead mb-4">Experience comfort with elegance on our premium houseboats. Unwind in style while gliding through Kerala's tranquil backwaters.</p>
                  <a href="frontend/pages/boats.php" class="btn btn-primary btn-lg px-4 py-2">
                    <i class="bi bi-calendar-check me-2"></i>Book Now
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="swiper-slide position-relative">
          <img src="img/img9.avif" class="w-100 d-block" alt="Backwater View" />
          <div class="position-absolute bottom-0 start-0 w-100 text-white p-4" 
               style="background: rgba(0,0,0,0.6);">
            <div class="container">
              <div class="row">
                <div class="col-lg-8">
                  <h2 class="display-5 fw-bold mb-3">Scenic Backwaters</h2>
                  <p class="lead mb-4">Unwind with breathtaking views of Kerala's natural beauty. Discover the serene charm of our backwater destinations.</p>
                  <a href="frontend/pages/boats.php" class="btn btn-primary btn-lg px-4 py-2">
                    <i class="bi bi-compass me-2"></i>Explore Boats
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="swiper-slide position-relative">
          <img src="img/img10.avif" class="w-100 d-block" alt="Romantic Cruise" />
          <div class="position-absolute bottom-0 start-0 w-100 text-white p-4" 
               style="background: rgba(0,0,0,0.6);">
            <div class="container">
              <div class="row">
                <div class="col-lg-8">
                  <h2 class="display-5 fw-bold mb-3">Romantic Cruises</h2>
                  <p class="lead mb-4">Perfect getaway for couples seeking a memorable experience. Create unforgettable moments on our romantic houseboat cruises.</p>
                  <a href="frontend/pages/boats.php" class="btn btn-primary btn-lg px-4 py-2">
                    <i class="bi bi-heart-fill me-2"></i>Plan Your Romance
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Pagination -->
      <div class="swiper-pagination"></div>
    </div>
  </div>

  <!-- Stats Section -->
  <section class="stats-section my-5">
    <div class="container">
      <div class="row text-center">
        <div class="col-md-3 col-6 mb-4">
          <div class="stat-number">50+</div>
          <div>Luxury Boats</div>
        </div>
        <div class="col-md-3 col-6 mb-4">
          <div class="stat-number">1000+</div>
          <div>Happy Customers</div>
        </div>
        <div class="col-md-3 col-6 mb-4">
          <div class="stat-number">5000+</div>
          <div>Hours Cruised</div>
        </div>
        <div class="col-md-3 col-6 mb-4">
          <div class="stat-number">4.8</div>
          <div>Avg. Rating</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center mb-5">
          <h2 class="section-title">Why Choose Us</h2>
          <p class="text-muted">Experience the best of Kerala's backwaters with our premium services</p>
        </div>
      </div>
      
      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature-card h-100">
            <div class="card-body text-center p-4">
              <div class="feature-icon text-primary">
                <i class="bi bi-award"></i>
              </div>
              <h5 class="card-title">Premium Quality</h5>
              <p class="card-text">Our luxury houseboats are equipped with modern amenities and staffed by experienced professionals.</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="feature-card h-100">
            <div class="card-body text-center p-4">
              <div class="feature-icon text-primary">
                <i class="bi bi-shield-check"></i>
              </div>
              <h5 class="card-title">Safe & Secure</h5>
              <p class="card-text">Your safety is our priority. All our boats are regularly inspected and maintained to the highest standards.</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="feature-card h-100">
            <div class="card-body text-center p-4">
              <div class="feature-icon text-primary">
                <i class="bi bi-headset"></i>
              </div>
              <h5 class="card-title">24/7 Support</h5>
              <p class="card-text">Our dedicated support team is available around the clock to assist you with any queries or concerns.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Popular Houseboats Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center mb-5">
          <h2 class="section-title">Popular Houseboats</h2>
          <p class="text-muted">Discover our most sought-after luxury houseboats</p>
        </div>
      </div>
      
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card boat-card shadow-sm h-100">
            <img src="img/1.webp" class="card-img-top" alt="Houseboat Serenity">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">Houseboat Serenity</h5>
              <p class="card-text flex-grow-1">Relax and enjoy a calm backwater ride with our cozy Serenity boat.</p>
              <div class="mt-auto">
                <a href="frontend/pages/boats.php" class="btn btn-primary w-100">
                  <i class="bi bi-boat me-1"></i>View Details
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card boat-card shadow-sm h-100">
            <img src="img/2.avif" class="card-img-top" alt="Houseboat Paradise">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">Houseboat Paradise</h5>
              <p class="card-text flex-grow-1">Enjoy premium comfort and scenic views with our Paradise boat.</p>
              <div class="mt-auto">
                <a href="frontend/pages/boats.php" class="btn btn-primary w-100">
                  <i class="bi bi-boat me-1"></i>View Details
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card boat-card shadow-sm h-100">
            <img src="img/b1.jpg" class="card-img-top" alt="Houseboat Royal">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">Houseboat Royal</h5>
              <p class="card-text flex-grow-1">Travel like royalty in our fully-equipped Royal houseboat.</p>
              <div class="mt-auto">
                <a href="frontend/pages/boats.php" class="btn btn-primary w-100">
                  <i class="bi bi-boat me-1"></i>View Details
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row mt-4">
        <div class="col-12 text-center">
          <a href="frontend/pages/boats.php" class="btn btn-outline-primary btn-lg">
            <i class="bi bi-collection me-2"></i>View All Houseboats
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="my-5">
    <div class="container">
      <div class="cta-section">
        <h2 class="display-5 fw-bold mb-3">Ready for an Unforgettable Experience?</h2>
        <p class="lead mb-4">Book your dream houseboat cruise today and create memories that will last a lifetime.</p>
        <a href="frontend/pages/boats.php" class="btn btn-light btn-lg px-5 py-3">
          <i class="bi bi-calendar-check me-2"></i>Book Now
        </a>
      </div>
    </div>
  </section>

  <?php include "frontend/includes/about.php";?>
  <?php include "frontend/includes/contact.php";?>
  <?php include "frontend/includes/footer.php";?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="js/swiper.js"></script>

  <!-- Initialize dropdowns -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
      var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
      var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
          return new bootstrap.Dropdown(dropdownToggleEl);
      });
  });
  </script>
</body>
</html>