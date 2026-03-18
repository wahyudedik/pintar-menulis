

<?php $__env->startSection('title', 'WhatsApp Analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">WhatsApp Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Monitor performa WhatsApp integration</p>
        </div>
        <div class="flex items-center gap-3">
            <select id="periodSelect" onchange="changePeriod(this.value)"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                <option value="1d">Hari Ini</option>
                <option value="7d" selected>7 Hari</option>
                <option value="30d">30 Hari</option>
                <option value="90d">90 Hari</option>
            </select>
            <a href="<?php echo e(route('admin.whatsapp-analytics.export')); ?>"
               class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                Export CSV
            </a>
            <button onclick="refreshData()"
                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                Refresh
            </button>
        </div>
    </div>

    <?php $overview = $analytics['overview'] ?? []; ?>

    <!-- Overview Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Total Pesan</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($overview['total_messages'] ?? 0)); ?></p>
            <p class="text-xs text-green-600 mt-1">↑ <?php echo e($overview['message_growth'] ?? 0); ?>%</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Pengguna Aktif</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($overview['active_users'] ?? 0)); ?></p>
            <p class="text-xs text-gray-500 mt-1">periode ini</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Response Rate</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($overview['response_rate'] ?? 0, 1)); ?>%</p>
            <p class="text-xs text-gray-500 mt-1">rata-rata</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Avg Response Time</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($overview['avg_response_time'] ?? 0, 1)); ?>s</p>
            <p class="text-xs text-gray-500 mt-1">detik</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Message Stats -->
        <?php $messages = $analytics['messages'] ?? []; ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Statistik Pesan</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pesan Masuk</span>
                    <span class="font-semibold text-gray-900"><?php echo e(number_format($messages['incoming'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pesan Keluar</span>
                    <span class="font-semibold text-gray-900"><?php echo e(number_format($messages['outgoing'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pesan Gagal</span>
                    <span class="font-semibold text-red-600"><?php echo e(number_format($messages['failed'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Broadcast Terkirim</span>
                    <span class="font-semibold text-gray-900"><?php echo e(number_format($messages['broadcast_sent'] ?? 0)); ?></span>
                </div>
            </div>
        </div>

        <!-- Subscription Stats -->
        <?php $subs = $analytics['subscriptions'] ?? []; ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Subscriber WhatsApp</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Subscriber</span>
                    <span class="font-semibold text-gray-900"><?php echo e(number_format($subs['total'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Subscriber Aktif</span>
                    <span class="font-semibold text-green-600"><?php echo e(number_format($subs['active'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Baru Periode Ini</span>
                    <span class="font-semibold text-blue-600">+<?php echo e(number_format($subs['new'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Churn Rate</span>
                    <span class="font-semibold text-red-600"><?php echo e(number_format($subs['churn_rate'] ?? 0, 1)); ?>%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Engagement Stats -->
        <?php $engagement = $analytics['engagement'] ?? []; ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Engagement</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Avg Conversation Length</span>
                    <span class="font-semibold text-gray-900"><?php echo e(number_format($engagement['avg_conversation_length'] ?? 0, 1)); ?> pesan</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Retention Rate</span>
                    <span class="font-semibold text-green-600"><?php echo e(number_format($engagement['retention_rate'] ?? 0, 1)); ?>%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Engagement Trend</span>
                    <span class="font-semibold <?php echo e(($engagement['trend'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                        <?php echo e(($engagement['trend'] ?? 0) >= 0 ? '↑' : '↓'); ?> <?php echo e(abs($engagement['trend'] ?? 0)); ?>%
                    </span>
                </div>
            </div>
        </div>

        <!-- Device Status -->
        <?php $device = $analytics['device_status'] ?? []; ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Status Device</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Status</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium <?php echo e(($device['connected'] ?? false) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); ?>">
                        <?php echo e(($device['connected'] ?? false) ? 'Connected' : 'Disconnected'); ?>

                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">API Health</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium <?php echo e(($device['api_healthy'] ?? false) ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'); ?>">
                        <?php echo e(($device['api_healthy'] ?? false) ? 'Healthy' : 'Degraded'); ?>

                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Queue Size</span>
                    <span class="font-semibold text-gray-900"><?php echo e(number_format($device['queue_size'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Avg Processing Time</span>
                    <span class="font-semibold text-gray-900"><?php echo e(number_format($device['avg_processing_time'] ?? 0, 1)); ?>s</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function changePeriod(period) {
    fetch(`<?php echo e(route('admin.whatsapp-analytics.data')); ?>?period=${period}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        // Reload page with period param for simplicity
        window.location.href = `<?php echo e(route('admin.whatsapp-analytics.index')); ?>?period=${period}`;
    });
}

function refreshData() {
    fetch('<?php echo e(route('admin.whatsapp-analytics.refresh')); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(() => window.location.reload());
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\whatsapp-analytics\index.blade.php ENDPATH**/ ?>