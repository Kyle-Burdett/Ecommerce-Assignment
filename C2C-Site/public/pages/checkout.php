<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
}

require_once('../../private/db-credentials.php');

$userId = intval($_SESSION['user_id']);
$errors = array('address' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (isset($_POST['product-id'])) {

    $productId = intval($_POST['product-id']);
    $link = mysqli_connect($hostName, $dbUsername, $dbPassword, $c2cDb);

    if ($link === false) {
      die("Could not connect");
    }

    $sqlPrep = mysqli_prepare($link, "SELECT p.product_name, p.price, p.inventory, u.user_id FROM products p JOIN users u ON p.user_id = u.user_id WHERE product_id = ?");
    if ($sqlPrep) {
      mysqli_stmt_bind_param($sqlPrep, 'i', $productId);
      mysqli_execute($sqlPrep);
      mysqli_stmt_bind_result($sqlPrep, $fetchedName, $fetchedPrice, $inventory, $fetchedId);

      if (mysqli_stmt_fetch($sqlPrep)) {
        $name = $fetchedName;
        $price = floatval($fetchedPrice);
        $total = $price;
        $inventory = intval($inventory);
        $sellerId = intval($fetchedId);
      } else {
        mysqli_stmt_close($sqlPrep);
        mysqli_close($link);
        header('Location: plp.php');
        exit;
      }

      mysqli_stmt_close($sqlPrep);
    }

    $sqlPrep = mysqli_prepare($link, "SELECT physical_address FROM users WHERE user_id = ?");
    if ($sqlPrep) {
      mysqli_stmt_bind_param($sqlPrep, 'i', $userId);
      mysqli_execute($sqlPrep);
      mysqli_stmt_bind_result($sqlPrep, $fetchedAddress);

      if (mysqli_stmt_fetch($sqlPrep)) {
        $address = $fetchedAddress;
      } else {
        $address = '';
      }

      mysqli_stmt_close($sqlPrep);

      mysqli_close($link);
    }
  } else {
    $address = trimInput($_POST['physical-address']);

    if (empty($address)) {
      $address = "";
    } else if (strlen($address) > 100) {
      $errors['address'] = "Address needs to be under 100 characters";
    } else {
      $errors['address'] = "";
    }

    if (!array_filter($errors)) {

      $link = mysqli_connect($hostName, $dbUsername, $dbPassword, $c2cDb);

      if ($link === false) {
        die("Could not connect");
      }

      $orderDate = date("Y-m-d H:i:s");
      $orderStatus = "pending";

      $sqlPrep = mysqli_prepare($link, "INSERT INTO orders (order_date, order_status, total, shipping_address, buyer_id, seller_id) VALUES (?, ?, ?, ?, ?, ?)");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "ssdsii", $orderDate, $orderStatus, $total, $address, $sellerId, $userId);
        if (mysqli_execute($sqlPrep)) {
          $orderId = intval(mysqli_insert_id($link));
        }
        mysqli_stmt_close($sqlPrep);
      }

      $sqlPrep = mysqli_prepare($link, "INSERT INTO order_items (order_id, product_id) VALUES (?, ?)");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "ii", $orderId, $productId);
        mysqli_execute($sqlPrep);
        mysqli_stmt_close($sqlPrep);
      }

      $sqlPrep = mysqli_prepare($link, "UPDATE products SET inventory = inventory - 1 WHERE product_id = ? AND inventory > 0");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "i", $productId);
        mysqli_execute($sqlPrep);
        mysqli_stmt_close($sqlPrep);
      }

      mysqli_close($link);
      header('Location: buyer-orders.php');
    }
  }
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
  <link rel="stylesheet" href="../styling/checkout.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>Checkout</title>
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
  <section class="checkout-section">
    <div class="heading-back-container">
      <a href="my-profile.php" class="back-link"><img src="../icons/arrow-left.svg" alt="Back arrow">
        <p>Back</p>
      </a>
      <h1>Checkout</h1>
    </div>
    <form class="checkout-container" method="post" action="checkout.php">
      <div class="checkout-input-container">
        <label class="label" for="shipping-address">Shipping Address</label>
        <p class="error-text"></p>
        <input class="text-input input" type="text" id="shipping-address" name="shipping-address" value="<?php echo $address ?>">
      </div>
      <div class="order-details-container">
        <div class="product-container">
          <h6 class="product-name"><?php echo htmlspecialchars($name) ?></h6>
          <p class="quantity">x<?php echo htmlspecialchars($inventory) ?></p>
          <p class="product-price">R <?php echo number_format($price, 2, ".", ",") ?></p>
        </div>
        <div class="total-container">
          <h6 class="total">Total: R <?php echo number_format($total, 2, ".", ",") ?></h6>
        </div>
        <div class="button-wrapper">
          <button class="submit" type="button" id="cancel-button" onclick="history.back()">Cancel</button>
          <button class="submit" type="submit">Place Order</button>
        </div>
        <p></p>
      </div>
    </form>

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