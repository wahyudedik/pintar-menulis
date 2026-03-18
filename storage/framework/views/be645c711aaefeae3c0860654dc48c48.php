

<?php $__env->startSection('sidebar-menu'); ?>
<!-- Dashboard -->
<a href="<?php echo e(route('dashboard')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('dashboard') ? 'bg-purple-50 text-purple-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Dashboard">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
</a>

<!-- Training -->
<a href="<?php echo e(route('guru.training')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('guru.training') || request()->routeIs('guru.training.show') ? 'bg-purple-50 text-purple-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Training">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
    </svg>
</a>

<!-- History -->
<a href="<?php echo e(route('guru.training.history')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('guru.training.history') ? 'bg-purple-50 text-purple-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="History">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
</a>

<!-- Analytics -->
<a href="<?php echo e(route('guru.analytics')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('guru.analytics') ? 'bg-purple-50 text-purple-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Analytics">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
</a>

<!-- Earnings -->
<a href="<?php echo e(route('guru.earnings')); ?>" 
   class="tooltip flex items-center justify-center w-12 h-12 rounded-lg transition <?php echo e(request()->routeIs('guru.earnings') || request()->routeIs('guru.withdrawal.*') ? 'bg-purple-50 text-purple-600' : 'text-gray-600 hover:bg-gray-100'); ?>"
   data-tooltip="Penghasilan">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\layouts\guru.blade.php ENDPATH**/ ?>