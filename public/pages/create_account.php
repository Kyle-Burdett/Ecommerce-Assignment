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
  <title>Create an Account</title>
</head>

<?php

  $nameRegex = '/^[a-zA-Z\s]+$/';
  $passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/';
  $errors = array('first-name'=>'', 'last-name'=>'', 'email-address'=>'', 'physical-address'=>'', 'password'=>'');

  $firstName = $lastName = $email = $physicalAddress = $password = '';

  if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $firstName = trimInput($_POST['first-name']);
    $lastName = trimInput($_POST['last-name']);
    $email = trimInput($_POST['email']);
    $physicalAddress = trimInput($_POST['physical-address']);
    $password = trimInput($_POST['password']);

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
        $errors['email'] = "Invalid email address.";
      } else {
        $errors['email'] = "";
      }
    }

    if (empty($physicalAddress)) {
      $errors['physical-address'] = "A physical address is required.";
    } else {
        $errors['physical-address'] = "";
    }

    if (empty($password)) {
      $errors['password'] = "A password is required.";
    } else {
      if (strlen($password) < 10) {
        $errors['password'] = "Password must be at least 10 characters long.";
      } else if (strlen($password) > 40) {
        $errors['password'] = "Password must be under 40 characters.";
      } else if (!preg_match($passwordRegex, $password)) {
        $errors['password'] = "Password must include at least one lower case character, uppercase character, number, and special character.";
      } else {
        $errors['password'] = "";
      }
    }

    if (!array_filter($errors)) {

      $link = mysqli_connect("localhost", "root", "", "c2c_db");

      if ($link === false) {
        die("Error: Failed to connect. ".mysqli_connect_error());
      }
      $sqlPrep = mysqli_prepare($link, "INSERT INTO buyers (first_name, last_name, email_address, physcial_address, buyer_password) VALUES (?, ?, ?, ?, ?)");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "sssss", $firstName, $lastName, $email, $physicalAddress, $password);
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
  <section class="signup-section">
    <div class="login-block">
      <h2>Create an Account</h2>
      <form class="login-form" action="create_account.php" target="_self">
        <div class="name-inputs-wrapper">
          <div class="name-input-container">
            <label class="login-label" for="first-name">First Name</label>
            <input type="text" id="first-name" name="first-name" class="login-input login-input-first" required>
          </div>
          <div class="name-input-container">
            <label class="login-label" for="last-name">Last Name</label>
            <input type="text" id="last-name" name="last-name" class="login-input login-input-first" required>
          </div>
        </div>
        <label class="login-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="login-input" required>
        <label class="login-label" for="physical-address">Physical Address</label>
        <input type="text" id="physical-address" name="physical-address" class="login-input" required>
        <label class="login-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="login-input login-input-last" required>
        <button class="login-submit" type="submit">Sign up</button>
      </form>
      <p class="sign-up-text">Have an Account? <a href="https://www.google.com" class="sign-up-link">Sign in</a></p>
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
  
  
  <script>
    function validatePassword() {
      const passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/';
    }
  </script>
</body>

</html>