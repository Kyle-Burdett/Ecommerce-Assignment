<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
}

require_once('db-credentials.php');

$userId = intval($_SESSION['user_id']);
$errors = array('name' => '', 'description' => '');

$link = mysqli_connect($hostName, $username, $password, $c2cDb);

if ($link === false) {
  die("Could not connect");
}

$sqlPrep = mysqli_prepare($link, "SELECT user_id, seller_name, seller_photo, seller_description FROM seller_info WHERE user_id = ?");
if ($sqlPrep) {
  mysqli_stmt_bind_param($sqlPrep, 'i', $userId);
  mysqli_execute($sqlPrep);
  mysqli_stmt_bind_result($sqlPrep, $fetchedId, $fetchedName, $fetchedImage, $fetchedDescription);

  if (mysqli_stmt_fetch($sqlPrep)) {
    $sellerId = intval($fetchedId);
    $name = $fetchedName;
    $sellerPhoto = $fetchedImage;
    $description = $fetchedDescription;
  } else {
    $sellerId = -1;
    $name = '';
    $sellerPhoto = '../images/product-placeholder.jpg';
    $description = '';
  }

  mysqli_stmt_close($sqlPrep);
}
mysqli_close($link);

$sellerImagePath = $sellerPhoto;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $name = trimInput($_POST['seller-name']);
  $description = trimInput($_POST['seller-description']);

  if (empty($name)) {
    $errors['name'] = "A name is required.";
  } else {
    if (strlen($name) > 100) {
      $errors['name'] = "Name needs to be under 100 characters";
    } else {
      $errors['name'] = "";
    }
  }

  if (empty($description)) {
    $description = "";
  } else if (strlen($description) > 1000) {
    $errors['description'] = "Description needs to be under 1000 characters";
  } else {
    $errors['description'] = "";
  }

  if (!array_filter($errors)) {

    $sellerImagePath = $sellerPhoto;

    if (isset($_FILES['seller-image']) && $_FILES['seller-image']['error'] === UPLOAD_ERR_OK) {
      $fileTmpPath = $_FILES['seller-image']['tmp_name'];
      $fileName = $_FILES['seller-image']['name'];
      $fileSize = $_FILES['seller-image']['size'];
      $fileType = $_FILES['seller-image']['type'];
      $fileNameCmps = explode(".", $fileName);
      $fileExtension = strtolower(end($fileNameCmps));

      $newFileName = time() . $fileName;

      $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

      if (in_array($fileExtension, $allowedfileExtensions)) {
        $uploadFileDir = '../uploads/users/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
          $sellerImagePath = $dest_path;
        } else {
          echo 'Error moving uploaded file.';
        }
      } else {
        echo 'Upload failed. Allowed types: ' . implode(',', $allowedfileExtensions);
      }
    }

    $link = mysqli_connect($hostName, $username, $password, $c2cDb);

    if ($link === false) {
      die("Could not connect");
    }

    if ($sellerId === -1) {
      $sqlPrep = mysqli_prepare($link, "INSERT INTO seller_info (user_id, seller_name, seller_photo, seller_description) VALUES (?, ?, ?, ?)");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "isss", $userId, $name, $sellerImagePath, $description);
        if (mysqli_execute($sqlPrep)) {
          $productId = mysqli_insert_id($link);
        }
        mysqli_stmt_close($sqlPrep);
      }
    } else {
      $sqlPrep = mysqli_prepare($link, "UPDATE seller_info SET seller_name = ?, seller_photo = ?, seller_description = ? WHERE user_id = ?");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "sssi", $name, $sellerImagePath, $description, $userId);
        mysqli_execute($sqlPrep);
        mysqli_stmt_close($sqlPrep);
      }
    }

    mysqli_close($link);
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
  <link rel="stylesheet" href="../styling/seller-info.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sora:wght@100..800&display=swap"
    rel="stylesheet">
  <title>Add Product Screen</title>
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
    <div class="search-filters-bar">
      <form class="search-form" action="plp.php" method="get">
        <div class="category-filter-container">
          <select title="category-filter" name="category" id="category" class="category-filter">
            <option value="all">All</option>
            <option value="computers">Computers</option>
            <option value="homemade">Homemade</option>
            <option value="tech">Tech</option>
            <option value="furniture">Furniture</option>
            <option value="decor">Decor</option>
            <option value="books">Books</option>
            <option value="uncategorized">Uncategorized</option>
          </select>
        </div>
        <div class="search-container">
          <label class="search-label" for="search">Search</label>
          <input type="text" name="search" id="search" class="search-input">
          <button class="search-button" type="submit"><img src="../icons/magnifying-glass.svg"
              alt="Orders Icon"></button>
        </div>
      </form>
    </div>
  </header>
  <section class="seller-info-section">
    <div class="heading-back-container">
      <a href="my-profile.php" class="back-link"><img src="../icons/arrow-left.svg" alt="Back arrow">
        <p>Back</p>
      </a>
      <h1>My Seller Info</h1>
    </div>

    <form class="seller-info-form" method="post" action="seller-info.php" enctype="multipart/form-data">
      <label class="label" for="seller-image">Seller Image</label>
      <img class="image-preview" src="<?php echo $sellerImagePath ?>" alt="Image Preview">
      <input class="image-input" type="file" id="seller-image" name="seller-image" alt="Seller Image">
      <label class="label" for="seller-name">Seller Name</label>
      <p class="error-text"></p>
      <input class="text-input input" type="text" id="seller-name" name="seller-name" value="<?php echo htmlspecialchars($name) ?>">
      <label class="label" for="seller-description">Description</label>
      <p class="error-text"></p>
      <textarea rows="10" cols="50" id="seller-description" name="seller-description"><?php echo htmlspecialchars($description) ?></textarea>
      <div class="button-wrapper">
        <button class="submit" type="button" id="cancel-button" onclick="history.back()">Cancel</button>
        <button class="submit" type="submit">Update</button>
      </div>
    </form>
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