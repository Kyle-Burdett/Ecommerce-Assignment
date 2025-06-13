<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: admin-login.php');
  exit;
}

$permissions = $_SESSION['permissions'];

if (!in_array('view_reports', $permissions)) {
  header('Location: admin-home.php');
  exit;
}

$userId = intval($_SESSION['user_id']);

$reports = ['totals', 'products', 'site_users'];
$reportSelect = 1;
$records = [];

$productTotalQuery = "SELECT COUNT(*) AS total FROM products";
$userTotalQuery = "SELECT COUNT(*) AS total FROM users";
$orderTotalQuery = "SELECT COUNT(*) AS total FROM orders";

$productQuery = "SELECT product_name, category, price FROM products";
$userQuery = "SELECT email_address, first_name, last_name FROM users";

$columnOneHeaders = ['Total Name', 'Product Name', 'User Email'];
$columnTwoHeaders = ['', 'Category', ''];
$columnThreeHeaders = ['Total', 'Price', 'Full Name'];
$currentIndex = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $records = [];

  if (isset($_POST['report-select'])) {
    $reportSelect = intval($_POST['report-select']);
    $link = mysqli_connect("localhost", "root", "", "c2c_db");

    if ($link === false) {
      die("Could not connect to server");
    }

    if ($reportSelect == 1) {
      $sqlPrep = mysqli_prepare($link, $productTotalQuery);
      mysqli_execute($sqlPrep);
      mysqli_stmt_bind_result($sqlPrep, $fetchedProductTotal);
      mysqli_stmt_fetch($sqlPrep);
      mysqli_stmt_close($sqlPrep);

      $sqlPrep = mysqli_prepare($link, $userTotalQuery);
      mysqli_execute($sqlPrep);
      mysqli_stmt_bind_result($sqlPrep, $fetchedUserTotal);
      mysqli_stmt_fetch($sqlPrep);
      mysqli_stmt_close($sqlPrep);

      $sqlPrep = mysqli_prepare($link, $orderTotalQuery);
      mysqli_execute($sqlPrep);
      mysqli_stmt_bind_result($sqlPrep, $fetchedOrderTotal);
      mysqli_stmt_fetch($sqlPrep);
      mysqli_stmt_close($sqlPrep);

      $records = [['Product Total', '', $fetchedProductTotal], ['User Total', '', $fetchedUserTotal], ['Order Total', '', $fetchedOrderTotal]];

    } else if ($reportSelect == 2) {
      $sqlQuery = $productQuery;
      $sqlPrep = mysqli_prepare($link, $sqlQuery);
      mysqli_execute($sqlPrep);
      mysqli_stmt_bind_result($sqlPrep, $fetchedColumn1Val, $fetchedColumn2Val, $fetchedColumn3Val);
      while (mysqli_stmt_fetch($sqlPrep)) {
        $records[] = ([$fetchedColumn1Val, $fetchedColumn2Val, $fetchedColumn3Val]);
      }
      mysqli_stmt_close($sqlPrep);
    } else {
      $sqlQuery = $userQuery;
      $sqlPrep = mysqli_prepare($link, $sqlQuery);
      mysqli_execute($sqlPrep);
      mysqli_stmt_bind_result($sqlPrep, $fetchedColumn1Val, $fetchedColumn2Val, $fetchedColumn3Val);
      while (mysqli_stmt_fetch($sqlPrep)) {
        $records[] = ([$fetchedColumn1Val, '', $fetchedColumn2Val . ' ' . $fetchedColumn3Val]);
      }
      mysqli_stmt_close($sqlPrep);
    }

    mysqli_close($link);
  }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styling/main.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/reports.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>User List</title>
</head>

<body>
  <div class="side-nav-section-container">
    <header class="side-nav">
      <div class="logo-section">
        <a class="side-nav-logo-container" href="https://google.com"><img class="nav-header-logo"
            src="../images/site-logo.png" alt="C2C Logo"></a>
      </div>
      <div class="side-nav-items-container">
        <div class="side-nav-item">
          <a href="admin-home.php" class="side-nav-link">
            <p class="nav-text">Home</p><img class="nav-icon" src="../icons/house.svg" alt="Home">
          </a>
        </div>
        <div class="side-nav-item">
          <a href="all-users.php" class="side-nav-link">
            <p class="nav-text">Users</p><img class="nav-icon" src="../icons/user.svg" alt="Users">
          </a>
        </div>
        <div class="side-nav-item">
          <a href="customize-site.php" class="side-nav-link">
            <p class="nav-text">Site Customization</p><img class="nav-icon" src="../icons/paintbrush.svg"
              alt="Customize Site">
          </a>
        </div>
        <div class="side-nav-item nav-active">
          <a href="reports.php" class="side-nav-link">
            <p class="nav-text">Reports</p><img class="nav-icon" src="../icons/file.svg" alt="Reports">
          </a>
        </div>
      </div>
    </header>
    <section class="reports-section">
      <div class="heading-container">
        <h1>Reports</h1>
      </div>
      <div class="report-select-container">
        <form class="report-select-form" method="post" action="reports.php">
          <label class="label" for="report-select">Select Report</label>
          <select class="input" title="report-select" id="report-select" name="report-select">
            <option class="option-text" value="1" <?php echo $reportSelect == 1 ? "selected" : "" ?>>Totals</option>
            <option class="option-text" value="2" <?php echo $reportSelect == 2 ? "selected" : "" ?>>Products</option>
            <option class="option-text" value="3" <?php echo $reportSelect == 3 ? "selected" : "" ?>>Users</option>
          </select>
          <button class="submit" type="submit">Load</button>
        </form>
      </div>
      <div class="reports-container">
        <div class="reports-header">
          <p class="report-header"><?php echo htmlspecialchars($columnOneHeaders[$reportSelect - 1]) ?></p>
          <p class="third-header report-header"><?php echo htmlspecialchars($columnTwoHeaders[$reportSelect - 1]) ?></p>
          <p class="fourth-header report-header"><?php echo htmlspecialchars($columnThreeHeaders[$reportSelect - 1]) ?></p>
        </div>
        <?php if (empty($records)): ?>
          <div class="no-reports-message"><p>There are no reports currently available to view.</p></div>
        <?php else:
        foreach($records as $record): ?>
          <div class="report-item">
          <p class="first-text grid-item-text"><?php echo htmlspecialchars($record[0]); ?></p>
          <p class="third-text grid-item-text"><?php echo htmlspecialchars($record[1]); ?></p>
          <p class="fourth-text grid-item-text"><?php echo htmlspecialchars($record[2]); ?></p>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>
</body>

</html>