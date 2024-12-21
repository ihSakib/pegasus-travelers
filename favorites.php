<?php
// Include the database connection
include 'db/config.php';
if (!isset($_SESSION['user_id'])) {
  header("Location:signin.php");
  exit();
}
// Assume $user_id is defined and available
$user_id = $_SESSION['user_id']; // Or however you get the user ID

// Combined query using LEFT JOIN
$queryFavouriteAll = "
    SELECT ft.*, 
           COALESCE(tp.package_name, v.title, f.title) AS name,
           COALESCE(tp.details, v.details, f.details) AS details,
           COALESCE(tp.price, v.price, f.price) AS price,
           COALESCE(tp.img, v.img, f.img) AS img,
           COALESCE(v.country, tp.location, CONCAT(f.countryFrom, ' to ', f.destination)) AS location,
           CASE 
               WHEN tp.id IS NOT NULL THEN 'travel'
               WHEN v.id IS NOT NULL THEN 'visa'
               WHEN f.id IS NOT NULL THEN 'flight'
           END AS category
    FROM favorites ft
    LEFT JOIN travel_packages tp ON ft.product_id = tp.id AND ft.category = 'travel'
    LEFT JOIN visas v ON ft.product_id = v.id AND ft.category = 'visa'
    LEFT JOIN flights f ON ft.product_id = f.id AND ft.category = 'flight'
    WHERE ft.user_id = :user_id
    ORDER BY ft.created_at DESC
";


// Prepare and execute the combined query
$stmtFavouriteAll = $pdo->prepare($queryFavouriteAll);
$stmtFavouriteAll->execute(['user_id' => $user_id]);

// Fetch all combined favorite items
$favouriteAll = $stmtFavouriteAll->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Packages</title>
    <link rel="stylesheet" href="path/to/tailwind.css">
  </head>

  <body class="bg-gray-100 ">
    <?php include "client/components/header.php" ?>

    <div class="flex flex-col justify-between min-h-dvh">
      <main class="container mx-auto px-6 md:px-8 lg:px-14 xl:px-20 pt-20 md:pt-28 pb-20">
        <h1 class="text-2xl font-bold mb-6">Your Favorite Packages</h1>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <?php if (count($favouriteAll) == 0): ?>

            <p class="text-neutral-900 font-semibold">No favorite items!</p>

          <?php else: ?>

            <?php foreach ($favouriteAll as $item): ?>
              <article class="rounded-xl bg-white p-3 shadow-lg hover:shadow-xl">
                <a href="place_order.php?id=<?php echo urlencode($item['product_id']); ?>&category=<?php echo htmlspecialchars($item['category']); ?>"
                  class="block">
                  <div class="relative flex items-end overflow-hidden rounded-xl">
                    <img src="<?php echo 'admin/img/' . htmlspecialchars($item['img']); ?>"
                      alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-48 object-cover" />
                    <div class="absolute bottom-3 left-3 inline-flex items-center rounded-lg bg-white p-2 shadow-md">
                      <span class="text-slate-400 text-sm"><?php echo ucfirst($item['category']); ?></span>
                    </div>
                  </div>
                </a>
                <div class="mt-1 p-2">
                  <a href="place_order.php?id=<?php echo urlencode($item['product_id']); ?>&category=<?php echo htmlspecialchars($item['category']); ?>"
                    class="block">
                    <h2 class="text-slate-700 font-semibold"><?php echo htmlspecialchars($item['name']); ?></h2>
                    <p class="text-slate-400 mt-1 text-sm">
                      <?php echo htmlspecialchars($item['location'] ?? 'N/A'); ?>
                    </p>
                  </a>
                  <div class="mt-3 flex items-end justify-between">
                    <span
                      class="text-sm md:text-lg font-bold text-blue-500">৳<?php echo number_format($item['price'], 2); ?></span>
                    <a title="Toggle favorite" href="#" data-product-id="<?= urlencode($item['product_id']); ?>"
                      data-category="<?= htmlspecialchars($item['category']); ?>"
                      class="fav-btn group inline-flex rounded-xl <?= $item['product_id'] ? 'bg-red-200' : 'bg-red-100'; ?> p-2 hover:bg-red-200">
                      <svg xmlns="http://www.w3.org/2000/svg"
                        class="group-hover:text-red-600 <?= $item['product_id'] ? 'text-red-600' : 'text-red-400'; ?> h-3 w-3 md:h-4 md:w-4"
                        viewBox="0 0 24 24" fill="currentColor">
                        <path
                          d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                      </svg>
                    </a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>

          <?php endif; ?>
        </div>
      </main>

      <?php include 'client/components/footer.php'; ?>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.fav-btn').forEach(button => {
          button.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default anchor behavior

            const productId = this.getAttribute('data-product-id');
            const category = this.getAttribute('data-category');

            // Send AJAX request to PHP script
            fetch('client/models/add_fav.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              credentials: 'same-origin', // Include cookies with the request
              body: new URLSearchParams({
                id: productId,
                category: category
              })
            })
              .then(response => {
                if (response.ok) {
                  return response.text(); // No response body needed
                } else {
                  throw new Error('Network response was not ok.');
                }
              })
              .then(() => {
                // Toggle the button's state and color based on the current state
                if (this.classList.contains('bg-red-100')) {
                  // Button selected state
                  this.classList.remove('bg-red-100', 'text-red-400');
                  this.classList.add('bg-red-200', 'text-red-600');
                  this.setAttribute('title', 'Remove from favorites');

                  // Change SVG color
                  const svg = this.querySelector('svg');
                  if (svg) {
                    svg.classList.remove('text-red-400');
                    svg.classList.add('text-red-600');
                  }
                } else {
                  // Button deselected state
                  this.classList.remove('bg-red-200', 'text-red-600');
                  this.classList.add('bg-red-100', 'text-red-400');
                  this.setAttribute('title', 'Add to favorites');

                  // Change SVG color
                  const svg = this.querySelector('svg');
                  if (svg) {
                    svg.classList.remove('text-red-600');
                    svg.classList.add('text-red-400');
                  }
                }
              })
              .catch(error => {
                console.error('Error during AJAX request:', error);
              });
          });
        });
      });
    </script>
  </body>

</html>