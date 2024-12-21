<?php
include '../../db/config.php'; // Your database connection

// Check if the role parameter is set
$role = isset($_GET['role']) ? $_GET['role'] : '';

// Handle admin logout
if ($role === 'admin') {
  // Destroy admin session
  unset($_SESSION['admin_id']);
  setcookie('admin', '', time() - 3600, '/'); // Remove the admin session cookie
  header('Location: ../../admin.php'); // Redirect to admin login page
  exit();
}

exit();
?>