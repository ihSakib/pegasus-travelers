<?php
include "../../db/config.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: signin.php"); // Redirect to login page if not logged in
  exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from session

// Initialize variables to store error messages or success message
$success = $error = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the input values from the form
  $name = trim($_POST['name']);
  $phone = trim($_POST['phone']);
  $location = trim($_POST['location']);

  // Validate the inputs (you can add more validation rules as needed)
  if (empty($name) || empty($phone) || empty($location)) {
    $error = "All fields are required.";
  } else {
    try {
      // Prepare the SQL statement
      $sql = "UPDATE users SET name = :name , phone = :phone, location = :location WHERE id = :id";
      $stmt = $pdo->prepare($sql);

      // Bind parameters
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':phone', $phone);
      $stmt->bindParam(':location', $location);
      $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

      // Execute the statement
      if ($stmt->execute()) {
        $success = "Profile updated successfully.";

      } else {
        $error = "Failed to update profile.";
      }
    } catch (PDOException $e) {
      $error = "Error: " . $e->getMessage();
    }
  }
}


// Close the database connection
$stmt = null;
$pdo = null;
