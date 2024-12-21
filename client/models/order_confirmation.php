<?php
// Include database configuration
include "../../db/config.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: ../../signin.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's name and email from the database
$userName = '';
$userEmail = '';
$status = 'Pending';

try {
  $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
  $stmt->execute([':id' => $user_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user) {
    $userName = $user['name'];
    $userEmail = $user['email'];
  } else {
    die("User not found.");
  }
} catch (Exception $e) {
  die("Error fetching user details: " . $e->getMessage());
}

// Get product ID and category from URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch product details
$sql = "";
$params = [];

// Query based on category
switch ($category) {
  case 'travel':
    $sql = "SELECT * FROM travel_packages WHERE id = :id";
    break;
  case 'visa':
    $sql = "SELECT * FROM visas WHERE id = :id";
    break;
  case 'flight':
    $sql = "SELECT * FROM flights WHERE id = :id";
    break;
  case 'offer':
    $sql = "SELECT * FROM offers WHERE id = :id";
    break;
  default:
    die("Invalid category");
}

$params = [':id' => $product_id];
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// If no product found
if (!$product) {
  die("Product not found");
}

// Define order details
$quantity = 1; // Assuming a default quantity of 1

// Calculate total amount based on category
if ($category == 'offer') {
  $price = $product['package_price'];
  $title = $product['package_name'];
  $discount = $product['discount'] ?? 0; // Default discount to 0 if not set
  $total_amount = ($price - ($price * $discount / 100)) * $quantity;
} elseif ($category == 'travel') {
  $price = $product['price'];
  $title = $product['package_name'];
  $total_amount = $price * $quantity;
} else {
  $price = $product['price'];
  $title = $product['title'];
  $total_amount = $price * $quantity;
}

date_default_timezone_set('Asia/Dhaka');
$order_date = date('Y-m-d H:i:s');

// Function to generate a unique 15-digit order_id
function generateOrderId($pdo)
{
  do {
    // Generate a random 15-digit number
    $order_id = random_int(100000000000000, 999999999999999);

    // Check if the order_id already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE id = :order_id");
    $stmt->execute([':order_id' => $order_id]);
    $exists = $stmt->fetchColumn();
  } while ($exists); // Repeat if the order_id is not unique

  return $order_id;
}


$order_id = generateOrderId($pdo);

// Insert order into orders table
$sql = "INSERT INTO orders (id, customer_id, order_date, total_amount, status, created_at) VALUES (:order_id, :customer_id, :order_date, :total_amount, 'Pending', NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':order_id' => $order_id,
  ':customer_id' => $user_id,
  ':order_date' => $order_date,
  ':total_amount' => $total_amount
]);

// Insert order item into order_items table
$sql = "INSERT INTO order_items (order_id, category, package_name, item_id, quantity, price) VALUES (:order_id, :category, :package_name, :item_id, :quantity, :price)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':order_id' => $order_id,
  ':category' => $category,
  ':package_name' => $title,
  ':item_id' => $product_id,
  ':quantity' => $quantity,
  ':price' => $price
]);

// Send the order details to the user via email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/src/Exception.php';
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
try {
  // Server settings
  include '../../smtp/config.php';

  // Recipients
  $mail->setFrom('mail@pegasustravelers.com', 'Pegasus Travelers');
  $mail->addAddress($userEmail, $userName); // Use the fetched user's name and email

  // Email subject
  $mail->Subject = 'Your Order Details - Pegasus Travelers';

  // Prepare the email body using the template
  ob_start();
  include '../Email/place-booking-mail.php';
  $mailBody = ob_get_clean();

  // Assign the processed content as the email body
  $mail->Body = $mailBody;
  $mail->isHTML(true); // Set email format to HTML

  // Send the email
  $mail->send();
} catch (Exception $e) {
  echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

// Redirect to a confirmation page or show a success message
header("Location: ../pages/order_success.php");
exit();
?>