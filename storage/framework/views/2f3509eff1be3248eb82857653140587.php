

<?php $__env->startSection('title', 'Tambah Kompetitor'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="<?php echo e(route('competitor-analysis.index')); ?>" class="hover:text-purple-600">Competitor Analysis</a>
            <span>/</span>
            <span class="text-gray-900">Tambah Kompetitor</span>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900">Tambah Kompetitor Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau aktivitas kompetitor untuk mendapatkan insight konten</p>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <?php if(session('error')): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800"><?php echo e(session('error')); ?></p>
            </div>
            <?php endif; ?>

            <form id="competitorForm" action="<?php echo e(route('competitor-analysis.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <!-- Loading Overlay -->
                <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-purple-600 mx-auto mb-4"></div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Menganalisis Kompetitor...</h3>
                        <p id="loadingText" class="text-sm text-gray-600 mb-4">Sedang mengambil data profil...</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="progressBar" class="bg-purple-600 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Proses ini membutuhkan waktu 30-60 detik</p>
                    </div>
                </div>

                <!-- Platform -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Platform <span class="text-red-600">*</span>
                    </label>
                    <select name="platform" id="platform" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Pilih Platform</option>
                        
                        <!-- Social Media Platforms -->
                        <optgroup label="📱 Social Media">
                            <option value="instagram">📷 Instagram</option>
                            <option value="tiktok">🎵 TikTok</option>
                            <option value="facebook">👥 Facebook</option>
                            <option value="youtube">📺 YouTube</option>
                            <option value="x">❌ X (Twitter)</option>
                            <option value="linkedin">💼 LinkedIn</option>
                        </optgroup>
                        
                        <!-- E-commerce Platforms -->
                        <optgroup label="🛒 Toko Online">
                            <option value="shopee">🛍️ Shopee</option>
                            <option value="tokopedia">🟢 Tokopedia</option>
                            <option value="lazada">🔵 Lazada</option>
                            <option value="bukalapak">🔴 Bukalapak</option>
                            <option value="blibli">🟠 Blibli</option>
                            <option value="jdid">🔴 JD.ID</option>
                            <option value="zalora">👗 Zalora</option>
                            <option value="sociolla">💄 Sociolla</option>
                            <option value="orami">👶 Orami</option>
                            <option value="bhinneka">💻 Bhinneka</option>
                        </optgroup>
                        
                        <!-- International E-commerce -->
                        <optgroup label="🌍 International">
                            <option value="amazon">📦 Amazon</option>
                            <option value="alibaba">🟡 Alibaba</option>
                            <option value="ebay">🔵 eBay</option>
                            <option value="etsy">🎨 Etsy</option>
                            <option value="shopify">🛒 Shopify Store</option>
                        </optgroup>
                    </select>
                    <?php $__errorArgs = ['platform'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Username -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Username Kompetitor <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="username" id="username" required
                           placeholder="Contoh: esteh.specialtea"
                           value="<?php echo e(old('username')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p id="usernameHelp" class="text-xs text-gray-500 mt-1">Masukkan username tanpa @ atau dengan @</p>
                    <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Category (Optional) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori Bisnis (Opsional)
                    </label>
                    <input type="text" name="category" id="category"
                           placeholder="Contoh: Minuman, Makanan, Fashion, dll"
                           value="<?php echo e(old('category')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Membantu kategorisasi kompetitor</p>
                    <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg">
                    <h3 class="text-sm font-semibold text-purple-900 mb-2">🤖 100% AI-Powered Analysis - GRATIS & UNLIMITED!</h3>
                    <div class="text-xs text-purple-800 space-y-1 mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-green-600">✓</span>
                            <span>Profil & engagement rate kompetitor</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-green-600">✓</span>
                            <span>Pola posting (waktu, frekuensi)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-green-600">✓</span>
                            <span>Konten dengan performa terbaik</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-green-600">✓</span>
                            <span>Tone & style komunikasi</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-green-600">✓</span>
                            <span>Hashtag strategy mereka</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-green-600">✓</span>
                            <span>Content gap (peluang konten yang bisa dimanfaatkan)</span>
                        </div>
                        <div id="ecommerceFeatures" class="hidden flex items-center space-x-2">
                            <span class="text-green-600">✓</span>
                            <span>Rating toko & response time</span>
                        </div>
                        <div id="ecommerceFeatures2" class="hidden flex items-center space-x-2">
                            <span class="text-green-600">✓</span>
                            <span>Jumlah produk & strategi pricing</span>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center space-x-3">
                    <button type="submit" id="submitBtn"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition font-medium flex items-center justify-center">
                        <span id="submitText">🤖 Mulai Analisis AI</span>
                        <div id="submitSpinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white ml-2"></div>
                    </button>
                    <a href="<?php echo e(route('competitor-analysis.index')); ?>"
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('competitorForm');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const loadingText = document.getElementById('loadingText');
    const progressBar = document.getElementById('progressBar');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');

    // Loading messages with progress
    const loadingSteps = [
        { text: 'Memvalidasi username kompetitor...', progress: 10 },
        { text: 'Menganalisis profil dengan AI...', progress: 25 },
        { text: 'Mengambil data konten terbaru...', progress: 40 },
        { text: 'Menganalisis pola posting...', progress: 55 },
        { text: 'Mengidentifikasi content gaps...', progress: 70 },
        { text: 'Membuat insight strategis...', progress: 85 },
        { text: 'Menyelesaikan analisis...', progress: 95 }
    ];

    let currentStep = 0;

    function updateLoadingProgress() {
        if (currentStep < loadingSteps.length) {
            const step = loadingSteps[currentStep];
            loadingText.textContent = step.text;
            progressBar.style.width = step.progress + '%';
            currentStep++;
            
            // Random delay between 3-8 seconds for realistic feel
            const delay = Math.random() * 5000 + 3000;
            setTimeout(updateLoadingProgress, delay);
        }
    }

    form.addEventListener('submit', function(e) {
        // Validate form first
        const platform = document.getElementById('platform').value;
        const username = document.getElementById('username').value.trim();

        if (!platform) {
            alert('Silakan pilih platform terlebih dahulu');
            e.preventDefault();
            return;
        }

        if (!username) {
            alert('Silakan masukkan username kompetitor');
            e.preventDefault();
            return;
        }

        // Show loading state
        loadingOverlay.classList.remove('hidden');
        submitBtn.disabled = true;
        submitText.textContent = 'Sedang Menganalisis...';
        submitSpinner.classList.remove('hidden');

        // Start progress animation
        currentStep = 0;
        updateLoadingProgress();

        // Set timeout to handle long requests (2 minutes)
        setTimeout(function() {
            if (!loadingOverlay.classList.contains('hidden')) {
                loadingText.textContent = 'Analisis membutuhkan waktu lebih lama dari biasanya...';
                progressBar.style.width = '100%';
            }
        }, 120000);
    });

    // Handle form validation on input
    const usernameInput = document.getElementById('username');
    const platformSelect = document.getElementById('platform');
    const usernameHelp = document.getElementById('usernameHelp');
    
    // Update placeholder and help text based on selected platform
    platformSelect.addEventListener('change', function() {
        const platform = this.value;
        const examples = {
            // Social Media
            'instagram': { placeholder: 'Contoh: esteh.specialtea', help: 'Masukkan username Instagram tanpa @' },
            'tiktok': { placeholder: 'Contoh: @estehspecialtea', help: 'Masukkan username TikTok dengan atau tanpa @' },
            'facebook': { placeholder: 'Contoh: EstehSpecialTea', help: 'Masukkan nama halaman Facebook' },
            'youtube': { placeholder: 'Contoh: EstehSpecialTea', help: 'Masukkan nama channel YouTube' },
            'x': { placeholder: 'Contoh: estehspecialtea', help: 'Masukkan username X (Twitter) tanpa @' },
            'linkedin': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan username LinkedIn' },
            
            // Indonesian E-commerce
            'shopee': { placeholder: 'Contoh: estehspecialtea', help: 'Masukkan nama toko Shopee' },
            'tokopedia': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan nama toko Tokopedia' },
            'lazada': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan nama toko Lazada' },
            'bukalapak': { placeholder: 'Contoh: estehspecialtea', help: 'Masukkan nama toko Bukalapak' },
            'blibli': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan nama merchant Blibli' },
            'jdid': { placeholder: 'Contoh: estehspecialtea', help: 'Masukkan nama toko JD.ID' },
            'zalora': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan nama brand Zalora' },
            'sociolla': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan nama brand Sociolla' },
            'orami': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan nama brand Orami' },
            'bhinneka': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan nama brand Bhinneka' },
            
            // International E-commerce
            'amazon': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan seller ID Amazon' },
            'alibaba': { placeholder: 'Contoh: estehspecialtea', help: 'Masukkan company name Alibaba' },
            'ebay': { placeholder: 'Contoh: estehspecialtea', help: 'Masukkan username seller eBay' },
            'etsy': { placeholder: 'Contoh: EstehSpecialTea', help: 'Masukkan nama shop Etsy' },
            'shopify': { placeholder: 'Contoh: esteh-special-tea', help: 'Masukkan subdomain Shopify (tanpa .myshopify.com)' }
        };
        
        if (examples[platform]) {
            usernameInput.placeholder = examples[platform].placeholder;
            usernameHelp.textContent = examples[platform].help;
        } else {
            usernameInput.placeholder = 'Contoh: username-kompetitor';
            usernameHelp.textContent = 'Masukkan username atau nama toko kompetitor';
        }
        
        // Clear username when platform changes
        usernameInput.value = '';
        usernameInput.classList.remove('border-green-300', 'border-red-300');
        usernameInput.classList.add('border-gray-300');
        
        // Show/hide e-commerce specific features
        const ecommerceFeatures = document.getElementById('ecommerceFeatures');
        const ecommerceFeatures2 = document.getElementById('ecommerceFeatures2');
        const ecommercePlatforms = ['shopee', 'tokopedia', 'lazada', 'bukalapak', 'blibli', 'jdid', 'zalora', 'sociolla', 'orami', 'bhinneka', 'amazon', 'alibaba', 'ebay', 'etsy', 'shopify'];
        
        if (ecommercePlatforms.includes(platform)) {
            ecommerceFeatures.classList.remove('hidden');
            ecommerceFeatures2.classList.remove('hidden');
        } else {
            ecommerceFeatures.classList.add('hidden');
            ecommerceFeatures2.classList.add('hidden');
        }
    });
    
    usernameInput.addEventListener('input', function() {
        let value = this.value.trim();
        
        // Remove @ if user adds it
        if (value.startsWith('@')) {
            value = value.substring(1);
            this.value = value;
        }
        
        // Basic validation feedback
        if (value.length > 0) {
            this.classList.remove('border-red-300');
            this.classList.add('border-green-300');
        } else {
            this.classList.remove('border-green-300');
            this.classList.add('border-gray-300');
        }
    });

    // Auto-suggest category based on username
    usernameInput.addEventListener('blur', function() {
        const username = this.value.toLowerCase();
        const categoryInput = document.getElementById('category');
        
        if (!categoryInput.value && username) {
            // Simple category suggestions based on username patterns
            if (username.includes('food') || username.includes('kuliner') || username.includes('makan') || username.includes('cafe') || username.includes('resto')) {
                categoryInput.value = 'Makanan & Minuman';
            } else if (username.includes('fashion') || username.includes('style') || username.includes('outfit') || username.includes('boutique')) {
                categoryInput.value = 'Fashion';
            } else if (username.includes('beauty') || username.includes('makeup') || username.includes('skincare') || username.includes('salon')) {
                categoryInput.value = 'Kecantikan';
            } else if (username.includes('tech') || username.includes('gadget') || username.includes('digital')) {
                categoryInput.value = 'Teknologi';
            } else if (username.includes('travel') || username.includes('wisata') || username.includes('tour')) {
                categoryInput.value = 'Travel & Wisata';
            } else if (username.includes('fitness') || username.includes('gym') || username.includes('sport')) {
                categoryInput.value = 'Olahraga & Fitness';
            }
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\competitor-analysis\create.blade.php ENDPATH**/ ?>