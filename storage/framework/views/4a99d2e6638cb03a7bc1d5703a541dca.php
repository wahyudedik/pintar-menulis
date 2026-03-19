

<?php $__env->startSection('title', 'Create New Content'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Create New Content</h1>
            <p class="text-sm text-gray-500">Project: <?php echo e($project->business_name); ?></p>
        </div>
        <a href="<?php echo e(route('projects.show', $project)); ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Project
        </a>
    </div>

    <!-- Content Creation Form -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">New Content</h3>
        </div>
        <div class="p-6">
            <form action="<?php echo e(route('projects.content.store', $project)); ?>" method="POST" id="contentForm">
                <?php echo csrf_field(); ?>
                
                <!-- Content Type & Platform -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Content Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="type" id="type" required>
                            <option value="">Pilih Jenis Konten</option>
                            <option value="caption" <?php echo e(old('type') == 'caption' ? 'selected' : ''); ?>>Caption Media Sosial</option>
                            <option value="article" <?php echo e(old('type') == 'article' ? 'selected' : ''); ?>>Artikel/Blog Post</option>
                            <option value="ad_copy" <?php echo e(old('type') == 'ad_copy' ? 'selected' : ''); ?>>Iklan</option>
                            <option value="email" <?php echo e(old('type') == 'email' ? 'selected' : ''); ?>>Email Marketing</option>
                            <option value="product_desc" <?php echo e(old('type') == 'product_desc' ? 'selected' : ''); ?>>Deskripsi Produk</option>
                        </select>
                        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="platform" class="block text-sm font-medium text-gray-700 mb-2">Target Platform (Optional)</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['platform'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="platform" id="platform">
                            <option value="">Pilih Platform (Opsional)</option>
                            <option value="instagram" <?php echo e(old('platform') == 'instagram' ? 'selected' : ''); ?>>Instagram</option>
                            <option value="facebook" <?php echo e(old('platform') == 'facebook' ? 'selected' : ''); ?>>Facebook</option>
                            <option value="tiktok" <?php echo e(old('platform') == 'tiktok' ? 'selected' : ''); ?>>TikTok</option>
                            <option value="twitter" <?php echo e(old('platform') == 'twitter' ? 'selected' : ''); ?>>Twitter/X</option>
                            <option value="whatsapp" <?php echo e(old('platform') == 'whatsapp' ? 'selected' : ''); ?>>WhatsApp Status</option>
                            <option value="shopee" <?php echo e(old('platform') == 'shopee' ? 'selected' : ''); ?>>Shopee</option>
                            <option value="tokopedia" <?php echo e(old('platform') == 'tokopedia' ? 'selected' : ''); ?>>Tokopedia</option>
                            <option value="website" <?php echo e(old('platform') == 'website' ? 'selected' : ''); ?>>Website/Blog</option>
                        </select>
                        <?php $__errorArgs = ['platform'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Konten</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           name="title" id="title" value="<?php echo e(old('title')); ?>" 
                           placeholder="Masukkan judul deskriptif untuk konten ini" required>
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                              name="content" id="content" rows="8" 
                              placeholder="Tulis konten Anda di sini..." required><?php echo e(old('content')); ?></textarea>
                    <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="text-sm text-gray-500 mt-1">
                        <span id="charCount">0</span> characters
                    </p>
                </div>

                <!-- Tags -->
                <div class="mb-6">
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['tags'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           name="tags" id="tags" value="<?php echo e(old('tags')); ?>" 
                           placeholder="Masukkan tag dipisahkan koma (contoh: promo, sale, produk baru)">
                    <?php $__errorArgs = ['tags'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="text-sm text-gray-500 mt-1">Pisahkan beberapa tag dengan koma</p>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                              name="notes" id="notes" rows="3" 
                              placeholder="Tambahkan catatan atau instruksi untuk reviewer..."><?php echo e(old('notes')); ?></textarea>
                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <div class="flex space-x-3">
                        <button type="submit" name="action" value="draft" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">
                            Simpan sebagai Draft
                        </button>
                        <?php if($project->canUserEdit(auth()->user())): ?>
                        <button type="submit" name="action" value="review" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition text-sm">
                            Kirim untuk Review
                        </button>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo e(route('projects.show', $project)); ?>" class="text-gray-600 hover:text-gray-800 text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Assistant Panel -->
    <div class="bg-white rounded-lg border border-gray-200 mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                Asisten AI Penulis
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="md:col-span-3">
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="aiPrompt" 
                           placeholder="Deskripsikan apa yang ingin Anda tulis... (contoh: 'Buat postingan promosi untuk peluncuran produk baru')">
                </div>
                <div class="flex space-x-2">
                    <select class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="aiTone">
                        <option value="professional">Profesional</option>
                        <option value="casual">Santai</option>
                        <option value="friendly">Ramah</option>
                        <option value="exciting">Menarik</option>
                        <option value="persuasive">Persuasif</option>
                    </select>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm" type="button" id="generateAI">
                        Generate
                    </button>
                </div>
            </div>
            
            <!-- Local Language Option -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">🗣️ Bahasa Daerah (Opsional)</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="aiLocalLanguage">
                    <option value="">Tidak pakai bahasa daerah</option>
                    <option value="jawa">🗣️ Bahasa Jawa (Halus/Ngoko)</option>
                    <option value="sunda">🗣️ Bahasa Sunda</option>
                    <option value="betawi">🗣️ Bahasa Betawi</option>
                    <option value="minang">🗣️ Bahasa Minang</option>
                    <option value="bali">🗣️ Bahasa Bali</option>
                    <option value="batak">🗣️ Bahasa Batak</option>
                    <option value="madura">🗣️ Bahasa Madura</option>
                    <option value="bugis">🗣️ Bahasa Bugis</option>
                    <option value="banjar">🗣️ Bahasa Banjar</option>
                    <option value="mixed">🌍 Mix Bahasa (Indo + Daerah)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Tambahkan sentuhan lokal untuk target market spesifik</p>
            </div>
            <div id="aiResult" class="mt-4 hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-medium text-blue-900 mb-2">Konten yang Dihasilkan AI:</p>
                            <div id="aiContent" class="text-gray-700"></div>
                        </div>
                        <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition ml-4" id="useAI">
                            Gunakan Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('charCount');
    
    contentTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        
        // Platform-specific character limits
        const platform = document.getElementById('platform').value;
        const limits = {
            'instagram': 2200,
            'twitter': 280,
            'tiktok': 150,
            'whatsapp': 100
        };
        
        if (platform && limits[platform]) {
            const limit = limits[platform];
            const remaining = limit - count;
            const color = remaining < 0 ? 'text-red-500' : remaining < 50 ? 'text-yellow-500' : 'text-green-500';
            charCount.innerHTML = `${count}/${limit} <span class="${color}">(${remaining} remaining)</span>`;
        }
    });
    
    // Platform change handler
    document.getElementById('platform').addEventListener('change', function() {
        contentTextarea.dispatchEvent(new Event('input'));
    });
    
    // AI Content Generation
    document.getElementById('generateAI').addEventListener('click', function() {
        const prompt = document.getElementById('aiPrompt').value.trim();
        const tone = document.getElementById('aiTone').value;
        const type = document.getElementById('type').value;
        const platform = document.getElementById('platform').value;
        const localLanguage = document.getElementById('aiLocalLanguage').value;
        
        if (!prompt) {
            alert('Silakan masukkan deskripsi untuk konten yang ingin Anda buat.');
            return;
        }
        
        if (!type) {
            alert('Silakan pilih jenis konten terlebih dahulu.');
            return;
        }
        
        const btn = this;
        const originalText = btn.textContent;
        btn.textContent = 'Menghasilkan...';
        btn.disabled = true;
        
        fetch('<?php echo e(route("api.ai.generate-content")); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                prompt: prompt,
                tone: tone,
                type: type,
                platform: platform,
                local_language: localLanguage,
                business_context: {
                    name: '<?php echo e(addslashes($project->business_name)); ?>',
                    type: '<?php echo e(addslashes($project->business_type)); ?>',
                    description: '<?php echo e(addslashes($project->business_description)); ?>',
                    audience: '<?php echo e(addslashes($project->target_audience ?? '')); ?>',
                    brand_tone: '<?php echo e(addslashes($project->brand_tone ?? '')); ?>'
                }
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('aiContent').textContent = data.content;
                document.getElementById('aiResult').classList.remove('hidden');
            } else {
                alert('Gagal menghasilkan konten: ' + (data.message || 'Error tidak diketahui'));
            }
        })
        .catch(error => {
            console.error('AI Generation Error:', error);
            alert('Gagal menghasilkan konten. Silakan coba lagi.');
        })
        .finally(() => {
            btn.textContent = originalText;
            btn.disabled = false;
        });
    });
    
    // Use AI generated content
    document.getElementById('useAI').addEventListener('click', function() {
        const aiContent = document.getElementById('aiContent').textContent;
        document.getElementById('content').value = aiContent;
        document.getElementById('content').dispatchEvent(new Event('input'));
        document.getElementById('aiResult').classList.add('hidden');
        document.getElementById('aiPrompt').value = '';
        
        // Scroll to content textarea
        document.getElementById('content').scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    
    // Form validation
    document.getElementById('contentForm').addEventListener('submit', function(e) {
        const content = document.getElementById('content').value.trim();
        const title = document.getElementById('title').value.trim();
        
        if (!content || !title) {
            e.preventDefault();
            alert('Silakan isi judul dan konten.');
            return false;
        }
        
        // Show loading state — disable AFTER a tick so button value is submitted
        const clickedBtn = document.activeElement;
        setTimeout(() => {
            const submitBtns = this.querySelectorAll('button[type="submit"]');
            submitBtns.forEach(btn => {
                btn.disabled = true;
                btn.textContent = 'Menyimpan...';
            });
        }, 50);
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/projects/content/create.blade.php ENDPATH**/ ?>