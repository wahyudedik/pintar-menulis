

<?php $__env->startSection('title', 'Submit Feedback'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6" x-data="feedbackForm()">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Submit Feedback</h1>
            <p class="text-sm text-gray-500 mt-1">Laporkan bug, request fitur, atau berikan saran</p>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('feedback.store')); ?>" method="POST" class="bg-white rounded-lg border border-gray-200 p-6">
            <?php echo csrf_field(); ?>

            <!-- Type Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Feedback <span class="text-red-600">*</span></label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition hover:border-red-500"
                           :class="type === 'bug' ? 'border-red-500 bg-red-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="bug" x-model="type" required class="sr-only">
                        <span class="text-3xl mb-2">🐛</span>
                        <span class="text-sm font-medium text-gray-900">Bug Report</span>
                        <span class="text-xs text-gray-500 mt-1 text-center">Ada yang error</span>
                    </label>

                    <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition hover:border-blue-500"
                           :class="type === 'feature' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="feature" x-model="type" required class="sr-only">
                        <span class="text-3xl mb-2">💡</span>
                        <span class="text-sm font-medium text-gray-900">Feature Request</span>
                        <span class="text-xs text-gray-500 mt-1 text-center">Ide fitur baru</span>
                    </label>

                    <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition hover:border-green-500"
                           :class="type === 'improvement' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="improvement" x-model="type" required class="sr-only">
                        <span class="text-3xl mb-2">⚡</span>
                        <span class="text-sm font-medium text-gray-900">Improvement</span>
                        <span class="text-xs text-gray-500 mt-1 text-center">Saran perbaikan</span>
                    </label>

                    <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition hover:border-yellow-500"
                           :class="type === 'question' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="question" x-model="type" required class="sr-only">
                        <span class="text-3xl mb-2">❓</span>
                        <span class="text-sm font-medium text-gray-900">Question</span>
                        <span class="text-xs text-gray-500 mt-1 text-center">Pertanyaan</span>
                    </label>
                </div>
            </div>

            <!-- Title -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Judul <span class="text-red-600">*</span>
                </label>
                <input type="text" name="title" required maxlength="255"
                       placeholder="Contoh: Button generate tidak berfungsi"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Detail <span class="text-red-600">*</span>
                </label>
                <textarea name="description" required rows="6" minlength="10"
                          placeholder="Jelaskan secara detail...&#10;&#10;Untuk bug report, sertakan:&#10;- Langkah-langkah untuk reproduce&#10;- Apa yang diharapkan&#10;- Apa yang terjadi&#10;&#10;Untuk feature request, sertakan:&#10;- Masalah yang ingin diselesaikan&#10;- Solusi yang diusulkan&#10;- Manfaat fitur ini"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                <p class="text-xs text-gray-500 mt-1">Minimum 10 karakter. Semakin detail, semakin cepat kami bisa bantu.</p>
            </div>

            <!-- Page URL (Auto-filled) -->
            <input type="hidden" name="page_url" x-model="pageUrl">

            <!-- Browser Info (Auto-filled) -->
            <input type="hidden" name="browser" x-model="browserInfo">

            <!-- Screenshot (Optional) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Screenshot (Optional)
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <input type="file" @change="handleScreenshot" accept="image/*" class="hidden" id="screenshot-input">
                    <label for="screenshot-input" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Click untuk upload screenshot</p>
                        <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                    </label>
                    <div x-show="screenshotPreview" x-cloak class="mt-4">
                        <img :src="screenshotPreview" class="max-w-full h-auto rounded-lg mx-auto" style="max-height: 200px;">
                        <button type="button" @click="removeScreenshot" class="mt-2 text-sm text-red-600 hover:text-red-700">
                            Remove
                        </button>
                    </div>
                </div>
                <input type="hidden" name="screenshot" x-model="screenshotData">
            </div>

            <!-- Submit Button -->
            <div class="flex space-x-3">
                <a href="<?php echo e(route('feedback.index')); ?>" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Submit Feedback
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function feedbackForm() {
    return {
        type: 'bug',
        pageUrl: document.referrer || window.location.href,
        browserInfo: navigator.userAgent,
        screenshotPreview: null,
        screenshotData: null,
        
        handleScreenshot(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File terlalu besar. Maksimal 2MB.');
                return;
            }
            
            // Read file as base64
            const reader = new FileReader();
            reader.onload = (e) => {
                this.screenshotPreview = e.target.result;
                this.screenshotData = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        
        removeScreenshot() {
            this.screenshotPreview = null;
            this.screenshotData = null;
            document.getElementById('screenshot-input').value = '';
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.' . auth()->user()->role, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\feedback\create.blade.php ENDPATH**/ ?>