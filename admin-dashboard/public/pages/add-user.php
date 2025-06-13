<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

require_once('../../private/db-credentials.php');

$userId = intval($_SESSION['user_id']);
$permissions = $_SESSION['permissions'];
$userRoleMap = [
  'admin' => 1,
  'manager' => 2,
  'viewer' => 3
];

if (!in_array('manage_users', $permissions)) {
  header('Location: admin-home.php');
  exit;
}

$errors = array('username' => '', 'email' => '', 'user-role' => '', 'password' => '');
$passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?+\-&])/';

if (isset($_GET['user_id'])) {
  $userViewId = intval($_GET['user_id']);
  $link = mysqli_connect($hostName, $dbUsername, $dbPassword, $adminDb);

  if ($link === false) {
    die("Could not connect");
  }

  $sqlPrep = mysqli_prepare($link, "SELECT u.user_id, u.username, u.email_address, r.role_name FROM admin_users u JOIN user_roles ur ON u.user_id = ur.user_id JOIN roles r ON ur.role_id = r.role_id  WHERE u.user_id = ?");
  if ($sqlPrep) {
    mysqli_stmt_bind_param($sqlPrep, 'i', $userViewId);
    mysqli_execute($sqlPrep);
    mysqli_stmt_bind_result($sqlPrep, $fetchedId, $fetchedUsername, $fetchedEmail, $fetchedRole);

    if (mysqli_stmt_fetch($sqlPrep)) {
      $userViewId = $fetchedId;
      $username = $fetchedUsername;
      $email = $fetchedEmail;
      $userRole = $fetchedRole;
    } else {
      $userViewId = -1;
      $username = '';
      $email = '';
      $userRole = '';
    }

    mysqli_stmt_close($sqlPrep);
  }
  mysqli_close($link);
} else {
  $userViewId = -1;
  $username = '';
  $email = '';
  $userRole = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);

    $link = mysqli_connect($hostName, $dbUsername, $dbPassword, $adminDb);

    if ($link === false) {
      die("Could not connect");
    }

    $sqlPrep = mysqli_prepare($link, "DELETE FROM admin_users WHERE user_id = ?");
    if ($sqlPrep) {
      mysqli_stmt_bind_param($sqlPrep, "i", $deleteId);
      mysqli_execute($sqlPrep);
      mysqli_stmt_close($sqlPrep);
    }

    mysqli_close($link);
    header('Location: all-users.php');
    exit;
  } else {
    if (isset($_POST['user_view_id'])) {
      $userViewId = intval($_POST['user_view_id']);
    }
    $username = trimInput($_POST['username']);
    $email = trimInput($_POST['email']);
    $userRole = trimInput($_POST['user-role']);
    $password = trimInput($_POST['password']);

    if (empty($username)) {
      $errors['username'] = "A username is required.";
    } else {
      if (strlen($username) > 100) {
        $errors['username'] = "Username needs to be under 100 characters";
      } else {
        $errors['username'] = "";
      }
    }

    if (empty($userRole)) {
      $errors['user-role'] = "A user role is required.";
    } else if (!array_key_exists($userRole, $userRoleMap)) {
      $errors['user-role'] = "Invalid role selected.";
    } else {
      $errors['user-role'] = "";
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

    if (!empty($password) || $userViewId == -1) {
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
    }

    if (!array_filter($errors)) {

      if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
      }

      $userRoleId = $userRoleMap[$userRole];
      $link = mysqli_connect($hostName, $dbUsername, $dbPassword, $adminDb);

      if ($link === false) {
        die("Could not connect");
      }

      if ($userViewId === -1) {
        $sqlPrep = mysqli_prepare($link, "INSERT INTO admin_users (username, email_address, user_password) VALUES (?, ?, ?)");
        if ($sqlPrep) {
          mysqli_stmt_bind_param($sqlPrep, "sss", $username, $email, $passwordHash);
          if (mysqli_execute($sqlPrep)) {
            $userViewId = mysqli_insert_id($link);
          }
          mysqli_stmt_close($sqlPrep);
        }
        $sqlPrep = mysqli_prepare($link, "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
        if ($sqlPrep) {
          mysqli_stmt_bind_param($sqlPrep, "ii", $userViewId, $userRoleId);
          mysqli_execute($sqlPrep);
          mysqli_stmt_close($sqlPrep);
        }
      } else {
        if (!empty($password)) {
          $sqlPrep = mysqli_prepare($link, "UPDATE admin_users SET username = ?, email_address = ?, user_password = ? WHERE user_id = ?");
          mysqli_stmt_bind_param($sqlPrep, "sssi", $username, $email, $passwordHash, $userViewId);
          mysqli_execute($sqlPrep);
          mysqli_stmt_close($sqlPrep);
        } else {
          $sqlPrep = mysqli_prepare($link, "UPDATE admin_users SET username = ?, email_address = ? WHERE user_id = ?");
          mysqli_stmt_bind_param($sqlPrep, "ssi", $username, $email, $userViewId);
          mysqli_execute($sqlPrep);
          mysqli_stmt_close($sqlPrep);
        }

        $sqlPrep = mysqli_prepare($link, "UPDATE user_roles SET role_id = ? WHERE user_id = ?");
        if ($sqlPrep) {
          mysqli_stmt_bind_param($sqlPrep, "ii", $userRoleId, $userViewId);
          mysqli_execute($sqlPrep);
          mysqli_stmt_close($sqlPrep);
        }
      }

      mysqli_close($link);
    }
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
  <link rel="stylesheet" href="../styling/add-user.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>User Details</title>
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
    <section class="add-user-section">
      <div class="heading-back-container">
        <a href="all-users.php" class="back-link"><img src="../icons/arrow-left.svg" alt="Back arrow">
          <p>Back</p>
        </a>
        <h1>User</h1>
      </div>

      <form class="add-user-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <?php if ($userViewId !== -1): ?>
          <input type="hidden" name="user_view_id" value="<?php echo $userViewId; ?>">
        <?php endif; ?>
        <label class="label" for="username">Username</label>
        <p class="error-text"><?php echo $errors['username'] ?></p>
        <input class="input text-input" type="text" id="username" name="username" required
          value="<?php echo $username ?>">
        <label class="label" for="email">Email Address</label>
        <p class="error-text"><?php echo $errors['email'] ?></p>
        <input class="input text-input" type="email" id="email" name="email" required
          value="<?php echo $email ?>">
        <label class="label" for="user-role">User Role</label>
        <p class="error-text"><?php echo $errors['user-role'] ?></p>
        <select class="input" title="user-role" id="user-role" name="user-role">
          <option <?php echo ($userRole === 'viewer') ? 'selected' : '' ?> value="viewer">Viewer</option>
          <option <?php echo ($userRole === 'manager') ? 'selected' : '' ?> value="manager">Manager</option>
          <option <?php echo ($userRole === 'admin') ? 'selected' : '' ?> value="admin">Admin</option>
        </select>
        <label class="label" for="password">Password</label>
        <p class="error-text"><?php echo $errors['password'] ?></p>
        <input class="input text-input" type="password" id="password" name="password" <?php echo ($userViewId == -1) ? "required" : "" ?>
          value="">
        <div class="button-wrapper">
          <button class="submit" type="button" id="cancel-button" onclick="history.back()">Cancel</button>
          <button class="submit" type="submit"><?php echo ($userViewId === -1) ? 'Add' : 'Update' ?></button>
        </div>
      </form>
      <?php if ($userViewId !== -1): ?>
        <form class="delete-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
          <input type="hidden" name="delete_id" value="<?php echo $userViewId; ?>">
          <button class="submit delete-button">Delete User</button>
        </form>
      <?php endif; ?>

    </section>
  </div>
</body>

</html>