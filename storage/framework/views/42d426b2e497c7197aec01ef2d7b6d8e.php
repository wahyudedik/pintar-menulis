

<?php $__env->startSection('content'); ?>
<div class="p-6" x-data="{ correctedOutput: '<?php echo e(addslashes($order->result)); ?>' }">
    <!-- Header -->
    <div class="mb-6">
        <a href="<?php echo e(route('guru.training')); ?>" class="text-purple-600 hover:text-purple-700 text-sm">
            ← Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Review Training Data</h1>
        <p class="text-sm text-gray-600 mt-1">Evaluasi kualitas output AI dan berikan feedback</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left: Order Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 p-4 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-600">Order ID</p>
                        <p class="text-sm font-semibold">#<?php echo e($order->id); ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-600">Kategori</p>
                        <p class="text-sm font-semibold"><?php echo e($order->category); ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-600">Client</p>
                        <p class="text-sm font-semibold"><?php echo e($order->user->name); ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-600">Operator</p>
                        <p class="text-sm font-semibold"><?php echo e($order->operator ? $order->operator->name : 'N/A'); ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-600">Completed</p>
                        <p class="text-sm font-semibold"><?php echo e($order->completed_at ? $order->completed_at->format('d M Y H:i') : $order->updated_at->format('d M Y H:i')); ?></p>
                    </div>

                    <?php if($order->rating): ?>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Client Rating</p>
                        <div class="flex items-center">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <svg class="w-4 h-4 <?php echo e($i <= $order->rating ? 'text-yellow-500' : 'text-gray-300'); ?>" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right: Review Form -->
        <div class="lg:col-span-2">
            <!-- Brief -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Brief dari Client</h3>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-800 whitespace-pre-wrap"><?php echo e($order->brief); ?></p>
                </div>
            </div>

            <!-- AI Output -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="text-base font-semibold text-gray-900 mb-3">AI Generated Output</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-sm text-gray-800 whitespace-pre-wrap"><?php echo e($order->result); ?></p>
                </div>
            </div>

            <!-- Review Form -->
            <form method="POST" action="<?php echo e(route('guru.training.store')); ?>" class="bg-white rounded-lg border border-gray-200 p-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="order_id" value="<?php echo e($order->id); ?>">

                <h3 class="text-base font-semibold text-gray-900 mb-4">Training Review</h3>

                <!-- Quality Rating -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Quality Rating *</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-400 transition">
                            <input type="radio" name="quality_rating" value="poor" required class="sr-only peer">
                            <div class="text-center peer-checked:text-red-600">
                                <div class="text-2xl mb-1">😞</div>
                                <div class="text-sm font-medium">Poor</div>
                            </div>
                            <div class="absolute inset-0 border-2 border-red-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                        </label>

                        <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-yellow-400 transition">
                            <input type="radio" name="quality_rating" value="fair" required class="sr-only peer">
                            <div class="text-center peer-checked:text-yellow-600">
                                <div class="text-2xl mb-1">😐</div>
                                <div class="text-sm font-medium">Fair</div>
                            </div>
                            <div class="absolute inset-0 border-2 border-yellow-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                        </label>

                        <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition">
                            <input type="radio" name="quality_rating" value="good" required class="sr-only peer">
                            <div class="text-center peer-checked:text-blue-600">
                                <div class="text-2xl mb-1">🙂</div>
                                <div class="text-sm font-medium">Good</div>
                            </div>
                            <div class="absolute inset-0 border-2 border-blue-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                        </label>

                        <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-400 transition">
                            <input type="radio" name="quality_rating" value="excellent" required class="sr-only peer">
                            <div class="text-center peer-checked:text-green-600">
                                <div class="text-2xl mb-1">😄</div>
                                <div class="text-sm font-medium">Excellent</div>
                            </div>
                            <div class="absolute inset-0 border-2 border-green-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                        </label>
                    </div>
                </div>

                <!-- Corrected Output -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Corrected Output (opsional)
                    </label>
                    <textarea name="corrected_output" x-model="correctedOutput" rows="6"
                              placeholder="Jika ada koreksi, tulis versi yang lebih baik di sini..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika output sudah sempurna</p>
                </div>

                <!-- Feedback Notes -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Feedback Notes (opsional)
                    </label>
                    <textarea name="feedback_notes" rows="3"
                              placeholder="Catatan tambahan untuk improvement..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                </div>

                <!-- Submit -->
                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm font-medium">
                        Submit Training Data
                    </button>
                    <a href="<?php echo e(route('guru.training')); ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\guru\training-review.blade.php ENDPATH**/ ?>