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
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pesanan #<?php echo e($order->id); ?> - <?php echo e($order->package->name); ?>

            </h2>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                    <?php echo e(ucfirst($order->status)); ?>

                </span>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info Utama -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Berlangganan</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Mulai</p>
                                    <p class="font-medium"><?php echo e($order->start_date->format('d M Y')); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Berakhir</p>
                                    <p class="font-medium"><?php echo e($order->end_date->format('d M Y')); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Total Harga</p>
                                    <p class="font-medium">Rp <?php echo e(number_format($order->package->price, 0, ',', '.')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Permintaan Copywriting</h3>
                                <a href="<?php echo e(route('copywriting.create', $order)); ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Request Baru
                                </a>
                            </div>
                            
                            <?php if($order->copywritingRequests->isEmpty()): ?>
                                <p class="text-gray-500 text-sm text-center py-8 italic">Belum ada permintaan copywriting.</p>
                            <?php else: ?>
                                <div class="space-y-4">
                                    <?php $__currentLoopData = $order->copywritingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-bold text-gray-900"><?php echo e(ucfirst($request->type)); ?> - <?php echo e(ucfirst($request->platform)); ?></h4>
                                                    <p class="text-xs text-gray-500 mt-1"><?php echo e($request->created_at->format('d M Y H:i')); ?></p>
                                                </div>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    <?php echo e(ucfirst($request->status)); ?>

                                                </span>
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <a href="<?php echo e(route('copywriting.show', $request)); ?>" class="text-sm text-blue-600 hover:text-blue-900">Detail & Hasil</a>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Quota -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Sisa Kuota</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Caption</span>
                                        <span class="font-bold text-gray-900"><?php echo e($order->remaining_caption_quota); ?> / <?php echo e($order->package->caption_quota); ?></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e(($order->remaining_caption_quota / $order->package->caption_quota) * 100); ?>%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Deskripsi Produk</span>
                                        <span class="font-bold text-gray-900"><?php echo e($order->remaining_product_description_quota); ?> / <?php echo e($order->package->product_description_quota); ?></span>
                                    </div>
                                    <?php if($order->package->product_description_quota > 0): ?>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e(($order->remaining_product_description_quota / $order->package->product_description_quota) * 100); ?>%"></div>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-xs text-gray-400 italic">Tidak termasuk dalam paket ini.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
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
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\orders\show.blade.php ENDPATH**/ ?>