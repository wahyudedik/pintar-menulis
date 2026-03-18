

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Payment Verification</h1>
        <p class="text-sm text-gray-600 mt-1">Verifikasi pembayaran dari client</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['pending']); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Verified Today</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['verified_today']); ?></p>
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
                    <p class="text-sm text-gray-600">Total Verified</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['total_verified']); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Rejected</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($stats['rejected']); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200" x-data="{ tab: 'pending' }">
        <div class="border-b border-gray-200">
            <div class="flex space-x-4 px-4">
                <button @click="tab = 'pending'" 
                        :class="tab === 'pending' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-600'"
                        class="py-3 px-4 border-b-2 font-medium text-sm transition">
                    Pending (<?php echo e($stats['pending']); ?>)
                </button>
                <button @click="tab = 'verified'" 
                        :class="tab === 'verified' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-600'"
                        class="py-3 px-4 border-b-2 font-medium text-sm transition">
                    Verified
                </button>
                <button @click="tab = 'rejected'" 
                        :class="tab === 'rejected' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-600'"
                        class="py-3 px-4 border-b-2 font-medium text-sm transition">
                    Rejected
                </button>
            </div>
        </div>

        <!-- Pending Payments -->
        <div x-show="tab === 'pending'" class="p-4">
            <?php if($pendingPayments->count() > 0): ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $pendingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Payment Info -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3 text-sm">Payment Information</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Payment ID</span>
                                    <span class="font-medium">#<?php echo e($payment->id); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Order ID</span>
                                    <span class="font-medium">#<?php echo e($payment->order_id); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Client</span>
                                    <span class="font-medium"><?php echo e($payment->user->name); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Method</span>
                                    <span class="font-medium"><?php echo e($payment->payment_method); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Transaction ID</span>
                                    <span class="font-medium"><?php echo e($payment->transaction_id ?? '-'); ?></span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-2">
                                    <span class="text-gray-600">Amount</span>
                                    <span class="font-bold text-red-600">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Submitted</span>
                                    <span class="font-medium"><?php echo e($payment->created_at->format('d M Y H:i')); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Proof Image -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3 text-sm">Payment Proof</h4>
                            <a href="<?php echo e(asset('storage/' . $payment->proof_image)); ?>" target="_blank">
                                <img src="<?php echo e(asset('storage/' . $payment->proof_image)); ?>" 
                                     alt="Payment Proof" 
                                     class="w-full rounded-lg border border-gray-200 hover:opacity-75 transition cursor-pointer">
                            </a>
                            <p class="text-xs text-gray-500 mt-2 text-center">Click to view full size</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 mt-4 pt-4 border-t border-gray-200">
                        <form action="<?php echo e(route('admin.payments.verify', $payment)); ?>" method="POST" class="flex-1">
                            <?php echo csrf_field(); ?>
                            <button type="submit" 
                                    onclick="return confirm('Verify pembayaran ini?')"
                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                                Verify Payment
                            </button>
                        </form>
                        <form action="<?php echo e(route('admin.payments.reject', $payment)); ?>" method="POST" class="flex-1">
                            <?php echo csrf_field(); ?>
                            <button type="submit" 
                                    onclick="return confirm('Reject pembayaran ini?')"
                                    class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-medium">
                                Reject Payment
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500">Tidak ada pembayaran yang perlu diverifikasi</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Verified Payments -->
        <div x-show="tab === 'verified'" class="p-4">
            <?php if($verifiedPayments->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Payment ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Verified At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $verifiedPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">#<?php echo e($payment->id); ?></td>
                            <td class="px-4 py-3 text-sm"><?php echo e($payment->user->name); ?></td>
                            <td class="px-4 py-3 text-sm"><?php echo e($payment->payment_method); ?></td>
                            <td class="px-4 py-3 text-sm font-semibold">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
                            <td class="px-4 py-3 text-sm"><?php echo e($payment->updated_at->format('d M Y H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <p class="text-gray-500">Belum ada pembayaran yang diverifikasi</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Rejected Payments -->
        <div x-show="tab === 'rejected'" class="p-4">
            <?php if($rejectedPayments->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Payment ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Rejected At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $rejectedPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">#<?php echo e($payment->id); ?></td>
                            <td class="px-4 py-3 text-sm"><?php echo e($payment->user->name); ?></td>
                            <td class="px-4 py-3 text-sm"><?php echo e($payment->payment_method); ?></td>
                            <td class="px-4 py-3 text-sm font-semibold">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
                            <td class="px-4 py-3 text-sm"><?php echo e($payment->updated_at->format('d M Y H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <p class="text-gray-500">Belum ada pembayaran yang direject</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\admin\payments.blade.php ENDPATH**/ ?>