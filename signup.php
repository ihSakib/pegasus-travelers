<?php
// Set session lifetime to 7 days (604800 seconds)
ini_set('session.gc_maxlifetime', 604800);
ini_set('session.cookie_lifetime', 604800);
require 'db/config.php';

if (isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Collect and sanitize user input
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  $name = trim($_POST['name']);
  $location = trim($_POST['location']);
  $phone = trim($_POST['phone']);
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Handle image upload
  $img = $_FILES['profile_image']['name'];
  $target_dir = "client/img/";
  $target_file = $target_dir . basename($img);

  // Handle file upload
  if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
    die("Sorry, there was an error uploading your file.");
  }

  // Check if the username or email already exists
  $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username OR email = :email');
  $stmt->execute(['username' => $username, 'email' => $email]);
  $user = $stmt->fetch();

  if ($user) {
    $_SESSION['reg_error'] = 'Username or email already exists.';
    header('Location: signup.php');
    exit;
  }

  // Insert new user into the database
  $stmt = $pdo->prepare('INSERT INTO users (username, email, phone, password, created_at, name, location, profile_image)
VALUES (:username, :email, :phone, :password, NOW(), :name, :location, :profile_image)');
  $stmt->execute([
    'username' => $username,
    'email' => $email,
    'phone' => $phone,
    'password' => $hashedPassword,
    'name' => $name,
    'location' => $location,
    'profile_image' => $img
  ]);

  // Start a session and set user information
  $_SESSION['user_id'] = $pdo->lastInsertId();
  $_SESSION['username'] = $username;
  $_SESSION['user_email'] = $email;

  // Optionally set a cookie
  setcookie('username', $username, time() + (86400 * 7), "/"); // Cookie lasts 7 days

  // Redirect to a welcome page or dashboard
  header('Location: index.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Pegasus Travelers</title>
    <style>
      nav {
        position: static !important;
      }

      .border-red-500 {
        border-color: red !important;
      }

      .border-green-500 {
        border-color: green !important;
      }
    </style>
  </head>

  <body>
    <?php include "client/components/header.php" ?>

    <main class="mx-6 mt-[50px] mb-16">
      <div style="box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);" class="max-w-xl mx-auto  bg-white  rounded-lg mt-10 overflow-hidden ">
        <h2 class="text-2xl font-bold py-3 md:py-4 bg-[#2563eb] text-white  text-center">Create An Account</h2>
        <?php
        if (isset($_SESSION['reg_error'])) {
          echo "<p class='text-center text-red-600 py-2 pb-4'>";
          echo $_SESSION["reg_error"];
          echo "</p>";
          $_SESSION["reg_error"] = '';
        }
        ?>
        <form id="signupForm" action="" method="post" class="space-y-4 p-6" enctype="multipart/form-data">
          <div class="grid grid-cols-1 gap-4">
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2 xl:gap-4 ">
              <div class="flex flex-col">
                <label for="username" class="font-medium mb-1">Username <span
                    class="text-red-600 font-semibold">*</span></label>
                <input name="username" type="text" id="username" class="border border-gray-300 p-2 rounded-md" required>
              </div>
              <div class="flex flex-col">
                <label for="email" class="font-medium mb-1">Email <span
                    class="text-red-600 font-semibold">*</span></label>
                <input name="email" type="email" id="email" class="border border-gray-300 p-2 rounded-md" required>
              </div>
              <div class="flex flex-col xl:col-span-2">
                <label for="phone" class="font-medium mb-1">Phone <span
                    class="text-red-600 font-semibold">*</span></label>
                <input name="phone" type="tel" id="phone" class="border border-gray-300 p-2 rounded-md" required>
              </div>
            </div>

            <div class="grid grid-cols-1 ">
              <div class="flex flex-col">
                <label for="name" class="font-medium mb-1">Name <span
                    class="text-red-600 font-semibold">*</span></label>
                <input name="name" type="text" id="name" class="border border-gray-300 p-2 rounded-md" required>
              </div>
            </div>
          </div>

          <div class="flex flex-col">
            <label for="location" class="font-medium mb-1">Address <span
                class="text-red-600 font-semibold">*</span></label>
            <textarea name="location" id="location" class="border border-gray-300 p-2 rounded-md"></textarea>
          </div>

          <div class="flex flex-col">
            <label for="profile_image" class="font-medium mb-1">Profile Image <span
                class="text-red-600 font-semibold">*</span></label>
            <input name="profile_image" type="file" id="profile_image" class="border border-gray-300 p-2 rounded-md">
          </div>

          <div class="grid grid-cols-1 gap-4 xl:grid-cols-2 xl:gap-4 ">
            <div class="flex flex-col ">
              <label for="password" class="font-medium mb-1" minlength="8">Password <span
                  class="text-red-600 font-semibold">*</span></label>
              <div class="relative">
                <input name="password" type="password" id="password"
                  class="border border-gray-300 p-2 rounded-md pr-10  w-full" required>
                <div class=" absolute right-3 top-0 bottom-0 flex flex-col justify-center">
                  <i class="fas fa-eye cursor-pointer text-gray-500" id="togglePassword"></i>
                </div>
              </div>
            </div>
            <div class="flex flex-col ">
              <label for="confirm_password" class="font-medium mb-1" minlength="8">Confirm Password <span
                  class="text-red-600 font-semibold">*</span></label>
              <div class="relative">
                <input name="confirm_password" type="password" id="confirm_password"
                  class="border border-gray-300 p-2 rounded-md pr-10  w-full" required>
                <div class=" absolute right-3 top-0 bottom-0 flex flex-col justify-center">
                  <i class="fas fa-eye cursor-pointer text-gray-500" id="toggleConfirmPassword"></i>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600">Sign Up</button>
        </form>

        <p class="mb-6 text-center">
          Already have an account? <a href="signin.php" class="text-blue-500 hover:underline"><i
              class="fas fa-sign-in-alt"></i> Sign In</a>
        </p>
      </div>
    </main>
    <?php include 'client/components/footer.php'; ?>

    <script>
      // Toggle show/hide password for the password field
      const togglePassword = document.querySelector('#togglePassword');
      const passwordInput = document.querySelector('#password');

      togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
      });

      // Toggle show/hide password for the confirm password field
      const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
      const confirmPasswordInput = document.querySelector('#confirm_password');

      toggleConfirmPassword.addEventListener('click', function () {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
      });

      // Validate if password and confirm password match
      const signupForm = document.getElementById("signupForm");
      const passwordField = document.getElementById("password");
      const confirmPasswordField = document.getElementById("confirm_password");

      function validatePasswords() {
        if (passwordField.value !== confirmPasswordField.value) {
          passwordField.classList.add("border-red-500");
          confirmPasswordField.classList.add("border-red-500");
          passwordField.classList.remove("border-green-500");
          confirmPasswordField.classList.remove("border-green-500");
        } else {
          passwordField.classList.remove("border-red-500");
          confirmPasswordField.classList.remove("border-red-500");
          passwordField.classList.add("border-green-500");
          confirmPasswordField.classList.add("border-green-500");
        }
      }

      // Check password match on input event
      confirmPasswordField.addEventListener('input', validatePasswords);
      // passwordField.addEventListener('input', validatePasswords);

      signupForm.addEventListener("submit", function (event) {
        if (passwordField.value !== confirmPasswordField.value) {
          alert("Passwords do not match.");
          event.preventDefault(); // Prevent form submission
        }
      });
    </script>
  </body>

</html>