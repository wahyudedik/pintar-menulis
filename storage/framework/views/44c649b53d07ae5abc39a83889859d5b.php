

<?php $__env->startSection('title', 'Caption Result'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="<?php echo e(route('image-caption.index')); ?>" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Gallery
        </a>
        
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Caption Result ✨</h1>
            <form action="<?php echo e(route('image-caption.destroy', $imageCaption)); ?>" method="POST" onsubmit="return confirm('Yakin hapus caption ini?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Image & Info -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <img src="<?php echo e(Storage::url($imageCaption->image_path)); ?>" alt="Product" class="w-full">
            </div>

            <!-- Detected Objects -->
            <?php if($imageCaption->detected_objects): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">🔍 Objek Terdeteksi</h3>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $imageCaption->detected_objects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $object): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-700">
                        <?php echo e($object); ?>

                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Dominant Colors -->
            <?php if($imageCaption->dominant_colors): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">🎨 Warna Dominan</h3>
                <div class="flex gap-2">
                    <?php $__currentLoopData = $imageCaption->dominant_colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg border border-gray-300" style="background-color: <?php echo e($color); ?>"></div>
                        <span class="text-xs text-gray-600"><?php echo e($color); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Editing Tips -->
            <?php if($imageCaption->editing_tips): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">💡 Tips Editing</h3>
                <ul class="space-y-2">
                    <?php $__currentLoopData = $imageCaption->editing_tips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex gap-2 text-sm text-gray-700">
                        <span class="text-blue-600">•</span>
                        <span><?php echo e($tip); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <!-- Captions -->
        <div class="space-y-4">
            <!-- Single Post Caption -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">📝 Caption Single Post</h3>
                    <button onclick="copySingleCaption()" class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        Copy
                    </button>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap" id="singleCaption"><?php echo e($imageCaption->caption_single); ?></p>
                </div>
            </div>

            <!-- Carousel Captions -->
            <?php if($imageCaption->caption_carousel): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">📱 Caption Carousel (3 Slide)</h3>
                    <button onclick="copyCarouselCaption()" class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        Copy All
                    </button>
                </div>
                <div class="space-y-3">
                    <?php $__currentLoopData = $imageCaption->caption_carousel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                <?php echo e($index + 1); ?>

                            </span>
                            <span class="text-xs font-medium text-gray-600">Slide <?php echo e($index + 1); ?></span>
                        </div>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap carousel-slide"><?php echo e($slide); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Info Box -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <div class="text-sm">
                        <p class="font-medium text-gray-900 mb-1">💡 Tips Penggunaan</p>
                        <ul class="text-gray-700 space-y-1 text-xs">
                            <li>• Edit caption sesuai brand voice kamu</li>
                            <li>• Tambahkan CTA yang spesifik</li>
                            <li>• Gunakan carousel untuk storytelling</li>
                            <li>• Terapkan tips editing untuk hasil maksimal</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copySingleCaption() {
    const text = document.getElementById('singleCaption').textContent;
    copyToClipboard(text, 'Caption single post berhasil dicopy! ✓');
}

function copyCarouselCaption() {
    const slides = document.querySelectorAll('.carousel-slide');
    let text = '';
    slides.forEach((slide, index) => {
        text += `Slide ${index + 1}:\n${slide.textContent}\n\n`;
    });
    copyToClipboard(text.trim(), 'Caption carousel berhasil dicopy! ✓');
}

function copyToClipboard(text, message) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        alert(message);
    } catch (err) {
        alert('Gagal copy caption');
    }
    
    document.body.removeChild(textarea);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\image-caption\show.blade.php ENDPATH**/ ?>