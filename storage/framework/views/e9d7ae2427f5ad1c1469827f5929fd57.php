

<?php $__env->startSection('title', 'Content Calendar - ' . $calendar->title); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <a href="<?php echo e(route('bulk-content.index')); ?>" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Calendars
        </a>
        
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900"><?php echo e($calendar->title); ?></h1>
                <div class="flex items-center gap-3 mt-2">
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                        <?php echo e($calendar->duration === '7_days' ? '7 Hari' : '30 Hari'); ?>

                    </span>
                    <span class="text-sm text-gray-600">
                        <?php echo e($calendar->start_date->format('d M Y')); ?> - <?php echo e($calendar->end_date->format('d M Y')); ?>

                    </span>
                    <span class="text-sm text-gray-600">•</span>
                    <span class="text-sm text-gray-600"><?php echo e(count($calendar->content_items)); ?> konten</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('bulk-content.export', [$calendar, 'csv'])); ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold">Content Calendar</h3>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-1 gap-4">
                <?php $__currentLoopData = $calendar->content_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition" x-data="{ expanded: false }">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <span class="text-lg font-bold text-blue-600"><?php echo e($item['day_number']); ?></span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900"><?php echo e($item['day_name']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e(\Carbon\Carbon::parse($item['scheduled_date'])->format('d M Y')); ?> • <?php echo e($item['scheduled_time']); ?></p>
                                </div>
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                    <?php echo e($item['theme']); ?>

                                </span>
                            </div>

                            <div class="mt-3">
                                <p class="text-sm text-gray-700 whitespace-pre-wrap" 
                                   :class="expanded ? '' : 'line-clamp-3'"><?php echo e($item['caption']); ?></p>
                                
                                <?php if(strlen($item['caption']) > 200): ?>
                                <button @click="expanded = !expanded" class="text-xs text-blue-600 hover:text-blue-700 mt-1">
                                    <span x-show="!expanded">Show more</span>
                                    <span x-show="expanded">Show less</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 ml-4">
                            <button onclick='editContent(<?php echo e($item["day_number"]); ?>, <?php echo e(json_encode($item["caption"])); ?>, <?php echo e(json_encode($item["scheduled_time"])); ?>)' 
                                class="px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Edit
                            </button>
                            <button onclick='copyCaption(<?php echo e(json_encode($item["caption"])); ?>)' 
                                class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Edit Content</h3>
        </div>
        
        <form id="editForm" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="edit_day_number" name="day_number">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
                <textarea id="edit_caption" 
                    name="caption" 
                    rows="8"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Posting Time</label>
                <input type="time" 
                    id="edit_time" 
                    name="scheduled_time"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editContent(dayNumber, caption, time) {
    document.getElementById('edit_day_number').value = dayNumber;
    document.getElementById('edit_caption').value = caption;
    document.getElementById('edit_time').value = time;
    
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function copyCaption(caption) {
    // Create temporary textarea
    const textarea = document.createElement('textarea');
    textarea.value = caption;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    
    // Select and copy
    textarea.select();
    textarea.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        alert('Caption berhasil dicopy! ✓');
    } catch (err) {
        alert('Gagal copy caption');
    }
    
    // Remove temporary element
    document.body.removeChild(textarea);
}

document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const dayNumber = formData.get('day_number');
    const data = {
        caption: formData.get('caption'),
        scheduled_time: formData.get('scheduled_time')
    };
    
    try {
        const response = await fetch(`<?php echo e(route('bulk-content.show', $calendar)); ?>/update/${dayNumber}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Content updated successfully!');
            location.reload();
        } else {
            throw new Error(result.message || 'Update failed');
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\bulk-content\show.blade.php ENDPATH**/ ?>