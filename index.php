<?php
include 'db/config.php';
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = 'null';
}
// Fetch travel packages with favorites status
$queryTravels = "
    SELECT tp.*, f.id AS favorite_id
    FROM travel_packages tp
    LEFT JOIN favorites f
    ON tp.id = f.product_id AND f.category = 'travel' AND f.user_id = ?
";
$stmtTravels = $pdo->prepare($queryTravels);
$stmtTravels->execute([$user_id]);
$travels = $stmtTravels->fetchAll(PDO::FETCH_ASSOC);

// Fetch visas with favorites status
$queryVisas = "
    SELECT v.*, f.id AS favorite_id
    FROM visas v
    LEFT JOIN favorites f
    ON v.id = f.product_id AND f.category = 'visa' AND f.user_id = ?
";
$stmtVisas = $pdo->prepare($queryVisas);
$stmtVisas->execute([$user_id]);
$visas = $stmtVisas->fetchAll(PDO::FETCH_ASSOC);

// Fetch flights with favorites status
$queryFlights = "
    SELECT fl.*, f.id AS favorite_id
    FROM flights fl
    LEFT JOIN favorites f
    ON fl.id = f.product_id AND f.category = 'flight' AND f.user_id = ?
";
$stmtFlights = $pdo->prepare($queryFlights);
$stmtFlights->execute([$user_id]);
$flights = $stmtFlights->fetchAll(PDO::FETCH_ASSOC);


$sql = "SELECT * FROM offers ORDER BY id desc";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Fetch all offers
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pegasus Travelers</title>
    <link href="https://fonts.cdnfonts.com/css/gagalin" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/holla" rel="stylesheet">
   
    <style>
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

      .header-txt {
        font-family: 'Gagalin', sans-serif !important;
        color: #0c3894 !important;
      }

      .sub-header {
        font-family: 'Holla', sans-serif !important;
        color: #0c3894 !important;

      }
    </style>

  <body>
    <?php include 'client/components/header.php'; ?>

    <main>
      <!-- hero section -->
      <section class="xl:pt-[150px] pb-10 md:pb-20 pt-28 mx-3 ">
        <div
          class="max-w-screen-xl mx-auto text-gray-600 gap-x-12 items-center justify-items-center overflow-hidden md:grid md:grid-cols-2 md:px-8">

          <div class="flex-none  space-y-2 md:space-y-4  px-4 sm:max-w-lg md:px-0 lg:max-w-xl">
            <h1 class="sub-header text-lg tracking-wide text-indigo-600 font-medium">
              Unleash Your Travel Desire!
            </h1>
            <h2
              class="text-3xl header-txt md:text-4xl tracking-widest text-gray-800 font-extrabold lg:text-4xl xl:text-5xl">
              Pegasus Travelers
            </h2>
            <p class="">
              Your Path to Unforgettable Adventures
              Visas, Flights, Hotels, Sightseeing, Transport—We've Got You Covered. Just Pack Your Bags and Go! Let’s
              turn your travel dreams into a great adventure!
            </p>
            <?php if (!isset($_SESSION['user_id'])): ?>
              <div class="items-center gap-x-3 space-y-3 sm:flex sm:space-y-0">
                <a href="signin.php"
                  class="block py-2 px-4 text-center text-white font-medium bg-indigo-600 duration-150 hover:bg-indigo-500 active:bg-indigo-700 rounded-lg shadow-lg hover:shadow-none">
                  Sign In
                </a>
                <a href="signup.php"
                  class="flex items-center justify-center gap-x-2 py-2 px-4 text-gray-700 hover:text-gray-500 font-medium duration-150 active:bg-gray-100 border rounded-lg md:inline-flex">Get
                  Started<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd"
                      d="M2 10a.75.75 0 01.75-.75h12.59l-2.1-1.95a.75.75 0 111.02-1.1l3.5 3.25a.75.75 0 010 1.1l-3.5 3.25a.75.75 0 11-1.02-1.1l2.1-1.95H2.75A.75.75 0 012 10z"
                      clip-rule="evenodd"></path>
                  </svg>
                </a>
              </div>
            <?php endif; ?>

          </div>

          <div class="rounded-lg overflow-hidden mt-6  md:mt-0  md:block md:max-w-[100%]  ">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 ">
              <div x-data="{
        currentSlide: 0,
        slides: [
          <?php
          foreach ($offers as $offer) {
            echo "{ image: 'admin/handle-offers/img/{$offer['img']}',link:'place_order.php?id={$offer['id']}&category=offer&dc={$offer['discount']}' },";
          }
          ?>
        ],
        interval: null,
        startAutoSlide() {
          this.interval = setInterval(() => {
            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
          }, 3000); // 2000 milliseconds = 2 seconds
        },
        stopAutoSlide() {
          clearInterval(this.interval);
        },
        nextSlide() {
          this.stopAutoSlide();
          this.currentSlide = (this.currentSlide + 1) % this.slides.length;
          this.startAutoSlide();
        },
        prevSlide() {
          this.stopAutoSlide();
          this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
          this.startAutoSlide();
        }
      }" x-init="startAutoSlide()" class="relative overflow-hidden">

                <!-- Slideshow Container -->
                <div class="flex transition-transform duration-700 "
                  :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                  <template x-for="(slide, index) in slides" :key="index">
                    <div class="flex-none w-full">
                      <!-- Clickable Slide -->
                      <a :href="slide.link" class=" rounded-lg overflow-hidden">
                        <img :src="slide.image" alt="Special Offer"
                          class="w-full h-80 object-cover rounded-lg shadow-md hover:opacity-90 transition-opacity">
                      </a>
                    </div>
                  </template>
                </div>

                <!-- Previous Button -->
                <button @click="prevSlide()"
                  class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white opacity-50 hover:opacity-100 p-2 rounded-full shadow-md hover:bg-gray-200">
                  <i class="fas fa-chevron-left text-gray-800"></i>
                </button>

                <!-- Next Button -->
                <button @click="nextSlide()"
                  class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white opacity-50 hover:opacity-100 p-2 rounded-full shadow-md hover:bg-gray-200">
                  <i class="fas fa-chevron-right text-gray-800"></i>
                </button>

              </div>
            </div>
          </div>

        </div>
      </section>

      <!-- tabs -->
      <section class="container mx-auto p-4
       <?php if (isset($_SESSION['user_id'])): ?>
        pt-10
       <?php endif; ?>">

        <!-- Tabs-btn -->
          <div class=" flex border-b border-gray-300 justify-center ">
            <button class="click tab-btn py-2 px-4 " onclick="showTab('travel')">Tour</button>
            <button class="tab-btn py-2 px-4 text-gray-600 hover:text-blue-500" onclick="showTab('visa')">Visa</button>
            <button class="tab-btn py-2 px-4 text-gray-600 hover:text-blue-500"
              onclick="showTab('flight')">Flight</button>
          </div>

        <!-- Tab Contents -->
        <div>
          <!-- travel packages section -->
          <div id="travel" class="tab-content active">
            <section class="mt-8 mb-16">
              <h1 class="mb-4 xl:mb-6 text-center font-sans text-2xl lg:text-4xl xl:text-5xl font-bold text-gray-900">
                Tour
                Package<span class="text-blue-600">.</span></h1>

              <div class="mx-auto grid max-w-screen-xl grid-cols-2 gap-6  md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($travels as $travel): ?>
                  <article class="rounded-xl bg-white p-3 shadow-lg hover:shadow-xl">
                    <a href="place_order.php?id=<?php echo urlencode($travel['id']); ?>&category=travel" class="block">

                      <div class="relative flex items-end overflow-hidden rounded-xl">
                        <img src="<?php echo 'admin/img/' . htmlspecialchars($travel['img']); ?>"
                          alt="<?php echo htmlspecialchars($travel['package_name']); ?>"
                          class="w-full h-48 object-cover" />
                        <div class="absolute bottom-3 left-3 inline-flex items-center rounded-lg bg-white p-2 shadow-md">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                          <span
                            class="text-slate-400 ml-1 text-sm"><?php echo htmlspecialchars($travel['rating']); ?></span>
                        </div>
                      </div>
                    </a>

                    <div class="mt-1 p-2">
                      <a href="place_order.php?id=<?php echo urlencode($travel['id']); ?>&category=travel" class="block">
                        <h2 class="text-slate-700 font-semibold"><?php echo htmlspecialchars($travel['package_name']); ?>
                        </h2>
                        <p class="text-slate-400 mt-1 text-sm"><?php echo htmlspecialchars($travel['location']); ?></p>
                        <div class="mt-3 flex items-end justify-between">
                          <p>
                            <span
                              class="text-sm md:text-lg font-bold text-blue-500">৳<?php echo number_format($travel['price'], 2); ?></span>
                          </p>
                      </a>
                      <?php if (isset($_SESSION['user_id'])): ?>
                        <button title="Add or remove as favorite" href="#"
                          data-product-id="<?= urlencode($travel['id']); ?>" data-category="travel"
                          data-user-id="<?= urlencode($_SESSION['user_id']); ?>"
                          class="fav-btn group inline-flex rounded-xl <?= $travel['favorite_id'] ? 'bg-red-200' : 'bg-red-100'; ?> p-2 hover:bg-red-200">
                          <svg xmlns="http://www.w3.org/2000/svg"
                            class="group-hover:text-red-600 <?= $travel['favorite_id'] ? 'text-red-600' : 'text-red-400'; ?> h-3 w-3 md:h-4 md:w-4"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path
                              d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                          </svg>
                        </button>
                      <?php endif; ?>

                    </div>
                  </article>
                <?php endforeach; ?>
              </div>
            </section>
          </div>

          <!-- visa packages section -->
          <div id="visa" class="tab-content ">
            <section class="mt-8 mb-16">
              <h1 class="mb-4 xl:mb-6 text-center font-sans text-2xl lg:text-4xl xl:text-5xl font-bold text-gray-900">
                Visa Assistance<span class="text-blue-600">.</span></h1>
              <div class="mx-auto grid max-w-screen-xl grid-cols-2 gap-6  md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($visas as $visa): ?>
                  <article class="rounded-xl bg-white p-3 shadow-lg hover:shadow-xl ">
                    <a href="place_order.php?id=<?php echo urlencode($visa['id']); ?>&category=visa" class="block ">
                      <div class="relative flex items-end overflow-hidden rounded-xl">
                        <img src="<?php echo 'admin/img/' . htmlspecialchars($visa['img']); ?>"
                          alt="<?php echo htmlspecialchars($visa['title']); ?>" class="w-full h-48 object-cover" />
                        <!--  -->
                      </div>
                    </a>
                    <div class="mt-1 p-2">
                      <a href="place_order.php?id=<?php echo urlencode($visa['id']); ?>&category=visa" class="block ">
                        <h2 class="text-slate-700 font-semibold"><?php echo htmlspecialchars($visa['title']); ?></h2>
                        <p class="text-slate-400 mt-1 text-sm"><?php echo htmlspecialchars($visa['country']); ?></p>

                      </a>

                      <div class="mt-3 flex items-end justify-between">
                        <p>
                          <span
                            class="text-sm md:text-lg font-bold text-blue-500">৳<?php echo number_format($visa['price'], 2); ?></span>
                        </p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                          <button title="Add or remove as favorite" href="#"
                            data-product-id="<?= urlencode($visa['id']); ?>" data-category="visa"
                            data-user-id="<?= urlencode($_SESSION['user_id']); ?>"
                            class="fav-btn group inline-flex rounded-xl <?= $visa['favorite_id'] ? 'bg-red-200' : 'bg-red-100'; ?> p-2 hover:bg-red-200">
                            <svg xmlns="http://www.w3.org/2000/svg"
                              class="group-hover:text-red-600 <?= $visa['favorite_id'] ? 'text-red-600' : 'text-red-400'; ?> h-3 w-3 md:h-4 md:w-4"
                              viewBox="0 0 24 24" fill="currentColor">
                              <path
                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                          </button>
                        <?php endif; ?>

                      </div>

                    </div>
                  </article>

                <?php endforeach; ?>
              </div>
            </section>
          </div>

          <!-- flight packages section -->
          <div id="flight" class="tab-content ">
            <section class="mt-8 mb-16">
              <h1 class="mb-4 xl:mb-6 text-center font-sans text-2xl lg:text-4xl xl:text-5xl font-bold text-gray-900">
                Air Ticket<span class="text-blue-600">.</span></h1>
              <div class="mx-auto grid max-w-screen-xl grid-cols-2 gap-6  md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($flights as $flight): ?>
                  <article class="rounded-xl bg-white p-3 shadow-lg hover:shadow-xl  ">
                    <a href="place_order.php?id=<?php echo urlencode($flight['id']); ?>&category=flight" class="block">
                      <div class="relative flex items-end overflow-hidden rounded-xl">
                        <img src="<?php echo 'admin/img/' . htmlspecialchars($flight['img']); ?>"
                          alt="<?php echo htmlspecialchars($flight['title']); ?>" class="w-full h-48 object-cover" />
                        <div class="absolute hidden bottom-3 left-3 items-center rounded-lg bg-white p-2 shadow-md">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                              d="M12.293 7.293a1 1 0 011.414 0L17 10.586V9a1 1 0 012 0v4a1 1 0 01-1 1h-4a1 1 0 110-2h1.586l-3.293-3.293a1 1 0 010-1.414zM10 2a1 1 0 011 1v4H9V3a1 1 0 011-1zM8 16a1 1 0 010 2H6a1 1 0 010-2h2z" />
                          </svg>
                          <span class="text-slate-400 ml-1 text-sm">Flight</span>
                        </div>
                      </div>
                    </a>
                    <div class="mt-1 p-2">
                      <a href="place_order.php?id=<?php echo urlencode($flight['id']); ?>&category=flight" class="block">
                        <h2 class="text-slate-700 font-semibold"><?php echo htmlspecialchars($flight['title']); ?></h2>
                        <p class="text-slate-400 mt-1 text-sm">
                          From: <?php echo htmlspecialchars($flight['countryFrom']); ?>
                          <br>To: <?php echo htmlspecialchars($flight['destination']); ?>
                        </p>
                      </a>
                      <div class="mt-3 flex items-end justify-between ">
                        <p>
                          <span
                            class="text-sm md:text-lg font-bold text-blue-500">৳<?php echo number_format($flight['price'], 2); ?></span>
                        </p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                          <button title="Add or remove as favorite" href="#"
                            data-product-id="<?= urlencode($flight['id']); ?>" data-category="flight"
                            data-user-id="<?= urlencode($_SESSION['user_id']); ?>"
                            class="fav-btn group inline-flex rounded-xl <?= $flight['favorite_id'] ? 'bg-red-200' : 'bg-red-100'; ?> p-2 hover:bg-red-200">
                            <svg xmlns="http://www.w3.org/2000/svg"
                              class="group-hover:text-red-600 <?= $flight['favorite_id'] ? 'text-red-600' : 'text-red-400'; ?> h-3 w-3 md:h-4 md:w-4"
                              viewBox="0 0 24 24" fill="currentColor">
                              <path
                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                          </button>
                        <?php endif; ?>

                      </div>
                    </div>
                  </article>
                <?php endforeach; ?>
              </div>
            </section>
          </div>

        </div>

      </section>

      <!-- why choice us section -->
      <section class="bg-gray-50  py-14 pb-20">
        <div class="max-w-7xl mx-auto px-6 sm:px-6 lg:px-8">
          <h2 class="mb-14 text-xl md:text-2xl lg:text-3xl text-center font-bold text-neutral-900">Why Choose Us!
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="flex flex-col items-center text-center p-6 bg-white shadow-md rounded-md">
              <i class="fas fa-users text-blue-600 text-4xl mb-4"></i>
              <h3 class="text-xl font-semibold mb-2">Experienced Team</h3>
              <p class="text-gray-600">Our team of travel experts ensures a hassle-free experience from booking to
                boarding.</p>
            </div>
            <div class="flex flex-col items-center text-center p-6 bg-white shadow-md rounded-md">
              <i class="fas fa-tag text-green-600 text-4xl mb-4"></i>
              <h3 class="text-xl font-semibold mb-2">Best Price Guarantee</h3>
              <p class="text-gray-600">We offer competitive prices on all travel and visa services, ensuring you get the
                best deals.</p>
            </div>
            <div class="flex flex-col items-center text-center p-6 bg-white shadow-md rounded-md">
              <i class="fas fa-headset text-purple-600 text-4xl mb-4"></i>
              <h3 class="text-xl font-semibold mb-2">24/7 Support</h3>
              <p class="text-gray-600">Our support team is available around the clock to assist you with any inquiries.
              </p>
            </div>
            <div class="flex flex-col items-center text-center p-6 bg-white shadow-md rounded-md">
              <i class="fas fa-plane text-yellow-600 text-4xl mb-4"></i>
              <h3 class="text-xl font-semibold mb-2">Tailored Packages</h3>
              <p class="text-gray-600">Customized travel and visa solutions to meet your specific needs and preferences.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- faq section -->
      <section id="faq" class="px-4 bg-gray-900">
        <section class="py-10 bg-gray-900 sm:py-16 lg:py-24">
          <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
              <h2 class="text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">Questions & Answers</h2>
              <p class="max-w-xl mx-auto mt-4 text-base leading-relaxed text-gray-300">Explore the common questions and
                answers.</p>
            </div>

            <div class="grid grid-cols-1 mt-12 md:mt-20 md:grid-cols-2 gap-y-16 gap-x-20">
              <div class="flex items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-gray-700 rounded-full">
                  <span class="text-lg font-semibold text-white">?</span>
                </div>
                <div class="ml-4">
                  <p class="text-xl font-semibold text-white">Can I change my booking after confirmation?</p>
                  <p class="mt-4 text-base text-gray-400">Yes, you can change your booking after confirmation, subject
                    to availability and applicable terms and conditions. Changes may incur additional charges depending
                    on the airline or service provider. Please contact our support team or log in to your account on
                    Pegasus Travelers to modify your booking details.</p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-gray-700 rounded-full">
                  <span class="text-lg font-semibold text-white">?</span>
                </div>
                <div class="ml-4">
                  <p class="text-xl font-semibold text-white">How can I make payment?</p>
                  <p class="mt-4 text-base text-gray-400">We accept multiple payment methods including bKash, Nagad,
                    Rocket, credit/debit cards, and bank transfers. You can select your preferred method during
                    checkout. Follow the steps provided to complete your payment securely through our trusted gateways.
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-gray-700 rounded-full">
                  <span class="text-lg font-semibold text-white">?</span>
                </div>
                <div class="ml-4">
                  <p class="text-xl font-semibold text-white">Do you provide discounts?</p>
                  <p class="mt-4 text-base text-gray-400">Yes, Pegasus Travelers offers various discounts on flights,
                    travel packages, and visas. Look out for special promotions on our website, or follow us on social
                    media to get the latest updates on exclusive offers tailored just for you.</p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-gray-700 rounded-full">
                  <span class="text-lg font-semibold text-white">?</span>
                </div>
                <div class="ml-4">
                  <p class="text-xl font-semibold text-white">How do you provide support?</p>
                  <p class="mt-4 text-base text-gray-400">Our support team is available to assist you 24/7 through live
                    chat, phone, and email. Whether you have questions about bookings, payments, or any other service,
                    our dedicated support team in Bangladesh is here to help you every step of the way.</p>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-center mt-12 md:mt-20">
              <div class="px-8 py-4 text-center bg-gray-800 rounded-full">
                <p class="text-gray-50">Didn’t find the answer you are looking for? <a href="contact.php" title=""
                    class="text-yellow-300 transition-all duration-200 hover:text-yellow-400 focus:text-yellow-400 hover:underline">Contact
                    our support</a></p>
              </div>
            </div>
          </div>
        </section>

      </section>

      <!-- newsletter section -->
      <section class="newsletter ">
        <div class="relative isolate overflow-hidden text-gray-900 py-16 sm:py-24 lg:py-32">
          <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-2">
              <div class="max-w-xl lg:max-w-lg">
                <h2 class="text-3xl font-bold tracking-tight  sm:text-4xl">Subscribe to our newsletter.</h2>
                <p class="mt-4 text-lg leading-8 ">Get the latest travel deals, visa updates, and flight
                  offers delivered to your inbox. Stay informed and plan your next adventure with ease.</p>
                <div class="mt-6 flex max-w-md gap-x-4">
                  <label for="email-address" class="sr-only">Email address</label>
                  <input id="email-address" name="email" type="email" autocomplete="email" required
                    class="min-w-0 flex-auto rounded-md border-0 bg-white/5 px-3.5 py-2  shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6"
                    placeholder="Enter your email">
                  <button type="submit"
                    class="flex-none rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Subscribe</button>
                </div>

              </div>
              <dl class="grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:pt-2">
                <div class="flex flex-col items-start">
                  <div class="rounded-md bg-white/5 p-2 ring-1 ring-slate-200">
                    <svg class="h-6 w-6 " fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                      aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                    </svg>
                  </div>
                  <dt class="mt-4 font-semibold ">Exclusive Offers</dt>
                  <dd class="mt-2 leading-7 text-gray-900">Receive exclusive travel deals, discounts on visas, and early
                    bird flight offers that you won’t find anywhere else.</dd>
                </div>
                <div class="flex flex-col items-start">
                  <div class="rounded-md bg-white/5 p-2 ring-1 ring-slate-300">
                    <svg class="h-6 w-6 " fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                      aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.05 4.575a1.575 1.575 0 10-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 013.15 0v1.5m-3.15 0l.075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 013.15 0V15M6.9 7.575a1.575 1.575 0 10-3.15 0v8.175a6.75 6.75 0 006.75 6.75h2.018a5.25 5.25 0 003.712-1.538l1.732-1.732a5.25 5.25 0 001.538-3.712l.003-2.024a.668.668 0 01.198-.471 1.575 1.575 0 10-2.228-2.228 3.818 3.818 0 00-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0116.35 15m.002 0h-.002" />
                    </svg>
                  </div>
                  <dt class="mt-4 font-semibold ">No spam</dt>
                  <dd class="mt-2 leading-7 text-gray-900">We respect your privacy. You’ll only receive relevant updates
                    and offers from us, with no unnecessary clutter.</dd>
                </div>
              </dl>
            </div>
          </div>
          <div class="absolute left-1/2 top-0 -z-10 -translate-x-1/2 blur-3xl xl:-top-6" aria-hidden="true">
            <div class="aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30"
              style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
            </div>
          </div>
        </div>

      </section>

    </main>

    <?php include 'client/components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function () {
        $('.tab-btn').click(function () {
          // Remove active class from all tabs
          $('.tab-btn').removeClass('click');

          // Add active class to the clicked tab
          $(this).addClass('click');
        });
      });

      function showTab(tabId) {
        // Hide all tab contents
        $('.tab-content').removeClass('active');

        // Show the selected tab content
        $('#' + tabId).addClass('active');
      }
    </script>

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