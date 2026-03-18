<nav class="bg-white shadow-sm border-b" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="<?php echo e(route('dashboard')); ?>" class="text-xl font-bold text-blue-600 mr-6 whitespace-nowrap">
                    <?php echo e(config('app.name', 'Noteds')); ?>

                </a>
                <div class="hidden md:flex items-center space-x-1">
                    <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo e(request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'); ?>">
                        Dashboard
                    </a>
                    <a href="<?php echo e(route('ai.generator')); ?>" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo e(request()->routeIs('ai.generator') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'); ?>">
                        AI Generator
                    </a>

                    
                    <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                        <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-md <?php echo e(request()->routeIs('analytics*') || request()->routeIs('bulk-content*') || request()->routeIs('caption-history*') || request()->routeIs('image-caption*') || request()->routeIs('keyword-research*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'); ?>">
                            Konten <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                            <a href="<?php echo e(route('analytics.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Analytics</a>
                            <a href="<?php echo e(route('bulk-content.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Bulk Content</a>
                            <a href="<?php echo e(route('caption-history.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Caption History</a>
                            <a href="<?php echo e(route('image-caption.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Image Caption</a>
                            <a href="<?php echo e(route('keyword-research.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Keyword Research</a>
                        </div>
                    </div>

                    
                    <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                        <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-md <?php echo e(request()->routeIs('templates*') || request()->routeIs('brand-voices*') || request()->routeIs('competitor-analysis*') || request()->routeIs('projects*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'); ?>">
                            Tools <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                            <a href="<?php echo e(route('templates.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Templates</a>
                            <a href="<?php echo e(route('brand-voices.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Brand Voice</a>
                            <a href="<?php echo e(route('competitor-analysis.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Competitor Analysis</a>
                            <a href="<?php echo e(route('projects.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Projects</a>
                        </div>
                    </div>

                    
                    <a href="<?php echo e(route('browse.operators')); ?>" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md <?php echo e(request()->routeIs('browse.operators') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'); ?>">
                        Marketplace
                    </a>

                    
                    <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                        <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-md <?php echo e(request()->routeIs('orders*') || request()->routeIs('subscription*') || request()->routeIs('client.referral*') || request()->routeIs('feedback*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'); ?>">
                            Akun <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                            <a href="<?php echo e(route('orders.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">My Orders</a>
                            <a href="<?php echo e(route('subscription.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Subscription</a>
                            <a href="<?php echo e(route('client.referral.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Referral</a>
                            <a href="<?php echo e(route('feedback.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Feedback</a>
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
                <a href="<?php echo e(route('profile.edit')); ?>" class="text-sm text-gray-700 hover:text-blue-600 hidden sm:block"><?php echo e(auth()->user()->name); ?></a>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\layouts\client-nav.blade.php ENDPATH**/ ?>