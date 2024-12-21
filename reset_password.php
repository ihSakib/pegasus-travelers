<?php include 'db/config.php' ?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Pegasus Travelers</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="bg-gray-100 ">
    <?php include "client/components/header.php" ?>
    <main class="flex flex-col justify-center items-center min-h-screen">
      <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md ">
        <h1 class="text-2xl font-semibold mb-4 text-center text-gray-700">Reset Your Password</h1>
        <form action="client/models/update_password.php" method="post" id="resetForm">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

          <!-- New Password Field -->
          <div class="mb-4">
            <label for="password" class="block text-gray-600">New Password:</label>
            <div class="relative">
              <input type="password" id="password" name="password"
                class="mt-1 p-2 w-full border border-gray-300 rounded" required minlength="8"
                placeholder="Enter a new password">
              <div class="absolute top-0 bottom-0 right-3 flex flex-col justify-center">
                <i class="fas fa-eye cursor-pointer text-gray-500" id="togglePassword"></i>
              </div>
            </div>
          </div>

          <!-- Confirm Password Field -->
          <div class="mb-4">
            <label for="confirmPassword" class="block text-gray-600">Confirm Password:</label>
            <div class="relative">
              <input type="password" id="confirmPassword" name="confirmPassword"
                class="mt-1 p-2 w-full border border-gray-300 rounded" required minlength="8"
                placeholder="Confirm your password">
              <div class="absolute top-0 bottom-0 right-3 flex flex-col justify-center">
                <i class="fas fa-eye cursor-pointer text-gray-500" id="toggleConfirmPassword"></i>
              </div>
            </div>
          </div>


          <button type="submit"
            class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">Reset
            Password</button>
        </form>
      </div>
    </main>
    <?php include 'client/components/footer.php'; ?>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Show/hide password functionality for the New Password field
        const togglePassword = document.getElementById('togglePassword');
        togglePassword.addEventListener('click', function () {
          const passwordField = document.getElementById('password');
          const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordField.setAttribute('type', type);
          this.classList.toggle('fa-eye-slash');
        });

        // Show/hide password functionality for the Confirm Password field
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        toggleConfirmPassword.addEventListener('click', function () {
          const confirmPasswordField = document.getElementById('confirmPassword');
          const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
          confirmPasswordField.setAttribute('type', type);
          this.classList.toggle('fa-eye-slash');
        });

        // Client-side form validation
        document.getElementById('resetForm').addEventListener('submit', function (e) {
          const password = document.getElementById('password').value;
          const confirmPassword = document.getElementById('confirmPassword').value;

          // Check if passwords match
          if (password.length < 8) {
            alert('Password must be at least 8 characters long.');
            e.preventDefault();
            return;
          }

          if (password !== confirmPassword) {
            alert('Passwords do not match. Please try again.');
            e.preventDefault();
          }
        });
      });
    </script>
  </body>

</html>