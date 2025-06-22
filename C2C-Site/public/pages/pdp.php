<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
}

require_once('../../private/db-credentials.php');

$link = mysqli_connect($hostName, $dbUsername, $dbPassword, $c2cDb);

if ($link === false) {
  die("Could not connect");
}

$logoPath = "";

$siteOptionName = "site_logo";
$sqlPrep = mysqli_prepare($link, "SELECT option_value FROM site_options WHERE option_name = ?");
  if ($sqlPrep) {
    mysqli_stmt_bind_param($sqlPrep, 's', $siteOptionName);
    mysqli_execute($sqlPrep);
    mysqli_stmt_bind_result($sqlPrep, $fetchedLogoPath);

    if (mysqli_stmt_fetch($sqlPrep)) {
      $logoPath = $fetchedLogoPath;
    }
    mysqli_stmt_close($sqlPrep);
  }
  mysqli_close($link);

$userId = intval($_SESSION['user_id']);
$searchCategory = "all";

if (isset($_GET['product_id'])) {
  $productId = intval($_GET['product_id']);
  $link = mysqli_connect($hostName, $dbUsername, $dbPassword, $c2cDb);

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
      $productImage = (!empty($fetchedImage)) ? htmlspecialchars($fetchedImage) : '../images/product-placeholder.jpg';
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

$link = mysqli_connect($hostName, $dbUsername, $dbPassword, $c2cDb);

if ($link === false) {
  die("Could not connect");
}

$sqlPrep = mysqli_prepare($link, "SELECT user_id, seller_name, seller_photo, seller_description FROM seller_info WHERE user_id = ?");
if ($sqlPrep) {
  mysqli_stmt_bind_param($sqlPrep, 'i', $productSellerId);
  mysqli_execute($sqlPrep);
  mysqli_stmt_bind_result($sqlPrep, $fetchedId, $fetchedName, $fetchedImage, $fetchedDescription);

  if (mysqli_stmt_fetch($sqlPrep)) {
    $sellerId = intval($fetchedId);
    $sellerName = $fetchedName;
    $sellerPhoto = $fetchedImage;
    $sellerDescription = $fetchedDescription;
  } else {
    $sellerId = -1;
    $sellerName = '';
    $sellerPhoto = '../images/product-placeholder.jpg';
    $sellerDescription = '';
  }

  mysqli_stmt_close($sqlPrep);
}
mysqli_close($link);

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
      <a class="header-logo-container" href="homepage.php"><img class="header-logo"
          src="<?php echo htmlspecialchars($logoPath) ?>" alt="C2C Logo"></a>
      <div class="header-icons-container">
        <a class="header-icon" href="my-profile.php">
          <p>Account</p><img src="../icons/user.svg" alt="Account Icon">
        </a>
        <a class="header-icon" href="buyer-orders.php">
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
        <?php if ($inventory > 0): ?>
        <form method="get" action="<?php echo ($userId == $productSellerId) ? "add-product.php" : "checkout.php" ?>">
          <input type="hidden" name="product-id" id="product-id" value="<?php echo htmlspecialchars($productId) ?>">
          <button class="buy-button"><?php echo ($userId == $productSellerId) ? 'See product' : 'Order' ?></button>
        </form>
        <?php endif; ?>
        <h3 class="info-heading">Seller</h3>
        <div class="seller-container">
          <h6 class="seller-name"><?php echo ($sellerId == -1) ? htmlspecialchars($firstName) . " " . htmlspecialchars($lastName) : htmlspecialchars($sellerName) ?></h6>
          <p class="info-text"><?php echo ($sellerId != -1) ? htmlspecialchars($sellerDescription) : "" ?></p>
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
              <li><a href="my-profile.php">Account</a></li>
              <li><a href="buyer-orders.php">My Orders</a></li>
              <li><a href="seller-info.php">Seller Info</a></li>
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