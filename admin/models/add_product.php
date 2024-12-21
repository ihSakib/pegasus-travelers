<?php
// Database connection
include "../../db/config.php"; // Assuming this sets up the $pdo variable

try {
  // Get the product type from the form
  $type = $_POST['type'];

  // Initialize variables for file upload
  $img = $_FILES['img']['name'];
  $target_dir = "../img/";
  $target_file = $target_dir . basename($img);

  // Handle file upload
  if (!move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
    die("Sorry, there was an error uploading your file.");
  }

  // Prepare and bind statements based on the product type
  if ($type == 'travel_packages') {
    $sql = "INSERT INTO travel_packages (package_name, details, price, img, location, rating, created_at) 
                VALUES (:package_name, :details, :price, :img, :location, :rating, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':package_name', $_POST['package_name']);
    $stmt->bindParam(':details', $_POST['details']);
    $stmt->bindParam(':price', $_POST['price']);
    $stmt->bindParam(':img', $img);
    $stmt->bindParam(':location', $_POST['location']);
    $stmt->bindParam(':rating', $_POST['rating']);

  } elseif ($type == 'visas') {
    $sql = "INSERT INTO visas (title, details, img, country, price, requirements, created_at) 
                VALUES (:title, :details, :img, :country, :price, :requirements, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':title', $_POST['title']);
    $stmt->bindParam(':details', $_POST['details']);
    $stmt->bindParam(':img', $img);
    $stmt->bindParam(':country', $_POST['country']);
    $stmt->bindParam(':price', $_POST['price']);
    $stmt->bindParam(':requirements', $_POST['requirements']);

  } elseif ($type == 'flights') {
    $sql = "INSERT INTO flights (title, img, details, countryFrom, destination, price, created_at) 
                VALUES (:title, :img, :details, :from, :to, :price, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':title', $_POST['title']);
    $stmt->bindParam(':img', $img);
    $stmt->bindParam(':details', $_POST['details']);
    $stmt->bindParam(':from', $_POST['from']);
    $stmt->bindParam(':to', $_POST['destination']);
    $stmt->bindParam(':price', $_POST['price']);

  } else {
    die("Invalid product type.");
  }

  // Execute the prepared statement
  $stmt->execute();

  // Redirect back to the previous page
  header("Location: " . $_SERVER['HTTP_REFERER']);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>