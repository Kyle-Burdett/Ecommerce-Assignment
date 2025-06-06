<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
}

$userId = intval($_SESSION['user_id']);
$searchCategory = "all";

if (isset($_GET['product_id'])) {
  $productId = intval($_GET['product_id']);
  $link = mysqli_connect("localhost", "root", "", "c2c_db");

  if ($link === false) {
    die("Could not connect");
  }

  $sqlPrep = mysqli_prepare($link, "SELECT p.product_id, p.product_name, p.price, p.product_description, p.category, p.user_id, p.inventory, p.product_image, u.first_name, u.last_name FROM products p JOIN users u ON p.user_id = u.user_id WHERE product_id = ?");
  if ($sqlPrep) {
    mysqli_stmt_bind_param($sqlPrep, 'i', $productId);
    mysqli_execute($sqlPrep);
    mysqli_stmt_bind_result($sqlPrep, $fetchedId, $fetchedName, $fetchedPrice, $fetchedDescription, $fetchedCategory, $fetchedUserId, $fetchedInventory, $fetchedImage, $fetchedFirstName, $fetchedLastName);

    if (mysqli_stmt_fetch($sqlPrep)) {
      $productId = $fetchedId;
      $name = $fetchedName;
      $price = $fetchedPrice;
      $description = $fetchedDescription;
      $category = $fetchedCategory;
      $productSellerId = $fetchedUserId;
      $inventory = $fetchedInventory;
      $productImage = (!empty($fetchedImage)) ? htmlspecialchars($fetchedImage) : '../images/product-placeholder.jpg';;
      $firstName = $fetchedFirstName;
      $lastName = $fetchedLastName;
    } else {
      header('Location: page-not-found.php');
    }

    mysqli_stmt_close($sqlPrep);
  }
  mysqli_close($link);
} else {
  header('Location: page-not-found.php');
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
  <link rel="stylesheet" href="../styling/pdp.css">

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
  <section class="pdp-section">
    <div class="product-container">
      <div class="images-container">
        <img class="main-image" src="<?php echo $productImage ?>" alt="product-main">
      </div>
      <div class="info-container">
        <p class="category-text"><?php echo htmlspecialchars($category) ?></p>
        <h1 class="product-name"><?php echo htmlspecialchars($name) ?></h1>
        <h3 class="product-price">R <?php echo htmlspecialchars(number_format($price, 2, ".", ",")) ?></h3>
        <p class="product-description"><?php echo htmlspecialchars($description) ?></p>
        <button class="buy-button"><?php echo ($userId == $productSellerId) ? 'See product' : 'Add to Cart' ?></button>
        <h3 class="info-heading">Seller</h3>
        <div class="seller-container">
          <h6 class="seller-name"><?php echo htmlspecialchars($firstName) . " " . htmlspecialchars($lastName) ?></h6>
          <p class="info-text">Hey! I’m James, trying to sell second hand equipment. Check out my details.</p>
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