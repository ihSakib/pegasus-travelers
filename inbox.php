<?php
// inbox.php
include 'db/config.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location:admin.php");
  exit();
}

try {
  // Query to fetch messages with user profile images
  $stmt = $pdo->prepare("SELECT cm.*, u.profile_image 
                           FROM contact_messages cm 
                           LEFT JOIN users u ON cm.email = u.email 
                           ORDER BY cm.submitted_at DESC");
  $stmt->execute();
  $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Query to check if there are new messages (submitted in the last 24 hours)
  $stmt_new = $pdo->prepare("SELECT COUNT(*) AS new_count 
                               FROM contact_messages 
                               WHERE submitted_at > NOW() - INTERVAL 1 DAY");
  $stmt_new->execute();
  $new_messages = $stmt_new->fetch(PDO::FETCH_ASSOC)['new_count'];

} catch (PDOException $e) {
  echo 'Error: ' . $e->getMessage();
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox | Pegasus Travelers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body {
        font-family: 'Poppins', sans-serif;
      }

      .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out, padding 0.4s ease-out;
      }

      .accordion-content.open {
        max-height: 500px;
        /* This value should cover the expected height of the content */
        padding-top: 1rem;
        padding-bottom: 1rem;
      }
    </style>
  </head>

  <body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-700 text-white py-4 px-6 lg:px-20 shadow-md">
      <div class="container mx-auto flex items-center justify-between">
        <!-- Logo and Company Name -->
        <div class="flex items-center space-x-3">
          <i class="fas fa-plane-departure text-3xl"></i> <!-- Adding a travel-related icon for style -->
          <h1 class="text-2xl font-bold">Pegasus Travelers</h1>
        </div>
        <div class="relative">
          <i class="fas fa-bell text-2xl cursor-pointer"></i>
          <?php if ($new_messages > 0): ?>
            <span
              class="absolute top-0 right-0 block w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
              <?php echo $new_messages; ?>
            </span>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto max-w-4xl p-6 mt-6">
      <h2 class="text-4xl font-semibold mb-6 text-gray-800">Inbox</h2>

      <div id="accordion" class="space-y-4">
        <?php foreach ($messages as $message): ?>
          <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <button
              class="w-full text-left px-6 py-4 flex items-center justify-between bg-gray-50 border-b border-gray-200 hover:bg-gray-100 focus:outline-none"
              onclick="toggleAccordion('message-<?php echo $message['id']; ?>')">
              <div class="flex items-center">
                <?php if ($message['registered'] && $message['profile_image']): ?>
                  <img src="client/img/<?php echo htmlspecialchars($message['profile_image']); ?>" alt="Profile Image"
                    class="w-10 h-10 rounded-full mr-4">
                <?php else: ?>
                  <i class="fas fa-user-circle text-blue-600 text-2xl mr-4"></i>
                <?php endif; ?>
                <div>
                  <p class="font-semibold text-gray-800">
                    <?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?>
                    <?php echo $message['registered'] == 'yes' ? '(Registered)' : '(Unregistered)'; ?>
                  </p>
                  <p class="text-gray-600">Subject: <?php echo htmlspecialchars($message['subject']); ?></p>
                </div>
              </div>
              <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300"
                id="arrow-message-<?php echo $message['id']; ?>"></i>
            </button>
            <div class="accordion-content" id="message-<?php echo $message['id']; ?>">
              <p class="text-gray-800 px-6 pt-4"><strong>Email:</strong>
                <?php echo htmlspecialchars($message['email']); ?></p>
              <p class="text-gray-800 px-6 pb-4"><strong>Phone:</strong>
                <?php echo htmlspecialchars($message['phone']); ?></p>
              <p class="text-gray-800 px-6"><strong>Message:</strong>
                <?php echo nl2br(htmlspecialchars(substr($message['message'], 0, 100))); ?>...</p>
              <p class="text-gray-500 text-sm px-6 mt-2"><strong>Submitted At:</strong>
                <?php echo htmlspecialchars($message['submitted_at']); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </main>

    <!-- Footer -->
    <!-- <footer class="bg-gray-800 text-white py-4">
      <div class="container mx-auto text-center">
        <p>&copy; <?php echo date('Y'); ?> Pegasus Travelers. All rights reserved.</p>
      </div>
    </footer> -->

    <script>
      function toggleAccordion(id) {
        const content = document.getElementById(id);
        const arrow = document.getElementById('arrow-' + id);

        // Toggle the accordion animation
        if (content.classList.contains('open')) {
          content.classList.remove('open');
          arrow.classList.replace('fa-chevron-up', 'fa-chevron-down');
        } else {
          // Close all other accordions before opening the selected one
          document.querySelectorAll('.accordion-content').forEach(el => {
            el.classList.remove('open');
          });
          document.querySelectorAll('.fa-chevron-up').forEach(el => {
            el.classList.replace('fa-chevron-up', 'fa-chevron-down');
          });

          // Open the selected accordion
          content.classList.add('open');
          arrow.classList.replace('fa-chevron-down', 'fa-chevron-up');
        }
      }
    </script>
  </body>

</html>