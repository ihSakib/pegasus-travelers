
<style>
  .chatbox {
    display: none;
    position: fixed;
    bottom: 70px;
    right: 1rem;
    max-width: min(90%,300px);
    max-height: 80vh;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
    transition: opacity 0.3s ease, transform 0.3s ease;
    opacity: 0;
    transform: translateY(100%);
  }

  .chatbox.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
  }

  .chatbox-header {
    background-color: #25D366;
    color: #ffffff;
    padding: 1rem;
    border-radius: 0.75rem 0.75rem 0 0;
    display: flex;
    gap: 16px;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .chatbox-content {
    padding: 1rem;
    overflow-y: auto;
    height: calc(100% - 4rem);
    background-color: #f9f9f9;
  }

  .chatbox-content p {
    margin-bottom: 0.75rem;
  }
</style>

<!-- WhatsApp Button -->
<div class="fixed z-[1000] bottom-4 right-4  items-center">
  <button id="whatsapp-button"
    class="bg-green-500 text-white rounded-md text-sm py-2 px-4  shadow-lg hover:bg-green-600 transition duration-300 transform hover:scale-110">
    <i class="fab fa-whatsapp  font-semibold"></i> Chat
  </button>
</div>

<!-- Chatbox -->
<div id="chatbox" class="chatbox z-[1000]">
  <div class="chatbox-header">
    <div class="rounded-full overflow-hidden">
      <img class="w-[50px] h-[50px]" src="https://i.ibb.co/SmR1Pzp/456236550-122100222998480188-130.png">
    </div>
    <span class="text-lg font-semibold">Pegasus Travelers</span>
  </div>
  <div class="chatbox-content text-sm">
    <p><strong class="text-green-600">Agent:</strong> Hello! How can I assist you today?</p>
    <p><strong class="text-green-600">You:</strong> I have a question regarding your services.</p>
    <p><strong class="text-green-600">Agent:</strong> Of course! What would you like to know?</p>
  </div>
  <div class="text-center py-8">
    <a href="https://api.whatsapp.com/send?phone=%2B8801864946718" target='_blank'
      class="py-2 px-6  rounded-md bg-green-600 hover:bg-green-400 text-white"> <i class="fas fa-comment-dots"></i> Chat
      Now</a>
  </div>
</div>

<script>
  document.getElementById('whatsapp-button').addEventListener('click', function () {
    document.getElementById('chatbox').classList.toggle('show');
  });



  // Hide chatbox when clicking outside
  document.addEventListener('click', function (event) {
    const chatbox = document.getElementById('chatbox');
    const button = document.getElementById('whatsapp-button');
    if (!chatbox.contains(event.target) && !button.contains(event.target)) {
      chatbox.classList.remove('show');
    }
  });
</script>