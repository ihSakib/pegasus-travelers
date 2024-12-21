<?php include '../../db/config.php'; ?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Link Sent | Pegasus Travelers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" defer></script>
  </head>

  <body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md text-center">
      <div class="mb-4">
        <i class="fas fa-check-circle text-green-500 text-5xl"></i>
      </div>
      <h1 class="text-2xl font-semibold mb-4 text-gray-700">Reset Link Sent!</h1>
      <p class="text-gray-600">A password reset link has been sent to your email address. Please check your inbox and
        follow the instructions to reset your password.</p>
      <div class="mt-6">
        <a href="../../index.php"
          class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
          <i class="fas fa-home mr-2"></i> Return to Homepage
        </a>
      </div>
    </div>
  </body>

</html>