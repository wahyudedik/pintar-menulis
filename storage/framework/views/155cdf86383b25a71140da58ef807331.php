

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola semua user di platform</p>
        </div>
        <a href="<?php echo e(route('admin.users.create')); ?>" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
            + Add User
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Total Users</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total_users']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Clients</p>
            <p class="text-2xl font-bold text-blue-600"><?php echo e($stats['clients']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Operators</p>
            <p class="text-2xl font-bold text-green-600"><?php echo e($stats['operators']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Gurus</p>
            <p class="text-2xl font-bold text-purple-600"><?php echo e($stats['gurus']); ?></p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Admins</p>
            <p class="text-2xl font-bold text-red-600"><?php echo e($stats['admins']); ?></p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Orders</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Earnings</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Joined</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($user->email); ?></div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded
                                <?php if($user->role === 'admin'): ?> bg-red-100 text-red-700
                                <?php elseif($user->role === 'operator'): ?> bg-green-100 text-green-700
                                <?php elseif($user->role === 'guru'): ?> bg-purple-100 text-purple-700
                                <?php else: ?> bg-blue-100 text-blue-700
                                <?php endif; ?>">
                                <?php echo e(ucfirst($user->role)); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <?php echo e($user->orders_count + $user->operator_orders_count); ?>

                        </td>
                        <td class="px-4 py-3">
                            <?php if($user->role === 'operator' && $user->operatorProfile): ?>
                                <span class="text-sm font-medium text-green-600">
                                    Rp <?php echo e(number_format($user->operatorProfile->total_earnings ?? 0, 0, ',', '.')); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($user->role === 'operator' && $user->operatorProfile): ?>
                                <?php if($user->operatorProfile->is_verified): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-700">Verified</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-700">Unverified</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <?php echo e($user->created_at->format('d M Y')); ?>

                        </td>
                        <td class="px-4 py-3 text-sm space-x-2">
                            <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="text-blue-600 hover:text-blue-700">Edit</a>
                            
                            <?php if($user->role === 'operator' && $user->operatorProfile): ?>
                                <?php if($user->operatorProfile->is_verified): ?>
                                    <form action="<?php echo e(route('admin.users.unverify', $user)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-700">Unverify</button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?php echo e(route('admin.users.verify', $user)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="text-green-600 hover:text-green-700">Verify</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if($user->id !== auth()->id()): ?>
                                <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            <?php echo e($users->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/admin/users.blade.php ENDPATH**/ ?>