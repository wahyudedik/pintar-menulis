<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Admin Dashboard'); ?> - <?php echo e(config('app.name', 'Noteds')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-6">
                    <a href="<?php echo e(route('dashboard')); ?>" class="text-xl font-bold text-blue-600 whitespace-nowrap"><?php echo e(config('app.name', 'Noteds')); ?></a>
                    <div class="hidden lg:flex items-center space-x-1">
                        <a href="<?php echo e(route('dashboard')); ?>" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm <?php echo e(request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : ''); ?>">Dashboard</a>
                        <a href="<?php echo e(route('admin.users')); ?>" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm <?php echo e(request()->routeIs('admin.users*') ? 'bg-blue-50 text-blue-600' : ''); ?>">Users</a>

                        
                        <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm flex items-center gap-1 <?php echo e(request()->routeIs('admin.packages*') || request()->routeIs('admin.subscriptions*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
                                Paket <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <a href="<?php echo e(route('admin.packages')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Kelola Paket</a>
                                <a href="<?php echo e(route('admin.subscriptions')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Subscriptions</a>
                            </div>
                        </div>

                        
                        <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm flex items-center gap-1 <?php echo e(request()->routeIs('admin.payments*') || request()->routeIs('admin.reports*') || request()->routeIs('admin.withdrawals*') || request()->routeIs('admin.referrals*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
                                Keuangan <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <a href="<?php echo e(route('admin.payments')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Payments</a>
                                <a href="<?php echo e(route('admin.reports')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Reports</a>
                                <a href="<?php echo e(route('admin.withdrawals')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Withdrawals</a>
                                <a href="<?php echo e(route('admin.referrals.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Referrals</a>
                            </div>
                        </div>

                        
                        <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm flex items-center gap-1 <?php echo e(request()->routeIs('admin.ai*') || request()->routeIs('admin.ml*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
                                AI & ML <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <a href="<?php echo e(route('admin.ai-usage.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">AI Usage</a>
                                <a href="<?php echo e(route('admin.ai-health.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">AI Health</a>
                                <a href="<?php echo e(route('admin.ai-models.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">AI Models</a>
                                <a href="<?php echo e(route('admin.ml-analytics.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">ML Analytics</a>
                                <a href="<?php echo e(route('admin.ml-data.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">ML Data</a>
                            </div>
                        </div>

                        
                        <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                            <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm flex items-center gap-1 <?php echo e(request()->routeIs('admin.banner*') || request()->routeIs('admin.ad-placements*') || request()->routeIs('admin.feedback*') || request()->routeIs('admin.guru-monitor*') || request()->routeIs('admin.whatsapp*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
                                Lainnya <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <a href="<?php echo e(route('admin.banner-information.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Banners</a>
                                <a href="<?php echo e(route('admin.ad-placements.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Ad Placements</a>
                                <a href="<?php echo e(route('admin.feedback')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Feedback</a>
                                <a href="<?php echo e(route('admin.guru-monitor.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Guru Monitor</a>
                                <a href="<?php echo e(route('admin.whatsapp-analytics.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">WhatsApp Analytics</a>
                                <a href="<?php echo e(route('admin.payment-settings')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Settings</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <?php if (isset($component)) { $__componentOriginal6541145ad4a57bfb6e6f221ba77eb386 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6541145ad4a57bfb6e6f221ba77eb386 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notification-bell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notification-bell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6541145ad4a57bfb6e6f221ba77eb386)): ?>
<?php $attributes = $__attributesOriginal6541145ad4a57bfb6e6f221ba77eb386; ?>
<?php unset($__attributesOriginal6541145ad4a57bfb6e6f221ba77eb386); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6541145ad4a57bfb6e6f221ba77eb386)): ?>
<?php $component = $__componentOriginal6541145ad4a57bfb6e6f221ba77eb386; ?>
<?php unset($__componentOriginal6541145ad4a57bfb6e6f221ba77eb386); ?>
<?php endif; ?>
                    <span class="text-gray-700 text-sm hidden sm:block"><?php echo e(auth()->user()->name); ?></span>
                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Admin</span>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-gray-700 hover:text-blue-600 text-sm">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <?php if(session('success')): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <?php echo e(session('success')); ?>

        </div>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <?php echo e(session('error')); ?>

        </div>
    </div>
    <?php endif; ?>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</body>
</html>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\layouts\admin-nav.blade.php ENDPATH**/ ?>