<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

require_once('../../private/db-credentials.php');

$userId = intval($_SESSION['user_id']);

if (isset($_GET['order_id'])) {

  $orderId = intval($_GET['order_id']);
  $link = mysqli_connect($hostName, $username, $password, $c2cDb);

  if ($link === false) {
    die("Could not connect");
  }

  $sqlPrep = mysqli_prepare($link, "SELECT o.order_id, o.order_date, o.order_status, o.total, o.shipping_address, o.shipping_date, o.received_date, o.buyer_id, o.seller_id, p.product_name, p.price FROM orders o JOIN order_items oi ON o.order_id = oi.order_id JOIN products p ON oi.product_id = p.product_id WHERE o.order_id = ?");
  if ($sqlPrep) {
    mysqli_stmt_bind_param($sqlPrep, 'i', $orderId);
    mysqli_execute($sqlPrep);
    mysqli_stmt_bind_result($sqlPrep, $fetchedId, $fetchedDate, $fetchedStatus, $fetchedTotal, $fetchedAddress, $fetchedShippingDate, $fetchedReceivedDate, $fetchedBuyerId, $fetchedSellerId, $fetchedProductName, $fetchedPrice);

    if (mysqli_stmt_fetch($sqlPrep)) {
      $orderId = $fetchedId;
      $orderDate = date('d/m/Y', strtotime($fetchedDate));
      $status = $fetchedStatus;
      $total = number_format(floatval($fetchedTotal), 2, ".", ",");
      $address = $fetchedAddress;
      if (isset($fetchedShippingDate)) {
        $shippingDate = date('d/m/Y', strtotime($fetchedShippingDate));
      } else {
        $shippingDate = "";
      }
      if (isset($fetchedReceivedDate)) {
        $receivedDate = date('d/m/Y', strtotime($fetchedReceivedDate));
      } else {
        $receivedDate = "";
      }
      $buyerId = intval($fetchedBuyerId);
      $sellerId = intval($fetchedSellerId);
      $productName = $fetchedProductName;
      $productPrice = number_format(floatval($fetchedPrice), 2, ".", ",");
    } else {
      header("Location: page-not-found.php");
      exit;
    }

    mysqli_stmt_close($sqlPrep);
  }
  mysqli_close($link);

  if ($userId != $buyerId && $userId != $sellerId) {
    header('Location: page-not-found.php');
    exit;
  }

  $buttonText = "Ship Order";

  if ($status == "shipping") {
    $buttonText = "Complete Order";
  }

  $submitValue = "shipping";

  if ($status == "shipping") {
    $submitValue = "completed";
  }
} else {
  header('Location: page-not-found.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if ($userId == $sellerId) {
    $allowedStatuses = ['shipping', 'completed'];
    $status = trimInput($_POST['order-status']);

    if (!in_array($status, $allowedStatuses)) {
      die("Invalid status");
    }

    if (isset($_POST['order_id'])) {
      $orderId = intval($_POST['order_id']);
    }

    if ($status && $orderId) {

      $link = mysqli_connect($hostName, $username, $password, $c2cDb);

      if ($link === false) {
        die("Could not connect");
      }

      $sqlPrep = mysqli_prepare($link, "UPDATE orders SET order_status = ? WHERE order_id = ?");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "si", $status, $orderId);
        mysqli_execute($sqlPrep);
        mysqli_stmt_close($sqlPrep);
      }

      mysqli_close($link);
    }
  }

  header("Location: order.php?order_id=" . urlencode($orderId));
  exit;
}

function trimInput($data)
  {
    $data = trim($data);
    return $data;
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
  <link rel="stylesheet" href="../styling/order.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>Order Page</title>
</head>

<body>
  <header class="header">
    <div class="logo-icons-bar">
      <a class="header-logo-container" href="https://google.com"><img class="header-logo"
          src="../images/site-logo.png" alt="C2C Logo"></a>
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
  <section class="order-section">
    <div class="heading-back-container">
      <a href="<?php echo ($userId == $sellerId) ? "seller-orders.php" : "buyer-orders.php" ?>" class="back-link"><img src="../icons/arrow-left.svg" alt="Back arrow">
        <p>Back</p>
      </a>
      <h1>Order <?php echo htmlspecialchars($orderId) ?></h1>
    </div>
    <div class="order-info-container">
      <p class="order-address">Address: <b><?php echo htmlspecialchars($address) ?></b></p>
      <p class="items-text">Items:</p>
      <div class="product-container">
        <h6 class="product-name"><?php echo htmlspecialchars($productName) ?></h6>
        <p class="quantity">x1</p>
        <p class="product-price">R <?php echo htmlspecialchars($productPrice) ?></p>
      </div>
      <p class="total-text">Total: <b>R <?php echo htmlspecialchars($total) ?></b></p>
      <div class="date-container">
        <p class="date-text">Order Date: <b><?php echo htmlspecialchars($orderDate) ?></b></p>
        <p class="date-text">Shipped Date: <b><?php echo htmlspecialchars($shippingDate) ?></b></p>
        <p class="date-text">Received Date: <b><?php echo htmlspecialchars($receivedDate) ?></b></p>
      </div>
      <p class="order-status">Status: <b><?php echo htmlspecialchars($status) ?></b></p>
      <?php if ($userId == $sellerId && $status != "completed"): ?>
        <form class="change-status-form" method="post" action="order.php?order_id=<?php echo urlencode($orderId); ?>">
          <input type="hidden" value="<?php echo htmlspecialchars($submitValue) ?>" name="order-status" id="order-status">
          <input type="hidden" value="<?php echo htmlspecialchars($orderId) ?>" name="order_id" id="order_id">
          <button class="fulfill-order-button" type="submit"><?php echo htmlspecialchars($buttonText) ?></button>
        </form>
      <?php endif; ?>
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