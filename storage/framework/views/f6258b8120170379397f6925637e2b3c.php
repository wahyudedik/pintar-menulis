

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang, <?php echo e(auth()->user()->name); ?></p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total Orders</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo e($stats['total_orders']); ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Active</p>
                    <p class="text-2xl font-semibold text-green-600"><?php echo e($stats['active_orders']); ?></p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Pending</p>
                    <p class="text-2xl font-semibold text-yellow-600"><?php echo e($stats['pending_requests']); ?></p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Completed</p>
                    <p class="text-2xl font-semibold text-blue-600"><?php echo e($stats['completed_requests']); ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 Enhanced Analytics Dashboard -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">📊 Content Performance Overview</h2>
            <div class="flex gap-2">
                <a href="<?php echo e(route('analytics.index')); ?>" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                    📈 Full Analytics
                </a>
                <button onclick="refreshDashboardAnalytics()" class="px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition">
                    🔄 Refresh
                </button>
            </div>
        </div>

        <!-- Performance Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Best Performing Content -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg border border-green-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-green-900">🏆 Best Performer</h3>
                    <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">This Week</span>
                </div>
                
                <div class="space-y-3">
                    <div class="bg-white rounded-lg p-3 border border-green-100">
                        <p class="text-sm text-gray-800 line-clamp-2" id="dashBestCaption">
                            <?php echo e($dashboardAnalytics['best_caption'] ?? '🔥 Flash Sale 50% OFF! Jangan sampai kehabisan, stock terbatas!'); ?>

                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600" id="dashBestEngagement"><?php echo e($dashboardAnalytics['best_engagement'] ?? '8.5'); ?>%</div>
                            <div class="text-green-700">Engagement</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600" id="dashBestReach"><?php echo e(number_format($dashboardAnalytics['best_reach'] ?? 5200)); ?></div>
                            <div class="text-green-700">Reach</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Optimal Posting Time -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-blue-900">⏰ Optimal Time</h3>
                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">AI Analyzed</span>
                </div>
                
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600" id="dashOptimalTime"><?php echo e($dashboardAnalytics['optimal_time'] ?? '19:00'); ?></div>
                        <div class="text-blue-700 text-sm">Peak Hour</div>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-800">Best Day:</span>
                            <span class="font-semibold text-blue-900" id="dashBestDay"><?php echo e($dashboardAnalytics['best_day'] ?? 'Tuesday'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-800">Avg Response:</span>
                            <span class="font-semibold text-blue-900" id="dashAvgResponse"><?php echo e($dashboardAnalytics['avg_response'] ?? '2.5h'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROI Summary -->
            <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-lg border border-purple-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-purple-900">💰 ROI Summary</h3>
                    <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">Last 30 Days</span>
                </div>
                
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600" id="dashAvgROI"><?php echo e($dashboardAnalytics['avg_roi'] ?? '245'); ?>%</div>
                        <div class="text-purple-700 text-sm">Average ROI</div>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-purple-800">Total Sales:</span>
                            <span class="font-semibold text-purple-900" id="dashTotalSales"><?php echo e($dashboardAnalytics['total_sales'] ?? 'Rp 15.2M'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-purple-800">Marketing Cost:</span>
                            <span class="font-semibold text-purple-900" id="dashMarketingCost"><?php echo e($dashboardAnalytics['marketing_cost'] ?? 'Rp 4.8M'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Insights & Recommendations -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Content Performance Insights -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Performance Insights</h3>
                
                <div class="space-y-4">
                    <!-- Platform Performance -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Platform Performance</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-pink-500 mr-2">📱</span>
                                    <span class="text-sm text-gray-600">Instagram</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-pink-500 h-2 rounded-full" style="width: <?php echo e($platformPerformance['instagram'] ?? 85); ?>%"></div>
                                    </div>
                                    <span class="text-sm font-medium"><?php echo e($platformPerformance['instagram'] ?? 85); ?>%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-blue-500 mr-2">📘</span>
                                    <span class="text-sm text-gray-600">Facebook</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo e($platformPerformance['facebook'] ?? 72); ?>%"></div>
                                    </div>
                                    <span class="text-sm font-medium"><?php echo e($platformPerformance['facebook'] ?? 72); ?>%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-black mr-2">🎵</span>
                                    <span class="text-sm text-gray-600">TikTok</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-black h-2 rounded-full" style="width: <?php echo e($platformPerformance['tiktok'] ?? 68); ?>%"></div>
                                    </div>
                                    <span class="text-sm font-medium"><?php echo e($platformPerformance['tiktok'] ?? 68); ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Type Performance -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Content Type Performance</h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-green-50 rounded-lg p-3">
                                <div class="text-green-900 font-medium">Promo Posts</div>
                                <div class="text-green-700 text-lg font-bold">+42%</div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-3">
                                <div class="text-blue-900 font-medium">Educational</div>
                                <div class="text-blue-700 text-lg font-bold">+28%</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-3">
                                <div class="text-purple-900 font-medium">Behind Scenes</div>
                                <div class="text-purple-700 text-lg font-bold">+35%</div>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-3">
                                <div class="text-orange-900 font-medium">User Generated</div>
                                <div class="text-orange-700 text-lg font-bold">+51%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Recommendations -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🤖 AI Recommendations</h3>
                
                <div class="space-y-4">
                    <!-- Immediate Actions -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                            <span class="text-red-500 mr-2">🚨</span>
                            Immediate Actions
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start">
                                <span class="text-red-500 mr-2 mt-0.5">•</span>
                                <span class="text-gray-700">Post pada <strong>Selasa 19:00 WIB</strong> untuk engagement maksimal</span>
                            </div>
                            <div class="flex items-start">
                                <span class="text-red-500 mr-2 mt-0.5">•</span>
                                <span class="text-gray-700">Gunakan hashtag <strong>#PromoSpesial #Terbatas</strong> untuk boost reach</span>
                            </div>
                            <div class="flex items-start">
                                <span class="text-red-500 mr-2 mt-0.5">•</span>
                                <span class="text-gray-700">Fokus konten <strong>User Generated Content</strong> (+51% engagement)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Strategy -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                            <span class="text-blue-500 mr-2">📅</span>
                            Weekly Strategy
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start">
                                <span class="text-blue-500 mr-2 mt-0.5">•</span>
                                <span class="text-gray-700">Senin: Educational content (tips & tricks)</span>
                            </div>
                            <div class="flex items-start">
                                <span class="text-blue-500 mr-2 mt-0.5">•</span>
                                <span class="text-gray-700">Selasa: Promo/Sales content (peak performance)</span>
                            </div>
                            <div class="flex items-start">
                                <span class="text-blue-500 mr-2 mt-0.5">•</span>
                                <span class="text-gray-700">Rabu: Behind-the-scenes content</span>
                            </div>
                            <div class="flex items-start">
                                <span class="text-blue-500 mr-2 mt-0.5">•</span>
                                <span class="text-gray-700">Kamis: User testimonials & reviews</span>
                            </div>
                            <div class="flex items-start">
                                <span class="text-blue-500 mr-2 mt-0.5">•</span>
                                <span class="text-gray-700">Jumat: Interactive content (polls, Q&A)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Growth Opportunities -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                            <span class="text-green-500 mr-2">🚀</span>
                            Growth Opportunities
                        </h4>
                        <div class="bg-green-50 rounded-lg p-3">
                            <div class="text-sm text-green-800 space-y-1">
                                <div>• Increase TikTok posting frequency (+68% potential)</div>
                                <div>• Collaborate with micro-influencers</div>
                                <div>• Create video content series</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-200 p-6">
            <h3 class="text-lg font-semibold text-indigo-900 mb-4">⚡ Quick Actions</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="<?php echo e(route('ai.generator')); ?>" class="bg-white rounded-lg p-4 border border-indigo-200 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl">🤖</span>
                        <svg class="w-4 h-4 text-indigo-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-indigo-900">Generate Content</div>
                    <div class="text-xs text-indigo-700">Create AI-powered captions</div>
                </a>

                <a href="<?php echo e(route('analytics.index')); ?>" class="bg-white rounded-lg p-4 border border-indigo-200 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl">📊</span>
                        <svg class="w-4 h-4 text-indigo-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-indigo-900">View Analytics</div>
                    <div class="text-xs text-indigo-700">Deep dive into performance</div>
                </a>

                <a href="<?php echo e(route('competitor-analysis.index')); ?>" class="bg-white rounded-lg p-4 border border-indigo-200 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl">🔍</span>
                        <svg class="w-4 h-4 text-indigo-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-indigo-900">Competitor Analysis</div>
                    <div class="text-xs text-indigo-700">Monitor competitors</div>
                </a>

                <button onclick="generateOptimalContent()" class="bg-white rounded-lg p-4 border border-indigo-200 hover:shadow-md transition group text-left">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl">✨</span>
                        <svg class="w-4 h-4 text-indigo-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-indigo-900">Optimal Content</div>
                    <div class="text-xs text-indigo-700">AI-suggested content now</div>
                </button>
            </div>
        </div>
    </div>

    <!-- Active Packages -->
    <?php if($activeOrders->count() > 0): ?>
    <div class="bg-white rounded-lg border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Paket Aktif</h2>
        </div>
        <div class="p-4 space-y-4">
            <?php $__currentLoopData = $activeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900"><?php echo e($order->package->name); ?></h3>
                        <p class="text-xs text-gray-500 mt-1">Berlaku hingga <?php echo e($order->end_date->format('d M Y')); ?></p>
                    </div>
                    <a href="<?php echo e(route('copywriting.create', $order)); ?>" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        Request
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Caption Quota</p>
                        <p class="text-sm font-semibold text-gray-900"><?php echo e($order->remaining_caption_quota); ?> / <?php echo e($order->package->caption_quota); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Product Desc Quota</p>
                        <p class="text-sm font-semibold text-gray-900"><?php echo e($order->remaining_product_description_quota); ?> / <?php echo e($order->package->product_description_quota); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Requests -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Request Terbaru</h2>
        </div>
        <?php if($recentRequests->count() > 0): ?>
        <div class="divide-y divide-gray-200">
            <?php $__currentLoopData = $recentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('copywriting.show', $request)); ?>" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900"><?php echo e(ucfirst($request->type)); ?></span>
                            <span class="px-2 py-1 text-xs rounded-full
                                <?php if($request->status === 'completed'): ?> bg-green-100 text-green-700
                                <?php elseif($request->status === 'in_progress'): ?> bg-blue-100 text-blue-700
                                <?php elseif($request->status === 'pending'): ?> bg-yellow-100 text-yellow-700
                                <?php else: ?> bg-gray-100 text-gray-700
                                <?php endif; ?>">
                                <?php echo e(ucfirst($request->status)); ?>

                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1"><?php echo e($request->platform); ?></p>
                        <p class="text-xs text-gray-400 mt-1"><?php echo e($request->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="p-4 border-t border-gray-200 text-center">
            <a href="<?php echo e(route('copywriting.index')); ?>" class="text-sm text-blue-600 hover:text-blue-700">
                Lihat Semua
            </a>
        </div>
        <?php else: ?>
        <div class="p-8 text-center">
            <p class="text-sm text-gray-500">Belum ada request copywriting</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Banner Popup -->
<?php if (isset($component)) { $__componentOriginal411849fa56991479085a8247fc6f96f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal411849fa56991479085a8247fc6f96f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.banner-popup','data' => ['type' => 'client']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banner-popup'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'client']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal411849fa56991479085a8247fc6f96f5)): ?>
<?php $attributes = $__attributesOriginal411849fa56991479085a8247fc6f96f5; ?>
<?php unset($__attributesOriginal411849fa56991479085a8247fc6f96f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal411849fa56991479085a8247fc6f96f5)): ?>
<?php $component = $__componentOriginal411849fa56991479085a8247fc6f96f5; ?>
<?php unset($__componentOriginal411849fa56991479085a8247fc6f96f5); ?>
<?php endif; ?>

<script>
    // 📊 Dashboard Analytics Functions
    function refreshDashboardAnalytics() {
        const refreshBtn = document.querySelector('button[onclick="refreshDashboardAnalytics()"]');
        const originalText = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '⏳ Refreshing...';
        refreshBtn.disabled = true;

        // Simulate API call to refresh dashboard analytics
        fetch('/api/dashboard/analytics-refresh', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update dashboard analytics data
                updateDashboardData(data.analytics);
                
                // Show success message
                showNotification('✅ Analytics data refreshed successfully!', 'success');
            } else {
                showNotification('❌ Failed to refresh analytics data', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('❌ Network error occurred', 'error');
        })
        .finally(() => {
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
        });
    }

    function updateDashboardData(analytics) {
        // Update best performer data
        if (analytics.best_caption) {
            document.getElementById('dashBestCaption').textContent = analytics.best_caption;
        }
        if (analytics.best_engagement) {
            document.getElementById('dashBestEngagement').textContent = analytics.best_engagement + '%';
        }
        if (analytics.best_reach) {
            document.getElementById('dashBestReach').textContent = formatNumber(analytics.best_reach);
        }

        // Update optimal time data
        if (analytics.optimal_time) {
            document.getElementById('dashOptimalTime').textContent = analytics.optimal_time;
        }
        if (analytics.best_day) {
            document.getElementById('dashBestDay').textContent = analytics.best_day;
        }
        if (analytics.avg_response) {
            document.getElementById('dashAvgResponse').textContent = analytics.avg_response;
        }

        // Update ROI data
        if (analytics.avg_roi) {
            document.getElementById('dashAvgROI').textContent = analytics.avg_roi + '%';
        }
        if (analytics.total_sales) {
            document.getElementById('dashTotalSales').textContent = analytics.total_sales;
        }
        if (analytics.marketing_cost) {
            document.getElementById('dashMarketingCost').textContent = analytics.marketing_cost;
        }
    }

    function generateOptimalContent() {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '⏳ Generating...';
        button.disabled = true;

        // Call AI to generate optimal content based on current analytics
        fetch('/api/ai/generate-optimal-content', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                use_analytics: true,
                content_type: 'optimal_timing'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show generated content in modal or redirect to AI generator
                showOptimalContentModal(data.content);
            } else {
                showNotification('❌ Failed to generate optimal content', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('❌ Network error occurred', 'error');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }

    function showOptimalContentModal(content) {
        // Create and show modal with generated content
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">✨ AI-Generated Optimal Content</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                        <h4 class="font-medium text-blue-900 mb-2">📈 Optimized for Your Best Performance Time</h4>
                        <p class="text-sm text-blue-800">Generated based on your analytics: Best day (Tuesday), Optimal time (19:00 WIB)</p>
                    </div>
                    
                    <div class="space-y-4">
                        ${content.captions ? content.captions.map(caption => `
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">${caption.platform}</span>
                                    <button onclick="copyToClipboard('${caption.text.replace(/'/g, "\\'")}', this)" class="text-xs text-blue-600 hover:text-blue-700">📋 Copy</button>
                                </div>
                                <p class="text-sm text-gray-900 mb-2">${caption.text}</p>
                                <div class="text-xs text-gray-500">
                                    Expected engagement: <span class="font-medium text-green-600">${caption.expected_engagement}%</span>
                                </div>
                            </div>
                        `).join('') : '<p class="text-gray-500">No content generated</p>'}
                    </div>
                    
                    <div class="mt-6 flex gap-3">
                        <a href="${window.location.origin}/ai-generator" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition text-center">
                            🚀 Generate More in AI Generator
                        </a>
                        <button onclick="this.closest('.fixed').remove()" class="px-6 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    function copyToClipboard(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = button.textContent;
            button.textContent = '✅ Copied!';
            button.classList.add('text-green-600');
            
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('text-green-600');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            showNotification('❌ Failed to copy to clipboard', 'error');
        });
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg text-white text-sm font-medium transition-all duration-300 transform translate-x-full`;
        
        if (type === 'success') {
            notification.classList.add('bg-green-600');
        } else if (type === 'error') {
            notification.classList.add('bg-red-600');
        } else {
            notification.classList.add('bg-blue-600');
        }
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    // Auto-refresh dashboard data every 10 minutes
    setInterval(() => {
        // Silent refresh without showing loading state
        fetch('/api/dashboard/analytics-refresh', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDashboardData(data.analytics);
            }
        })
        .catch(error => {
            // Silent fail - non-critical refresh
        });
    }, 600000); // 10 minutes

    // Initialize dashboard on page load
    document.addEventListener('DOMContentLoaded', function() {
        
        // Add smooth animations to cards
        const cards = document.querySelectorAll('.bg-white, .bg-gradient-to-br');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/dashboard/client.blade.php ENDPATH**/ ?>