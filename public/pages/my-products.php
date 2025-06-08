<?php 

session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$userId = intval($_SESSION['user_id']);

$link = mysqli_connect("localhost", "root", "", "c2c_db");

if ($link === false) {
  die("Could not connect to server");
}

$sqlPrep = mysqli_prepare($link, "SELECT  product_id, product_name, price, category, inventory, product_image FROM products WHERE user_id = ?");
mysqli_stmt_bind_param($sqlPrep, 'i', $userId);
mysqli_execute($sqlPrep);
mysqli_stmt_bind_result($sqlPrep, $productId, $name, $price, $category, $inventory, $productImage);


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styling/main.css">
  <link rel="stylesheet" href="../styling/my-products.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>Product List</title>
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
  <section class="my-products-section">
    <div class="heading-container">
      <div class="heading-back-container"><a class="back-link" onclick="history.back()"><img src="../icons/arrow-left.svg" alt="Back arrow"><p>Back</p></a><h1>My Products</h1></div>
      <a class="add-product-button" href="add-product.php">Add</a>
    </div>
    
    <div class="products-container">
        <div class="products-header">
          <div></div>
          <p class="product-header">Product Name</p>
          <p class="product-header">In Stock</p>
          <p class="product-header price-header">Price</p>
        </div>
        <?php 

        $hasProducts = false;
        
        while (mysqli_stmt_fetch($sqlPrep)) {
          if (!$hasProducts) {
            $hasProducts = true;
          }
          
          $priceFormatted = htmlspecialchars(number_format($price, 2, ".", ","));
          $nameFormatted = htmlspecialchars($name);
          $inventoryFormatted = htmlspecialchars($inventory);
          if (isset($productImage)) {
            $imagePathFormatted = htmlspecialchars($productImage);
          } else {
            $imagePathFormatted = '../images/product-placeholder.jpg';
          }
          $safeProductId = urlencode(intval($productId));
          $productItem = "<a class=\"product-item-link\" href=\"add-product.php?product_id=$safeProductId\"><div class=\"product-item\">
          <img class=\"product-image\" src=\"$imagePathFormatted\" alt=\"Product Image\">
          <p class=\"product-name grid-item-text\">$nameFormatted</p>
          <p class=\"quantity grid-item-text\">$inventoryFormatted</p>
          <p class=\"product-price grid-item-text\">R $priceFormatted</p>
        </div></a>";
        echo $productItem;
        }

        if (!$hasProducts) {
          echo "<div class=\"no-products-message\"><p>You have no products listed</p></div>";
        }

        mysqli_stmt_close($sqlPrep);
        mysqli_close($link);
        ?>
      </div>
  </section>
  <footer>
    <div class="links-section">
      <div class="links-container">
        <div class="links-block">
          <div class="links-column">
            <p class="links-column-heading">Search by category:</p>
            <ul>
              <li><a href="plp.php/category=computers&search=">Computers</a></li>
              <li><a href="plp.php/category=tech&search=">Tech</a></li>
              <li><a href="plp.php/category=homemade&search=">Homemade</a></li>
              <li><a href="plp.php/category=furniture&search=">Furniture</a></li>
              <li><a href="plp.php/category=decor&search=">Decor</a></li>
              <li><a href="plp.php/category=books&search=">Books</a></li>
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