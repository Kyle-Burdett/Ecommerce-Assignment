<?php
session_start();
$errors = array('email-address' => '', 'password' => '');

$email = $password = '';

$dbError = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $email = trimInput($_POST['email']);
  $password = trimInput($_POST['password']);

  if (empty($email)) {
    $errors['email-address'] = "An email is required.";
  } else {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email-address'] = "Invalid email address.";
    } else {
      $errors['email-address'] = "";
    }
  }

  if (empty($password)) {
    $errors['password'] = "A password is required.";
  }

  if (!array_filter($errors)) {

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $link = mysqli_connect("localhost", "root", "", "c2c_db");

    if ($link === false) {
      die("Error: Failed to connect. " . mysqli_connect_error());
    }
    $sqlPrep = mysqli_prepare($link, "INSERT INTO buyers (first_name, last_name, email_address, physical_address, buyer_password) VALUES (?, ?, ?, ?, ?)");
    if ($sqlPrep) {
      mysqli_stmt_bind_param($sqlPrep, "sssss", $firstName, $lastName, $email, $physicalAddress, $passwordHash);
      mysqli_stmt_execute($sqlPrep);
      mysqli_stmt_close($sqlPrep);
    } else {
      $dbError = "Something went wrong Please try again later.";
    }

    $userId =  mysqli_insert_id($link);
    $_SESSION['user_id'] = $userId;

    mysqli_close($link);

    header('Location: homepage.php');
    exit();
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
      <p class="sign-up-text">Don't have an account? <a href="create_account.php" class="sign-up-link">Sign up</a></p>
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