<?php
// add_offer.php

// Database connection
require '../../../db/config.php'; // Adjust the path if necessary

$response = ['success' => false, 'error' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $category = $_POST['category'];
  $package_id = intval($_POST['package_id']);
  $discount = floatval($_POST['discount']);
  $type = $_POST['type'];

  // Determine the package name based on the category and package_id
  $package_name = '';
  $package_price = 0;
  $package_details = '';
  $visa_requirements = null; // Initialize with null
  $table = '';

  if ($category == 'travel') {
    $table = 'travel_packages';
  } elseif ($category == 'visa') {
    $table = 'visas';
  } elseif ($category == 'flight') {
    $table = 'flights';
  } else {
    $response['error'] = 'Invalid category';
    echo json_encode($response);
    exit();
  }

  try {
    // Adjust SQL query to match table structure
    $sql = "SELECT title AS package_name, price, details FROM $table WHERE id = :package_id";
    if ($category == 'travel') {
      $sql = "SELECT package_name AS package_name, price, details FROM $table WHERE id = :package_id";
    } elseif ($category == 'visa') {
      $sql = "SELECT title AS package_name, price, details, requirements FROM $table WHERE id = :package_id";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':package_id', $package_id);
    $stmt->execute();
    $package = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($package) {
      $package_name = $package['package_name'];
      $package_price = $package['price'];
      $package_details = $package['details'];

      if ($category == 'visa') {
        $visa_requirements = $package['requirements'];
      }
    } else {
      $response['error'] = 'Invalid package ID';
      echo json_encode($response);
      exit();
    }
  } catch (PDOException $e) {
    $response['error'] = 'Database error: ' . $e->getMessage();
    echo json_encode($response);
    exit();
  }

  // Handle image upload
  if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $image = $_FILES['img'];
    $imageName = 'IMG_' . uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
    $targetPath = '../img/' . $imageName;

    if (move_uploaded_file($image['tmp_name'], $targetPath)) {
      try {
        // Insert data into offers table
        $sql = "INSERT INTO offers (type, package_name, package_details, package_price, category, package_id, discount, img";

        // Include visa_requirements if category is 'visa'
        if ($category == 'visa') {
          $sql .= ", visa_requirements";
        }

        $sql .= ") VALUES (:type, :package_name, :package_details, :package_price, :category, :package_id, :discount, :img";

        if ($category == 'visa') {
          $sql .= ", :visa_requirements";
        }

        $sql .= ")";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':package_name', $package_name);
        $stmt->bindParam(':package_details', $package_details);
        $stmt->bindParam(':package_price', $package_price);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':package_id', $package_id);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':img', $imageName);

        // Bind visa_requirements if applicable
        if ($category == 'visa') {
          $stmt->bindParam(':visa_requirements', $visa_requirements);
        }

        if ($stmt->execute()) {
          $response['success'] = true;
        } else {
          $response['error'] = 'Failed to insert offer';
        }
      } catch (PDOException $e) {
        $response['error'] = 'Database error: ' . $e->getMessage();
      }
    } else {
      $response['error'] = 'Failed to upload image';
    }
  } else {
    $response['error'] = 'No image uploaded or upload error';
  }

  echo json_encode($response);
} else {
  $response['error'] = 'Invalid request method';
  echo json_encode($response);
}
?>