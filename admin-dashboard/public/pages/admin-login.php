<?php
session_start();

if (isset($_SESSION['user_id'])) {
  header('Location: admin-home.php');
  exit;
}

$errors = array('sign_in' => '');
$loginErrorMessage = "Invalid Email or Password";

$email = $password = '';

$dbError = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $email = trimInput($_POST['email']);
  $password = trimInput($_POST['password']);

  $link = mysqli_connect("localhost", "root", "", "c2c_admin");
  if ($link === false) {
    die("Error: Failed to connect. " . mysqli_connect_error());
  }

  $emailSqlPrep = mysqli_prepare($link, "SELECT * FROM admin_users WHERE email_address = ?");
  if ($emailSqlPrep) {
    mysqli_stmt_bind_param($emailSqlPrep, "s", $email);
    mysqli_stmt_execute($emailSqlPrep);
    $result = mysqli_stmt_get_result($emailSqlPrep);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
      $userPassword = $row['user_password'];

      if (password_verify($password, $userPassword)) {
        $userId = $row['user_id'];
        $errors['sign_in'] = "";
        $_SESSION['user_id'] = $userId;
        mysqli_stmt_close($emailSqlPrep);

        $sqlPrep = mysqli_prepare($link, "SELECT DISTINCT p.permission_name FROM admin_users u JOIN user_roles ur ON u.user_id = ur.user_id JOIN role_permissions rp ON ur.role_id = rp.role_id JOIN permissions p ON rp.permission_id = p.permission_id WHERE u.user_id = ?");
        mysqli_stmt_bind_param($sqlPrep, "i", $userId);
        mysqli_stmt_execute($sqlPrep);
        mysqli_stmt_bind_result($sqlPrep, $permissionName);

        $permissions = [];
        while (mysqli_stmt_fetch($sqlPrep)) {
          $permissions[] = $permissionName;
        }

        mysqli_stmt_close($sqlPrep);
        $_SESSION['permissions'] = $permissions;
      } else {
        $errors['sign_in'] = $loginErrorMessage;
        mysqli_stmt_close($emailSqlPrep);
      }
    } else {
      $errors['sign_in'] = $loginErrorMessage;
      mysqli_stmt_close($emailSqlPrep);
    }
  }

  mysqli_close($link);

  if (!array_filter($errors)) {
    header('Location: admin-home.php');
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
  <link rel="stylesheet" href="../styling/admin-login.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>Admin Login</title>
</head>

<body>
  <header class="header">
    <div class="logo-icons-bar">
      <a class="header-logo-container" href="https://google.com"><img class="header-logo"
          src="../images/site-logo.png" alt="C2C Logo"></a>
    </div>
  </header>
  <section class="login-section">
    <div class="login-block">
      <h2>Admin Login</h2>
      <form class="login-form" action="admin-login.php" method="post">
        <label class="login-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="login-input" required value="<?php echo htmlspecialchars($email); ?>">
        <label class="login-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="login-input login-input-last" required>
        <button class="login-submit" type="submit">Login</button>
        <p class="error-text"><?php echo $errors['sign_in'] ?></p>
      </form>
    </div>
  </section>
</body>

</html>