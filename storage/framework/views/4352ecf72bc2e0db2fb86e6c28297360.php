

<?php $__env->startSection('title', 'Template Marketplace'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6" x-data="templateMarketplace()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">📚 Template Marketplace</h1>
                <p class="text-sm text-gray-500 mt-1">Temukan & bagikan template copywriting terbaik untuk bisnis Anda</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('templates.create')); ?>" 
                   class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Template Baru
                </a>
                <button @click="showFilters = !showFilters"
                        class="px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span x-text="showFilters ? 'Sembunyikan Filter' : 'Tampilkan Filter'"></span>
                </button>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-900 mb-1">💡 Tentang Template Marketplace</h4>
                    <p class="text-sm text-blue-800 mb-2">
                        Marketplace ini berisi <strong>500+ template system</strong> dan <strong>template dari community</strong>. Anda bisa:
                    </p>
                    <ul class="text-sm text-blue-800 space-y-1 ml-4 list-disc">
                        <li>Browse & gunakan template gratis atau premium</li>
                        <li>Buat template sendiri dan bagikan ke community</li>
                        <li>Rate & review template yang Anda gunakan</li>
                        <li>Export/import template untuk backup</li>
                        <li>Earn revenue dari template premium Anda</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Templates</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($templates->total()); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">System Templates</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">500+</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Community</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($templates->total()); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">My Templates</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(auth()->user()->templates()->count()); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters (Collapsible) -->
    <div x-show="showFilters" x-cloak x-transition class="mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Filter & Pencarian</h3>
            <form method="GET" action="<?php echo e(route('templates.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Template</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="Cari..." 
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="viral_clickbait" <?php echo e(request('category') == 'viral_clickbait' ? 'selected' : ''); ?>>Viral & Clickbait</option>
                        <option value="event_promo" <?php echo e(request('category') == 'event_promo' ? 'selected' : ''); ?>>Event & Promo</option>
                        <option value="branding_tagline" <?php echo e(request('category') == 'branding_tagline' ? 'selected' : ''); ?>>Branding</option>
                        <option value="monetization" <?php echo e(request('category') == 'monetization' ? 'selected' : ''); ?>>Monetization</option>
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="free" <?php echo e(request('type') == 'free' ? 'selected' : ''); ?>>Free</option>
                        <option value="premium" <?php echo e(request('type') == 'premium' ? 'selected' : ''); ?>>Premium</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                    <select name="sort" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="popular" <?php echo e(request('sort') == 'popular' ? 'selected' : ''); ?>>Populer</option>
                        <option value="rating" <?php echo e(request('sort') == 'rating' ? 'selected' : ''); ?>>Rating</option>
                        <option value="newest" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>Terbaru</option>
                    </select>
                </div>

                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Terapkan Filter
                    </button>
                    <a href="<?php echo e(route('templates.index')); ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">📋 Browse Templates</h2>
            <span class="text-sm text-gray-600"><?php echo e($templates->total()); ?> templates tersedia</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 mb-1 text-sm"><?php echo e($template->title); ?></h3>
                        <div class="flex items-center gap-2 text-xs">
                            <?php if($template->is_premium): ?>
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">💎 Premium</span>
                            <?php else: ?>
                            <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs font-medium">🆓 Free</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button @click="toggleFavorite(<?php echo e($template->id); ?>)" 
                            :class="favorites.includes(<?php echo e($template->id); ?>) ? 'text-red-500' : 'text-gray-300'"
                            class="hover:text-red-500 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                        </svg>
                    </button>
                </div>

                <p class="text-xs text-gray-600 mb-3 line-clamp-2"><?php echo e($template->description); ?></p>

                <div class="flex items-center justify-between mb-3 text-xs">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <span class="font-medium"><?php echo e(number_format($template->rating_average, 1)); ?></span>
                        <span class="text-gray-500">(<?php echo e($template->total_ratings); ?>)</span>
                    </div>
                    <span class="text-gray-500"><?php echo e($template->usage_count); ?>x digunakan</span>
                </div>

                <div class="flex gap-2">
                    <a href="<?php echo e(route('templates.show', $template->id)); ?>" 
                       class="flex-1 px-3 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition text-xs font-medium">
                        Lihat Detail
                    </a>
                    <?php if($template->user_id == auth()->id()): ?>
                    <a href="<?php echo e(route('templates.edit', $template->id)); ?>" 
                       class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-3 text-center py-12 bg-white rounded-lg border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada template ditemukan</h3>
                <p class="text-gray-500 mb-4">Coba ubah filter atau buat template baru</p>
                <a href="<?php echo e(route('templates.create')); ?>" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Buat Template Pertama
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            <?php echo e($templates->links()); ?>

        </div>
    </div>
</div>

<script>
    function templateMarketplace() {
        return {
            favorites: [],
            showFilters: false,

            init() {
                this.loadFavorites();
            },

            loadFavorites() {
                const saved = localStorage.getItem('template_favorites');
                if (saved) {
                    this.favorites = JSON.parse(saved);
                }
            },

            async toggleFavorite(templateId) {
                try {
                    const response = await fetch(`/templates/${templateId}/favorite`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        if (data.is_favorited) {
                            this.favorites.push(templateId);
                        } else {
                            this.favorites = this.favorites.filter(id => id !== templateId);
                        }
                        localStorage.setItem('template_favorites', JSON.stringify(this.favorites));
                    }
                } catch (error) {
                    console.error('Toggle favorite error:', error);
                }
            }
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/template-marketplace/index.blade.php ENDPATH**/ ?>