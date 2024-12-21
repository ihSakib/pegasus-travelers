<?php
include '../../db/config.php'; // Your database connection

// Check if the role parameter is set
$role = isset($_GET['role']) ? $_GET['role'] : '';

// Handle user logout
if ($role === 'user' && isset($_SESSION['user_id'])) {
  // Destroy user session
  unset($_SESSION['user_id']);
  setcookie('user', '', time() - 3600, '/'); // Remove the user session cookie
  header('Location: ../../index.php'); // Redirect to user login page
  exit();
}

exit();
?>