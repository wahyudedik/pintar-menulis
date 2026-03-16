@props(['type'])

<div x-data="bannerPopup('{{ $type }}')" 
     x-show="showBanner" 
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
     style="display: none;">
    
    <!-- Modal -->
    <div @click.away="closeBanner()" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white" x-text="bannerData.title"></h3>
            <button @click="closeBanner()" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div class="prose prose-blue max-w-none" x-html="bannerData.content"></div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="dontShowAgain" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm text-gray-700">Don't show this again</span>
            </label>
            <button @click="closeBanner()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Got it!
            </button>
        </div>
    </div>
</div>

<script>
function bannerPopup(type) {
    return {
        showBanner: false,
        bannerData: {
            title: '',
            content: ''
        },
        dontShowAgain: false,
        storageKey: `banner_closed_${type}`,

        init() {
            // Check if banner was closed within the last 24 hours
            const stored = localStorage.getItem(this.storageKey);
            if (stored) {
                const { permanent, timestamp } = JSON.parse(stored);
                if (permanent) return;
                if (timestamp && (Date.now() - timestamp) < 24 * 60 * 60 * 1000) return;
                // 24 hours passed — clear and show again
                localStorage.removeItem(this.storageKey);
            }

            // Fetch banner data
            this.fetchBanner(type);
        },

        async fetchBanner(type) {
            try {
                const response = await fetch(`/api/banner/${type}`);
                if (!response.ok) return; // No active banner — silent fail
                const data = await response.json();

                if (data.success && data.banner) {
                    this.bannerData = data.banner;
                    // Show banner after a short delay
                    setTimeout(() => {
                        this.showBanner = true;
                    }, 500);
                }
            } catch (error) {
                // No banner to display - silent fail
            }
        },

        closeBanner() {
            if (this.dontShowAgain) {
                localStorage.setItem(this.storageKey, JSON.stringify({ permanent: true }));
            } else {
                // Hide for 24 hours
                localStorage.setItem(this.storageKey, JSON.stringify({ permanent: false, timestamp: Date.now() }));
            }
            this.showBanner = false;
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

.prose {
    color: #374151;
    line-height: 1.75;
}

.prose strong {
    font-weight: 600;
    color: #111827;
}

.prose a {
    color: #2563eb;
    text-decoration: underline;
}

.prose a:hover {
    color: #1d4ed8;
}

.prose p {
    margin-bottom: 1rem;
}

.prose ul, .prose ol {
    margin-left: 1.5rem;
    margin-bottom: 1rem;
}

.prose li {
    margin-bottom: 0.5rem;
}
</style>
