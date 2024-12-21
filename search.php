<?php include 'db/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Pegasus Travelers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <style>

  </style>

  <body class="bg-gray-50  text-gray-900 font-sans">

    <?php include "client/components/header.php" ?>

    <main class="py-10 pb-20 pt-[100px] md:pt-[120px] lg:pt-[150px]">
      <div class="container mx-auto px-4 md:px-10 lg:px-16 xl:px-20">
        <h2 class="text-3xl font-bold text-center mb-8">Search Results</h2>

        <div class="grid gap-6 grid-cols-2 md:grid-cols-2 lg:grid-cols-3">
          <?php
          // Get search query
          $query = $_GET['query'] ?? '';

          // Search queries for different tables
          $queries = [
            'flight' => "SELECT id, title, details, img, price, countryFrom, destination FROM flights WHERE title LIKE :query OR details LIKE :query OR countryFrom LIKE :query OR destination LIKE :query",
            'travel_packages' => "SELECT id, package_name AS title, details, location, img, price FROM travel_packages WHERE package_name LIKE :query OR details LIKE :query OR location LIKE :query",
            'visa' => "SELECT id, title, country as details, img, price, country FROM visas WHERE country LIKE :query OR title LIKE :query OR details LIKE :query",
          ];

          $resultsFound = false; // Flag to check if any results were found
          
          foreach ($queries as $category => $sql) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['query' => "%$query%"]);

            // Check if there are any results
            if ($stmt->rowCount() > 0) {
              $resultsFound = true; // Set the flag to true if results are found
            }

            // Display results
            while ($row = $stmt->fetch()) {
              $linkCategory = ($category === 'travel_packages') ? 'travel' : $category; // Adjust category for travel packages
          
              // Capitalize the first letter of each word in the category
              $categoryLabel = ucfirst($linkCategory);

              echo '<div class="bg-white p-4 rounded shadow relative">'; // Added relative positioning
              echo '<div class="absolute top-5 left-5 bg-blue-500 text-white text-xs px-2 py-1 rounded">' . htmlspecialchars($categoryLabel) . '</div>'; // Category label on top-left corner of the image
              echo '<img src="admin/img/' . htmlspecialchars($row['img']) . '" alt="' . htmlspecialchars($row['title']) . '" class="h-40 w-full object-cover mb-4 rounded">';
              echo '<h3 class="text-lg font-semibold">' . htmlspecialchars($row['title']) . '</h3>';
              echo '<p class="text-sm text-gray-600 mb-4">' . htmlspecialchars(substr($row['details'], 0, 100)) . '...</p>';
              echo '<div class="flex justify-between items-center">';
              echo '<span class="price text-blue-600 text-xs md:text-sm lg:text-lg font-bold">$' . htmlspecialchars($row['price']) . '</span>';
              echo '<a href="place_order.php?id=' . $row['id'] . '&category=' . $linkCategory . '" class="buy text-white text-xs md:text-sm lg:text-lg bg-blue-500 px-3 py-2 rounded hover:bg-blue-600"><i class="fas fa-shopping-cart"></i> <span class="ml-1">Book</span></a>';
              echo '</div>';
              echo '</div>';
            }
          }

          // Display "No results found" if no results were found in any category
          if (!$resultsFound) {
            echo '<div class="text-center col-span-full mt-10">';
            echo '<i class="fas fa-frown fa-3x text-gray-400 mb-4"></i>';
            echo '<p class="text-lg font-semibold text-gray-600">No results found</p>';
            echo '</div>';
          }
          ?>
        </div>
      </div>
    </main>

    <?php include 'client/components/footer.php'; ?>

  </body>

</html>