<div class="grid gap-4 md:gap-8 px-2 justify-center grid-cols-2 sm:grid-cols-[repeat(auto-fit,200px)]">
  <?php foreach ($teamMembers as $member): ?>
    <div class="bg-blue-50 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
      <div class="overflow-hidden">
        <img src="company/img/<?php echo htmlspecialchars($member['img']); ?>"
          alt="<?php echo htmlspecialchars($member['name']); ?>"
          class="w-full h-30 object-cover transform hover:scale-105 transition-transform duration-300">
      </div>
      <div class="p-6 text-center">
        <h3 class=" text-lg lg:text-xl font-semibold text-gray-800 mb-1"><?php echo htmlspecialchars($member['name']); ?>
        </h3>
        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($member['position']); ?></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>