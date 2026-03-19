

<?php $__env->startSection('title', 'Error Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="errorLogs()">

    
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">🪵 Error Logs</h1>
            <p class="text-sm text-gray-500 mt-1">
                File: <code class="bg-gray-100 px-1 rounded text-xs">storage/logs/laravel.log</code>
                &nbsp;·&nbsp; Ukuran: <span class="font-medium"><?php echo e($stats['file_size']); ?></span>
                &nbsp;·&nbsp; Terakhir diubah: <span class="font-medium"><?php echo e($stats['last_modified']); ?></span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.error-logs.download')); ?>"
               class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </a>
            <form method="POST" action="<?php echo e(route('admin.error-logs.clear')); ?>"
                  onsubmit="return confirm('Yakin ingin mengosongkan semua log?')">
                <?php echo csrf_field(); ?>
                <button type="submit"
                        class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Kosongkan
                </button>
            </form>
        </div>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <?php
        $statCards = [
            ['label'=>'Total',    'key'=>'total',    'color'=>'gray',   'icon'=>'📋'],
            ['label'=>'Error',    'key'=>'error',    'color'=>'red',    'icon'=>'🔴'],
            ['label'=>'Critical', 'key'=>'critical', 'color'=>'purple', 'icon'=>'🚨'],
            ['label'=>'Warning',  'key'=>'warning',  'color'=>'yellow', 'icon'=>'🟡'],
            ['label'=>'Info',     'key'=>'info',     'color'=>'blue',   'icon'=>'🔵'],
            ['label'=>'Debug',    'key'=>'debug',    'color'=>'green',  'icon'=>'🟢'],
        ];
        $colorMap = [
            'gray'   => 'bg-gray-50 border-gray-200 text-gray-700',
            'red'    => 'bg-red-50 border-red-200 text-red-700',
            'purple' => 'bg-purple-50 border-purple-200 text-purple-700',
            'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
            'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
            'green'  => 'bg-green-50 border-green-200 text-green-700',
        ];
        ?>
        <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('admin.error-logs.index', array_merge(request()->query(), ['level' => $card['key'] === 'total' ? 'all' : $card['key'], 'page' => 1]))); ?>"
           class="border rounded-lg p-3 text-center <?php echo e($colorMap[$card['color']]); ?> hover:opacity-80 transition">
            <div class="text-xl"><?php echo e($card['icon']); ?></div>
            <div class="text-2xl font-bold"><?php echo e($stats[$card['key']] ?? 0); ?></div>
            <div class="text-xs font-medium"><?php echo e($card['label']); ?></div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.error-logs.index')); ?>" class="bg-white border border-gray-200 rounded-xl p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari pesan</label>
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Cari error, class, file..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Level</label>
                <select name="level" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <?php $__currentLoopData = ['all'=>'Semua Level','error'=>'Error','critical'=>'Critical','warning'=>'Warning','info'=>'Info','debug'=>'Debug']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php echo e($level === $val ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Per halaman</label>
                <select name="per_page" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <?php $__currentLoopData = [25, 50, 100, 200]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($n); ?>" <?php echo e($perPage == $n ? 'selected' : ''); ?>><?php echo e($n); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Filter</button>
            <a href="<?php echo e(route('admin.error-logs.index')); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Reset</a>
        </div>
    </form>

    
    <div class="flex items-center justify-between mb-3">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium"><?php echo e(count($entries)); ?></span> dari <span class="font-medium"><?php echo e($total); ?></span> entri
            <?php if($search): ?> &nbsp;·&nbsp; pencarian: "<span class="font-medium text-blue-600"><?php echo e($search); ?></span>" <?php endif; ?>
        </p>
        
        <?php if($totalPages > 1): ?>
        <div class="flex items-center gap-1">
            <?php if($page > 1): ?>
            <a href="<?php echo e(route('admin.error-logs.index', array_merge(request()->query(), ['page' => $page - 1]))); ?>"
               class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">← Prev</a>
            <?php endif; ?>
            <span class="px-3 py-1 text-sm text-gray-600"><?php echo e($page); ?> / <?php echo e($totalPages); ?></span>
            <?php if($page < $totalPages): ?>
            <a href="<?php echo e(route('admin.error-logs.index', array_merge(request()->query(), ['page' => $page + 1]))); ?>"
               class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    
    <?php if(count($entries) === 0): ?>
    <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
        <div class="text-4xl mb-3">✅</div>
        <p class="text-gray-500">Tidak ada log yang ditemukan.</p>
    </div>
    <?php else: ?>
    <div class="space-y-2">
        <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        $levelColors = [
            'error'     => 'border-red-300 bg-red-50',
            'critical'  => 'border-purple-400 bg-purple-50',
            'emergency' => 'border-red-600 bg-red-100',
            'alert'     => 'border-orange-400 bg-orange-50',
            'warning'   => 'border-yellow-300 bg-yellow-50',
            'notice'    => 'border-blue-200 bg-blue-50',
            'info'      => 'border-blue-200 bg-blue-50',
            'debug'     => 'border-gray-200 bg-gray-50',
        ];
        $badgeColors = [
            'error'     => 'bg-red-100 text-red-700',
            'critical'  => 'bg-purple-100 text-purple-700',
            'emergency' => 'bg-red-200 text-red-800',
            'alert'     => 'bg-orange-100 text-orange-700',
            'warning'   => 'bg-yellow-100 text-yellow-700',
            'notice'    => 'bg-blue-100 text-blue-700',
            'info'      => 'bg-blue-100 text-blue-700',
            'debug'     => 'bg-gray-100 text-gray-600',
        ];
        $borderClass = $levelColors[$entry['level']] ?? 'border-gray-200 bg-white';
        $badgeClass  = $badgeColors[$entry['level']] ?? 'bg-gray-100 text-gray-600';
        $entryId     = 'entry-' . $i;
        ?>
        <div class="border rounded-lg <?php echo e($borderClass); ?> overflow-hidden" x-data="{ open: false }">
            
            <div class="flex items-start gap-3 p-3 cursor-pointer select-none" @click="open = !open">
                <span class="flex-shrink-0 px-2 py-0.5 text-xs font-bold rounded uppercase <?php echo e($badgeClass); ?>">
                    <?php echo e($entry['level']); ?>

                </span>
                <span class="flex-shrink-0 text-xs text-gray-500 whitespace-nowrap pt-0.5">
                    <?php echo e($entry['datetime']); ?>

                </span>
                <span class="flex-1 text-sm text-gray-800 font-mono leading-snug break-all">
                    <?php echo e($entry['message']); ?>

                </span>
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            
            <div x-show="open" x-cloak class="border-t border-gray-200 bg-white">
                
                <?php if(!empty($entry['context'])): ?>
                <div class="p-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Context</p>
                    <?php if(is_array($entry['context'])): ?>
                        <?php $__currentLoopData = $entry['context']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($key !== 'exception'): ?>
                        <div class="flex gap-2 text-xs mb-1">
                            <span class="font-medium text-gray-600 min-w-24"><?php echo e($key); ?></span>
                            <span class="text-gray-800 font-mono break-all"><?php echo e(is_array($val) ? json_encode($val) : $val); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <p class="text-xs font-mono text-gray-700 break-all"><?php echo e($entry['context']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                
                <?php if(!empty($entry['stacktrace'])): ?>
                <div class="p-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Stack Trace</p>
                    <pre class="text-xs font-mono text-gray-700 bg-gray-900 text-green-300 p-3 rounded overflow-x-auto whitespace-pre-wrap leading-relaxed max-h-64 overflow-y-auto"><?php echo e($entry['stacktrace']); ?></pre>
                </div>
                <?php endif; ?>

                
                <div class="p-3 border-t border-gray-100">
                    <details>
                        <summary class="text-xs font-semibold text-gray-400 cursor-pointer hover:text-gray-600">Raw log entry</summary>
                        <pre class="mt-2 text-xs font-mono text-gray-600 bg-gray-50 p-2 rounded overflow-x-auto whitespace-pre-wrap max-h-48 overflow-y-auto"><?php echo e($entry['full']); ?></pre>
                    </details>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if($totalPages > 1): ?>
    <div class="flex justify-center gap-2 mt-6">
        <?php if($page > 1): ?>
        <a href="<?php echo e(route('admin.error-logs.index', array_merge(request()->query(), ['page' => $page - 1]))); ?>"
           class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50">← Sebelumnya</a>
        <?php endif; ?>
        <span class="px-4 py-2 text-sm text-gray-600">Halaman <?php echo e($page); ?> dari <?php echo e($totalPages); ?></span>
        <?php if($page < $totalPages): ?>
        <a href="<?php echo e(route('admin.error-logs.index', array_merge(request()->query(), ['page' => $page + 1]))); ?>"
           class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Selanjutnya →</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function errorLogs() {
    return {};
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/admin/error-logs/index.blade.php ENDPATH**/ ?>