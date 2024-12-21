<?php
// Include the database connection
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: php/signin.php");
  exit();
}

$userId = $_SESSION['user_id'];

try {
  $queryOrders = $pdo->prepare("
        SELECT orders.*, oi.*
        FROM orders
        JOIN order_items oi ON orders.id = oi.order_id
        WHERE orders.customer_id = :customer_id
        ORDER BY orders.order_date DESC
    ");

  // Bind the user ID to the query
  $queryOrders->bindParam(':customer_id', $userId, PDO::PARAM_INT);
  $queryOrders->execute();

  // Fetch all the results
  $ordersWithItems = $queryOrders->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  </head>

  <body class="bg-gray-100">
    <?php include "client/components/header.php" ?>

    <main class="container mx-auto p-8 md:p-18 lg:p-32">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">My Bookings</h1>

      <div class="mb-6 flex items-end flex-wrap gap-4">
        <div>
          <label for="statusFilter" class="block text-gray-700">Status</label>
          <select id="statusFilter" class="mt-1 block w-full p-2 bg-white border border-gray-300 rounded-md">
            <option value="">All</option>
            <option value="Pending">Pending</option>
            <option value="Completed">Completed</option>
            <option value="Canceled">Canceled</option>
          </select>
        </div>

        <div>
          <label for="dateFilter" class="block text-gray-700">Booking Date</label>
          <input type="date" id="dateFilter" class="mt-1 block w-full p-2 bg-white border border-gray-300 rounded-md">
        </div>

        <div>
          <label for="itemNameFilter" class="block text-gray-700">Item Name</label>
          <input type="text" id="itemNameFilter"
            class="mt-1 block w-full p-2 bg-white border border-gray-300 rounded-md" placeholder="Enter item name">
        </div>

        <div>
          <label for="bookingIdFilter" class="block text-gray-700">Booking ID</label>
          <input type="text" id="bookingIdFilter"
            class="mt-1 block w-full p-2 bg-white border border-gray-300 rounded-md" placeholder="Enter booking ID">
        </div>
      </div>
      <!-- Notification container for copy booking id-->
      <div id="notification" class="hidden fixed bottom-4 left-4 bg-green-500 text-white py-2 px-4 rounded shadow-md">
        Booking ID copied.
      </div>

      <!-- Orders List -->
      <div id="ordersList">
        <?php if (!empty($ordersWithItems)): ?>
          <?php foreach ($ordersWithItems as $order): ?>
            <div class="order-item bg-white rounded-lg shadow-md p-4 mb-6"
              data-status="<?php echo htmlspecialchars($order['status']); ?>"
              data-date="<?php echo htmlspecialchars(date('Y-m-d', strtotime($order['order_date']))); ?>"
              data-item-name="<?php echo htmlspecialchars($order['package_name']); ?>"
              data-booking-id="<?php echo htmlspecialchars($order['order_id']); ?>">
              <div class="md:flex md:justify-between md:items-center">
                <div class="mb-4 md:mb-0">
                  <h2 class="text md:text-lg font-semibold text-gray-700">Booking ID:
                    <?php echo htmlspecialchars($order['order_id']); ?>
                    <!-- Copy Button -->
                    <button class="ml-2  text-blue-500 hover:text-blue-700"
                      onclick="copyBookingId('<?php echo htmlspecialchars($order['order_id']); ?>')">
                      <i class="fas fa-copy"></i>
                    </button>
                  </h2>
                  <p class="text-sm mt-2 text-gray-500">Booking Date:
                    <?php echo htmlspecialchars($order['order_date']); ?>
                  </p>
                  <?php
                  $status = htmlspecialchars($order['status']);
                  $statusClass = '';
                  $paymentClass = '';
                  $paymentText = '';

                  switch ($status) {
                    case 'pending':
                      $statusClass = 'text-blue-500'; // Changed to blue for pending status
                      $paymentClass = 'bg-red-500 py-1 px-2 rounded-sm';
                      // Changed to blue for pending payment
                      $paymentText = 'Unpaid';
                      break;
                    case 'completed':
                      $statusClass = 'text-green-500';
                      $paymentClass = 'bg-green-500 py-1 px-2 rounded-sm';
                      $paymentText = 'Paid';
                      break;
                    default:
                      $statusClass = 'text-red-500';
                      $paymentClass = 'bg-blue-500 py-1 px-2 rounded-sm';
                      $paymentText = 'Eligible for a refund if paid';
                      break;
                  }
                  ?>

                  <p class="text-sm text-gray-500">
                    Status:
                    <span class="<?= $statusClass; ?>">
                      <?= $status; ?>
                    </span>
                  </p>

                  <p class="text-sm mt-3 text-gray-500">
                    <span class="<?= $paymentClass; ?> text-xs text-white leading-none">
                      <?= $paymentText; ?>
                    </span>
                  </p>
                </div>
                <div class="text-right">
                  <p class="text md:text-lg lg:text-xl font-bold text-blue-600">
                    <?php echo number_format($order['total_amount'], 2); ?> BDT.
                  </p>
                  <a href="client/models/generate_invoice.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>"
                    class="text-neutral-600 text-sm md:text-base mt-3 block hover:underline">
                    <i class="fa-solid fa-download"></i> Invoice
                  </a>
                </div>
              </div>
              <div class="mt-4">
                <ul>
                  <li class="flex justify-between gap-4 items-center border-b border-gray-200 py-2">
                    <div class="text-gray-700">
                      <span class="font-medium"><?php echo htmlspecialchars($order['package_name']); ?></span>
                      <span class="text-sm text-gray-500"> (<?php echo htmlspecialchars($order['category']); ?>)</span>
                    </div>
                    <div class="text-right">
                      <p class="text-sm text-gray-600">Quantity: <?php echo htmlspecialchars($order['quantity']); ?></p>
                      <p class="text-sm text-gray-600">Amount:
                        <?php echo number_format($order['total_amount'], 2); ?> BDT.
                      </p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-gray-600">You have no bookings yet.</p>
        <?php endif; ?>
      </div>
    </main>

    <?php include 'client/components/footer.php'; ?>

    <script>
      // Event listeners for filter inputs
      document.getElementById('statusFilter').addEventListener('input', filterOrders);
      document.getElementById('dateFilter').addEventListener('input', filterOrders);
      document.getElementById('itemNameFilter').addEventListener('input', filterOrders);
      document.getElementById('bookingIdFilter').addEventListener('input', filterOrders);

      function filterOrders() {
        var statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        var dateFilter = document.getElementById('dateFilter').value;
        var itemNameFilter = document.getElementById('itemNameFilter').value.toLowerCase();
        var bookingIdFilter = document.getElementById('bookingIdFilter').value.toLowerCase();

        var orders = document.querySelectorAll('.order-item');

        orders.forEach(function (order) {
          var orderStatus = order.getAttribute('data-status').toLowerCase();
          var orderDate = order.getAttribute('data-date');
          var orderItemName = order.getAttribute('data-item-name').toLowerCase();
          var orderBookingId = order.getAttribute('data-booking-id').toLowerCase();

          // Convert both dates to comparable formats
          var dateFilterMatch = !dateFilter || new Date(orderDate).toISOString().slice(0, 10) === dateFilter;

          var statusMatch = statusFilter === '' || orderStatus === statusFilter;
          var itemNameMatch = itemNameFilter === '' || orderItemName.includes(itemNameFilter);
          var bookingIdMatch = bookingIdFilter === '' || orderBookingId.includes(bookingIdFilter);

          if (statusMatch && dateFilterMatch && itemNameMatch && bookingIdMatch) {
            order.style.display = '';
          } else {
            order.style.display = 'none';
          }
        });
      }

      // Copy Booking ID to clipboard
      function copyBookingId(bookingId) {
        // Create a temporary input element
        const tempInput = document.createElement('input');
        document.body.appendChild(tempInput);

        // Set the input value to the Booking ID and select it
        tempInput.value = bookingId;
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        // Copy the selected text
        document.execCommand('copy');

        // Remove the temporary input element
        document.body.removeChild(tempInput);

        // Show a success notification at the bottom of the page
        const notification = document.getElementById('notification');
        notification.textContent = `Booking ID copied.`;
        notification.classList.remove('hidden');

        // Remove the notification after 2 seconds
        setTimeout(() => {
          notification.classList.add('hidden');
        }, 2000);
      }


    </script>
  </body>

</html>