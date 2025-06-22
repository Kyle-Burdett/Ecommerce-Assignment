<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

require_once('../../private/db-credentials.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styling/main.css">
  <link rel="stylesheet" href="../styling/homepage.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>C2C Homepage</title>
</head>

<body>
  <header class="header">
    <div class="logo-icons-bar">
      <a class="header-logo-container" href="/public/pages/homepage.php"><img class="header-logo"
          src="../images/site-logo.png" alt="C2C Logo"></a>
      <div class="header-icons-container">
        <a class="header-icon" href="/public/pages/my-profile.php">
          <p>Account</p><img src="../icons/user.svg" alt="Account Icon">
        </a>
        <a class="header-icon" href="/public/pages/buyer-orders.php">
          <p>Orders</p><img src="../icons/bag-shopping.svg" alt="Orders Icon">
        </a>
      </div>
    </div>
    <div class="search-filters-bar">
      <form class="search-form" action="plp.php" method="get">
        <div class="category-filter-container">
          <select title="category-filter" name="category" id="category" class="category-filter">
            <option value="all">All</option>
            <option value="computers">Computers</option>
            <option value="homemade">Homemade</option>
            <option value="tech">Tech</option>
            <option value="furniture">Furniture</option>
            <option value="decor">Decor</option>
            <option value="books">Books</option>
            <option value="uncategorized">Uncategorized</option>
          </select>
        </div>
        <div class="search-container">
          <label class="search-label" for="search">Search</label>
          <input type="text" name="search" id="search" class="search-input">
          <button class="search-button" type="submit"><img src="../icons/magnifying-glass.svg"
            alt="Orders Icon"></button>
        </div>
      </form>
    </div>
  </header>
  <section class="homepage-section">
    <div class="list-section products-section">
      <h1>Featured Products</h1>
      <div class="featured-product-container">
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="product-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <div class="product-text-container">
              <p class="product-card-text">GTX 2000</p>
              <p class="product-price">R 19 999</p>
            </div>
          </div>
        </a>
      </div>
    </div>
    <div class="list-section">
      <h1>Popular Categories</h1>
      <div class="category-container">
        <a class="card-link" href="www.google.com">
          <div class="category-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <p class="card-text">Computers</p>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="category-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <p class="card-text">Homemade</p>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="category-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <p class="card-text">Tech</p>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="category-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <p class="card-text">Furniture</p>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="category-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <p class="card-text">Decor</p>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="category-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <p class="card-text">Books</p>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="category-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <p class="card-text">Fashion</p>
          </div>
        </a>
        <a class="card-link" href="www.google.com">
          <div class="category-card">
            <img src="../images/product-placeholder.jpg" alt="category-image">
            <p class="card-text">Home</p>
          </div>
        </a>
      </div>
    </div>
  </section>
  <footer>
    <div class="links-section">
      <div class="links-container">
        <div class="links-block">
          <div class="links-column">
            <p class="links-column-heading">Search by category:</p>
            <ul>
              <li><a href="plp.php?category=computers&search=">Computers</a></li>
              <li><a href="plp.php?category=tech&search=">Tech</a></li>
              <li><a href="plp.php?category=homemade&search=">Homemade</a></li>
              <li><a href="plp.php?category=furniture&search=">Furniture</a></li>
              <li><a href="plp.php?category=decor&search=">Decor</a></li>
              <li><a href="plp.php?category=books&search=">Books</a></li>
            </ul>
          </div>
          <div class="links-column">
            <p class="links-column-heading">Profile Options</p>
            <ul>
              <li><a href="/public/pages/my-profile.php">Account</a></li>
              <li><a href="/public/pages/buyer-orders.php">My Orders</a></li>
              <li><a href="/public/pages/seller-info.php">Seller Info</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="notice-section">
      <p>Note that this is a protoype site meant for assessment purposes. This is not a real C2C site and should not be
        used for any real purchases or transactions.</p>
    </div>
  </footer>
</body>

</html>