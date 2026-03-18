

<?php $__env->startSection('title', 'Competitor Alerts'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="<?php echo e(route('competitor-analysis.index')); ?>" class="hover:text-purple-600">Competitor Analysis</a>
            <span>/</span>
            <span class="text-gray-900">Alerts</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">🔔 Competitor Alerts</h1>
                <p class="text-sm text-gray-500 mt-1">Notifikasi aktivitas kompetitor</p>
            </div>
            <?php if($alerts->where('is_read', false)->count() > 0): ?>
            <form action="<?php echo e(route('competitor-analysis.alerts.read-all')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    Mark All as Read
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 flex space-x-2" x-data="{ filter: 'all' }">
        <button @click="filter = 'all'" 
                :class="filter === 'all' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition">
            All (<?php echo e($alerts->count()); ?>)
        </button>
        <button @click="filter = 'unread'" 
                :class="filter === 'unread' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition">
            Unread (<?php echo e($alerts->where('is_read', false)->count()); ?>)
        </button>
        <button @click="filter = 'new_post'" 
                :class="filter === 'new_post' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition">
            New Posts
        </button>
        <button @click="filter = 'high_engagement'" 
                :class="filter === 'high_engagement' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition">
            High Engagement
        </button>
    </div>

    <!-- Alerts List -->
    <?php if($alerts->isEmpty()): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Alert</h3>
        <p class="text-sm text-gray-600">Alert akan muncul saat kompetitor melakukan aktivitas baru</p>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-200">
        <?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="p-6 hover:bg-gray-50 transition <?php echo e($alert->is_read ? 'opacity-60' : 'bg-blue-50'); ?>"
             x-data="{ show: true }"
             x-show="show"
             data-filter="<?php echo e($alert->is_read ? 'read' : 'unread'); ?> <?php echo e($alert->alert_type); ?>">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <!-- Alert Type Badge -->
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="px-3 py-1 text-xs rounded-full font-medium
                            <?php echo e($alert->alert_type === 'new_post' ? 'bg-blue-100 text-blue-700' : 
                               ($alert->alert_type === 'high_engagement' ? 'bg-green-100 text-green-700' : 
                               ($alert->alert_type === 'pattern_change' ? 'bg-purple-100 text-purple-700' : 
                               'bg-orange-100 text-orange-700'))); ?>">
                            <?php if($alert->alert_type === 'new_post'): ?>
                                📝 New Post
                            <?php elseif($alert->alert_type === 'high_engagement'): ?>
                                🔥 High Engagement
                            <?php elseif($alert->alert_type === 'pattern_change'): ?>
                                📊 Pattern Change
                            <?php else: ?>
                                ⚠️ <?php echo e(ucfirst(str_replace('_', ' ', $alert->alert_type))); ?>

                            <?php endif; ?>
                        </span>
                        <span class="text-xs text-gray-500"><?php echo e($alert->triggered_at->diffForHumans()); ?></span>
                        <?php if(!$alert->is_read): ?>
                        <span class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full font-bold">NEW</span>
                        <?php endif; ?>
                    </div>

                    <!-- Competitor Info -->
                    <div class="flex items-center space-x-2 mb-2">
                        <a href="<?php echo e(route('competitor-analysis.show', $alert->competitor)); ?>" 
                           class="font-semibold text-purple-600 hover:text-purple-700">
                            <?php echo e($alert->competitor->username); ?>

                        </a>
                        <span class="text-xs text-gray-500 capitalize">• <?php echo e($alert->competitor->platform); ?></span>
                    </div>

                    <!-- Alert Message -->
                    <p class="text-sm text-gray-900 mb-3"><?php echo e($alert->alert_message); ?></p>

                    <!-- Post Details (if available) -->
                    <?php if($alert->post): ?>
                    <div class="p-3 bg-white rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700 mb-2 line-clamp-2"><?php echo e($alert->post->caption); ?></p>
                        <div class="flex items-center space-x-4 text-xs text-gray-600">
                            <span>❤️ <?php echo e(number_format($alert->post->likes_count)); ?></span>
                            <span>💬 <?php echo e(number_format($alert->post->comments_count)); ?></span>
                            <span>📊 <?php echo e(number_format($alert->post->engagement_rate, 1)); ?>%</span>
                            <span>🕐 <?php echo e($alert->post->posted_at->format('d M Y H:i')); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Alert Data (if available) -->
                    <?php if($alert->alert_data): ?>
                    <div class="mt-2">
                        <details class="text-xs">
                            <summary class="cursor-pointer text-purple-600 hover:text-purple-700">View Details</summary>
                            <pre class="mt-2 p-2 bg-gray-100 rounded text-xs overflow-x-auto"><?php echo e(json_encode($alert->alert_data, JSON_PRETTY_PRINT)); ?></pre>
                        </details>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="ml-4 flex flex-col space-y-2">
                    <?php if(!$alert->is_read): ?>
                    <form action="<?php echo e(route('competitor-analysis.alert.read', $alert)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" 
                                class="px-3 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700 transition">
                            Mark Read
                        </button>
                    </form>
                    <?php endif; ?>
                    <a href="<?php echo e(route('competitor-analysis.show', $alert->competitor)); ?>" 
                       class="px-3 py-1 bg-gray-100 text-gray-700 text-xs text-center rounded hover:bg-gray-200 transition">
                        View Competitor
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Pagination -->
    <?php if($alerts->hasPages()): ?>
    <div class="mt-6">
        <?php echo e($alerts->links()); ?>

    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php $__env->startPush('head'); ?>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\competitor-analysis\alerts.blade.php ENDPATH**/ ?>