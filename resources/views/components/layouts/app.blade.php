<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Page Title' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- Preline UI CSS -->
    <link rel="stylesheet" href="https://preline.co/assets/css/main.min.css">
    <!-- Preline UI JS -->
    <script src="https://preline.co/assets/js/preline.js"></script>
</head> 
<body class="bg-slate-200">
    @livewire('partials.navbar')

    {{-- Здесь попадёт ваш компонент --}}
    {{ $slot }}

    @livewire('partials.footer')
    
    <!-- Floating Chat Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <button type="button" class="flex items-center justify-center w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300" id="chat-button">
            <!-- Chat Icon -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
        </button>
    </div>

    <!-- Chat Modal -->
    <div id="chat-modal" class="fixed bottom-6 right-6 z-40 hidden">
        <div class="bg-white rounded-lg shadow-xl w-80 h-96 flex flex-col overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-blue-600 text-white p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div>
                    <h3 class="font-semibold">Поддержка</h3>
                </div>
                <button id="close-chat" class="text-white hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Chat Messages -->
            <div id="chat-messages" class="flex-1 p-4 overflow-y-auto bg-gray-50">
                <div class="mb-4">
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                        <p class="text-sm text-gray-800">Здравствуйте! Как мы можем вам помочь?</p>
                        <span class="text-xs text-gray-500 mt-1 block">Поддержка • сейчас</span>
                    </div>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="p-4 border-t bg-white">
                <div class="flex space-x-2">
                    <input type="text" id="chat-input" placeholder="Напишите сообщение..." 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <button id="send-message" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
    
    <!-- Initialize Preline UI -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.HSStaticMethods.autoInit();
            
            // Chat functionality
            const chatButton = document.getElementById('chat-button');
            const chatModal = document.getElementById('chat-modal');
            const closeChat = document.getElementById('close-chat');
            const chatInput = document.getElementById('chat-input');
            const sendButton = document.getElementById('send-message');
            const chatMessages = document.getElementById('chat-messages');

            // Open chat modal
            chatButton.addEventListener('click', function() {
                chatModal.classList.remove('hidden');
                chatModal.classList.add('animate-fade-in');
                chatInput.focus();
            });

            // Close chat modal
            closeChat.addEventListener('click', function() {
                chatModal.classList.add('hidden');
                chatModal.classList.remove('animate-fade-in');
            });

            // Send message function
            function sendMessage() {
                const message = chatInput.value.trim();
                if (message) {
                    // Add user message
                    addMessage(message, 'user');
                    chatInput.value = '';
                    
                    // Simulate bot response (замените на реальную логику)
                    setTimeout(() => {
                        addMessage('Спасибо за ваше сообщение! Наш специалист свяжется с вами в ближайшее время.', 'bot');
                    }, 1000);
                }
            }

            // Add message to chat
            function addMessage(text, sender) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'mb-4';
                
                const isUser = sender === 'user';
                const bgColor = isUser ? 'bg-blue-600 text-white' : 'bg-white';
                const alignment = isUser ? 'ml-auto text-right' : '';
                const senderName = isUser ? 'Вы' : 'Поддержка';
                
                messageDiv.innerHTML = `
                    <div class="${bgColor} rounded-lg p-3 shadow-sm max-w-xs ${alignment}">
                        <p class="text-sm">${text}</p>
                        <span class="text-xs ${isUser ? 'text-blue-200' : 'text-gray-500'} mt-1 block">${senderName} • сейчас</span>
                    </div>
                `;
                
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Send message on button click
            sendButton.addEventListener('click', sendMessage);

            // Send message on Enter key
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });

            // Close chat when clicking outside
            document.addEventListener('click', function(e) {
                if (!chatModal.contains(e.target) && !chatButton.contains(e.target) && !chatModal.classList.contains('hidden')) {
                    chatModal.classList.add('hidden');
                    chatModal.classList.remove('animate-fade-in');
                }
            });
        });
    </script>
    
    <!-- Custom CSS for animations -->
    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #chat-messages {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }
        
        #chat-messages::-webkit-scrollbar {
            width: 4px;
        }
        
        #chat-messages::-webkit-scrollbar-track {
            background: #f7fafc;
        }
        
        #chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 20px;
        }
    </style>
</body>
</html>
