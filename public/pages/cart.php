<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styling/main.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/footer.css">
  <link rel="stylesheet" href="../styling/cart.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>Shopping Cart</title>
</head>

<body>
  <header class="header">
    <div class="logo-icons-bar">
      <a class="header-logo-container" href="https://google.com"><img class="header-logo"
          src="../images/logo-placeholder-image.png" alt="C2C Logo"></a>
      <div class="header-icons-container">
        <a class="header-icon" href="https://google.com">
          <p>Account</p><img src="../icons/user.svg" alt="Account Icon">
        </a>
        <a class="header-icon" href="https://google.com">
          <p>Orders</p><img src="../icons/bag-shopping.svg" alt="Orders Icon">
        </a>
      </div>
    </div>
    <div class="search-filters-bar">
      <form id="search-form" action="plp.php" method="get">
        <div class="category-filter-container">
          <select title="category-filter" name="category" id="category" class="category-filter">
            <option value="all" <?php echo ($searchCategory === 'all') ? 'selected' : '' ?>>All</option>
            <option value="computers" <?php echo ($searchCategory === 'computers') ? 'selected' : '' ?>>Computers</option>
            <option value="homemade" <?php echo ($searchCategory === 'homemade') ? 'selected' : '' ?>>Homemade</option>
            <option value="tech" <?php echo ($searchCategory === 'tech') ? 'selected' : '' ?>>Tech</option>
            <option value="furniture" <?php echo ($searchCategory === 'furniture') ? 'selected' : '' ?>>Furniture</option>
            <option value="decor" <?php echo ($searchCategory === 'decor') ? 'selected' : '' ?>>Decor</option>
            <option value="books" <?php echo ($searchCategory === 'books') ? 'selected' : '' ?>>Books</option>
            <option value="uncategorized" <?php echo ($searchCategory === 'uncategorized') ? 'selected' : '' ?>>Uncategorized</option>
          </select>
        </div>
        <div class="search-form">
          <div class="search-container">
            <label class="search-label" for="search">Search</label>
            <input type="text" name="search" id="search" class="search-input" value="<?php echo htmlspecialchars($search) ?>">
            <button class="search-button" type="submit"><img src="../icons/magnifying-glass.svg"
                alt="Orders Icon"></button>
          </div>
        </div>
      </form>
    </div>
  </header>
  <section class="cart-section">
    <h1>Shopping Cart</h1>
    <div class="cart-container">
      <div class="cart-items-container">
        <div class="cart-items-header">
          <div></div>
          <p class="cart-item-header">Product Name</p>
          <p class="cart-item-header">Quantity</p>
          <p class="cart-item-header price-header">Price</p>
          <p class="cart-item-header"></p>
        </div>
        <div class="product-item">
          <img class="product-image" src="../images/product-placeholder.jpg" alt="Product Image">
          <p class="product-name grid-item-text">Gaming Laptop 1</p>
          <p class="quantity grid-item-text">1</p>
          <p class="product-price grid-item-text">R 19,999</p>
          <div class="remove-button grid-item-text"><img src="../icons/trash-can.svg" alt="Remove"></div>
        </div>
      </div>
      <div class="summary-checkout-container">
        <h5 class="total-text">SubTotal: R19,999</h5>
        <button class="checkout-button">Checkout</button>
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
              <li><a href="https://www.google.com">Computers</a></li>
              <li><a href="https://www.google.com">Tech</a></li>
              <li><a href="https://www.google.com">Homemade</a></li>
              <li><a href="https://www.google.com">Furniture</a></li>
              <li><a href="https://www.google.com">Decor</a></li>
              <li><a href="https://www.google.com">Books</a></li>
            </ul>
          </div>
          <div class="links-column">
            <p class="links-column-heading">Profile Options</p>
            <ul>
              <li><a href="https://www.google.com">Account</a></li>
              <li><a href="https://www.google.com">Orders</a></li>
              <li><a href="https://www.google.com">Seller Info</a></li>
            </ul>
          </div>
          <div class="links-column">
            <p class="links-column-heading">Site Information</p>
            <ul>
              <li><a href="https://www.google.com">Terms & Conditions</a></li>
              <li><a href="https://www.google.com">Privacy Policy</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="notice-section">
      <p>This is a protoype site meant for assessment purposes. This is not an official C2C site and should not be
        used for any real purchases or transactions.</p>
    </div>
  </footer>
</body>

</html>