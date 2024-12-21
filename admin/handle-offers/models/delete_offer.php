<?php
require '../../../db/config.php'; // Adjust the path if necessary

if (!isset($_SESSION['admin_id'])) {
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit();
}

if (isset($_GET['id'])) {
  $offerId = intval($_GET['id']);

  // Fetch the image filename from the database
  $stmt = $pdo->prepare('SELECT img FROM offers WHERE id = :id');
  $stmt->execute(['id' => $offerId]);
  $offer = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($offer) {
    $imagePath = '../img/' . $offer['img'];

    // Delete the offer from the database
    $stmt = $pdo->prepare('DELETE FROM offers WHERE id = :id');
    $result = $stmt->execute(['id' => $offerId]);

    if ($result) {
      // Delete the image file
      if (file_exists($imagePath)) {
        unlink($imagePath);
      }

      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['success' => false, 'error' => 'Error deleting offer from the database']);
    }
  } else {
    echo json_encode(['success' => false, 'error' => 'Offer not found']);
  }
} else {
  echo json_encode(['success' => false, 'error' => 'No ID provided']);
}
?>