<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styling/main.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/footer.css">
  <link rel="stylesheet" href="../styling/plp.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>PDP Page</title>
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
      <div class="category-filter-container">
        <select title="category-filter" name="category-filter" id="category-filter" class="category-filter">
          <option value="all">All</option>
          <option value="computers">Computers</option>
          <option value="homemade">Homemade</option>
          <option value="tech">Tech</option>
          <option value="furniture">Furniture</option>
          <option value="decor">Decor</option>
          <option value="books">Books</option>
        </select>
      </div>
      <form class="search-form">
        <div class="search-container">
          <label class="search-label" for="search">Search</label>
          <input type="text" name="search" id="search" class="search-input">
          <button class="search-button" type="submit"><img src="../icons/magnifying-glass.svg"
              alt="Orders Icon"></button>
        </div>
      </form>
    </div>
  </header>
  <section class="plp-section">
    <h5 class="search-results-text">50 Search Results</h5>
    <div class="product-list-container">
      <div class="product-item">
        <img class="product-image" src="../images/product-placeholder.jpg" alt="Product Image">
        <div class="text-container">
          <a href="https://www.google.com" class="product-link"><p class="product-name">Gaming Laptop 1</p></a>
          <p class="product-price">R 19,999</p>
          <p class="product-description">Description. This is the best laptop you could ever buy. So by now today. Lorem ipsum dolor ipsum...</p>
          <p class="product-seller">Seller: <a class="seller-name" href="https://www.google.com">John Smith</a></p>
        </div>
      </div>
      <div class="product-item">
        <img class="product-image" src="../images/product-placeholder.jpg" alt="Product Image">
        <div class="text-container">
          <a href="https://www.google.com" class="product-link"><p class="product-name">Bloodborne PC</p></a>
          <p class="product-price">R 1,200,000</p>
          <p class="product-description">Description. What we will never have.</p>
          <p class="product-seller">Seller: <a class="seller-name" href="https://www.google.com">Micahel Zaki</a></p>
        </div>
      </div>
      <div class="product-item">
        <img class="product-image" src="../images/product-placeholder.jpg" alt="Product Image">
        <div class="text-container">
          <a href="https://www.google.com" class="product-link"><p class="product-name">Shirt</p></a>
          <p class="product-price">R 19,999</p>
          <p class="product-description">Description. It’s incredibly comfortable.</p>
          <p class="product-seller">Seller: <a class="seller-name" href="https://www.google.com">John Smith</a></p>
        </div>
      </div>
      <div class="product-item">
        <img class="product-image" src="../images/product-placeholder.jpg" alt="Product Image">
        <div class="text-container">
          <a href="https://www.google.com" class="product-link"><p class="product-name">Headset</p></a>
          <p class="product-price">R 19,999</p>
          <p class="product-description">Description. This is the best headset you could ever buy. So by now today. Lorem ipsum dolor ipsum. Seriously, it gives the best experience for everything...</p>
          <p class="product-seller">Seller: <a class="seller-name" href="https://www.google.com">John Smith</a></p>
        </div>
      </div>
      <div class="product-item">
        <img class="product-image" src="../images/product-placeholder.jpg" alt="Product Image">
        <div class="text-container">
          <a href="https://www.google.com" class="product-link"><p class="product-name">Gaming Laptop 2</p></a>
          <p class="product-price">R 25,000</p>
          <p class="product-description">Description. This is the best laptop you could ever buy. Much better than Laptop 1 So by now today. Lorem ipsum dolor ipsum...</p>
          <p class="product-seller">Seller: <a class="seller-name" href="https://www.google.com">John Smith</a></p>
        </div>
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
      <p>Note that this is a protoype site meant for assessment purposes. This is not a real C2C site and should not be
        used for any real purchases or transactions.</p>
    </div>
  </footer>
</body>

</html>