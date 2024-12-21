<?php include 'db/config.php' ?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request | Pegasus Travelers</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" defer></script>
  </head>

  <body class="bg-gray-100 ">

    <?php include "client/components/header.php" ?>
    <main class="flex items-center justify-center min-h-screen">
      <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-semibold mb-4 text-center text-gray-700">Password Reset Request</h1>
        <form action="client/models/send_reset_link.php" method="post">
          <div class="mb-4">
            <label for="email" class="block text-gray-600">Enter your email:</label>
            <input type="email" id="email" name="email" class="mt-1 p-2 w-full border border-gray-300 rounded" required>
          </div>
          <button type="submit"
            class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">Send
            Reset Link</button>
        </form>
      </div>
    </main>
    <?php include 'client/components/footer.php'; ?>

  </body>

</html>