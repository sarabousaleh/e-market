<?php
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->

  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>e-market</title>

  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/mystyle.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
  <style>
    
  </style>
  
</head>

<body>
  <div class="hero_area">
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container " style="justify-content: space-between;">
          <a class="navbar-brand " href="index.php"> e-market </a>

          <a class="nav-link" href="cart.php"><i class="fa fa-shopping-basket" style="font-size: 24px; color: white; "></i></a>

    
        </nav>
      </div>
    </header>
    <!-- end header section -->
    <!-- slider section -->
    <section class="slider_section">
      <div id="customCarousel1" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="container">
              <div class="row">
                <div class="col-md-6 col-lg-5">
                  <div class="detail-box">
                    <h1>
                      e-market
                    </h1>
                    <p>
                      Welcome to e-market, your friendly corner store! Find all you need in one place—fresh food, household basics, and more. Easy, affordable, and always here to make your shopping simple. Welcome to e-market, where your convenience comes first!
                    </p>
                  </div>
                </div>
                <div class="col-md-6 col-lg-7">
                  <div class="img-box">
                    <img src="images/slider-img.png" alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>


  <section class="blog_section layout_padding">
  <div class="container">
    <div class="heading_container heading_center">
      <h2>Categories</h2>
    </div>

    <div class="row">
      <?php
      // Connect to your database (replace dbname, username, password with your actual database credentials)
      $pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

      // Fetch categories from the database
      $query = $pdo->query('SELECT * FROM categories');
      $categories = $query->fetchAll(PDO::FETCH_ASSOC);

      foreach ($categories as $category) {
        echo '
          <div class="col-md-6 col-lg-4 mx-auto">
            <div class="box">
              <div class="img-box">
                <a href="category_page.php?CategoryID=' . $category['CategoryID'] . '">
                  <img src="images/' . $category['imageName'] . '" alt="">
                </a>
              </div>
              <div class="detail-box" style="text-align: center;">
                <h5>' . $category['CategoryName'] . '</h5>
              </div>
            </div>
          </div>';
      }
      ?>
    </div>
  </div>
</section>


  <?php include('navbar.php'); ?>
  

  

  <!-- jQuery -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <!-- popper js -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <!-- bootstrap js -->
  <script src="js/bootstrap.js"></script>
  <!-- owl slider -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <!-- custom js -->
  <script src="js/custom.js"></script>
</body>

</html>