<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: admin-login.php');
  exit;
}

require_once('../../private/db-credentials.php');

$userId = $_SESSION['user_id'];
$permissions = $_SESSION['permissions'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styling/main.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/admin-home.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>Admin Home</title>
</head>

<body>
  <div class="side-nav-section-container">
    <header class="side-nav">
      <div class="logo-section">
      <a class="side-nav-logo-container" href="https://google.com"><img class="nav-header-logo"
          src="../images/site-logo.png" alt="C2C Logo"></a>
    </div>
    <div class="side-nav-items-container">
      <div class="side-nav-item nav-active">
        <a href="admin-home.php" class="side-nav-link"><p class="nav-text">Home</p><img class="nav-icon" src="../icons/house.svg" alt="Home"></a>
      </div>
      <div class="side-nav-item">
        <a href="all-users.php" class="side-nav-link"><p class="nav-text">Users</p><img class="nav-icon" src="../icons/user.svg" alt="Users"></a>
      </div>
      <div class="side-nav-item">
        <a href="customize-site.php" class="side-nav-link"><p class="nav-text">Site Customization</p><img class="nav-icon" src="../icons/paintbrush.svg" alt="Customize Site"></a>
      </div>
      <div class="side-nav-item">
        <a href="reports.php" class="side-nav-link"><p class="nav-text">Reports</p><img class="nav-icon" src="../icons/file.svg" alt="Reports"></a>
      </div>
    </div>
  </header>
  <section class="admin-home-section">
    <h1>C2C Dashboard</h1>
    <div class="admin-options">
      <a href="all-users.php">
      <div class="option-container">
        <h6>View and Manage Admin Users</h6>
      </div>
      </a>
      <a href="site-customization.php">
      <div class="option-container">
        <h6>Customize Site</h6>
      </div>
      </a>
      <a href="reports.php">
      <div class="option-container">
        <h6>View Site Reports</h6>
      </div>
      </a>
    </div>
  </section>
  </div>
</body>

</html>