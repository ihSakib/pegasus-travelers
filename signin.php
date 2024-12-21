<?php
// Set session lifetime to 7 days (604800 seconds)
ini_set('session.gc_maxlifetime', 604800);
ini_set('session.cookie_lifetime', 604800);
require 'db/config.php'; // Include your database connection

if (isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Collect and sanitize user input
  $identifier = trim($_POST['identifier']); // This can be either email or username
  $password = trim($_POST['password']);

  // Fetch user data from the database, checking both email and username
  $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE username = :identifier OR email = :identifier');
  $stmt->execute(['identifier' => $identifier]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];

    // Optionally set a cookie
    setcookie('user_email', $user['email'], time() + (86400 * 7), "/"); // Cookie lasts 7 days

    // Redirect to a dashboard or home page
    header('Location: index.php');
    exit;
  } else {
    $_SESSION['error'] = 'Invalid email/username or password.';
    header('Location: signin.php');
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Pegasus Travelers</title>
    <style>
      nav {
        position: static !important;
      }
    </style>
  </head>

  <body>

    <?php include "client/components/header.php" ?>

    <main class="mx-6 mt-[50px] mb-16">
      <div style="box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);" class="max-w-lg mx-auto  bg-white rounded-lg mt-10 overflow-hidden">
        <h2  class="text-2xl font-bold py-3 md:py-4 bg-[#2563eb] text-white  text-center">Sign In</h2>
        <?php
        if (isset($_SESSION['error'])) {
          echo "<p class='text-center text-red-600 py-2 pb-4'>";
          echo $_SESSION["error"];
          echo "</p>";
          $_SESSION["error"] = '';
        }
        ?>
        <form action="" method="post" class="space-y-4 p-6">
          <div class="flex flex-col">
            <label for="identifier" class="font-medium mb-1">Email or Username</label>
            <input name="identifier" type="text" id="identifier" class="border border-gray-300 p-2 rounded-md" required>
          </div>

          <div class="flex flex-col ">
            <label for="password" class="font-medium mb-1">Password</label>
            <div class="relative">
              <input name="password" type="password" id="password"
                class="border w-full border-gray-300 p-2 rounded-md pr-10" required minlength="8">
              <div class="absolute right-3 top-0 bottom-0 flex flex-col justify-center">
                <i class="fas fa-eye  cursor-pointer text-gray-500" id="togglePassword"></i>
              </div>
            </div>
          </div>

          <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600">Sign In</button>
        </form>

        <p class=" text-center">
          <a href="signup.php" class="block px-4  text-blue-500 hover:underline">
            <i class="fas fa-user-plus"></i> Don't have an account? Sign Up
          </a>
          <a href="reset_request.php" class="block px-4  text-blue-500 hover:underline mt-2 mb-6">
            <i class="fas fa-key"></i> Forgot Password
          </a>
        </p>
      </div>
    </main>

    <?php include 'client/components/footer.php'; ?>

    <script>
      // Toggle show/hide password
      const togglePassword = document.querySelector('#togglePassword');
      const passwordInput = document.querySelector('#password');

      togglePassword.addEventListener('click', function () {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle the icon
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
      });
    </script>
  </body>

</html>