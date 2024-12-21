<?php
require '../../../db/config.php'; // Adjust the path if necessary

if (!isset($_SESSION['admin_id'])) {
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $offerId = intval($_POST['id']);
  $type = $_POST['type'];
  $discount = intval($_POST['discount']);

  // Prepare the SQL statement
  $updateSql = 'UPDATE offers SET type = :type, discount = :discount';

  $params = [
    'type' => $type,
    'discount' => $discount,
    'id' => $offerId
  ];

  // Check if an image is being updated
  if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $imgName = basename($_FILES['img']['name']);
    $imgPath = '../img/' . $imgName;

    // Get the old image path for deletion
    $stmt = $pdo->prepare('SELECT img FROM offers WHERE id = :id');
    $stmt->execute(['id' => $offerId]);
    $offer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($offer && file_exists('../img/' . $offer['img'])) {
      unlink('../img/' . $offer['img']);
    }

    move_uploaded_file($_FILES['img']['tmp_name'], $imgPath);

    // Update SQL statement to include the new image
    $updateSql .= ', img = :img';
    $params['img'] = $imgName;

  }

  $updateSql .= ' WHERE id = :id';

  // Execute the update query
  $stmt = $pdo->prepare($updateSql);
  $result = $stmt->execute($params);

  if ($result) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'error' => 'Error updating offer']);
  }
} else {
  echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>