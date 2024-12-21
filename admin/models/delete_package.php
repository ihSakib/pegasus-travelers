<?php
// Include the database connection
include "../../db/config.php"; // Assuming this sets up the $pdo variable

header('Content-Type: application/json');

// Check if id and table are set
if (isset($_POST['id']) && isset($_POST['table'])) {
  $id = $_POST['id'];
  $table = $_POST['table'];

  try {
    // Fetch the old image path before deleting the record
    $fetchImgSql = "SELECT img FROM $table WHERE id = :id";
    $fetchImgStmt = $pdo->prepare($fetchImgSql);
    $fetchImgStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $fetchImgStmt->execute();

    // Check if an image path is found
    $oldImg = $fetchImgStmt->fetchColumn();
    if ($oldImg) {
      // Construct the path to the image in the uploads folder
      $oldImgPath = '../img/' . $oldImg;

      // Delete the old image file if it exists
      if (file_exists($oldImgPath)) {
        unlink($oldImgPath);
      }
    }

    // Prepare the SQL statement to delete the record
    $sql = "DELETE FROM $table WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['success' => false, 'message' => 'Failed to delete the package.']);
    }
  } catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>