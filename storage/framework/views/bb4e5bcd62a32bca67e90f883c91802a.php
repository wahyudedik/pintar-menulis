

<?php $__env->startSection('sidebar-menu'); ?>
<!-- Dashboard -->
<a href="<?php echo e(route('dashboard')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Dashboard">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
</a>

<!-- AI Generator -->
<a href="<?php echo e(route('ai.generator')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('ai.generator') || request()->routeIs('caption-history.*') || request()->routeIs('my-stats') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="AI Generator">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
    </svg>
</a>

<!-- Image Caption -->
<a href="<?php echo e(route('image-caption.index')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('image-caption.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Image Caption">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
    </svg>
</a>

<!-- Analytics -->
<a href="<?php echo e(route('analytics.index')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('analytics.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Analytics">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
</a>

<!-- Browse Operators -->
<a href="<?php echo e(route('browse.operators')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('browse.operators') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Cari Operator">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
    </svg>
</a>

<!-- My Orders -->
<a href="<?php echo e(route('orders.index')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('orders.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Order Saya">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
</a>

<!-- Projects -->
<a href="<?php echo e(route('projects.index')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('projects.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Project">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
    </svg>
</a>

<!-- Subscription -->
<a href="<?php echo e(route('subscription.index')); ?>"
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('subscription.*') || request()->routeIs('pricing') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Langganan">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
    </svg>
</a>

<!-- Feedback & Support -->
<a href="<?php echo e(route('feedback.index')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('feedback.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Feedback & Support">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
    </svg>
</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/layouts/client.blade.php ENDPATH**/ ?>