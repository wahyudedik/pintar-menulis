{{-- Onboarding Wizard Modal --}}
<div x-data="onboardingWizard()" x-show="show" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.away="">

        {{-- Progress bar --}}
        <div class="h-1.5 bg-gray-100">
            <div class="h-1.5 bg-blue-600 transition-all duration-300 rounded-r-full"
                 :style="'width:' + ((step / 4) * 100) + '%'"></div>
        </div>

        <div class="p-6">

            {{-- Step 1: Welcome --}}
            <div x-show="step === 1" x-transition>
                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">👋</div>
                    <h2 class="text-xl font-bold text-gray-900">Selamat datang di Pintar Menulis!</h2>
                    <p class="text-sm text-gray-500 mt-2">Bantu kami kenali bisnis kamu supaya AI bisa generate konten yang lebih pas.</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama bisnis / brand kamu</label>
                    <input type="text" x-model="form.business_name" placeholder="Contoh: Toko Kue Mama, Studio Foto Bali"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button @click="step = 2" :disabled="!form.business_name"
                        class="w-full py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                    Lanjut →
                </button>
            </div>

            {{-- Step 2: Business Type --}}
            <div x-show="step === 2" x-transition>
                <div class="text-center mb-5">
                    <h2 class="text-lg font-bold text-gray-900">Bisnis kamu di bidang apa?</h2>
                    <p class="text-xs text-gray-500 mt-1">Pilih yang paling mendekati</p>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-5">
                    <template x-for="opt in businessTypes" :key="opt.value">
                        <button @click="form.business_type = opt.value"
                                class="p-3 border-2 rounded-xl text-left transition text-sm"
                                :class="form.business_type === opt.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                            <span x-text="opt.icon" class="text-lg"></span>
                            <p class="font-medium text-gray-900 mt-1" x-text="opt.label"></p>
                        </button>
                    </template>
                </div>
                <div class="flex gap-2">
                    <button @click="step = 1" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">← Kembali</button>
                    <button @click="step = 3" :disabled="!form.business_type"
                            class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Lanjut →
                    </button>
                </div>
            </div>

            {{-- Step 3: Platform --}}
            <div x-show="step === 3" x-transition>
                <div class="text-center mb-5">
                    <h2 class="text-lg font-bold text-gray-900">Mau bikin konten untuk platform apa?</h2>
                    <p class="text-xs text-gray-500 mt-1">Pilih platform utama kamu</p>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-5">
                    <template x-for="p in platforms" :key="p.value">
                        <button @click="form.primary_platform = p.value"
                                class="p-3 border-2 rounded-xl text-left transition text-sm"
                                :class="form.primary_platform === p.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                            <span x-text="p.icon" class="text-lg"></span>
                            <p class="font-medium text-gray-900 mt-1" x-text="p.label"></p>
                            <p class="text-xs text-gray-400" x-text="p.desc"></p>
                        </button>
                    </template>
                </div>
                <div class="flex gap-2">
                    <button @click="step = 2" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">← Kembali</button>
                    <button @click="step = 4" :disabled="!form.primary_platform"
                            class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Lanjut →
                    </button>
                </div>
            </div>

            {{-- Step 4: Goal --}}
            <div x-show="step === 4" x-transition>
                <div class="text-center mb-5">
                    <h2 class="text-lg font-bold text-gray-900">Tujuan utama bikin konten?</h2>
                </div>
                <div class="grid grid-cols-1 gap-2 mb-5">
                    <template x-for="g in goals" :key="g.value">
                        <button @click="form.content_goal = g.value"
                                class="p-3 border-2 rounded-xl text-left transition text-sm flex items-center gap-3"
                                :class="form.content_goal === g.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                            <span x-text="g.icon" class="text-xl"></span>
                            <div>
                                <p class="font-medium text-gray-900" x-text="g.label"></p>
                                <p class="text-xs text-gray-400" x-text="g.desc"></p>
                            </div>
                        </button>
                    </template>
                </div>
                <div class="flex gap-2">
                    <button @click="step = 3" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">← Kembali</button>
                    <button @click="submit()" :disabled="!form.content_goal || submitting"
                            class="flex-1 py-2.5 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="submitting ? 'Menyimpan...' : '🚀 Mulai Generate Konten!'"></span>
                    </button>
                </div>
            </div>

            {{-- Skip link --}}
            <div class="mt-4 text-center" x-show="step < 4">
                <button @click="skip()" class="text-xs text-gray-400 hover:text-gray-600 transition">Lewati untuk sekarang</button>
            </div>
        </div>
    </div>
</div>

<script>
function onboardingWizard() {
    return {
        show: true,
        step: 1,
        submitting: false,
        form: {
            business_name: '',
            business_type: '',
            primary_platform: '',
            content_goal: '',
        },
        businessTypes: [
            { value: 'kuliner', icon: '🍽️', label: 'Kuliner & F&B' },
            { value: 'fashion', icon: '👗', label: 'Fashion & Beauty' },
            { value: 'jasa', icon: '🔧', label: 'Jasa & Layanan' },
            { value: 'retail', icon: '🛍️', label: 'Retail & Toko' },
            { value: 'digital', icon: '💻', label: 'Produk Digital' },
            { value: 'kesehatan', icon: '💊', label: 'Kesehatan & Wellness' },
            { value: 'edukasi', icon: '📚', label: 'Edukasi & Kursus' },
            { value: 'properti', icon: '🏠', label: 'Properti & Interior' },
            { value: 'otomotif', icon: '🚗', label: 'Otomotif' },
            { value: 'lainnya', icon: '📦', label: 'Lainnya' },
        ],
        platforms: [
            { value: 'instagram', icon: '📱', label: 'Instagram', desc: 'Feed, Story, Reels' },
            { value: 'tiktok', icon: '🎵', label: 'TikTok', desc: 'Video pendek' },
            { value: 'facebook', icon: '👥', label: 'Facebook', desc: 'Post, Ads' },
            { value: 'shopee', icon: '🛒', label: 'Shopee/Tokopedia', desc: 'Deskripsi produk' },
            { value: 'whatsapp', icon: '💬', label: 'WhatsApp', desc: 'Broadcast, Status' },
            { value: 'youtube', icon: '▶️', label: 'YouTube', desc: 'Script, Deskripsi' },
        ],
        goals: [
            { value: 'closing', icon: '💰', label: 'Jualan & Closing', desc: 'Fokus konversi, biar langsung beli' },
            { value: 'awareness', icon: '📢', label: 'Branding & Awareness', desc: 'Biar orang kenal brand kamu' },
            { value: 'engagement', icon: '❤️', label: 'Engagement & Interaksi', desc: 'Banyak like, comment, share' },
            { value: 'viral', icon: '🔥', label: 'Viral & Growth', desc: 'Konten yang bisa viral' },
        ],
        async submit() {
            this.submitting = true;
            try {
                const resp = await fetch('{{ route("onboarding.complete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });
                const data = await resp.json();
                if (data.success) {
                    window.location.href = data.redirect || '{{ route("ai.generator") }}';
                }
            } catch (e) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.submitting = false;
            }
        },
        async skip() {
            await fetch('{{ route("onboarding.skip") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            this.show = false;
        },
    }
}
</script>
