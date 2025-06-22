<?php 

session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

require_once('../../private/db-credentials.php');

$userId = intval($_SESSION['user_id']);

$link = mysqli_connect($hostName, $dbUsername, $dbPassword, $c2cDb);

if ($link === false) {
  die("Could not connect to server");
}

$sqlPrep = mysqli_prepare($link, "SELECT  order_id, order_status, total, shipping_address FROM orders WHERE seller_id = ?");
mysqli_stmt_bind_param($sqlPrep, 'i', $userId);
mysqli_execute($sqlPrep);
mysqli_stmt_bind_result($sqlPrep, $orderId, $status, $total, $address);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styling/main.css">
  <link rel="stylesheet" href="../styling/orders.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>Seller Orders</title>
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
  <section class="my-orders-section">
    <div class="heading-back-container"><a class="back-link" onclick="history.back()"><img src="../icons/arrow-left.svg" alt="Back arrow"><p>Back</p></a><h1>Seller Orders</h1></div>
    
    <div class="orders-container">
        <div class="orders-header">
          <p class="order-header">Order Id</p>
          <p class="order-header">Status</p>
          <p class="address-header order-header">Address</p>
          <p class="order-header total-header">Total</p>
        </div>
        <?php 

        $hasOrders = false;
        
        while (mysqli_stmt_fetch($sqlPrep)) {
          if (!$hasOrders) {
            $hasOrders = true;
          }
          
          $safeOrderId = urlencode(intval($orderId));
          $orderIdFormatted = htmlspecialchars($orderId);
          $orderStatus = htmlspecialchars($status);
          $addressFormatted = htmlspecialchars($address);
          $totalFormatted = htmlspecialchars(number_format($total, 2, ".", ","));
          
          $orderItem = "<a class=\"order-link\" href=\"order.php?order_id=$safeOrderId\"><div class=\"order-item\">
          <p class=\"order-id grid-item-text\">$orderIdFormatted</p>
          <p class=\"status-text grid-item-text\">$orderStatus</p>
          <p class=\"address-text grid-item-text\">$addressFormatted</p>
          <p class=\"total grid-item-text\">R $totalFormatted</p>
        </div></a>";
        echo $orderItem;
        }

        if (!$hasOrders) {
          echo "<div class=\"no-orders-message\"><p>You have no orders</p></div>";
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