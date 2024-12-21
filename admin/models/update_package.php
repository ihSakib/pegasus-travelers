<?php
// Include your database connection
include "../../db/config.php"; // Assuming this sets up the $pdo variable
echo var_dump($_FILES);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get the package ID and type from the hidden inputs
  $id = $_POST['id'];
  $type = $_POST['type'];

  // Initialize the query and parameters array
  $query = "";
  $params = [':id' => $id];

  // Determine the table based on the type
  $table = '';
  if ($type === 'visa') {
    $table = 'visas';
  } elseif ($type === 'flight') {
    $table = 'flights';
  } elseif ($type === 'travel_package') {
    $table = 'travel_packages';
  }

  // Fetch the current image filename from the database
  if ($table) {
    $stmt = $pdo->prepare("SELECT img FROM $table WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $currentPackage = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handle image upload if a new image is uploaded
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
      // Check if there's an old image to delete
      if ($currentPackage && !empty($currentPackage['img'])) {
        $oldImage = "../img/" . $currentPackage['img'];

        // Delete the old image file if it exists
        if (file_exists($oldImage)) {
          if (!unlink($oldImage)) {
            // echo "Failed to delete the old image.";
          } else {
          }
        } else {
          // echo "Old image file does not exist.";
        }
      }

      // Now proceed to upload the new image
      $img = $_FILES['img']['name'];
      $target_dir = "../img/";
      $target_file = $target_dir . basename($img);

      // Move the uploaded file to the target directory
      if (move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
        $params[':img'] = $img;
      } else {
        // echo "Error uploading the image.";
        exit;
      }
    }
  }

  // Build the query based on the type
  if ($type === 'visa') {
    $query = "UPDATE visas 
                      SET details = :details, country = :country, requirements = :requirements, price = :price, title = :title";
    $params[':details'] = $_POST['details'];
    $params[':country'] = $_POST['country'];
    $params[':requirements'] = $_POST['requirements'];
    $params[':price'] = $_POST['price'];
    $params[':title'] = $_POST['title'];
  } elseif ($type === 'flight') {
    $query = "UPDATE flights 
                      SET title = :title, details = :details, countryFrom = :countryFrom, destination = :destination, price = :price";
    $params[':title'] = $_POST['title'];
    $params[':details'] = $_POST['details'];
    $params[':countryFrom'] = $_POST['countryFrom'];
    $params[':destination'] = $_POST['destination'];
    $params[':price'] = $_POST['price'];
  } elseif ($type === 'travel_package') {
    $query = "UPDATE travel_packages 
                      SET package_name = :package_name, price = :price, details = :details, location = :location, rating = :rating";
    $params[':package_name'] = $_POST['package_name'];
    $params[':price'] = $_POST['price'];
    $params[':details'] = $_POST['details'];
    $params[':location'] = $_POST['location'];
    $params[':rating'] = $_POST['rating'];
  }

  // Append the image update if necessary
  if (isset($params[':img'])) {
    $query .= ", img = :img";
  }

  // Finalize the query
  $query .= " WHERE id = :id";

  try {
    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    // header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
} else {
  echo "Invalid request method.";
}
?>