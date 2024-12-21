<?php include 'db/config.php'; ?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Pegasus Travelers</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="font-inter">
    <?php include "client/components/header.php" ?>

  <!-- Hero Section -->
<section class="relative h-dvh flex flex-col justify-center bg-[url('img/hero.jpg')] bg-cover bg-bottom z-5">
  <div class="mx-auto max-w-7xl xl:-mt-[50px] px-4 sm:px-6 lg:px-8 z-20 relative text-center">
    <h1 class="max-w-2xl mx-auto text-center font-manrope font-bold text-4xl text-gray-900 mb-5 md:text-5xl md:leading-normal">
      Discover the World with
      <span class="text-indigo-600">Pegasus Travelers</span>
    </h1>
    <p class="max-w-sm mx-auto text-center leading-7 text-gray-600 mb-9">
      Your gateway to the best travel deals, visa assistance, and personalized tour packages for a hassle-free
      journey.
    </p>
  </div>
</section>

    

    <!-- Meet Our Team Section -->
    <section class="py-14 lg:py-24">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-14">
          <h2 class="font-manrope text-2xl md:text-3xl lg:text-4xl text-center font-bold text-gray-900 mb-6">
            Meet Our Team
          </h2>
          <!-- <p class=" md:text-lg text-gray-500 text-center">
            Our team of experts is dedicated to simplifying your travel and visa arrangements, ensuring a smooth
            and enjoyable experience every step of the way.
          </p> -->
        </div>
        <?php
        $stmt = $pdo->query("SELECT * FROM team ORDER BY id");
        $teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'company/views/team.php';
        ?>
      </div>
    </section>
   
<!-- Clients' Reviews Section -->
    <section class="py-14 lg:py-24 bg-blue-50 ">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
        <div class="mb-16 rounded-full">
          <h2 class="text-2xl md:text-3xl lg:text-4xl font-manrope font-bold  text-center">
            What Our Happy Clients Say!
          </h2>
        </div>
        <?php
        $stmt = $pdo->query("SELECT * FROM clients");
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'company/views/clients.php';
        ?>
      </div>
    </section>




    <!-- Ready to Explore Section -->
    <section class="bg-[url('img/client-review.jpg')] bg-cover bg-top">
      <div class="mx-auto">
        <div class=" p-8 xl:p-11 text-center text-neutral-900">
          <h2 class="text-xl md:text-2xl  font-bold mb-6">Ready to Explore?</h2>
          <!-- <p class="text-lg mb-6 text-neutral-900">
            Contact us today to start planning your next adventure with Pegasus Travelers. We’re here to make your
            journey exceptional.
          </p> -->
          <a href="contact.php"
            class="py-2 px-5 text-sm bg-blue-500 shadow-md rounded-lg text-white font-semibold hover:bg-blue-700">Get in
            Touch</a>
        </div>
      </div>
    </section>

    <?php include 'client/components/footer.php'; ?>
  </body>

</html>