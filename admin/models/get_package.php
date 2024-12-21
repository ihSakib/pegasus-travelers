<?php
include "../../db/config.php"; // Assuming this sets up the $pdo variable

$id = $_POST['id'];
$type = $_POST['type'];

// Determine the table name based on the type
$table = '';
if ($type === 'Tour') {
  $table = 'travel_packages';
} elseif ($type === 'Visa') {
  $table = 'visas';
} elseif ($type === 'Air ticket') {
  $table = 'flights';
}

if ($table) {
  // Prepare SQL query to fetch data from the selected table
  if ($table === 'travel_packages') {
    // Alias 'package_name' to 'title' for travel_packages
    $stmt = $pdo->prepare("SELECT id, package_name as title, price, details, location, rating, img FROM $table WHERE id = :id");
  } else {
    // For other tables, select all columns
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
  }

  // Execute the query and fetch the data
  $stmt->execute([':id' => $id]);
  $data = $stmt->fetch(PDO::FETCH_ASSOC);

  // Output the data as JSON
  echo json_encode($data);
} else {
  // Output an empty JSON array if the table is not valid
  echo json_encode([]);
}
?>