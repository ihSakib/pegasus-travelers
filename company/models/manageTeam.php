<?php
require '../../db/config.php'; // Include your database configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'addTeamMember') {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $img = $_FILES['img'];

    // Handling image upload
    $targetDir = "../img/";
    $fileName = basename($img['name']);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($img['tmp_name'], $targetFilePath)) {
      $stmt = $pdo->prepare("INSERT INTO team (name, position, img) VALUES (:name, :position, :img)");
      $stmt->execute([':name' => $name, ':position' => $position, ':img' => $fileName]);

      echo json_encode(['success' => true, 'message' => 'Team member added successfully.']);
    } else {
      echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
    }
  } elseif ($action === 'editTeamMember') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $img = $_FILES['img'] ?? null;

    // Fetch current image name from database
    $stmt = $pdo->prepare("SELECT img FROM team WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $currentImage = $stmt->fetchColumn();

    // Update record without image
    if ($img && $img['error'] === UPLOAD_ERR_OK) {
      $targetDir = "../img/";
      $fileName = basename($img['name']);
      $targetFilePath = $targetDir . $fileName;

      // Delete the old image file if it exists
      if ($currentImage && file_exists($targetDir . $currentImage)) {
        unlink($targetDir . $currentImage);
      }

      if (move_uploaded_file($img['tmp_name'], $targetFilePath)) {
        // Update with the new image
        $stmt = $pdo->prepare("UPDATE team SET name = :name, position = :position, img = :img WHERE id = :id");
        $stmt->execute([':id' => $id, ':name' => $name, ':position' => $position, ':img' => $fileName]);

        echo json_encode(['success' => true, 'message' => 'Team member updated successfully.']);
      } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
      }
    } else {
      // Update without changing the image
      $stmt = $pdo->prepare("UPDATE team SET name = :name, position = :position WHERE id = :id");
      $stmt->execute([':id' => $id, ':name' => $name, ':position' => $position]);

      echo json_encode(['success' => true, 'message' => 'Team member updated successfully.']);
    }
  } elseif ($action === 'deleteTeamMember') {
    $id = $_POST['id'];

    // Fetch current image name from database
    $stmt = $pdo->prepare("SELECT img FROM team WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $currentImage = $stmt->fetchColumn();

    // Delete the record
    $stmt = $pdo->prepare("DELETE FROM team WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Delete the old image file if it exists
    if ($currentImage && file_exists("../img/" . $currentImage)) {
      unlink("../img/" . $currentImage);
    }

    echo json_encode(['success' => true, 'message' => 'Team member deleted successfully.']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
  }
}
?>
