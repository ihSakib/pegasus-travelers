<?php

// Database connection
require '../../../db/config.php'; // Adjust the path if necessary

$category = $_GET['category']; // Get the selected category from the query parameter

// Prepare the SQL query based on category
if ($category === 'travel') {
  $sql = "SELECT id, package_name AS name, price FROM travel_packages";
} elseif ($category === 'visa') {
  $sql = "SELECT id, title AS name, price FROM visas";
} elseif ($category === 'flight') {
  $sql = "SELECT id, title AS name, price FROM flights";
} else {
  echo json_encode([]);
  exit;
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return JSON encoded result
header('Content-Type: application/json');
echo json_encode($packages);
