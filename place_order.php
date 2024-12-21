<?php
// Check if user is logged in
include 'db/config.php';
if (!isset($_SESSION['user_id'])) {
  header("Location:signin.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Get product ID and category from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Verify the category and fetch the product details

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
    $sql = "SELECT *  FROM offers WHERE id = :id";
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

// Calculate total amount and quantity (default is 1 for simplicity)
$quantity = 1;
if ($category == 'offer') {
  $total_amount = $product['package_price'] * $quantity;

} else {
  $total_amount = $product['price'] * $quantity;

}

?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
      .card-img {
        width: 100%;
        height: auto;
      }
    </style>
  </head>

  <body>
    <?php include "client/components/header.php" ?>

    <main class="lg:pt-[120px] pt-[100px] pb-16 px-6">

      <section class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-1">Place Your Order</h1>
        <p class="text-red-600 text-sm  flex items-center">
          <i class="fas fa-exclamation-triangle mr-2"></i>
          Before placing an order, please check your phone number in your profile.
        </p>
        <p class="text-red-600 text-sm mb-4 mt-0.5 flex items-center">
          <i class="fa-solid fa-phone mr-2"></i>
          If you need more information before booking, please call on
          <a href="tel:+8801864946718" class="text-blue-500 underline hover:text-blue-700 ml-2">+8801864946718</a>.
        </p>

        <?php if ($category == 'travel'): ?>
          <div class="flex flex-col lg:flex-row">
            <div class="lg:w-1/2">
              <img src="admin/img/<?php echo htmlspecialchars($product['img']); ?>"
                alt="<?php echo htmlspecialchars($product['package_name'] ?? $product['title']); ?>"
                class="card-img rounded-lg">
            </div>
            <div class="lg:w-1/2 lg:pl-6 mt-4 lg:mt-0">
              <h2 class="text-xl font-semibold mb-2">
                <?php echo $product['package_name'] ?? $product['title']; ?>
              </h2>
              <p class="text-gray-600 mb-4">
                <?php echo nl2br($product['details']) ?? $product['description']; ?>
              </p>
              <p class="text-lg font-bold mb-4">Price: $<?php echo number_format($product['price'], 2); ?></p>
              <p class="text-lg font-bold mb-4">Quantity: <?php echo htmlspecialchars($quantity); ?></p>
              <p class="text-lg font-bold mb-4">Total: $<?php echo number_format($total_amount, 2); ?></p>
              <a href="client/models/order_confirmation.php?category=<?php echo urlencode($category); ?>&product_id=<?php echo urlencode($product_id); ?>"
                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600">
                <i class="fas fa-check-circle mr-2"></i> Confirm Order
              </a>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($category == 'visa'): ?>
          <div class="flex flex-col lg:flex-row">
            <div class="lg:w-1/2">
              <img src="admin/img/<?php echo htmlspecialchars($product['img']); ?>"
                alt="<?php echo htmlspecialchars($product['title']); ?>" class="card-img rounded-lg">
            </div>
            <div class="lg:w-1/2 lg:pl-6 mt-4 lg:mt-0">
              <h2 class="text-xl font-semibold mb-2">
                <?php echo htmlspecialchars($product['title']); ?>
              </h2>
              <p class="text-gray-600 ">
                <?php echo htmlspecialchars($product['country']); ?>
              </p>
              <details class="my-2">
                <summary class="text-bold">Details</summary>
                <p class="text-gray-600 mt-2 pb-4">
                  <?php echo nl2br($product['details']) ?? $product['description']; ?>
                </p>
              </details>
              <p class="text-gray-600 mb-4">
                <span class="text-red-500 text-bold ">Requirements:</span>
                <?php echo nl2br($product['requirements']); ?>
              </p>
              <p class="text-lg font-bold mb-4">Price: $<?php echo number_format($product['price'], 2); ?></p>
              <p class="text-lg font-bold mb-4">Quantity: <?php echo htmlspecialchars($quantity); ?></p>
              <p class="text-lg font-bold mb-4">Total: $<?php echo number_format($total_amount, 2); ?></p>
              <a href="client/models/order_confirmation.php?category=<?php echo urlencode($category); ?>&product_id=<?php echo urlencode($product_id); ?>"
                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600">
                <i class="fas fa-check-circle mr-2"></i> Confirm Order
              </a>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($category == 'flight'): ?>
          <div class="flex flex-col lg:flex-row">
            <div class="lg:w-1/2">
              <img src="admin/img/<?php echo htmlspecialchars($product['img']); ?>"
                alt="<?php echo htmlspecialchars($product['title']); ?>" class="card-img rounded-lg">
            </div>
            <div class="lg:w-1/2 lg:pl-6 mt-4 lg:mt-0">
              <h2 class="text-xl font-semibold mb-2">
                <?php echo htmlspecialchars($product['title']); ?>
              </h2>
              <p class="text-gray-600 mb-2">
                <strong>From:</strong> <?php echo htmlspecialchars($product['countryFrom']); ?>
              </p>
              <p class="text-gray-600 mb-4">
                <strong>Destination:</strong> <?php echo htmlspecialchars($product['destination']); ?>
              </p>
              <p class="text-gray-600 mb-4">
                <?php echo nl2br($product['details']) ?? $product['description']; ?>
              </p>
              <p class="text-lg font-bold mb-4">Price: $<?php echo number_format($product['price'], 2); ?></p>
              <p class="text-lg font-bold mb-4">Quantity: <?php echo htmlspecialchars($quantity); ?></p>
              <p class="text-lg font-bold mb-4">Total: $<?php echo number_format($total_amount, 2); ?></p>
              <a href="client/models/order_confirmation.php?category=<?php echo urlencode($category); ?>&product_id=<?php echo urlencode($product_id); ?>"
                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600">
                <i class="fas fa-check-circle mr-2"></i> Confirm Order
              </a>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($category == 'offer'): ?>
          <div class="flex flex-col lg:flex-row">
            <div class="lg:w-1/2">
              <img src="admin/handle-offers/img/<?php echo htmlspecialchars($product['img']); ?>"
                alt="<?php echo htmlspecialchars($product['package_name']); ?>" class="card-img rounded-lg">
            </div>
            <div class="lg:w-1/2 lg:pl-6 mt-4 lg:mt-0">
              <h2 class="text-xl font-semibold mb-2">
                <?php echo htmlspecialchars($product['package_name']); ?>
              </h2>
              <p class="text-gray-600 first-letter:uppercase ">
                <?php echo htmlspecialchars($product['type']); ?>, <?php echo htmlspecialchars($product['category']); ?>
              </p>
              </p>
              <details>
                <summary class="text-bold cursor-pointer">Details</summary>
                <p class="text-gray-600 mb-1 first-letter:uppercase">
                  <?php echo nl2br($product['package_details']); ?>
                </p>
              </details>
              <?php if ($product['category'] == 'visa'): ?>
                <p class="text-gray-600  first-letter:uppercase">
                  <span class="text-red-500 text-bold ">Requirements:</span>
                  <?php echo nl2br($product['visa_requirements']); ?>
                </p>
              <?php endif; ?>
              <p class="text-lg font-bold mt-4 ">Discount: <?php echo htmlspecialchars($product['discount']); ?>%</p>
              <p class="text-lg font-bold ">Price:
                <?php echo htmlspecialchars(($product['package_price'] - ($product['package_price'] * $product['discount'] / 100))); ?>
                BDT.
              </p>
              <p class="text font-semibold text-red-600 line-through mb-4 ">
                <?php echo htmlspecialchars($product['package_price']); ?> BDT.
              </p>

              <a href="client/models/order_confirmation.php?category=<?php echo urlencode($category); ?>&product_id=<?php echo urlencode($product_id); ?>"
                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600">
                <i class="fas fa-check-circle mr-2"></i> Confirm Offer
              </a>
            </div>
          </div>
        <?php endif; ?>

      </section>
    </main>
    <?php include 'client/components/footer.php'; ?>
  </body>

</html>