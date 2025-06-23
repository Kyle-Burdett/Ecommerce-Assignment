<?php

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  header("Access-Control-Allow-Origin: https://kyle-c2c-admin.wuaze.com");
  header("Access-Control-Allow-Methods: POST, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");
  http_response_code(200);
  exit();
}

header("Access-Control-Allow-Origin: https://kyle-c2c-admin.wuaze.com");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");


require_once('../private/db-credentials.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $siteOptionName = "site_logo";
  $link = mysqli_connect($hostName, $dbUsername, $dbPassword, $c2cDb);

  $sqlPrep = mysqli_prepare($link, "SELECT option_value FROM site_options WHERE option_name = ?");
  if ($sqlPrep) {
    mysqli_stmt_bind_param($sqlPrep, 's', $siteOptionName);
    mysqli_execute($sqlPrep);
    mysqli_stmt_bind_result($sqlPrep, $fetchedLogoPath);

    if (mysqli_stmt_fetch($sqlPrep)) {
      $logoPath = $fetchedLogoPath;
    }
    mysqli_stmt_close($sqlPrep);
  }

  if (isset($_FILES['site-logo']) && $_FILES['site-logo']['error'] === UPLOAD_ERR_OK) {

    $fileTmpPath = $_FILES['site-logo']['tmp_name'];
    $fileName = $_FILES['site-logo']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

    if (in_array($fileExtension, $allowedfileExtensions)) {

      $uploadFileDir = '../public/images/';
      $destPath = $uploadFileDir . $fileName;

      $pagePath = '../images/';
      $refPath = $pagePath . $fileName;

      if (file_exists($logoPath)) {
        unlink($logoPath);
      }

      if (move_uploaded_file($fileTmpPath, $destPath)) {
        $logoPath = $refPath;
        $sqlPrep = mysqli_prepare($link, "UPDATE site_options SET option_value = ? WHERE option_name = ?");
        if ($sqlPrep) {
          mysqli_stmt_bind_param($sqlPrep, 'ss', $logoPath, $siteOptionName);
          mysqli_execute($sqlPrep);
          mysqli_stmt_close($sqlPrep);
        }
        echo "file upload successful";
      } else {
        echo 'Error moving uploaded file.';
      }
    } else {
      echo 'Upload failed. Allowed types: ' . implode(',', $allowedfileExtensions);
    }
  }
  mysqli_close($link);
}
