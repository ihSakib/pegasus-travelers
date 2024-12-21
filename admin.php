<?php
require 'db/config.php'; // Your PDO database connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the user is logged in
if (isset($_SESSION['admin_id']) || isset($_COOKIE['admin'])) {
  $loggedIn = true;
} else {
  $loggedIn = false;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$loggedIn) {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  try {
    $stmt = $pdo->prepare("SELECT id, password FROM admin WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      $hashed_password = $user['password'];

      if (password_verify($password, $hashed_password)) {
        // Set session and cookie
        $_SESSION['admin_id'] = $user['id'];

        setcookie('admin', $username, time() + 3600, "/"); // Cookie expires in 1 hour
        header("Location: admin.php");
        exit();
      } else {
        $error = "Invalid credentials";
        $_SESSION['admin_signin_error'] = 'Invalid username or password!';
      }
    } else {
      $error = "Invalid credentials";
      $_SESSION['admin_signin_error'] = 'Invalid username or password!';
      header('Location: admin.php');
      exit();
    }
  } catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
  }
}

// Handle order update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
  echo var_dump($_POST);
  $orderId = $_POST['order_id'];
  $newStatus = $_POST['status'];

  // Update the order status
  $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
  $stmt->execute([':status' => $newStatus, ':id' => $orderId]);

  // Fetch user details by joining orders, order_items, and users tables
  $sql = "SELECT u.name, u.email, o.id as order_id, oi.package_name, oi.category, o.status
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN users u ON o.customer_id = u.id
            WHERE o.id = :order_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':order_id' => $orderId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Check if user data is fetched successfully
  if ($user) {
    $userName = $user['name'];
    $userEmail = $user['email'];
    $orderStatus = $user['status'];
    $packageName = $user['package_name'];
    $category = $user['category'];

    // Send the order status update email to the user
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);
    try {
      // Server settings
      include 'smtp/config.php';


      // Recipients
      $mail->setFrom('mail@pegasustravelers.com', 'Pegasus Travelers');
      $mail->addAddress($userEmail, $userName);

      // Email subject
      $mail->Subject = 'Your Booking Status Update - Pegasus Travelers';

      // Prepare the email body using the template
      ob_start();
      include 'client/Email/update-booking-mail.php'; // Your email template file
      $mailBody = ob_get_clean();

      // Assign the processed content as the email body
      $mail->Body = $mailBody;
      $mail->isHTML(true); // Set email format to HTML

      // Send the email
      $mail->send();
      // echo 'Status update email has been sent.';
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  } else {
    echo 'User information not found for this order.';
  }
}

// Fetch all travel packages
$sql = "SELECT * FROM travel_packages";
$stmt = $pdo->query($sql);
$travel_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all visa packages
$sql = "SELECT * FROM visas";
$stmt = $pdo->query($sql);
$visa_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all flight packages
$sql = "SELECT * FROM flights";
$stmt = $pdo->query($sql);
$flight_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch orders data
$query = "
    SELECT
        o.*, u.*, oi.*
    FROM
        orders o
    INNER JOIN order_items oi ON
        o.id = oi.order_id
    INNER JOIN users u ON
        o.customer_id = u.id
    ORDER BY o.order_date DESC;
";

$stmt = $pdo->query($query);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Pegasus Travelers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
      body {
        font-family: 'Poppins', sans-serif !important;
      }

      .tab-content {
        display: none;
      }

      .tab-content.active {
        display: block;
      }

      .tab-btn.click {
        color: dodgerblue;
        border-bottom: 2px solid dodgerblue;
      }
    </style>
  </head>

  <body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <!-- header -->
    <header class="bg-blue-700 text-white py-4 px-6 lg:px-20 shadow-md">
      <div class="container mx-auto flex items-center justify-between">
        <!-- Logo and Company Name -->
        <div class="flex items-center space-x-3">
          <i class="fas fa-plane-departure text-3xl"></i> <!-- Adding a travel-related icon for style -->
          <h1 class="text-2xl font-bold">Pegasus Travelers</h1>
        </div>

        <!-- Icons and Links -->
        <div class="relative  items-center space-x-4 hidden lg:flex">
          <?php if (isset($_SESSION['admin_id'])): ?>

            <!-- Message Icon -->
            <a href="inbox.php" target="_blank"
              class="text-green-400 hover:text-green-500 flex items-center space-x-2 font-semibold lg:text-lg">
              <i class="fas fa-envelope"></i>
              <span class="hidden md:inline">Messages</span> <!-- Show text on larger screens -->
            </a>

            <!-- offer icon -->
            <a href="handle_offers.php" target="_blank"
              class="text-yellow-400 hover:text-yellow-500 flex items-center space-x-2 font-semibold lg:text-lg">
              <i class="fas fa-gift"></i>
              <span class="hidden md:inline">Handle Offers</span> <!-- Show text on larger screens -->
            </a>

            <!-- Logout Icon -->
            <a href="admin/functions/logout.php?role=admin"
              class="text-red-400 hover:text-red-500 flex items-center space-x-2 font-semibold lg:text-lg">
              <i class="fas fa-sign-out-alt"></i>
              <span class="hidden md:inline">Logout</span> <!-- Show text on larger screens -->
            </a>
          <?php else: ?>
            <div></div>
          <?php endif; ?>
        </div>
        <!-- Mobile Menu Toggle (optional for responsiveness) -->
        <div class="lg:hidden flex items-center">
          <button id="mobile-menu-toggle" class="text-white focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
          </button>
        </div>
      </div>

    </header>

    <!-- Mobile Menu (optional) -->
    <div id="mobile-menu" class="lg:hidden hidden px-6 py-4 bg-blue-800">
      <?php if (isset($_SESSION['admin_id'])): ?>
        <a href="inbox.php" target="_blank" class="block text-green-400 hover:text-green-500 py-2">
          <i class="fas fa-envelope"></i> Messages
        </a>
        <a href="handle_offers.php" target="_blank" class="block text-yellow-400 hover:text-yellow-500 py-2">
          <i class="fas fa-gift"></i> Handle Offers
        </a>
        <a href="admin/functions/logout.php?role=admin" class="block text-red-400 hover:text-red-500 py-2">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      <?php endif; ?>
    </div>

    <main>
      <!-- admin login  -->
      <?php if (!$loggedIn): ?>
        <section class="mx-6">
          <div class="w-full max-w-md  my-28 mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <h2 class="text-2xl font-bold py-3 md:py-4 bg-[#2563eb] text-white  text-center">Admin Login</h2>
            <?php if (isset($_SESSION['admin_signin_error'])) {
              echo "<p class='text-red-500 mb-4 text-center'>";
              echo $_SESSION['admin_signin_error'];
              echo "</p>";
              unset($_SESSION['admin_signin_error']);
            }
            ?>

            <form class="admin-login p-8" method="post" action="">
              <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" id="username" name="username" required
                  class="mt-1 indent-2 block w-full  py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
              <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required
                  class="mt-1 indent-2 block w-full py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
              <button type="submit"
                class="w-full py-2 px-4 bg-indigo-600 text-white font-semibold rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Login</button>
            </form>
          </div>
        </section>

        <!-- main content -->
      <?php else: ?>
        <!-- tabs -->
        <div class="container mx-auto p-4 pt-8 pb-10">
          <!-- Tabs btn -->
          <div class="flex border-b border-gray-300 justify-center ">
            <a href="javascript:void(0)" class="click tab-btn py-2 px-4 " onclick="showTab('products')">PACKAGES</a>
            <a href="javascript:void(0)" class="tab-btn py-2 px-4 text-gray-600 hover:text-blue-500"
              onclick="showTab('orders')">BOOKINGS</a>
            <a href="javascript:void(0)" class="tab-btn py-2 px-4 text-gray-600 hover:text-blue-500"
              onclick="showTab('company')">COMPANY</a>
          </div>

          <!-- Tab Contents -->
          <!-- add -->
          <div id="products" class="tab-content active mx-6 md:mx-10 lg:mx-20">
            <h1 class="text-3xl font-bold my-6">Manage Packages</h1>
            <!-- Add Product Button -->
            <button id="addProductBtn"
              class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition flex items-center space-x-2">
              <i class="fas fa-plus"></i>
              <span>Add Package</span>
            </button>

            <!-- product table -->
            <div class="container mx-auto py-6">
              <!-- Filter Dropdown -->
              <div class="mb-4">

                <select id="typeFilter"
                  class="py-2 px-3 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="all">All</option>
                  <option value="Tour">Tour</option>
                  <option value="Visa">Visa</option>
                  <option value="Air ticket">Air Ticket</option>
                </select>
              </div>

              <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full divide-y divide-gray-200 ">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                        At</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Requirements</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Destination</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Table rows will be populated by JavaScript -->
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Product Add Modal -->
            <div id="productModal" class="fixed top-0 left-0 right-0 h-full
                 bg-gray-800 bg-opacity-60 flex items-center justify-center hidden ">
              <div class="bg-white rounded-lg max-w-xl p-6 mx-4 max-h-[80%]  w-full relative shadow-lg overflow-auto "
                id="modalContent">
                <span id="closeModalBtn"
                  class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 cursor-pointer text-2xl">&times;</span>
                <h2 class="text-2xl font-semibold mb-4">Add New Package</h2>
                <select id="productType"
                  class="block w-full border rounded-md p-3 mb-4 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition">
                  <option value="">Select Product Type</option>
                  <option value="tour">Tour Package</option>
                  <option value="visa">Visa Package</option>
                  <option value="air">Air Ticket</option>
                </select>

                <div id="productForm" class=""></div>
              </div>
            </div>
            <!-- Edit Modal -->
            <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
              <div class="bg-white rounded-lg shadow-lg max-w-lg w-full mx-4 p-6">
                <h2 id="modalTitle" class="text-lg font-semibold mb-4">Edit Package</h2>
                <div id="modalFormContainer" class="">
                  <!-- Form will be dynamically inserted here -->
                </div>
                <div class="flex justify-end space-x-4 mt-4">
                  <button type="button" id="closeModal"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                  <button type="button" id="saveChanges"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
                </div>
              </div>
            </div>

          </div>

          <!-- bookings -->
          <div id="orders" class="tab-content mx-6 md:mx-10 lg:mx-20">
            <div class="container mx-auto mt-6">
              <h1 class="text-3xl font-bold mb-6">Manage Bookings</h1>

              <!-- Filter by Order ID Form -->
              <div id="filter-form" class="mb-6">
                <div class="flex items-center">

                  <input id="filter-input" type="text"
                    class="border rounded px-2 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Enter Booking ID">

                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full text-center bg-white border border-gray-200 rounded-lg">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="py-2 px-4 border-b">Booking ID</th>
                      <th class="py-2 px-4 border-b">Date</th>
                      <th class="py-2 px-4 border-b">Customer</th>
                      <th class="py-2 px-4 border-b">Package</th>
                      <th class="py-2 px-4 border-b">Category</th>
                      <th class="py-2 px-4 border-b">Total Amount</th>
                      <th class="py-2 px-4 border-b">Status</th>
                    </tr>
                  </thead>
                  <tbody id="orders-table-body">
                    <?php foreach ($orders as $order): ?>
                      <tr class="order-row odd:bg-white even:bg-gray-50">
                        <td class="py-2 px-4 border-b order-id"><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td class="py-2 px-4 border-b">
                          <?php echo htmlspecialchars($order['name']); ?><br>
                          <?php echo htmlspecialchars($order['email']); ?><br>
                          <?php echo htmlspecialchars($order['phone']); ?><br>
                          <?php echo htmlspecialchars($order['location']); ?>
                        </td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($order['package_name']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($order['category']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($order['total_amount']); ?></td>
                        <td class="py-2 px-4 border-b">
                          <form class="order-handle-form" action="" method="post">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <input type="hidden" name="name" value="<?php echo $order['name']; ?>">
                            <input type="hidden" name="email" value="<?php echo $order['email']; ?>">

                            <select name="status"
                              class="border rounded px-2  focus:outline-none focus:ring-2 focus:ring-blue-400">
                              <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending
                              </option>
                              <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>
                                Completed
                              </option>
                              <option value="canceled" <?php echo $order['status'] == 'canceled' ? 'selected' : ''; ?>>
                                Canceled
                              </option>
                            </select>
                            <button type="submit"
                              class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded my-2 sm:ml-2">Update</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- company -->
          <div id="company" class="tab-content mx-6 md:mx-10 lg:mx-20">
            <h1 class="text-3xl font-bold my-6">Manage Company</h1>

            <!-- team members -->
            <div>
              <?php
              $stmt = $pdo->query("SELECT * FROM team");
              $teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
              ?>
              <h1 class="text-2xl  text-neutral-700 font-bold my-6">Team Members</h1>

              <div>
                <!-- Add Team Member Button -->
                <button id="tm-add" class="bg-green-600 text-white px-4 py-2 rounded mb-4 add-team-member"
                  data-action="add">
                  <i class="fas fa-plus"></i> Add Team Member
                </button>

                <!-- Team Members Table -->
                <div class="flex flex-col">
                  <div class="-m-1.5 overflow-x-auto">
                    <div class="p-1.5 min-w-full inline-block align-middle">
                      <div class="border rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                          <thead class="bg-gray-50">
                            <tr>
                              <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                Name</th>
                              <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                Position</th>
                              <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                Image</th>
                              <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                Action</th>
                            </tr>
                          </thead>
                          <tbody class="divide-y divide-gray-200">
                            <?php foreach ($teamMembers as $member): ?>
                              <tr>
                                <td class="name px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 text-center">
                                  <?= htmlspecialchars($member['name']) ?>
                                </td>
                                <td class="position px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-center">
                                  <?= htmlspecialchars($member['position']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm  text-gray-800 text-center">
                                  <img src="company/img/<?= htmlspecialchars($member['img']) ?>"
                                    alt="<?= htmlspecialchars($member['name']) ?>"
                                    class="h-10 w-10 mx-auto rounded-md object-cover">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                  <div class="flex gap-2 justify-center">
                                    <button data-id="<?php echo htmlspecialchars($member['id']); ?>" id="" type="button"
                                      class="edit-tm inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 focus:outline-none focus:text-blue-800 disabled:opacity-50 disabled:pointer-events-none">Edit</button>
                                    <button data-id="<?php echo htmlspecialchars($member['id']); ?>" id="" type="button"
                                      class="del-tm inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-red-600 hover:text-red-800 focus:outline-none focus:text-red-800 disabled:opacity-50 disabled:pointer-events-none">Delete</button>
                                  </div>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- clients Review -->
            <div class="my-8">
              <?php
              $stmt = $pdo->query("SELECT * FROM clients");
              $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
              ?>
              <h1 class="text-2xl text-neutral-700 font-bold my-6">Clients Review</h1>

              <div>
                <!-- Add Client Button -->
                <button id="cr-add" class="bg-green-600 text-white px-4 py-2 rounded mb-4 add-client" data-action="add">
                  <i class="fas fa-plus"></i> Add Client Review
                </button>

                <!-- Clients Table -->
                <div class="flex flex-col">
                  <div class="-m-1.5 overflow-x-auto">
                    <div class="p-1.5 min-w-full inline-block align-middle">
                      <div class="border rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                          <thead class="bg-gray-50">
                            <tr>
                              <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                Name</th>
                              <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                Comment</th>
                              <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                Title</th>
                              <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                Action</th>
                            </tr>
                          </thead>
                          <tbody class="divide-y divide-gray-200">
                            <?php foreach ($clients as $client): ?>
                              <tr>
                                <td class="name px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 text-center">
                                  <?= htmlspecialchars($client['name']) ?>
                                </td>
                                <td class="comment px-6 py-4 overflow-hidden whitespace-wrap text-sm text-gray-800 ">
                                  <p class="mx-auto overflow-y-auto max-h-20 w-[80%]">
                                    <?= htmlspecialchars($client['comments']) ?>
                                  </p>
                                </td>
                                <td class="title px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-center">
                                  <?= htmlspecialchars($client['title']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                  <div class="flex gap-2 justify-center">
                                    <button data-id="<?php echo htmlspecialchars($client['id']); ?>" id="" type="button"
                                      class="edit-cr inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 focus:outline-none focus:text-blue-800 disabled:opacity-50 disabled:pointer-events-none">Edit</button>
                                    <button data-id="<?php echo htmlspecialchars($client['id']); ?>" id="" type="button"
                                      class="del-cr inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-red-600 hover:text-red-800 focus:outline-none focus:text-red-800 disabled:opacity-50 disabled:pointer-events-none">Delete</button>
                                  </div>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

              </div>

            </div>

            <!-- team member and client review add Modals -->
            <div id="modals-container">
              <!-- Client Modal -->
              <div id="client-modal"
                class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 hidden">
                <div class="bg-white p-6 rounded shadow-lg w-96">
                  <h2 class="text-xl font-bold mb-4">Add Client</h2>
                  <form id="client-form">
                    <div class="mb-4">
                      <label class="block text-gray-700">Name</label>
                      <input type="text" name="name" class="w-full px-4 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                      <label class="block text-gray-700">Comment</label>
                      <textarea name="comment" class="w-full px-4 py-2 border rounded" required></textarea>
                    </div>
                    <div class="mb-4">
                      <label class="block text-gray-700">Title</label>
                      <input type="text" name="title" class="w-full px-4 py-2 border rounded" required>
                    </div>
                    <div class="flex justify-end">
                      <button type="button"
                        class="bg-gray-500 text-white px-4 py-2 rounded mr-2 close-modal">Close</button>
                      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add</button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Team Member Modal -->
              <div id="team-modal"
                class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 hidden">
                <div class="bg-white p-6 rounded shadow-lg w-96">
                  <h2 class="text-xl font-bold mb-4">Add Team Member</h2>
                  <form id="team-form">
                    <div class="mb-4">
                      <label class="block text-gray-700">Name</label>
                      <input type="text" name="name" class="w-full px-4 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                      <label class="block text-gray-700">Position</label>
                      <input type="text" name="position" class="w-full px-4 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                      <label class="block text-gray-700">Image</label>
                      <input type="file" name="img" class="w-full px-4 py-2 border rounded" required>
                    </div>
                    <div class="flex justify-end">
                      <button type="button"
                        class="bg-gray-500 text-white px-4 py-2 rounded mr-2 close-modal">Close</button>
                      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Team Members Edit Modal -->
            <div id="editTeamModal"
              class="fixed top-0 left-0 right-0 h-full bg-gray-600 bg-opacity-50 flex flex-col justify-center items-center hidden">
              <div class="bg-white rounded-lg shadow-lg w-96 p-6">
                <h2 class="text-xl font-bold mb-4">Edit Team Member</h2>
                <form id="editTeamForm" enctype="multipart/form-data">
                  <input type="hidden" name="action" value="editTeamMember">
                  <input type="hidden" name="id">

                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" class="mt-1 p-2 block w-full border rounded-md" required>
                  </div>

                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Position</label>
                    <input type="text" name="position" class="mt-1 p-2 block w-full border rounded-md" required>
                  </div>

                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Image</label>
                    <input type="file" name="img" class="mt-1 p-2 block w-full border rounded-md">
                  </div>

                  <div class="flex justify-end">
                    <button type="button"
                      class="close-modal px-4 py-2 mr-2 bg-gray-300 text-gray-700 rounded">Cancel</button>
                    <button type="button" id="saveTeamEdit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Clients' Review Edit Modal -->
            <div id="editClientReviewModal"
              class="fixed top-0 left-0 right-0 h-full bg-gray-600 bg-opacity-50 flex flex-col justify-center items-center hidden">
              <div class="bg-white rounded-lg shadow-lg w-96 p-6">
                <h2 class="text-xl font-bold mb-4">Edit Client Review</h2>
                <form id="editClientReviewForm">
                  <input type="hidden" name="action" value="editClient">
                  <input type="hidden" name="id">

                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" class="mt-1 p-2 block w-full border rounded-md" required>
                  </div>

                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Comment</label>
                    <textarea name="comment" class="mt-1 p-2 block w-full border rounded-md" required></textarea>
                  </div>

                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" class="mt-1 p-2 block w-full border rounded-md">
                  </div>

                  <div class="flex justify-end">
                    <button type="button"
                      class="close-modal px-4 py-2 mr-2 bg-gray-300 text-gray-700 rounded">Cancel</button>
                    <button type="button" id="saveClientReviewEdit"
                      class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                  </div>
                </form>
              </div>
            </div>


          </div>
        </div>
      <?php endif; ?>

    </main>
    <!-- Include jQuery if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function () {
        // Open Team Members Edit Modal
        $('.edit-tm').on('click', function () {
          const id = $(this).data('id');
          const name = $(this).closest('tr').find('.name').text().trim();
          const position = $(this).closest('tr').find('.position').text().trim();

          $('#editTeamModal').find('input[name="id"]').val(id);
          $('#editTeamModal').find('input[name="name"]').val(name);
          $('#editTeamModal').find('input[name="position"]').val(position);

          $('#editTeamModal').css("display", "flex");
        });

        // Save changes for Team Members
        $('#saveTeamEdit').on('click', function (event) {
          event.preventDefault(); // Prevent default form submission

          let formData = new FormData($('#editTeamForm')[0]);
          formData.append('action', 'editTeamMember');

          $.ajax({
            url: 'company/models/manageTeam.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              try {
                const result = JSON.parse(response);
                alert(result.message);
                location.reload(); // Reload the page to show updated data
              } catch (e) {
                alert('An unexpected error occurred. Please try again.');
              }
            },
            error: function (xhr, status, error) {
              alert(`Error: ${xhr.responseText || 'An error occurred while saving the changes.'}`);
            }
          });
        });

        // Open Clients' Review Edit Modal
        $('.edit-cr').on('click', function () {
          const id = $(this).data('id');
          const name = $(this).closest('tr').find('.name').text().trim();
          const comment = $(this).closest('tr').find('.comment').text().trim();
          const title = $(this).closest('tr').find('.title').text().trim();

          $('#editClientReviewModal').find('input[name="id"]').val(id);
          $('#editClientReviewModal').find('input[name="name"]').val(name);
          $('#editClientReviewModal').find('textarea[name="comment"]').val(comment);
          $('#editClientReviewModal').find('input[name="title"]').val(title);

          $('#editClientReviewModal').css("display", "flex");
        });

        // Save changes for Clients' Reviews
        $('#saveClientReviewEdit').on('click', function (event) {
          event.preventDefault(); // Prevent default form submission

          let formData = new FormData($('#editClientReviewForm')[0]);
          formData.append('action', 'editClient');

          $.ajax({
            url: 'company/models/manageClients.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              try {
                const result = JSON.parse(response);
                alert(result.message);
                location.reload(); // Reload the page to show updated data
              } catch (e) {
                alert('An unexpected error occurred. Please try again.');
              }
            },
            error: function (xhr, status, error) {
              alert(`Error: ${xhr.responseText || 'An error occurred while saving the changes.'}`);
            }
          });
        });

        // Delete Team Member
        $('.del-tm').on('click', function () {
          const id = $(this).data('id');
          let formData = new FormData();
          formData.append('action', 'deleteTeamMember');
          formData.append('id', id);

          $.ajax({
            url: 'company/models/manageTeam.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              try {
                const result = JSON.parse(response);
                alert(result.message);
                if (result.success) {
                  $(`.del-tm[data-id="${id}"]`).closest('tr').remove(); // Remove the row
                }
              } catch (e) {
                alert('An unexpected error occurred. Please try again.');
              }
            },
            error: function (xhr, status, error) {
              alert(`Error: ${xhr.responseText || 'An error occurred while deleting the team member.'}`);
            }
          });
        });

        // Delete Client Review
        $('.del-cr').on('click', function () {
          const id = $(this).data('id');
          let formData = new FormData();
          formData.append('action', 'deleteClient');
          formData.append('id', id);

          $.ajax({
            url: 'company/models/manageClients.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              try {
                const result = JSON.parse(response);
                alert(result.message);
                if (result.success) {
                  $(`.del-cr[data-id="${id}"]`).closest('tr').remove(); // Remove the row
                }
              } catch (e) {
                alert('An unexpected error occurred. Please try again.');
              }
            },
            error: function (xhr, status, error) {
              alert(`Error: ${xhr.responseText || 'An error occurred while deleting the client review.'}`);
            }
          });
        });

        // Close modals
        $('.close-modal').on('click', function () {
          $(this).closest('.fixed').hide();
        });
      });
    </script>

    <script>
      $(document).ready(function () {
        // Show Client Modal
        $('#cr-add').on('click', function () {
          $('#client-modal').removeClass('hidden');
        });

        // Show Team Modal
        $('#tm-add').on('click', function () {
          $('#team-modal').removeClass('hidden');
        });

        // Close Modals
        $('.close-modal').on('click', function () {
          $(this).closest('.fixed').addClass('hidden').find('form')[0].reset();
        });

        // AJAX for Client Form Submission
        $('#client-form').on('submit', function (e) {
          e.preventDefault();
          let formData = new FormData(this);
          formData.append('action', 'addClient');

          $.ajax({
            url: 'company/models/manageClients.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
              let data = JSON.parse(response);
              alert(data.message);
              if (data.success) {
                $('#client-form')[0].reset();
                $('#client-modal').addClass('hidden');
                location.reload(); // Optionally refresh the page or update the table dynamically
              }
            },
            error: function (error) {
              console.error('Error:', error);
            }
          });
        });

        // AJAX for Team Form Submission
        $('#team-form').on('submit', function (e) {
          e.preventDefault();
          let formData = new FormData(this);
          formData.append('action', 'addTeamMember');

          $.ajax({
            url: 'company/models/manageTeam.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
              let data = JSON.parse(response);
              alert(data.message);
              if (data.success) {
                $('#team-form')[0].reset();
                $('#team-modal').addClass('hidden');
                location.reload(); // Optionally refresh the page or update the table dynamically
              }
            },
            error: function (error) {
              console.error('Error:', error);
            }
          });
        });
      });

    </script>

    <script>
      // Function to show tab content
      function showTab(tabId) {
        // Hide all tab contents
        $('.tab-content').hide();

        // Show the selected tab content
        $('#' + tabId).show();
      }
      $(document).ready(function () {
        // Tab button click event
        $('.tab-btn').on('click', function () {
          // Remove active class from all tabs
          $('.tab-btn').removeClass('click');

          // Add active class to the clicked tab
          $(this).addClass('click');
        });

        // filter orders
        document.getElementById('filter-input').addEventListener('input', function () {
          const filterValue = this.value.toLowerCase();
          const rows = document.querySelectorAll('.order-row');

          rows.forEach(function (row) {
            const orderId = row.querySelector('.order-id').textContent.toLowerCase();

            if (orderId.includes(filterValue)) {
              row.style.display = ''; // Show the row
            } else {
              row.style.display = 'none'; // Hide the row
            }
          });
        });

        // Toggle mobile menu visibility
        document.getElementById('mobile-menu-toggle').addEventListener('click', function () {
          const mobileMenu = document.getElementById('mobile-menu');
          mobileMenu.classList.toggle('hidden');
        });


        // When click on add package button
        document.getElementById('addProductBtn').addEventListener('click', function () {
          const modal = document.getElementById('productModal');
          modal.classList.remove('hidden');
          modal.querySelector('#modalContent').classList.remove('scale-95');
          modal.querySelector('#modalContent').classList.add('scale-100');
        });
        // add package modal close
        document.getElementById('closeModalBtn').addEventListener('click', function () {
          const modal = document.getElementById('productModal');
          modal.classList.add('hidden');
          modal.querySelector('#modalContent').classList.remove('scale-100');
          modal.querySelector('#modalContent').classList.add('scale-95');
        });
        // close the modal when click on outside
        document.getElementById('productModal').addEventListener('click', function (event) {
          if (event.target === this) {
            this.classList.add('hidden');
            this.querySelector('#modalContent').classList.remove('scale-100');
            this.querySelector('#modalContent').classList.add('scale-95');
          }
        });
        //dynamically show product add form based on selected type
        document.getElementById('productType').addEventListener('change', function () {
          const type = this.value;
          let formHtml = '';

          if (type === 'tour') {
            formHtml = `
      <form id="tourForm" class="flex flex-col gap-y-4" action="admin/models/add_product.php" method="post" enctype="multipart/form-data">
        <input value="travel_packages" name="type" type="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="col-span-1">
            <label for="title" class="block text-gray-700">Title:</label>
            <input type="text" id="title" name="package_name" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
          <div class="col-span-1">
            <label for="price" class="block text-gray-700">Price:</label>
            <input type="number" id="price" name="price" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="col-span-1">
            <label for="location" class="block text-gray-700">Address:</label>
            <input type="text" id="location" name="location" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
          <div class="col-span-1">
            <label for="rating" class="block text-gray-700">Rating:</label>
            <input type="number" id="rating" name="rating" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" max="5">
          </div>
        </div>
        <div>
          <label for="details" class="block text-gray-700">Details:</label>
          <textarea id="details" name="details" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required></textarea>
        </div>
        <div>
          <label for="img" class="block text-gray-700">Image:</label>
          <input type="file" id="img" name="img" class="w-full border rounded-md p-1 text-gray-700">
        </div>
        <div>
          <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition">Add</button>
        </div>
      </form>
    `;
          } else if (type === 'visa') {
            formHtml = `
      <form id="visaForm" class="flex flex-col gap-y-4" action="admin/models/add_product.php" method="post" enctype="multipart/form-data">
        <input value="visas" name="type" type="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="col-span-1">
            <label for="title" class="block text-gray-700">Title:</label>
            <input type="text" id="title" name="title" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
          <div class="col-span-1">
            <label for="price" class="block text-gray-700">Price:</label>
            <input type="number" id="price" name="price" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="col-span-1">
            <label for="country" class="block text-gray-700">Country:</label>
            <input type="text" id="country" name="country" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
          <div class="col-span-1">
            <label for="requirements" class="block text-gray-700">Requirements:</label>
            <textarea id="requirements" name="requirements" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required></textarea>
          </div>
        </div>
        <div>
          <label for="details" class="block text-gray-700">Details:</label>
          <textarea id="details" name="details" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required></textarea>
        </div>
        <div>
          <label for="img" class="block text-gray-700">Image:</label>
          <input type="file" id="img" name="img" class="w-full border rounded-md p-1 text-gray-700">
        </div>
        <div>
          <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition">Submit</button>
        </div>
      </form>
    `;
          } else if (type === 'air') {
            formHtml = `
      <form id="airForm" class="flex flex-col gap-y-4" action="admin/models/add_product.php" method="post" enctype="multipart/form-data">
        <input value="flights" name="type" type="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="col-span-1">
            <label for="title" class="block text-gray-700">Title:</label>
            <input type="text" id="title" name="title" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
          <div class="col-span-1">
            <label for="price" class="block text-gray-700">Price:</label>
            <input type="number" id="price" name="price" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="col-span-1">
            <label for="from" class="block text-gray-700">From:</label>
            <input type="text" id="from" name="from" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
          <div class="col-span-1">
            <label for="destination" class="block text-gray-700">Destination:</label>
            <input type="text" id="destination" name="destination" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required>
          </div>
        </div>
        <div>
          <label for="details" class="block text-gray-700">Details:</label>
          <textarea id="details" name="details" class="w-full border rounded-md p-1 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition" required></textarea>
        </div>
        <div>
          <label for="img" class="block text-gray-700">Image:</label>
          <input type="file" id="img" name="img" class="w-full border rounded-md p-1 text-gray-700">
        </div>
        <div>
          <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition">Submit</button>
        </div>
      </form>
    `;
          }

          document.getElementById('productForm').innerHTML = formHtml;
        });


        //product table creation
        function fetchData() {
          $.ajax({
            url: 'admin/models/products.php',
            method: 'POST',
            dataType: 'json',
            success: function (data) {
              const tableBody = $('#dataTable tbody');
              tableBody.empty(); // Clear existing rows
              data.forEach(row => {
                tableBody.append(`
            <tr class="data-row odd:bg-white even:bg-gray-50" data-type="${row.type}">
              <td class="px-6 py-4  whitespace-nowrap">${row.type}</td>
              <td class="px-6 py-4 whitespace-nowrap">${row.title}</td>
              <td class="px-6 py-4 max-h-24 overflow-y-auto overflow-hidden break-words text-sm text-gray-500">
                <div style="width:400px;" class="max-h-24 overflow-y-auto">${row.details}</div>
              </td>
              <td class="px-6 py-4 whitespace-normal break-words">${row.price}</td>
              <td class="px-6 py-4 whitespace-nowrap">${row.created_at}</td>
              <td class="px-6 py-4 whitespace-normal">
                <div style="width:100px;">
                <img  src="admin/img/${row.img}" alt="Image" class="h-18 object-cover rounded-md shadow-md">
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">${row.location || ''}</td>
              <td class="px-6 py-4 whitespace-normal">${row.rating || ''}</td>
              <td class="px-6 py-4 whitespace-normal">${row.country || ''}</td>
              <td class="px-6 py-4 max-h-24 overflow-y-auto overflow-hidden break-words text-sm text-gray-500">
                <div style="width:400px;" class="max-h-24 overflow-y-auto">${row.requirements}</div>
              </td>
              <td class="px-6 py-4 whitespace-normal">${row.country_from || ''}</td>
              <td class="px-6 py-4 whitespace-normal">${row.destination || ''}</td>
              <td class="px-6 py-4 whitespace-nowrap ">
              <div class="flex item-center space-x-2">
                <button  data-id="${row.id}" data-type="${row.type}"
                  class="edit-btn flex items-center bg-blue-500 text-white font-bold py-1 px-3 rounded hover:bg-blue-600 transition duration-200">
                  <i class="fas fa-edit mr-1"></i>Edit
                </button>
                <button data-id="${row.id}" data-type="${row.type}"
                  class="del-btn flex items-center bg-red-500 text-white font-bold py-1 px-3 rounded hover:bg-red-600 transition duration-200">
                  <i class="fas fa-trash-alt mr-1"></i>Delete
                </button>
                </div>
              </td>
            </tr>
          `);
              });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              console.error('Error fetching data:', textStatus, errorThrown);
            }
          });
        }


        // Call fetchData when the page loads
        fetchData();


        // Add event listener for filtering product table
        $('#typeFilter').change(function () {
          const selectedType = $(this).val();
          filterTable(selectedType);
        });

        function filterTable(type) {
          // Show all rows if "All" is selected
          if (type === 'all') {
            $('.data-row').show();
          } else {
            // Show rows matching the selected type and hide others
            $('.data-row').each(function () {
              if ($(this).data('type') === type) {
                $(this).show();
              } else {
                $(this).hide();
              }
            });
          }
        }


        // Open edit package modal and populate form
        $(document).on('click', '.edit-btn', function () {
          const id = $(this).data('id');
          const type = $(this).data('type');
          // Show modal
          $('#editModal').removeClass('hidden');

          // Determine form HTML based on type
          let formHtml = '';
          if (type === 'Tour') {
            formHtml = `
        <form id="editForm" class="flex flex-col gap-y-4" enctype="multipart/form-data" method="post" action="admin/models/update_package.php">
          <input value="travel_package" name="type" type="hidden">
          <input type="hidden" name="id" value="${id}">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1">
              <label for="title" class="block text-gray-700">Title:</label>
              <input type="text" id="title" name="package_name" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
            <div class="col-span-1">
              <label for="price" class="block text-gray-700">Price:</label>
              <input type="number" id="price" name="price" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1">
              <label for="location" class="block text-gray-700">Location:</label>
              <input type="text" id="location" name="location" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
            <div class="col-span-1">
              <label for="rating" class="block text-gray-700">Rating:</label>
              <input type="number" id="rating" name="rating" class="w-full border rounded-md p-1 text-gray-700" max="5">
            </div>
          </div>
          <div>
            <label for="details" class="block text-gray-700">Details:</label>
            <textarea id="details" name="details" class="w-full border rounded-md p-1 text-gray-700" required></textarea>
          </div>
          <div>
            <label for="img" class="block text-gray-700">Image:</label>
            <input type="file" id="img" name="img" class="w-full border rounded-md p-1 text-gray-700">
          </div>
        </form>
      `;
          } else if (type === 'Visa') {
            formHtml = `
        <form id="editForm" class="flex flex-col gap-y-4" enctype="multipart/form-data" method="post" action="admin/models/update_package.php">
          <input value="visa" name="type" type="hidden">
          <input type="hidden" name="id" value="${id}">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1">
              <label for="title" class="block text-gray-700">Title:</label>
              <input type="text" id="title" name="title" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
            <div class="col-span-1">
              <label for="price" class="block text-gray-700">Price:</label>
              <input type="number" id="price" name="price" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1">
              <label for="country" class="block text-gray-700">Country:</label>
              <input type="text" id="country" name="country" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
            <div class="col-span-1">
              <label for="requirements" class="block text-gray-700">Requirements:</label>
              <textarea id="requirements" name="requirements" class="w-full border rounded-md p-1 text-gray-700" required></textarea>
            </div>
          </div>
          <div>
            <label for="details" class="block text-gray-700">Details:</label>
            <textarea id="details" name="details" class="w-full border rounded-md p-1 text-gray-700" required></textarea>
          </div>
          <div>
            <label for="img" class="block text-gray-700">Image:</label>
            <input type="file" id="img" name="img" class="w-full border rounded-md p-1 text-gray-700">
          </div>
        </form>
      `;
          } else if (type === 'Air ticket') {
            formHtml = `
        <form id="editForm" class="flex flex-col gap-y-4" enctype="multipart/form-data" method="post" action="admin/models/update_package.php"> 
          <input value="flight" name="type" type="hidden">
          <input type="hidden" name="id" value="${id}">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1">
              <label for="title" class="block text-gray-700">Title:</label>
              <input type="text" id="title" name="title" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
            <div class="col-span-1">
              <label for="price" class="block text-gray-700">Price:</label>
              <input type="number" id="price" name="price" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1">
              <label for="countryFrom" class="block text-gray-700">From:</label>
              <input type="text" id="from" name="countryFrom" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
            <div class="col-span-1">
              <label for="destination" class="block text-gray-700">Destination:</label>
              <input type="text" id="destination" name="destination" class="w-full border rounded-md p-1 text-gray-700" required>
            </div>
          </div>
          <div>
            <label for="details" class="block text-gray-700">Details:</label>
            <textarea id="details" name="details" class="w-full border rounded-md p-1 text-gray-700" required></textarea>
          </div>
          <div>
            <label for="img" class="block text-gray-700">Image:</label>
            <input type="file" id="img" name="img" class="w-full border rounded-md p-1 text-gray-700">
          </div>
        </form>
      `;
          }

          // Populate edit package form 
          $('#modalFormContainer').html(formHtml);

          // Fetch and populate existing data
          $.ajax({
            url: 'admin/models/get_package.php',
            method: 'POST',
            dataType: 'json',
            data: { id: id, type: type },
            success: function (data) {
              if (data) {
                $('#editForm').find('[name="title"]').val(data.title || '');
                $('#editForm').find('[name="package_name"]').val(data.title || '');
                $('#editForm').find('[name="price"]').val(data.price || '');
                $('#editForm').find('[name="details"]').val(data.details || '');
                $('#editForm').find('[name="location"]').val(data.location || '');
                $('#editForm').find('[name="rating"]').val(data.rating || '');
                $('#editForm').find('[name="country"]').val(data.country || '');
                $('#editForm').find('[name="requirements"]').val(data.requirements || '');
                $('#editForm').find('[name="countryFrom"]').val(data.countryFrom || '');
                $('#editForm').find('[name="destination"]').val(data.destination || '');
              }
            }
          });
        });

        // Close the edit modal when the close button is clicked
        $('#closeModal').on('click', function () {
          $('#editModal').addClass('hidden');
          // Reset form values if necessary
          $('#editForm')[0].reset();
        });

        // Close when clicking outside the modal content
        $('#editModal').on('click', function (event) {
          if (event.target === this) {
            $(this).addClass('hidden');
            $('#editForm')[0].reset();
          }
        });

        // $('#saveChanges').click(function () {
        //   $('#editForm').submit();
        // })

        // Save edited changes
        $('#saveChanges').click(function (e) {
          e.preventDefault(); // Prevent the default form submission

          // Show a loading spinner in the save button
          $(this).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

          // Use FormData to handle file uploads and other form data
          var formData = new FormData($('#editForm')[0]); // Get the form data including files

          // Use AJAX to submit the form
          $.ajax({
            url: $('#editForm').attr('action'), // Get the form action URL
            type: 'POST',
            data: formData,
            contentType: false, // Set contentType to false to send the data as FormData
            processData: false, // Prevent jQuery from automatically processing the data
            success: function (response) {
              // Show an alert indicating the changes have been saved
              alert('Saved');
            },
            error: function (xhr, status, error) {
              // Handle error (e.g., show an error message)
              alert('An error occurred: ' + error);
            },
            complete: function () {
              // Remove spinner and reset button text once the request is complete
              $('#saveChanges').html('Save');
            }
          });
        });


        // Event delegation: bind click event to .del-btn through the document
        $(document).on('click', '.del-btn', function () {
          // Get the data attributes from the clicked button
          var id = $(this).data('id');
          var type = $(this).data('type');
          var table;
          var $row = $(this).closest('tr'); // Find the closest parent <tr> of the clicked button

          // Determine the correct table based on the type
          if (type === 'Tour') {
            table = 'travel_packages';
          } else if (type === 'Air ticket') {
            table = 'flights';
          } else if (type === 'Visa') {
            table = 'visas';
          } else {
            alert('Invalid type');
            return;
          }

          // AJAX request to delete_package.php
          $.ajax({
            url: 'admin/models/delete_package.php',
            type: 'POST',
            data: { id: id, table: table },
            success: function (response) {
              // Check the response from the server
              if (response.success) {
                // Remove the parent <tr> of the clicked button
                alert('Package deleted successfully.');
                $row.remove();
              } else {
                alert('Error: ' + response.message);
              }
            },
            error: function () {
              alert('An error occurred while trying to delete the package.');
            }
          });
        });


      });

    </script>
    <!-- Include jQuery in your HTML if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
      $(document).ready(function () {
        $('.order-handle-form').on('submit', function (e) {
          e.preventDefault(); // Prevent the default form submission

          // Get the form and button elements
          var $form = $(this);
          var $submitButton = $form.find('button[type="submit"]');

          // Save the original button text
          var originalButtonText = $submitButton.text();

          // Show loading animation
          $submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

          // Perform the AJAX request
          $.ajax({
            url: $form.attr('action'), // The form action (same page in this case)
            method: 'POST',
            data: $form.serialize(), // Serialize form data
            success: function (response) {
              // Handle success response (e.g., display a success message or refresh the page)
              alert('Order status updated successfully!');
            },
            error: function (xhr, status, error) {
              // Handle error response
              alert('An error occurred while updating the order status.');
            },
            complete: function () {
              // Reset the button text and state after the request completes
              $submitButton.prop('disabled', false).html(originalButtonText);
            }
          });
        });
      });
    </script>

  </body>

</html>