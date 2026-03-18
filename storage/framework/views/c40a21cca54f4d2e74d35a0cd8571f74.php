<!-- 🤖 AI Assistant Widget (Bottom Right) -->
<div x-data="aiAssistantWidget()" x-init="init()" class="fixed bottom-6 right-6 z-40">
    <!-- Chat Widget Container -->
    <div x-show="isOpen" x-cloak class="bg-white rounded-lg shadow-2xl border border-gray-200 w-96 max-h-96 flex flex-col overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-300">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <span class="font-semibold">Smart Copy Assistant</span>
            </div>
            <button @click="isOpen = false" class="text-white hover:bg-white/20 p-1 rounded transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Chat Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            <!-- Welcome Message -->
            <div x-show="messages.length === 0" class="text-center py-4">
                <div class="text-3xl mb-2">👋</div>
                <p class="text-sm text-gray-700 font-medium">Halo! Ada yang bisa saya bantu?</p>
                <p class="text-xs text-gray-500 mt-1">Tanya soal aplikasi & digital marketing</p>
            </div>

            <!-- Messages -->
            <template x-for="(msg, idx) in messages" :key="idx">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user' 
                        ? 'bg-blue-600 text-white rounded-lg rounded-tr-none px-3 py-2 max-w-xs text-sm' 
                        : 'bg-white border border-gray-200 rounded-lg rounded-tl-none px-3 py-2 max-w-xs text-sm text-gray-800'"
                         x-text="msg.content">
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="isLoading" class="flex justify-start">
                <div class="bg-white border border-gray-200 rounded-lg rounded-tl-none px-3 py-2">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suggested Questions (show when no messages) -->
        <div x-show="messages.length === 0 && !isLoading" x-cloak class="px-4 py-3 border-t border-gray-200 bg-white">
            <p class="text-xs font-semibold text-gray-600 mb-2">Pertanyaan Populer:</p>
            <div class="space-y-2">
                <template x-for="(suggestion, idx) in suggestions.slice(0, 2)" :key="idx">
                    <button @click="sendMessage(suggestion)" 
                            class="w-full text-left text-xs p-2 rounded bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 transition truncate">
                        <span x-text="suggestion"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Input Area -->
        <div class="border-t border-gray-200 p-3 bg-white">
            <form @submit.prevent="sendMessage(userMessage)" class="flex space-x-2">
                <input type="text" 
                       x-model="userMessage" 
                       @keydown.enter="sendMessage(userMessage)"
                       placeholder="Tanya sesuatu..." 
                       :disabled="isLoading"
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100">
                <button type="submit" 
                        :disabled="isLoading || !userMessage.trim()"
                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Toggle Button (when closed) -->
    <button x-show="!isOpen" 
            @click="isOpen = true; loadSuggestions()"
            class="w-14 h-14 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl transition flex items-center justify-center hover:scale-110 transform duration-200 animate-pulse">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
    </button>
</div>

<script>
function aiAssistantWidget() {
    return {
        isOpen: false,
        messages: [],
        userMessage: '',
        isLoading: false,
        suggestions: [],
        context: 'general',
        
        init() {
            // Detect context based on current page
            this.detectContext();
        },
        
        detectContext() {
            const path = window.location.pathname;
            if (path.includes('/ai-generator')) {
                this.context = 'client_generator';
            } else if (path.includes('/analytics')) {
                this.context = 'client_analytics';
            } else if (path === '/' || path === '') {
                this.context = 'landing_page';
            } else {
                this.context = 'general';
            }
        },
        
        async loadSuggestions() {
            try {
                const response = await fetch(`/api/assistant/suggestions?context=${this.context}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.suggestions = data.suggestions || [];
                }
            } catch (error) {
                console.error('Load suggestions error:', error);
            }
        },
        
        async sendMessage(message) {
            if (!message.trim()) return;
            
            // Add user message
            this.messages.push({
                role: 'user',
                content: message
            });
            
            this.userMessage = '';
            this.isLoading = true;
            
            try {
                const response = await fetch('/api/assistant/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        message: message,
                        context: this.context
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.messages.push({
                        role: 'assistant',
                        content: data.response || 'Maaf, terjadi kesalahan. Silakan coba lagi.'
                    });
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: 'Maaf, asisten sedang tidak tersedia. Silakan coba lagi.'
                    });
                }
            } catch (error) {
                console.error('Send message error:', error);
                this.messages.push({
                    role: 'assistant',
                    content: 'Maaf, terjadi kesalahan. Silakan coba lagi.'
                });
            } finally {
                this.isLoading = false;
                // Auto scroll to bottom
                setTimeout(() => {
                    const messagesDiv = document.querySelector('[x-data="aiAssistantWidget()"] .overflow-y-auto');
                    if (messagesDiv) {
                        messagesDiv.scrollTop = messagesDiv.scrollHeight;
                    }
                }, 100);
            }
        }
    }
}
</script>

<style>
@keyframes slide-in-from-bottom-4 {
    from {
        opacity: 0;
        transform: translateY(1rem);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation: slide-in-from-bottom-4 0.3s ease-out;
}

.fade-in {
    animation: fade-in 0.3s ease-out;
}

@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\partials\ai-assistant-widget.blade.php ENDPATH**/ ?>