<div class="grid grid-cols-1 justify-center sm:grid-cols-[repeat(auto-fit,min(400px,90%))] gap-8 px-4">
  <?php foreach ($clients as $client): ?>
    <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300">
      <svg class="w-8 h-8 text-indigo-600 mb-4" fill="currentColor" viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M18 13V7a2 2 0 00-2-2h-3.172a2 2 0 00-1.414.586L9 9H5a1 1 0 00-1 1v3a2 2 0 002 2h3.172a2 2 0 001.414-.586l2.414-2.414A1 1 0 0114 11h4a1 1 0 011 1v1a1 1 0 001 1h1v-1a2 2 0 00-2-2z"
          clip-rule="evenodd"></path>
      </svg>
      <p class="text-gray-700 mb-4 text-sm lg:text-base">"<?php echo htmlspecialchars($client['comments']); ?>"</p>
      <div class="mt-4">
        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($client['name']); ?></p>
        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($client['title']); ?></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>