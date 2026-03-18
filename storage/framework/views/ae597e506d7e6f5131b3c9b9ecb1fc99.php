<?php if (isset($component)) { $__componentOriginal42b37f006f8ebbe12b66cfa27a5def06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06 = $attributes; } ?>
<?php $component = App\View\Components\PublicLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PublicLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
            <a href="<?php echo e(route('packages.index')); ?>" class="hover:text-blue-600">Paket</a>
            <span>/</span>
            <span class="text-gray-900"><?php echo e($package->name); ?></span>
        </div>

        <div class="bg-white rounded-2xl border-2 <?php echo e($package->name === 'Professional' ? 'border-blue-600' : 'border-gray-200'); ?> overflow-hidden">
            <?php if($package->name === 'Professional'): ?>
            <div class="bg-blue-600 text-white text-center py-2 text-sm font-semibold">
                PALING POPULER
            </div>
            <?php endif; ?>

            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900"><?php echo e($package->name); ?></h1>
                        <p class="text-gray-600 mt-2"><?php echo e($package->description); ?></p>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-bold text-gray-900">Rp <?php echo e(number_format($package->price, 0, ',', '.')); ?></div>
                        <div class="text-gray-500 text-sm">/bulan</div>
                        <?php if($package->yearly_price): ?>
                        <div class="text-sm text-green-600 mt-1">Rp <?php echo e(number_format($package->yearly_price, 0, ',', '.')); ?>/tahun</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Features -->
                <div class="border-t border-gray-200 pt-6 mb-8">
                    <h2 class="text-sm font-semibold text-gray-700 mb-4">Yang Kamu Dapatkan</h2>
                    <ul class="space-y-3">
                        <?php if($package->ai_quota_monthly): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700"><?php echo e(number_format($package->ai_quota_monthly)); ?> AI generation per bulan</span>
                        </li>
                        <?php endif; ?>
                        <?php if($package->caption_quota): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700"><?php echo e($package->caption_quota); ?> caption media sosial</span>
                        </li>
                        <?php endif; ?>
                        <?php if($package->product_description_quota): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700"><?php echo e($package->product_description_quota); ?> deskripsi produk</span>
                        </li>
                        <?php endif; ?>
                        <?php if(isset($package->revision_limit)): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Revisi <?php echo e($package->revision_limit); ?>x</span>
                        </li>
                        <?php endif; ?>
                        <?php if(isset($package->response_time_hours)): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Response time <?php echo e($package->response_time_hours); ?> jam</span>
                        </li>
                        <?php endif; ?>
                        <?php if(!empty($package->consultation_included)): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Konsultasi strategi konten</span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- CTA -->
                <div class="flex items-center space-x-4">
                    <a href="<?php echo e(route('pricing')); ?>"
                       class="flex-1 text-center bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                        Pilih Paket Ini
                    </a>
                    <a href="<?php echo e(route('packages.index')); ?>"
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition text-sm">
                        Lihat Semua Paket
                    </a>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06)): ?>
<?php $attributes = $__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06; ?>
<?php unset($__attributesOriginal42b37f006f8ebbe12b66cfa27a5def06); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal42b37f006f8ebbe12b66cfa27a5def06)): ?>
<?php $component = $__componentOriginal42b37f006f8ebbe12b66cfa27a5def06; ?>
<?php unset($__componentOriginal42b37f006f8ebbe12b66cfa27a5def06); ?>
<?php endif; ?>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\packages\show.blade.php ENDPATH**/ ?>