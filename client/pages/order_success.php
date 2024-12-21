<?php include '../../db/config.php'; ?>
<?php
if (!isset($_SESSION['user_id'])) {
  header("Location:../../signin.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" defer></script>
  </head>

  <body>
    <main class="min-h-screen flex flex-col items-center justify-center bg-gray-100 p-4">
      <div class="bg-white rounded-xl shadow-lg p-6 text-center">
        <div class="flex items-center justify-center">
          <i class="fas fa-check-circle text-green-500 text-5xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mt-4">Order Placed Successfully!</h1>
        <p class="text-gray-600 mt-2">
          Thank you for your purchase. You will be redirected to your orders page in
          <span id="countdown" class="font-bold text-gray-800">3</span> seconds.
        </p>
      </div>
    </main>

    <script>
      let countdown = 3;
      const countdownElement = document.getElementById('countdown');

      const interval = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;

        if (countdown <= 0) {
          clearInterval(interval);
          window.location.href = '../../bookings.php';
        }
      }, 1000); // Update every second
    </script>


  </body>

</html>