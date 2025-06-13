<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: admin-login.php');
  exit;
}

require_once('db-credentials.php');

$userId = $_SESSION['user_id'];
$permissions = $_SESSION['permissions'];

if (!in_array('edit_site', $permissions)) {
  header('Location: admin-home.php');
  exit;
}

$link = mysqli_connect($hostName, $username, $password, $adminDb);

  if ($link === false) {
    die("Could not connect");
  }

  $siteLogo = "site_logo";

  $sqlPrep = mysqli_prepare($link, "SELECT option_value FROM site_options WHERE option_name = ?");
  if ($sqlPrep) {
    mysqli_stmt_bind_param($sqlPrep, 's', $siteLogo);
    mysqli_execute($sqlPrep);
    mysqli_stmt_bind_result($sqlPrep, $fetchedImagePath);

    if (mysqli_stmt_fetch($sqlPrep)) {
      $imagePath = $fetchedImagePath;
    } else {
      $imagePath = '../images/site-logo.png';
    }

    mysqli_stmt_close($sqlPrep);
  }
  mysqli_close($link);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (isset($_FILES['site-logo']) && $_FILES['site-logo']['error'] === UPLOAD_ERR_OK) {

    $fileTmpPath = $_FILES['site-logo']['tmp_name'];
    $fileName = $_FILES['site-logo']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $newFileName = time() . '-' . $fileName;

    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

    if (in_array($fileExtension, $allowedfileExtensions)) {

      $destPath = '../images/site-logo.png';

      if (move_uploaded_file($fileTmpPath, $destPath)) {
        $productImagePath = $destPath;
      } else {
        echo 'Error moving uploaded file.';
      }
    } else {
      echo 'Upload failed. Allowed types: ' . implode(',', $allowedfileExtensions);
    }
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
  <link rel="stylesheet" href="../styling/customize-site.css">
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
        <a class="side-nav-logo-container" href="admin-home.php"><img class="nav-header-logo"
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
        <div class="side-nav-item nav-active">
          <a href="customize-site.php" class="side-nav-link">
            <p class="nav-text">Site Customization</p><img class="nav-icon" src="../icons/paintbrush.svg" alt="Customize Site">
          </a>
        </div>
        <div class="side-nav-item">
          <a href="reports.php" class="side-nav-link">
            <p class="nav-text">Reports</p><img class="nav-icon" src="../icons/file.svg" alt="Reports">
          </a>
        </div>
      </div>
    </header>
    <section class="customize-site-section">
      <h1>Customize Site</h1>
      <form class="customize-form" method="post" action="customize-site.php" enctype="multipart/form-data">
        <label class="label" for="site-logo">Product Image</label>
        <img class="image-preview" src="../images/site-logo.png" alt="Image Preview">
        <input class="image-input" type="file" id="site-logo" name="site-logo" alt="Site Logo">
        <div class="button-wrapper">
          <button class="submit" type="button" id="cancel-button" onclick="history.back()">Cancel</button>
          <button class="submit" type="submit">Update</button>
        </div>
      </form>
    </section>
  </div>
</body>

</html>