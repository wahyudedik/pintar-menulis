

<?php $__env->startSection('title', 'AI Generator'); ?>

<?php $__env->startPush('head'); ?>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-6" x-data="aiGenerator" x-init="init()">

    
    <div x-show="notificationVisible" x-cloak x-transition
         :class="notificationType === 'success' ? 'bg-green-600' : 'bg-red-600'"
         class="fixed bottom-6 right-6 z-50 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium max-w-xs"
         x-text="notificationMessage">
    </div>

    
    <?php echo $__env->make('client.partials.ai-generator.subscription-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('client.partials.ai-generator.header-navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Text Mode Toggle -->
    <div class="mb-6 flex justify-center" x-show="generatorType === 'text'" x-cloak>
        <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50">
            <button @click="mode = 'simple'"
                    :class="mode === 'simple' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-6 py-2 rounded-md text-sm font-medium transition">
                🎯 Mode Simpel
            </button>
            <button @click="mode = 'advanced'"
                    :class="mode === 'advanced' ? 'bg-white shadow-sm' : 'text-gray-600'"
                    class="px-6 py-2 rounded-md text-sm font-medium transition">
                ⚙️ Mode Lengkap
            </button>
        </div>
    </div>

    <!-- Content Area -->
    <div class="w-full">

        
        <div class="w-full">
            <div class="bg-white rounded-lg border border-gray-200 p-6"
                 x-show="generatorType === 'text' || generatorType === 'image' || generatorType === 'video'" x-cloak>
                <?php echo $__env->make('client.partials.ai-generator.form-text-simple', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('client.partials.ai-generator.form-text-advanced', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('client.partials.ai-generator.form-image-caption', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('client.partials.ai-generator.form-video', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            
            <?php echo $__env->make('client.partials.ai-generator.placeholder-and-predictor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        
        <?php echo $__env->make('client.partials.ai-generator.result-section', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->make('client.partials.ml-upgrade-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>
</div>

<?php echo $__env->make('client.partials.ai-generator._script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<style>
    [x-cloak] { display: none !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/client/ai-generator.blade.php ENDPATH**/ ?>