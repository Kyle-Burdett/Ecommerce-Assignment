<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styling/main.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/footer.css">
  <link rel="stylesheet" href="../styling/login.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>Login Page</title>
</head>

<?php

  $nameRegex = '/^[a-zA-Z\s]+$/';
  $errors = array('first-name'=>'', 'last-name'=>'', 'email'=>'');

  $firstName = $lastName = $email = '';

  if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $firstName = trimInput($_POST['first-name']);
    $lastName = trimInput($_POST['last-name']);
    $email = trimInput($_POST['email']);

    if (empty($firstName)) {
      $errors['first-name'] = "A first name is required.";
    } else {
      if (!preg_match($nameRegex, $firstName)) {
        $errors['first-name'] = "Name should have only upper and lower case characters(A-Z, a-z).";
      } else {
        $errors['first-name'] = "";
      }
    }

    if (empty($lastName)) {
      $errors['last-name'] = "A last name is required.";
    } else {
      if (!preg_match($nameRegex, $lastName)) {
        $errors['last-name'] = "Name should have only upper and lower case characters(A-Z, a-z).";
      } else {
        $errors['last-name'] = "";
      }
    }

    if (empty($email)) {
      $errors['email'] = "An email is required.";
    } else {
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email.";
      } else {
        $errors['email'] = "";
      }
    }

    if (isset($_POST['first-game'])) {
      $firstSouls = $_POST['first-game'];
    } else {
      $firstSouls = "";
    }

    if (isset($_POST['favourite-game'])) {
      $favSouls = $_POST['favourite-game'];
    } else {
      $favSouls = "";
    }

    if (isset($_POST['hardest-boss'])) {
      $hardestBoss = $_POST['hardest-boss'];
    } else {
      $hardestBoss = "";
    }

    if (isset($_POST['shield'])) {
      $useShieldText = $_POST['shield'];
      if ($useShieldText == "1") {
        $useShield = 1;
      } else {
        $useShield = 0;
      }
    } else {
      $useShield = 0;
    }

    if (isset($_POST['favourite-boss'])) {
      $favBoss = $_POST['favourite-boss'];
    } else {
      $favBoss = "";
    }

    if (!array_filter($errors)) {

      $link = mysqli_connect("localhost", "root", "", "darksoulsdb");

      if ($link === false) {
        die("Error: Failed to connect. ".mysqli_connect_error());
      }
      $sqlPrep = mysqli_prepare($link, "INSERT INTO soulanswers (firstName, lastName, email, firstSouls, favSouls, hardestBoss, shield, bestBoss) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "ssssssis", $firstName, $lastName, $email, $firstSouls, $favSouls, $hardestBoss, $useShield, $favBoss);
        mysqli_stmt_execute($sqlPrep);
        mysqli_stmt_close($sqlPrep);
      }

      mysqli_close($link);

      header('Location: thank-you.php');
    }

  }

  function trimInput($data) {
    $data = trim($data);
    return $data;
  }
?>

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
      <div class="category-filter-container">
        <select title="category-filter" name="category-filter" id="category-filter" class="category-filter">
          <option value="all">All</option>
          <option value="computers">Computers</option>
          <option value="homemade">Homemade</option>
          <option value="tech">Tech</option>
          <option value="furniture">Furniture</option>
          <option value="decor">Decor</option>
          <option value="books">Books</option>
        </select>
      </div>
      <form class="search-form">
        <div class="search-container">
          <label class="search-label" for="search">Search</label>
          <input type="text" name="search" id="search" class="search-input">
          <button class="search-button" type="submit"><img src="../icons/magnifying-glass.svg"
              alt="Orders Icon"></button>
        </div>
      </form>
    </div>
  </header>
  <section class="login-section">
    <div class="login-block">
      <h2>Login</h2>
      <form class="login-form">
        <label class="login-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="login-input" required>
        <label class="login-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="login-input login-input-last" required>
        <button class="login-submit" type="submit">Login</button>
      </form>
      <p class="sign-up-text">Don't have an account? <a href="https://www.google.com" class="sign-up-link">Sign up</a></p>
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