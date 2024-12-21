<?php
include 'db/config.php';

if (!isset($_SESSION['admin_id'])) {
  header('Location: admin.php');
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers Handling | Pegasus Travelers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- from node_modules -->
    <script src="node_modules/@material-tailwind/html/scripts/tabs.js"></script>
    <!-- from cdn -->
    <script src="https://unpkg.com/@material-tailwind/html@latest/scripts/tabs.js"></script>
    <style>
      body {
        font-family: 'Poppins', sans-serif !important;
      }

      .tab-content {
        display: none;
      }

      .tab-content.active {
        display: block;
      }

      .tab-btn.click {
        color: dodgerblue;
        border-bottom: 2px solid dodgerblue;
      }
    </style>
  </head>

  <body>
    <!-- header -->
    <header class="bg-blue-700 text-white py-4 px-6 lg:px-20 shadow-md">
      <div class="container mx-auto flex items-center justify-between">
        <!-- Logo and Company Name -->
        <div class="flex items-center space-x-3">
          <i class="fas fa-plane-departure text-3xl"></i> <!-- Adding a travel-related icon for style -->
          <h1 class="text-2xl font-bold">Pegasus Travelers</h1>
        </div>

        <!-- Icons and Links -->
        <div class="relative  items-center space-x-4 hidden lg:flex">
          <?php if (isset($_SESSION['admin_id'])): ?>

            <!-- Message Icon -->
            <a href="inbox" target="_blank"
              class="text-green-400 hover:text-green-500 flex items-center space-x-2 font-semibold lg:text-lg">
              <i class="fas fa-envelope"></i>
              <span class="hidden md:inline">Messages</span> <!-- Show text on larger screens -->
            </a>

            <!-- offer icon -->
            <a href="handle_offers.php" target="_blank"
              class="text-yellow-400 hover:text-yellow-500 flex items-center space-x-2 font-semibold lg:text-lg">
              <i class="fas fa-gift"></i>
              <span class="hidden md:inline">Handle Offers</span> <!-- Show text on larger screens -->
            </a>

            <!-- Logout Icon -->
            <a href="admin/functions/logout.php?role=admin"
              class="text-red-400 hover:text-red-500 flex items-center space-x-2 font-semibold lg:text-lg">
              <i class="fas fa-sign-out-alt"></i>
              <span class="hidden md:inline">Logout</span> <!-- Show text on larger screens -->
            </a>
          <?php else: ?>
            <div></div>
          <?php endif; ?>
        </div>
        <!-- Mobile Menu Toggle (optional for responsiveness) -->
        <div class="lg:hidden flex items-center">
          <button id="mobile-menu-toggle" class="text-white focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
          </button>
        </div>
      </div>

    </header>

    <!-- Mobile Menu (optional) -->
    <div id="mobile-menu" class="lg:hidden hidden px-6 py-4 bg-blue-800">
      <?php if (isset($_SESSION['admin_id'])): ?>
        <a href="inbox.php" target="_blank" class="block text-green-400 hover:text-green-500 py-2">
          <i class="fas fa-envelope"></i> Messages
        </a>
        <a href="handle_offers.php" target="_blank" class="block text-yellow-400 hover:text-yellow-500 py-2">
          <i class="fas fa-envelope"></i> Handle Offers
        </a>
        <a href="admin/functions/logout.php?role=admin" class="block text-red-400 hover:text-red-500 py-2">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      <?php endif; ?>
    </div>

    <main class="mx-6">

      <div class="border-b mt-6 border-gray-200">
        <nav class="flex gap-x-1 justify-center" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
          <button type="button" onclick="showTab('a')"
            class="tab-btn click py-2 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600  "
            id="tabs-with-underline-item-1" aria-selected="true" data-hs-tab="#tabs-with-underline-1"
            aria-controls="tabs-with-underline-1" role="tab">
            ADD
          </button>
          <button type="button" onclick="showTab('m')"
            class="tab-btn  py-2 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600"
            id="tabs-with-underline-item-2" aria-selected="false" data-hs-tab="#tabs-with-underline-2"
            aria-controls="tabs-with-underline-2" role="tab">
            MANAGE
          </button>
        </nav>
      </div>

      <div class="mt-3">

        <div id="a" class="tab-content active">
          <!-- add offer -->
          <h2 class="text-3xl font-semibold text-center my-4 mt-6">Add Offers</h2>
          <form id="addOfferForm" class="space-y-6 p-8  bg-gray-50 rounded-lg shadow-lg max-w-5xl mx-auto"
            enctype="multipart/form-data" method="post">
            <!-- Category and Package Selection -->
            <div class="mb-6">
              <label for="category" class="block text-lg font-semibold text-gray-800 mb-2">Category</label>
              <div class="relative">
                <select id="category" name="category"
                  class="block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-600 focus:ring-opacity-50"
                  required>
                  <option value="">Select Category</option>
                  <option value="travel">Travel</option>
                  <option value="visa">Visa</option>
                  <option value="flight">Flight</option>
                </select>
              </div>
            </div>

            <div id="packageContainer" class="mb-6 hidden">
              <label for="package" class="block text-lg font-semibold text-gray-800 mb-2">Package</label>
              <div class="relative">
                <select id="package" name="package_id"
                  class="block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-600 focus:ring-opacity-50"
                  required>
                  <!-- Options will be populated dynamically -->
                </select>
              </div>
            </div>

            <!-- Discount and Offer Type -->
            <div class="mb-6">
              <label for="discount" class="block text-lg font-semibold text-gray-800 mb-2">Discount (%)</label>
              <input type="number" id="discount" name="discount"
                class="block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-600 focus:ring-opacity-50"
                min="0" max="100" required>
            </div>

            <div class="mb-6">
              <label for="offerType" class="block text-lg font-semibold text-gray-800 mb-2">Offer Type</label>
              <select id="offerType" name="type"
                class="block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-600 focus:ring-opacity-50"
                required>
                <option value="">Select Offer Type</option>
                <option value="special offer">Special Offer</option>
                <option value="new">New</option>
              </select>
            </div>

            <div class="mb-6">
              <label for="img" class="block text-lg font-semibold text-gray-800 mb-2">Image</label>
              <input type="file" id="img" name="img" accept="image/*"
                class="block w-full text-gray-700 border border-gray-300 rounded-md shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-600 focus:ring-opacity-50"
                required>
            </div>

            <button type="submit"
              class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              Add Offer
            </button>
          </form>

        </div>

        <div id="m" class="tab-content ">
          <h2 class="text-3xl font-semibold text-center my-4 mt-6">Manage Offers</h2>
          <!-- Offers Management Table -->
          <div class="my-8 mt-0 max-w-7xl mx-auto overflow-auto bg-white rounded-lg shadow-lg">
            <table class="min-w-full overflow-auto text-center bg-white border border-gray-300 rounded-lg ">
              <thead class="bg-gray-100">
                <tr>
                  <th class="py-3 px-6 border-b text-left text-gray-600 font-medium">ID</th>
                  <th class="py-3 px-6 border-b text-left text-gray-600 font-medium">Type</th>
                  <th class="py-3 px-6 border-b text-left text-gray-600 font-medium">Category</th>
                  <th class="py-3 px-6 border-b text-left text-gray-600 font-medium">Package Name</th>
                  <th class="py-3 px-6 border-b text-left text-gray-600 font-medium">Package Price</th>
                  <th class="py-3 px-6 border-b text-left text-gray-600 font-medium">Discount (%)</th>
                  <th class="py-3 px-6 border-b text-left text-gray-600 font-medium">Discounted Price</th>
                  <th class="py-3 px-6 border-b text-left text-gray-600 font-medium">Photo</th>
                  <th class="py-3 px-6 border-b text-left text-gray-600 font-medium">Actions</th>
                </tr>
              </thead>
              <tbody id="offersTableBody" class="bg-white">
                <!-- Rows will be populated dynamically -->
              </tbody>
            </table>
          </div>

          <!-- Edit Offer Modal -->
          <div id="editOfferModal"
            class="fixed inset-0  flex items-center justify-center bg-gray-500 bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
              <h2 class="text-lg font-semibold mb-4">Edit Offer</h2>
              <form id="editOfferForm" enctype="multipart/form-data">
                <input type="hidden" id="offerId" name="id">

                <div class="mb-4">
                  <label for="offerType" class="block text-sm font-medium text-gray-700">Offer Type</label>
                  <select id="offerType" name="type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                    required>
                    <option value="special offer">Special Offer</option>
                    <option value="new">New</option>
                  </select>
                </div>

                <div class="mb-4">
                  <label for="offerDiscount" class="block text-sm font-medium text-gray-700">Discount (%)</label>
                  <input type="number" id="offerDiscount" name="discount"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                    min="0" max="100" required>
                </div>

                <div class="mb-4">
                  <label for="offerImg" class="block text-sm font-medium text-gray-700">Image</label>
                  <input type="file" id="offerImg" name="img" accept="image/*"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>

                <div class="flex gap-4">
                  <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Changes
                  </button>
                  <button type="button" id="cancelEditBtn"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

      </div>


    </main>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Variables
        const categorySelect = document.getElementById('category');
        const packageContainer = document.getElementById('packageContainer');
        const packageSelect = document.getElementById('package');
        const form = document.getElementById('addOfferForm');
        const tableBody = document.getElementById('offersTableBody');
        const modal = document.getElementById('editOfferModal');
        const editOfferForm = document.getElementById('editOfferForm');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const tabButtons = document.querySelectorAll('.tab-btn');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        // Category change event to show packages
        categorySelect.addEventListener('change', function () {
          const category = this.value;

          if (category) {
            packageContainer.style.display = 'block';

            fetch(`admin/handle-offers/models/fetch_packages.php?category=${category}`)
              .then(response => response.json())
              .then(data => {
                packageSelect.innerHTML = ''; // Clear existing options

                data.forEach(pkg => {
                  const option = document.createElement('option');
                  option.value = pkg.id;
                  option.textContent = `${pkg.name} - $${pkg.price}`;
                  packageSelect.appendChild(option);
                });
              })
              .catch(error => {
                console.error('Error fetching packages:', error);
              });
          } else {
            packageContainer.style.display = 'none';
          }
        });

        // Form submission for adding offers
        form.addEventListener('submit', function (event) {
          // event.preventDefault();

          const formData = new FormData(form);
          fetch('admin/handle-offers/models/add_offer.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(result => {
              if (result.success) {
                // alert('Offer added successfully!');
                form.reset();
                packageContainer.classList.add('hidden');
              } else {
                alert('Error adding offer: ' + result.error);
              }
            })
            .catch(error => console.error('Error:', error));
        });

        // Load and display offers
        function loadOffers() {
          fetch('admin/handle-offers/models/fetch_offers.php')
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                tableBody.innerHTML = '';
                data.data.forEach(offer => {
                  const row = document.createElement('tr');
                  row.innerHTML = `
                <td class="border px-4 py-2" data-id="${offer.id}">${offer.id}</td>
                <td class="border px-4 py-2" data-column="type">${offer.type}</td>
                <td class="border px-4 py-2" data-column="category">${offer.category}</td>
                <td class="border px-4 py-2" data-column="package_name">${offer.package_name}</td>
                <td class="border px-4 py-2" data-column="package_price">${Number(offer.package_price).toFixed(2)}</td>
                <td class="border px-4 py-2" data-column="discount">${offer.discount}</td>
                <td class="border px-4 py-2" data-column="discounted_price">${Number(offer.package_price - (offer.package_price * (offer.discount / 100))).toFixed(2)}</td>
                <td class="border px-4 py-2" data-column="img">
                  <img src="admin/handle-offers/img/${offer.img}" alt="Offer Image" class="w-16 h-16 object-cover">
                </td>
                <td class="border px-4 py-2">
                  <div class="flex gap-4">
                    <button class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 edit-btn">
                      <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 delete-btn" data-id="${offer.id}">
                      <i class="fas fa-trash"></i> Delete
                    </button>
                  </div>
                </td>
              `;
                  tableBody.appendChild(row);
                });
              } else {
                console.error('Error fetching offers:', data.error);
              }
            })
            .catch(error => console.error('Error fetching offers:', error));
        }

        // Initial load of offers
        loadOffers();

        // Handle editing offers
        tableBody.addEventListener('click', function (event) {
          const target = event.target;

          // Edit button click event
          if (target.classList.contains('edit-btn')) {
            const row = target.closest('tr');
            const id = row.querySelector('td[data-id]').textContent.trim();
            const type = row.querySelector('td[data-column="type"]').textContent.trim();
            const discount = row.querySelector('td[data-column="discount"]').textContent.trim();

            document.getElementById('offerId').value = id;
            document.getElementById('offerType').value = type;
            document.getElementById('offerDiscount').value = discount;
            modal.classList.remove('hidden');
          }

          // Delete button click event
          if (target.classList.contains('delete-btn')) {
            const id = target.dataset.id;
            if (confirm('Are you sure you want to delete this offer?')) {
              fetch(`admin/handle-offers/models/delete_offer.php?id=${id}`)
                .then(response => response.json())
                .then(result => {
                  if (result.success) {
                    alert('Offer deleted successfully');
                    target.closest('tr').remove();
                  } else {
                    alert('Error deleting offer: ' + result.error);
                  }
                })
                .catch(error => console.error('Error:', error));
            }
          }
        });

        // Edit offer form submission
        editOfferForm.addEventListener('submit', function (event) {
          event.preventDefault();
          const formData = new FormData(editOfferForm);
          fetch('admin/handle-offers/models/edit_offer.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(result => {
              if (result.success) {
                const row = tableBody.querySelector(`td[data-id="${formData.get('id')}"]`).closest('tr');
                row.querySelector('td[data-column="type"]').textContent = formData.get('type');
                let price = Number(row.querySelector('td[data-column="package_price"]').textContent);
                let dc = Number(formData.get('discount'));
                let pdc = price - (price * dc / 100);
                row.querySelector('td[data-column="discounted_price"]').textContent = pdc.toFixed(2);
                row.querySelector('td[data-column="discount"]').textContent = formData.get('discount');

                if (formData.get('img').name) {
                  const imageCell = row.querySelector('td[data-column="img"]');
                  const newImage = URL.createObjectURL(formData.get('img'));
                  imageCell.innerHTML = `<img src="${newImage}" alt="Offer Image" class="w-16 h-16 object-cover">`;
                }
                document.getElementById('editOfferForm').reset();
                modal.classList.add('hidden');

              } else {
                alert('Error updating offer: ' + result.error);
              }
            })
            .catch(error => console.error('Error:', error));
        });

        // Cancel edit modal
        cancelEditBtn.addEventListener('click', function () {
          document.getElementById('editOfferForm').reset();
          modal.classList.add('hidden');
        });

        // Close modal when clicking outside the modal content
        document.addEventListener('click', function (event) {
          const modal = document.getElementById('editOfferModal');
          const modalContent = modal.querySelector('.modal-content');

          // Check if the click is outside the modal content
          if (event.target === modal) {
            document.getElementById('editOfferForm').reset();
            modal.classList.add('hidden');
          }
        });

        // Mobile menu toggle
        mobileMenuToggle.addEventListener('click', function () {
          mobileMenu.classList.toggle('hidden');
        });

        // Tab button click event
        tabButtons.forEach(button => {
          button.addEventListener('click', function () {
            // Remove active class from all tabs
            tabButtons.forEach(btn => btn.classList.remove('click'));

            // Add active class to the clicked tab
            this.classList.add('click');
          });
        });


      });

      // Function to show tab content
      function showTab(tabId) {
        const tabContents = document.querySelectorAll('.tab-content');
        // Hide all tab contents
        tabContents.forEach(content => content.classList.remove('active'));

        // Show the selected tab content
        document.getElementById(tabId).classList.add('active');
      }
    </script>

  </body>

</html>