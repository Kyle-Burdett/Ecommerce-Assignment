<?php
session_start();

if (isset($_SESSION['user_id'])) {
  header('Location: homepage.php');
  exit;
}

require_once('../../private/db-credentials.php');

$errors = array('sign_in' => '');
$loginErrorMessage = "Invalid Email or Password";

$email = $password = '';

$dbError = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $email = trimInput($_POST['email']);
  $password = trimInput($_POST['password']);

  $link = mysqli_connect($hostName, $dbUsername, $dbPassword, $c2cDb);
  if ($link === false) {
    die("Error: Failed to connect. " . mysqli_connect_error());
  }

  $emailSqlPrep = mysqli_prepare($link, "SELECT * FROM users WHERE email_address = ?");
  if ($emailSqlPrep) {
    mysqli_stmt_bind_param($emailSqlPrep, "s", $email);
    mysqli_stmt_execute($emailSqlPrep);
    $result = mysqli_stmt_get_result($emailSqlPrep);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
      $userPassword = $row['user_password'];

      if (password_verify($password, $userPassword)) {
        $userId = $row['user_id'];
        $_SESSION['user_id'] = $userId;
        $errors['sign_in'] = "";
      } else {
        $errors['sign_in'] = $loginErrorMessage;
      }
    } else {
      $errors['sign_in'] = $loginErrorMessage;
    }

    mysqli_stmt_close($emailSqlPrep);
  }

  mysqli_close($link);

  if (!array_filter($errors)) {
    header('Location: homepage.php');
    exit();
  }

}

function trimInput($data) {
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
    
  </header>
  <section class="login-section">
    <div class="login-block">
      <h2>Login</h2>
      <form class="login-form" action="login.php" method="post">
        <label class="login-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="login-input" required value="<?php echo htmlspecialchars($email); ?>">
        <label class="login-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="login-input login-input-last" required>
        <button class="login-submit" type="submit">Login</button>
        <p class="error-text"><?php echo $errors['sign_in'] ?></p>
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