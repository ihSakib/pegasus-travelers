<?php
// Database connection
require '../../../db/config.php'; 

header('Content-Type: application/json');

try {
  // Prepare and execute the SQL query to fetch offers
  $sql = "SELECT * FROM offers";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();

  // Fetch all offers
  $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Return JSON response
  echo json_encode([
    'success' => true,
    'data' => $offers
  ]);
} catch (PDOException $e) {
  // Return error response
  echo json_encode([
    'success' => false,
    'error' => 'Database error: ' . $e->getMessage()
  ]);
}
?>