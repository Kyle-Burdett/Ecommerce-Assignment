<?php
session_start();

$nameRegex = '/^[a-zA-Z\s]+$/';
$passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?+\-&])/';
$errors = array('first-name' => '', 'last-name' => '', 'email-address' => '', 'physical-address' => '', 'password' => '');

$firstName = $lastName = $email = $physicalAddress = $password = '';

$dbError = "";

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
    $errors['email-address'] = "An email is required.";
  } else {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email-address'] = "Invalid email address.";
    } else {
      $errors['email-address'] = "";
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

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $link = mysqli_connect("localhost", "root", "", "c2c_db");

    if ($link === false) {
      die("Error: Failed to connect. " . mysqli_connect_error());
    }
    $sqlPrep = mysqli_prepare($link, "INSERT INTO users (first_name, last_name, email_address, physical_address, user_password) VALUES (?, ?, ?, ?, ?)");
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
  <title>Create an Account</title>
</head>

<body>
  <header class="header">
    <div class="logo-icons-bar">
      <a class="header-logo-container" href="homepage.php" class="header-logo"
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
  <section class="signup-section">
    <div class="login-block">
      <h2>Create an Account</h2>
      <form class="login-form" action="create_account.php" method="post" onsubmit="return validateSubmit()">
        <div class="name-inputs-wrapper">
          <div class="name-input-container">
            <label class="login-label" for="first-name">First Name</label>
            <p id="first-name-error" class="error-text"><?php echo $errors['first-name']?></p>
            <input type="text" id="first-name" name="first-name" class="login-input login-input-first" value="<?php echo htmlspecialchars($firstName) ?>" required>
          </div>
          <div class="name-input-container">
            <label class="login-label" for="last-name">Last Name</label>
            <p id="last-name-error" class="error-text"><?php echo $errors['last-name']?></p>
            <input type="text" id="last-name" name="last-name" class="login-input login-input-first" value="<?php echo htmlspecialchars($lastName) ?>" required>
          </div>
        </div>
        <label class="login-label" for="email">Email Address</label>
        <p id="email-error" class="error-text"><?php echo $errors['email-address']?></p>
        <input type="email" id="email" name="email" class="login-input" value="<?php echo htmlspecialchars($email) ?>" required>
        <label class="login-label" for="physical-address">Physical Address</label>
        <p id="address-error" class="error-text"><?php echo $errors['physical-address']?></p>
        <input type="text" id="physical-address" name="physical-address" class="login-input" value="<?php echo htmlspecialchars($physicalAddress) ?>" required>
        <label class="login-label" for="password">Password</label>
        <p id="password-error" class="error-text"><?php echo $errors['password']?></p>
        <input type="password" id="password" name="password" class="login-input login-input-last" value="<?php echo htmlspecialchars($password) ?>" required>
        <button class="login-submit" type="submit">Sign up</button>
      </form>
      <p class="sign-up-text">Have an Account? <a href="login.php" class="sign-up-link">Sign in</a></p>
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
    function validateSubmit() {
      const firstName = document.getElementById("first-name").value.trim();
      const lastName = document.getElementById("last-name").value.trim();
      const emailAddress = document.getElementById("email").value.trim();
      const physicalAddress = document.getElementById("physical-address").value.trim();
      const password = document.getElementById("password").value.trim();

      document.getElementById("first-name-error").textContent = "";
      document.getElementById("last-name-error").textContent = "";
      document.getElementById("email-error").textContent = "";
      document.getElementById("address-error").textContent = "";
      document.getElementById("password-error").textContent = "";

      let valid = true;
      
      if  (firstName === "") {
        valid = false;
        document.getElementById("first-name-error").textContent = "A first name is required";
      } else if (validateName(firstName) == false) {
        document.getElementById("first-name-error").textContent = "Name should have only upper and lower case characters(A-Z, a-z).";
        valid = false;
      }

      if  (lastName === "") {
        valid = false;
        document.getElementById("last-name-error").textContent = "A last name is required";
      } else if (validateName(lastName) == false) {
        document.getElementById("last-name-error").textContent = "Name should have only upper and lower case characters(A-Z, a-z).";
        valid = false;
      }

      if  (emailAddress === "") {
        valid = false;
        document.getElementById("email-error").textContent = "An email address is required";
      } else {
        const emailRegex = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
        if (!emailRegex.test(emailAddress)) {
          valid = false;
          document.getElementById("email-error").textContent = "Invalid email address";
        }
      }

      if  (physicalAddress === "") {
        valid = false;
        document.getElementById("address-error").textContent = "A physical address is required";
      }

      if  (password === "") {
        valid = false;
        document.getElementById("password-error").textContent = "A password is required";
      } else if (validatePassword(password) == false) {
        valid = false;
      }

      return valid;
    }

    function validateName(name) {
      const nameRegex = /^[a-zA-Z\s]+$/;
      if (!nameRegex.test(name)) {
        return false;
      } else {
        return true;
      }
    }

    function validatePassword(password) {
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?+-&])/;

      if (password.length < 10 || password.length > 40) {
        document.getElementById("password-error").textContent = "Password must be between 10 and 40 characters";
        return false;
      }

      if (!passwordRegex.test(password)) {
        document.getElementById("password-error").textContent = "Password must have lower case characters, upper case characters, numbers, and special characters";
        return false;
      }

      return true;
    }
  </script>

  <?php if (!empty($dbError)): ?>
    <script>
      alert(<?php echo json_encode($dbError); ?>);
    </script>
  <?php endif; ?>
</body>

</html>