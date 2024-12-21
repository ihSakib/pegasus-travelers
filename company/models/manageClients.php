<?php
require '../../db/config.php'; // Include your database configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'addClient') {
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $title = $_POST['title'];

    $stmt = $pdo->prepare("INSERT INTO clients (name, comments, title) VALUES (:name, :comment, :title)");
    $stmt->execute([':name' => $name, ':comment' => $comment, ':title' => $title]);

    echo json_encode(['success' => true, 'message' => 'Client added successfully.']);
  } elseif ($action === 'editClient') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $title = $_POST['title'];

    $stmt = $pdo->prepare("UPDATE clients SET name = :name, comments = :comment, title = :title WHERE id = :id");
    $stmt->execute([':id' => $id, ':name' => $name, ':comment' => $comment, ':title' => $title]);

    echo json_encode(['success' => true, 'message' => 'Client updated successfully.']);
  } elseif ($action === 'deleteClient') {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Client deleted successfully.']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
  }
}
?>