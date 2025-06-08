<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
}

$userId = intval($_SESSION['user_id']);
$errors = array('name' => '', 'price' => '', 'description' => '', 'category' => '', 'inventory' => '');

if (isset($_GET['product_id'])) {
  $productId = intval($_GET['product_id']);
  $link = mysqli_connect("localhost", "root", "", "c2c_db");

  if ($link === false) {
    die("Could not connect");
  }

  $sqlPrep = mysqli_prepare($link, "SELECT product_id, product_name, price, product_description, category, user_id, inventory, product_image FROM products WHERE product_id = ?");
  if ($sqlPrep) {
    mysqli_stmt_bind_param($sqlPrep, 'i', $productId);
    mysqli_execute($sqlPrep);
    mysqli_stmt_bind_result($sqlPrep, $fetchedId, $fetchedName, $fetchedPrice, $fetchedDescription, $fetchedCategory, $fetchedUserId, $fetchedInventory, $fetchedImage);

    if (mysqli_stmt_fetch($sqlPrep)) {
      $productId = $fetchedId;
      $name = $fetchedName;
      $price = $fetchedPrice;
      $description = $fetchedDescription;
      $category = $fetchedCategory;
      $productSellerId = $fetchedUserId;
      $inventory = $fetchedInventory;
      $productImage = $fetchedImage;
    } else {
      $productId = $productSellerId = -1;
      $name = $description = $category = '';
      $price = 0.00;
      $inventory = 0;
      $productImage = '../images/product-placeholder.jpg';
    }

    mysqli_stmt_close($sqlPrep);
  }
  mysqli_close($link);

  if ($productSellerId != -1 && $productSellerId != $userId) {
    header('Location: page-not-found.php');
  }
} else {
  $productId = -1;
  $name = $description = $category = '';
  $price = 0.00;
  $inventory = 0;
  $productImage = '../images/product-placeholder.jpg';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  if (isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
  }
  $name = trimInput($_POST['product-name']);
  $description = trimInput($_POST['product-description']);
  $category = trimInput($_POST['product-category']);
  $price = floatval($_POST['product-price']);
  $inventory = intval($_POST['product-inventory']);

  if (empty($name)) {
    $errors['name'] = "A product name is required.";
  } else {
    if (strlen($name) > 100) {
      $errors['name'] = "Name needs to be under 100 characters";
    } else {
      $errors['name'] = "";
    }
  }

  if (!isset($price) || !is_numeric($price)) {
    $errors['price'] = "A valid price is required.";
  } else {
    $errors['price'] = '';
  }

  if (empty($description)) {
    $description = "";
  } else if (strlen($description) > 1000) {
    $errors['description'] = "Description needs to be under 1000 characters";
  } else {
    $errors['description'] = "";
  }

  if (!isset($inventory) || !is_numeric($inventory)) {
    $errors['inventory'] = "A valid inventory is required.";
  } else {
    $errors['inventory'] = '';
  }

  if (empty($category)) {
    $errors['category'] = "A category is required.";
  } else {
    $errors['category'] = "";
  }

  if (!array_filter($errors)) {

    $productImagePath = $productImage;

    if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] === UPLOAD_ERR_OK) {
      $fileTmpPath = $_FILES['product-image']['tmp_name'];
      $fileName = $_FILES['product-image']['name'];
      $fileSize = $_FILES['product-image']['size'];
      $fileType = $_FILES['product-image']['type'];
      $fileNameCmps = explode(".", $fileName);
      $fileExtension = strtolower(end($fileNameCmps));

      $newFileName = time() . $fileName;

      $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

      if (in_array($fileExtension, $allowedfileExtensions)) {
        $uploadFileDir = '../uploads/products/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
          $productImagePath = $dest_path;
        } else {
          echo 'Error moving uploaded file.';
        }
      } else {
        echo 'Upload failed. Allowed types: ' . implode(',', $allowedfileExtensions);
      }
    }

    $link = mysqli_connect("localhost", "root", "", "c2c_db");

    if ($link === false) {
      die("Could not connect");
    }

    if ($productId === -1) {
      $sqlPrep = mysqli_prepare($link, "INSERT INTO products (product_name, price, product_description, category, user_id, inventory, product_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "sdssiis", $name, $price, $description, $category, $userId, $inventory, $productImagePath);
        if (mysqli_execute($sqlPrep)) {
          $productId = mysqli_insert_id($link);
        }
        mysqli_stmt_close($sqlPrep);
      }
    } else {
      $sqlPrep = mysqli_prepare($link, "UPDATE products SET product_name = ?, price = ?, product_description = ?, category = ?, inventory = ?, product_image = ? WHERE product_id = ?");
      if ($sqlPrep) {
        mysqli_stmt_bind_param($sqlPrep, "sdssisi", $name, $price, $description, $category, $inventory, $productImagePath, $productId);
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
  <link rel="stylesheet" href="../styling/add-product.css">
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
  <section class="add-product-section">
    <div class="heading-back-container">
      <a href="my-products.php" class="back-link"><img src="../icons/arrow-left.svg" alt="Back arrow">
        <p>Back</p>
      </a>
      <h1>Product Details</h1>
    </div>

    <form class="add-product-form" method="post" action="add-product.php" enctype="multipart/form-data">
      <label class="label" for="product-image">Product Image</label>
      <img class="image-preview" src="<?php echo $productImage ?>" alt="Image Preview">
      <input class="image-input" type="file" id="product-image" name="product-image" alt="Product Image">
      <?php if ($productId !== -1): ?>
        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
      <?php endif; ?>
      <label class="label" for="product-name">Product Name</label>
      <p class="error-text"><?php echo $errors['name'] ?></p>
      <input class="input text-input" type="text" id="product-name" name="product-name" required value="<?php echo htmlspecialchars($name) ?>">
      <label class="label" for="product-description">Description</label>
      <p class="error-text"><?php echo $errors['description'] ?></p>
      <textarea rows="10" cols="50" id="product-description" name="product-description"><?php echo htmlspecialchars($description) ?></textarea>
      <label class="label" for="product-category">Product category</label>
      <p class="error-text"><?php echo $errors['category'] ?></p>
      <select class="input" title="product-category" id="product-category" name="product-category">
        <option value="uncategorized" <?php echo ($category === 'uncategorized') ? 'selected' : '' ?>>Uncategorized</option>
        <option value="computers" <?php echo ($category === 'computers') ? 'selected' : '' ?>>Computers</option>
        <option value="homemade" <?php echo ($category === 'homemade') ? 'selected' : '' ?>>Homemade</option>
        <option value="tech" <?php echo ($category === 'tech') ? 'selected' : '' ?>>Tech</option>
        <option value="furniture" <?php echo ($category === 'furniture') ? 'selected' : '' ?>>Furniture</option>
        <option value="decor" <?php echo ($category === 'decor') ? 'selected' : '' ?>>Decor</option>
        <option value="books" <?php echo ($category === 'books') ? 'selected' : '' ?>>Books</option>
        <option value="fashion" <?php echo ($category === 'fashion') ? 'selected' : '' ?>>Fashion</option>
      </select>
      <div class="price-inventory-wrapper">
        <div>
          <label class="label" for="product-price">Price</label>
          <p class="error-text"><?php echo $errors['price'] ?></p>
          <input class="input text-input" type="number" id="product-price" name="product-price" step="0.01" required value="<?php echo htmlspecialchars($price) ?>">
        </div>
        <div>
          <label class="label" for="product-inventory">Inventory</label>
          <p class="error-text"><?php echo $errors['inventory'] ?></p>
          <input class="input text-input" type="number" id="product-inventory" name="product-inventory" required value="<?php echo htmlspecialchars($inventory) ?>">
        </div>
      </div>
      <div class="button-wrapper">
        <button class="submit" type="button" id="cancel-button" onclick="history.back()">Cancel</button>
        <button class="submit" type="submit"><?php echo ($productId === -1) ? 'Add' : 'Update' ?></button>
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
              <li><a href="plp.php/category=computers&search=">Computers</a></li>
              <li><a href="plp.php/category=tech&search=">Tech</a></li>
              <li><a href="plp.php/category=homemade&search=">Homemade</a></li>
              <li><a href="plp.php/category=furniture&search=">Furniture</a></li>
              <li><a href="plp.php/category=decor&search=">Decor</a></li>
              <li><a href="plp.php/category=books&search=">Books</a></li>
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