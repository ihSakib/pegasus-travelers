<?php

require_once 'db/config.php'; // Ensure this file initializes your PDO connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $location = $_POST['location'];

  // Handle image upload
  if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $imageTempPath = $_FILES['profile_image']['tmp_name'];
    $imageName = $_FILES['profile_image']['name'];
    $uploadPath = 'client/img/' . $imageName;

    // Delete the old image if it exists
    $query = "SELECT profile_image FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && !empty($user['profile_image'])) {
      $oldImagePath = 'client/img/' . $user['profile_image'];
      if (file_exists($oldImagePath)) {
        unlink($oldImagePath);
      }
    }

    // Move the new image to the directory
    move_uploaded_file($imageTempPath, $uploadPath);

    // Update the profile image in the database
    $query = "UPDATE users SET profile_image = :profile_image WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
      'profile_image' => $imageName,
      'id' => $user_id
    ]);
  }

  // Update other profile details in the database
  $query = "UPDATE users SET name = :name, phone = :phone, location = :location WHERE id = :id";
  $stmt = $pdo->prepare($query);
  $stmt->execute([
    'name' => $name,
    'phone' => $phone,
    'location' => $location,
    'id' => $user_id
  ]);

  // Redirect back to profile page after update
  header('Location: profile.php');
  exit();
}

// Fetch user data from the database
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $user_id]);
$user_profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_profile) {
  echo "User not found.";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="bg-gray-100">
    <?php include "client/components/header.php" ?>

    <div class=" px-4 py-[100px]  md:py-[120px]">
      <h2 class="text-xl pb-4 md:text-2xl lg:text-3xl font-semibold text-center">Your Profile</h2>
      <div class="flex mx-4  flex-col gap-y-1 md:flex-row md:mx-6 lg:mx-10 xl:mx-20  no-wrap ">
        <!-- Left Side -->
        <div class="w-full md:w-4/12 ">
          <div class="bg-white p-3 border-t-4 border-green-400">
            <div class="image overflow-hidden">
              <img class="h-auto w-full mx-auto"
                src="client/img/<?php echo htmlspecialchars($user_profile['profile_image']); ?>" alt="Profile Image"
                id="profileImage">
            </div>
            <h1 class="text-gray-900 font-bold text-xl leading-8 my-1" id="name">
              <?php echo htmlspecialchars($user_profile['name']); ?>
            </h1>
            <h3 class="text-gray-600 font-lg text-semibold leading-6"><span
                id="location"><?php echo htmlspecialchars($user_profile['location']); ?></span></h3>
            <ul
              class="bg-gray-100 text-gray-600 hover:text-gray-700 hover:shadow py-2 px-3 mt-3 divide-y rounded shadow-sm">
              <li class="flex items-center py-3">
                <span>Status</span>
                <span class="ml-auto"><span
                    class="bg-green-500 py-1 px-2 rounded text-white text-sm">Active</span></span>
              </li>
              <li class="flex items-center py-3">
                <span>Member since</span>
                <span class="ml-auto"
                  id="createdAt"><?php echo date('F j, Y', strtotime($user_profile['created_at'])); ?></span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Right Side -->
        <div class="w-full  md:mx-2 h-64">
          <div class=" bg-white p-3 shadow-sm rounded-sm  overflow-x-auto">
            <div class="flex items-center space-x-2 font-semibold text-gray-900 leading-8">
              <span class="text-green-500">
                <svg class="h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2.166 7.25L10 0l7.833 7.25H13v7.75H7V7.25H2.166z" />
                </svg>
              </span>
              <span class="tracking-wide">Profile Details</span>
              <button id="editButton"
                class="ml-auto bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">Edit</button>
              <!-- <button id="updateButton"
                class="ml-auto bg-green-500 text-white px-4 py-1 rounded hover:bg-green-600 hidden">Update</button> -->
            </div>
            <form action="profile.php" method="POST" enctype="multipart/form-data">
              <div class="text-gray-700 mt-4">
                <div class="grid md:grid-cols-2 text-sm">
                  <div class="grid grid-cols-2">
                    <div class="px-4 py-2 font-semibold">Name</div>
                    <div class="px-4 py-2">
                      <input type="text" name="name" id="nameInput"
                        value="<?php echo htmlspecialchars($user_profile['name']); ?>"
                        class="border-2 border-gray-300 p-1 rounded w-full hidden  indent-2">
                      <span id="nameDisplay"><?php echo htmlspecialchars($user_profile['name']); ?></span>
                    </div>
                  </div>
                  <div class="grid grid-cols-2">
                    <div class="px-4 py-2 font-semibold">Phone</div>
                    <div class="px-4 py-2">
                      <input type="text" name="phone" id="phoneInput"
                        value="<?php echo htmlspecialchars($user_profile['phone']); ?>"
                        class="border-2 border-gray-300 p-1 rounded w-full hidden  indent-2">
                      <span id="phoneDisplay"><?php echo htmlspecialchars($user_profile['phone']); ?></span>
                    </div>
                  </div>
                  <div class="grid grid-cols-2">
                    <div class="px-4 py-2 font-semibold">Address</div>
                    <div class="px-4 py-2">
                      <input type="text" name="location" id="locationInput"
                        value="<?php echo htmlspecialchars($user_profile['location']); ?>"
                        class="border-2 border-gray-300 p-1 rounded w-full hidden  indent-2">
                      <span id="locationDisplay"><?php echo htmlspecialchars($user_profile['location']); ?></span>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 overflow-auto">
                    <div class="px-4 py-2 font-semibold">Profile Image</div>
                    <div class="px-4 py-2">
                      <input type="file" name="profile_image" id="profileImageInput"
                        class="border-2 border-gray-300 p-1 indent-2 rounded w-full hidden">
                      <span id="profileImageText">
                        <?php if ($user_profile['profile_image']): ?>
                          Uploaded
                        <?php else: ?>
                          Not Uploaded
                        <?php endif; ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-center my-4">
                <button type="submit" id="submitButton" class="bg-green-500 text-white px-4 py-2 rounded hidden">Save
                  Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php include 'client/components/footer.php'; ?>

    <script>
      const editButton = document.getElementById('editButton');
      // const updateButton = document.getElementById('updateButton');
      const submitButton = document.getElementById('submitButton');

      editButton.addEventListener('click', () => {
        document.getElementById('nameDisplay').classList.add('hidden');
        document.getElementById('phoneDisplay').classList.add('hidden');
        document.getElementById('locationDisplay').classList.add('hidden');
        document.getElementById('profileImageText').classList.add('hidden');

        document.getElementById('nameInput').classList.remove('hidden');
        document.getElementById('phoneInput').classList.remove('hidden');
        document.getElementById('locationInput').classList.remove('hidden');
        document.getElementById('profileImageInput').classList.remove('hidden');

        editButton.classList.add('hidden');
        // updateButton.classList.remove('hidden');
        submitButton.classList.remove('hidden');
      });

      updateButton.addEventListener('click', () => {
        // This will trigger the form submission
        document.forms[0].submit();
      });
    </script>
  </body>

</html>