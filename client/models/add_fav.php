<?php
include '../../db/config.php';

// Check if required parameters are set
if (isset($_SESSION['user_id']) && isset($_POST['id']) && isset($_POST['category'])) {
  $user_id = $_SESSION['user_id'];
  $product_id = $_POST['id'];
  $category = $_POST['category'];

  try {
    // Check if the product is already in favorites
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND product_id = ? AND category = ?");
    $stmt->execute([$user_id, $product_id, $category]);

    if ($stmt->rowCount() == 0) {
      // Add to favorites
      $stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id, category) VALUES (?, ?, ?)");
      $stmt->execute([$user_id, $product_id, $category]);
    } else {
      // Remove from favorites
      $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ? AND category = ?");
      $stmt->execute([$user_id, $product_id, $category]);
    }
  } catch (Exception $e) {
    // Handle exception if needed, for now we won't return any JSON
  }
  // No need to echo anything or set content type
}
exit();
?>