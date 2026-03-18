

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Financial Reports</h1>
        <p class="text-sm text-gray-600 mt-1">Overview keuangan dan statistik platform</p>
    </div>

    <!-- Revenue Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Revenue</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">Rp <?php echo e(number_format($stats['totalRevenue'], 0, ',', '.')); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Operator Earnings</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">Rp <?php echo e(number_format($stats['operatorEarnings'], 0, ',', '.')); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Platform Commission</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">Rp <?php echo e(number_format($stats['platformCommission'], 0, ',', '.')); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Withdrawals</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">Rp <?php echo e(number_format($stats['pendingWithdrawals'], 0, ',', '.')); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Orders</p>
            <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['totalOrders']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Completed Orders</p>
            <p class="text-2xl font-bold text-green-600 mt-1"><?php echo e($stats['completedOrders']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Pending Orders</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1"><?php echo e($stats['pendingOrders']); ?></p>
        </div>
    </div>

    <!-- Withdrawal Stats -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Withdrawal Statistics</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-xs text-yellow-700 mb-1">Pending</p>
                <p class="text-2xl font-bold text-yellow-900"><?php echo e($withdrawalStats['pending']); ?></p>
                <p class="text-xs text-yellow-600 mt-1">Rp <?php echo e(number_format($withdrawalStats['pendingAmount'], 0, ',', '.')); ?></p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-xs text-blue-700 mb-1">Processing</p>
                <p class="text-2xl font-bold text-blue-900"><?php echo e($withdrawalStats['processing']); ?></p>
                <p class="text-xs text-blue-600 mt-1">Rp <?php echo e(number_format($withdrawalStats['processingAmount'], 0, ',', '.')); ?></p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-xs text-green-700 mb-1">Completed</p>
                <p class="text-2xl font-bold text-green-900"><?php echo e($withdrawalStats['completed']); ?></p>
                <p class="text-xs text-green-600 mt-1">Rp <?php echo e(number_format($withdrawalStats['completedAmount'], 0, ',', '.')); ?></p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <p class="text-xs text-red-700 mb-1">Rejected</p>
                <p class="text-2xl font-bold text-red-900"><?php echo e($withdrawalStats['rejected']); ?></p>
                <p class="text-xs text-red-600 mt-1">Rp <?php echo e(number_format($withdrawalStats['rejectedAmount'], 0, ',', '.')); ?></p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Revenue Over Time -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Revenue Over Time (Last 30 Days)</h3>
            <canvas id="revenueChart"></canvas>
        </div>

        <!-- Orders by Category -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Orders by Category</h3>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <!-- Top Operators -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top Operators</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Operator</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Completed Orders</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Rating</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Total Earnings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $topOperators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $operator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xl font-bold text-gray-400">#<?php echo e($index + 1); ?></td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($operator->name); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($operator->email); ?></div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($operator->completed_orders); ?></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="text-sm font-medium"><?php echo e(number_format($operator->operatorProfile->average_rating ?? 0, 1)); ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-green-600">
                            Rp <?php echo e(number_format($operator->operatorProfile->total_earnings ?? 0, 0, ',', '.')); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Over Time Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = <?php echo json_encode($revenueOverTime, 15, 512) ?>;
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.map(d => d.date),
        datasets: [{
            label: 'Revenue (Rp)',
            data: revenueData.map(d => d.revenue),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Orders by Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryData = <?php echo json_encode($ordersByCategory, 15, 512) ?>;
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryData.map(d => d.category),
        datasets: [{
            data: categoryData.map(d => d.count),
            backgroundColor: [
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(245, 158, 11)',
                'rgb(239, 68, 68)',
                'rgb(139, 92, 246)',
                'rgb(236, 72, 153)',
                'rgb(20, 184, 166)',
                'rgb(251, 146, 60)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\reports.blade.php ENDPATH**/ ?>