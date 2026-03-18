<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Dashboard')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <?php
            /** @var \App\Models\User $user */
            $user = Auth::user();
        ?>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Selamat Datang -->
                    <div class="md:col-span-3">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang, <?php echo e($user->name); ?>!</h3>
                        <p class="text-gray-600">Mulai buat konten promosi untuk bisnis Anda hari ini.</p>
                    </div>

                    <!-- Statistik / Pintasan -->
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                        <h4 class="font-bold text-blue-800 mb-2">Pesanan Aktif</h4>
                        <p class="text-3xl font-bold text-blue-900">
                            <?php echo e($user->orders()->where('status', 'active')->count()); ?></p>
                        <a href="<?php echo e(route('orders.index')); ?>"
                            class="mt-4 inline-block text-sm font-bold text-blue-600 hover:text-blue-800">Lihat Semua
                            →</a>
                    </div>

                    <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                        <h4 class="font-bold text-green-800 mb-2">Proyek Bisnis</h4>
                        <p class="text-3xl font-bold text-green-900"><?php echo e($user->projects()->count()); ?></p>
                        <a href="<?php echo e(route('projects.index')); ?>"
                            class="mt-4 inline-block text-sm font-bold text-green-600 hover:text-green-800">Kelola
                            Proyek →</a>
                    </div>

                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-100">
                        <h4 class="font-bold text-purple-800 mb-2">Request Konten</h4>
                        <p class="text-3xl font-bold text-purple-900"><?php echo e($user->copywritingRequests()->count()); ?></p>
                        <a href="<?php echo e(route('copywriting.index')); ?>"
                            class="mt-4 inline-block text-sm font-bold text-purple-600 hover:text-purple-800">Lihat
                            Request →</a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="mt-12">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Aktivitas Terakhir</h3>
                    <?php
                        $recentRequests = $user->copywritingRequests()->latest()->take(5)->get();
                    ?>

                    <?php if($recentRequests->isEmpty()): ?>
                        <p class="text-gray-500 text-sm italic">Belum ada aktivitas terbaru.</p>
                    <?php else: ?>
                        <div class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $recentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="py-4 flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo e(ucfirst($request->type)); ?> untuk
                                            <?php echo e(ucfirst($request->platform)); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo e($request->created_at->diffForHumans()); ?>

                                        </p>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo e(ucfirst($request->status)); ?>

                                    </span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\dashboard.blade.php ENDPATH**/ ?>