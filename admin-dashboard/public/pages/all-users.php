<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: admin-login.php');
  exit;
}

require_once('../../private/db-credentials.php');

$requiredPermissions = ['manage_users', 'view_users'];
$permissions = $_SESSION['permissions'];

if (!array_intersect($requiredPermissions, $permissions)) {
  header('Location: admin-home.php');
  exit;
}

$userId = intval($_SESSION['user_id']);

$link = mysqli_connect($hostName, $username, $password, $adminDb);

if ($link === false) {
  die("Could not connect to server");
}

$sqlPrep = mysqli_prepare($link, "SELECT  u.user_id, u.username, u.email_address, r.role_name FROM admin_users u JOIN user_roles ur ON u.user_id = ur.user_id JOIN roles r ON ur.role_id = r.role_id");
mysqli_execute($sqlPrep);
mysqli_stmt_bind_result($sqlPrep, $userViewId, $username, $email, $role);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styling/main.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/all-users.css">
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
        <div class="side-nav-item nav-active">
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
        <div class="side-nav-item">
          <a href="reports.php" class="side-nav-link">
            <p class="nav-text">Reports</p><img class="nav-icon" src="../icons/file.svg" alt="Reports">
          </a>
        </div>
      </div>
    </header>
    <section class="all-users-section">
      <div class="heading-container">
        <h1>Admin Users</h1>
        <?php if (in_array('manage_users', $permissions)) {
          echo "<a class=\"add-user-button\" href=\"add-user.php\">Add</a>";
        }
        ?>
      </div>
      <div class="users-container">
        <div class="users-header">
          <p class="user-header">Username</p>
          <p class="email-header user-header">Email</p>
          <p class="role-header user-header">Role</p>
        </div>
        <?php

        $hasUsers = false;

        while (mysqli_stmt_fetch($sqlPrep)) {
          if (!$hasUsers) {
            $hasUsers = true;
          }

          $safeUserId = urlencode(intval($userViewId));
          $usernameFormatted = htmlspecialchars($username);
          $emailFormatted = htmlspecialchars($email);
          $roleFormatted = htmlspecialchars($role);
          if (in_array('manage_users', $permissions)) {
            $userItem = "<a class=\"user-link\" href=\"add-user.php?user_id=$safeUserId\"><div class=\"user-item\">
          <p class=\"username grid-item-text\">$usernameFormatted</p>
          <p class=\"email-text grid-item-text\">$emailFormatted</p>
          <p class=\"role-text grid-item-text\">$roleFormatted</p>
        </div></a>";
          } else {
            $userItem = "<a class=\"user-link\"><div class=\"user-item\">
          <p class=\"username grid-item-text\">$usernameFormatted</p>
          <p class=\"email-text grid-item-text\">$emailFormatted</p>
          <p class=\"role-text grid-item-text\">$roleFormatted</p>
        </div></a>";
          }

          echo $userItem;
        }

        if (!$hasUsers) {
          echo "<div class=\"no-users-message\"><p>There are currently no users available to view.</p></div>";
        }

        mysqli_stmt_close($sqlPrep);
        mysqli_close($link);
        ?>
      </div>
    </section>
  </div>
</body>

</html>