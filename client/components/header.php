<?php
if (isset($_SESSION['user_id'])) {
  // Prepare and execute the SQL query to fetch user details
  $stmt = $pdo->prepare("SELECT name, email,phone, profile_image FROM users WHERE id = :user_id LIMIT 1");
  $stmt->execute(['user_id' => $_SESSION['user_id']]);

  // Fetch the user data
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Check if the user data is found
  if ($user) {
    $user_name = htmlspecialchars($user['name']);
    $user_email = htmlspecialchars($user['email']);
    $user_phone = htmlspecialchars($user['phone']);
    $user_img = !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'img/avatar.png'; // Default to avatar if no image
  } else {
    // Default values if user data is not found
    $user_name = 'John Doe';
    $user_email = 'john@example.com';
    $user_img = 'img/avatar.png';
  }
}
?>


<script>
    // Create a new link element
const faviconLink = document.createElement('link');

// Set the attributes for the favicon
faviconLink.rel = 'icon';
faviconLink.href = 'img/favicon.png';
faviconLink.type = 'image/png';

// Optionally, add the class attributes if needed
faviconLink.classList.add('w-4', 'h-4');

// Append the new favicon link to the head without deleting existing elements
document.head.appendChild(faviconLink);

</script>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.1/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<script src="//unpkg.com/alpinejs"></script>
<script src="https://cdn.tailwindcss.com"></script>
<style>
 
.top-nav {
 background-color: rgba(173, 216, 230, 0.05) !important;
   backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  box-shadow: none !important;
}

.top-nav .d-menu-item {
  color: #2d2f3c !important;
  font-weight: bold;
  transition: color 0.3s ease;
}

.top-nav .d-menu-item:hover {
  background-color: transparent !important;
  color: #3b82f6 !important;
}
</style>


<nav x-data="{ isOpen: false }" class="top-nav fixed z-[500] bg-white shadow top-0 left-0 right-0">
  <div class="container px-6 md:px-10 lg:px-16 py-3 mx-auto">
    <div class="lg:flex lg:items-center lg:justify-between">

      <!-- Logo and Mobile Menu Button -->
      <div class="flex items-center justify-between">
        <div class="flex gap-4 items-center">
          <a href="/" class="inline">
            <img class="w-[45px] md:w-[50px] lg:w-[60px] inline" src="img/logo.png" alt="Logo">
          </a>
          <form action="search.php" method="get" class="relative hidden md:block  ">
            <input type="text" name="query"
              class=" pl-10 pr-4 py-2  rounded-lg  focus:outline-none focus:ring-1 focus:ring-blue-500 border"
              placeholder="Search..." required>
            <div class="absolute inset-y-0 py-1 left-0 flex items-center pl-3">
              <i class="fas fa-search text-gray-400"></i>
            </div>
          </form>
        </div>

        <!-- Mobile menu button -->
        <div class="flex items-center  lg:hidden">
          <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="signin.php" class=" mr-4  rounded-sm">
             <button style="vertical-align:middle" type="button" class="font-semibold text-[#2d2f3c] bg-blue-200 hover:bg-blue-500 hover:text-white custom-hover transition-colors duration-300 rounded-md px-[12px] py-[8px] pt-[6px] text-sm">Sign In</button>

            </a>
          <?php endif; ?>
          <button @click="isOpen = !isOpen" type="button"
            class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600" aria-label="toggle menu">
            <svg x-show="!isOpen" xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16" />
            </svg>
            <svg x-show="isOpen" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
      

      <!-- Sidebar (Mobile Menu) -->
      <aside
        class="lg:hidden fixed inset-x-0 z-20 top-[69px] md:top-[74px] left-0 transition-all duration-300 ease-in-out bg-transparent shadow-md"
        x-show="isOpen" @click.away="isOpen = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform -translate-x-full">
        <div
          class="flex flex-col w-64 h-screen px-4 py-8 overflow-y-auto bg-white border-r dark:bg-gray-900 dark:border-gray-700">
          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="flex flex-col items-center mt-6 -mx-2">
              <img class="object-cover w-24 h-24 mx-2 rounded-full border-2 border-gray-700"
                src="client/img/<?php echo $user_img; ?>" alt="avatar">
              <h4 class="mx-2 mt-2 font-medium text-gray-800 dark:text-gray-300 "><?php echo $user_name; ?></h4>
              <p class="mx-2 mt-1 text-sm font-medium text-gray-600 dark:text-gray-400 "><?php echo $user_email; ?></p>
              <p class="mx-2 mt-1 text-sm font-medium text-gray-600 dark:text-gray-400 "><?php echo $user_phone; ?></p>
            </div>
          <?php endif; ?>
          <div class="flex flex-col justify-between flex-1 m-1">
            <nav class="space-y-1">
              <form action="search.php" method="get" class="relative  my-4">
                <input type="text" name="query"
                  class=" pl-10 pr-4 py-1 rounded-lg  focus:outline-none focus:ring-2 focus:ring-blue-500 w-full  "
                  placeholder="Search..." required>
                <div class="absolute inset-y-0 py-1 left-0 flex items-center pl-3">
                  <i class="fas fa-search text-gray-400"></i>
                </div>
              </form>
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php"
                  class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-800 rounded-md">
                  <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 14l6.16-3.422A12.045 12.045 0 0112 22.026v-7.622z" />
                  </svg>
                  View Profile
                </a>
              <?php endif; ?>

              <a href="/"
                class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-800 rounded-md">
                <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7m-2 2H5m2 0v6h10v-6" />
                </svg>
                Home
              </a>

              <a href="about.php"
                class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-800 rounded-md">
                <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 2H6m6 0v-4" />
                </svg>
                About
              </a>

              <a href="contact.php"
                class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-800 rounded-md">
                <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 15h12l-6-6-6 6z" />
                </svg>
                Contact
              </a>

              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="bookings.php"
                  class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-800 rounded-md">
                  <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                  </svg>
                  Bookings
                </a>
                <a href="favorites.php"
                  class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-800 rounded-md">
                  <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Favorites
                </a>
                <a href="client/functions/logout.php?role=user"
                  class="flex items-center px-3 py-2 text-red-600 hover:bg-red-100 dark:hover:bg-gray-800 rounded-md">
                  <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3m0 0l-3 3m3-3l3-3" />
                  </svg>
                  Logout
                </a>
              <?php endif; ?>
            </nav>
          </div>
        </div>
      </aside>

      <!-- Desktop Menu -->
      <div class=" hidden lg:flex lg:items-center lg:space-x-6">
        <a href="/" class="d-menu-item px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Home</a>
        <a href="about.php" class=" d-menu-item px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">About</a>
        <a href="contact.php" class="d-menu-item px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Contact</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="bookings.php" class=" d-menu-item px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Bookings</a>
          <a href="favorites.php" class="d-menu-item px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Favorites</a>
          <div x-data="{ isOpen: false }" class="relative">
            <button @click="isOpen = !isOpen" class="flex items-center  ">
              <img src="client/img/<?php echo $user_img; ?>" alt=""
                class=" bg-gray-100 rounded-full p-1 w-[50px] h-[50px]">
            </button>
            <div x-show="isOpen" @click.away="isOpen = false"
              class="absolute right-0 mt-4 w-52 p-4 bg-white border border-gray-200 rounded-md shadow-lg">
              <div class="flex flex-col items-center mt-6 -mx-2">
                <img class="object-cover w-24 h-24 mx-2 rounded-full  border-2 border-gray-700"
                  src="client/img/<?php echo $user_img; ?>" alt="avatar">
                <h4 class="mx-2 mt-2 font-medium text-gray-800 "><?php echo $user_name; ?></h4>
                <p class="mx-2 mt-1 text-sm font-medium text-gray-600 "><?php echo $user_email; ?></p>
                <p class="mx-2 mt-1 text-sm font-medium text-gray-600 "><?php echo $user_phone; ?></p>
              </div>
              <hr class="my-4">
              <div class=" mb-6">
                <a href="profile.php" class="flex items-center px-3 py-2  hover:bg-blue-100 rounded-md">
                  <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 14l6.16-3.422A12.045 12.045 0 0112 22.026v-7.622z" />
                  </svg>
                  View Profile
                </a>
                <a href="client/functions/logout.php?role=user"
                  class="flex items-center px-3 py-2 text-red-600 hover:bg-red-100  rounded-md">
                  <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3m0 0l-3 3m3-3l3-3" />
                  </svg>
                  Logout
                </a>
              </div>
            </div>
          </div>
        <?php else: ?>
         <a href="signin.php" class="  hover:bg-blue-700 bg-blue-500 text-white custom-hover transition duration-300 rounded-md px-4 py-[8px] pt-[7px] ">Sign In</a>

        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<?php include 'client/components/chatBubble.php' ?>