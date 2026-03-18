

<?php $__env->startSection('title', 'Competitor Analysis'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">🔍 Competitor Analysis</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau kompetitor & dapatkan insight untuk konten lebih baik</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Compare Button (Hidden by default) -->
            <button id="compareBtn" 
                    onclick="startComparison()" 
                    class="hidden px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span id="compareText">Compare Selected</span>
            </button>
            
            <!-- Toggle Compare Mode -->
            <button id="toggleCompareMode" 
                    onclick="toggleCompareMode()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <span id="toggleText">Compare Mode</span>
            </button>
            
            <a href="<?php echo e(route('competitor-analysis.create')); ?>" 
               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Kompetitor</span>
            </a>
        </div>
    </div>

    <!-- Compare Mode Instructions -->
    <div id="compareInstructions" class="hidden mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-center space-x-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-900">Mode Perbandingan Aktif</p>
                <p class="text-xs text-blue-700">Pilih 2-5 kompetitor untuk dibandingkan, lalu klik "Compare Selected"</p>
            </div>
        </div>
    </div>

    <!-- Alert Summary -->
    <?php if($unreadAlertsCount > 0): ?>
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-red-900"><?php echo e($unreadAlertsCount); ?> Alert Baru!</p>
                    <p class="text-xs text-red-700">Kompetitor ada aktivitas baru</p>
                </div>
            </div>
            <a href="<?php echo e(route('competitor-analysis.alerts')); ?>" 
               class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                Lihat Alert
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- 💰 Pricing Calculator Section -->
    <div class="mb-6 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl border border-green-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <span class="text-2xl mr-2">💰</span>
                    Pricing Calculator
                </h2>
                <p class="text-sm text-gray-600 mt-1">Hitung harga jual yang optimal berdasarkan modal & kompetitor</p>
            </div>
            <button onclick="togglePricingCalculator()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                <span id="calculatorToggleText">Buka Calculator</span>
            </button>
        </div>
        
        <!-- Pricing Calculator Form (Hidden by default) -->
        <div id="pricingCalculator" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Input Modal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modal Produk (Rp)</label>
                    <input type="number" id="modalCost" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="50000" min="1">
                </div>
                
                <!-- Target Profit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Profit (%)</label>
                    <input type="number" id="targetProfit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="30" min="1" max="500" value="30">
                </div>
                
                <!-- Competitor Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Kompetitor (Rp) <span class="text-xs text-gray-500">(opsional)</span></label>
                    <input type="number" id="competitorPrice" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="75000" min="0">
                </div>
                
                <!-- Calculate Button -->
                <div class="flex items-end">
                    <button onclick="calculatePricing()" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2 hidden" id="loadingSpinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span id="calculateButtonText">Hitung Harga</span>
                        </span>
                    </button>
                </div>
            </div>
            
            <!-- Results Section -->
            <div id="pricingResults" class="hidden">
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Hasil Analisis Harga</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Recommended Price -->
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <h4 class="font-semibold text-green-900 mb-2">💡 Harga Rekomendasi</h4>
                            <p class="text-2xl font-bold text-green-600" id="recommendedPrice">-</p>
                            <p class="text-sm text-green-700" id="recommendedReason">-</p>
                        </div>
                        
                        <!-- Break Even -->
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <h4 class="font-semibold text-blue-900 mb-2">⚖️ Break Even Point</h4>
                            <p class="text-2xl font-bold text-blue-600" id="breakEvenPrice">-</p>
                            <p class="text-sm text-blue-700">Harga minimal untuk BEP</p>
                        </div>
                        
                        <!-- Profit Margin -->
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                            <h4 class="font-semibold text-purple-900 mb-2">📈 Profit Margin</h4>
                            <p class="text-2xl font-bold text-purple-600" id="profitMargin">-</p>
                            <p class="text-sm text-purple-700">Keuntungan per unit</p>
                        </div>
                    </div>
                    
                    <!-- Pricing Strategy -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">🎯 Strategi Pricing</h4>
                        <div id="pricingStrategy" class="space-y-2">
                            <!-- Dynamic content will be inserted here -->
                        </div>
                    </div>
                    
                    <!-- Generated Caption -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">📝 Caption Promo Otomatis</h4>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div id="generatedCaption" class="text-gray-700 whitespace-pre-wrap">
                                <!-- Generated caption will appear here -->
                            </div>
                            <div class="mt-3 flex space-x-2">
                                <button onclick="copyCaption()" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                                    📋 Copy Caption
                                </button>
                                <button onclick="regenerateCaption()" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition">
                                    🔄 Generate Ulang
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pricing Table -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">📋 Tabel Harga untuk Posting</h4>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div id="pricingTable" class="text-gray-700">
                                <!-- Pricing table will appear here -->
                            </div>
                            <div class="mt-3">
                                <button onclick="copyPricingTable()" class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition">
                                    📋 Copy Tabel Harga
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Competitors List -->
    <?php if($competitors->isEmpty()): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <div class="max-w-md mx-auto">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Kompetitor</h3>
            <p class="text-sm text-gray-600 mb-6">Mulai pantau kompetitor untuk mendapatkan insight konten yang lebih baik</p>
            <a href="<?php echo e(route('competitor-analysis.create')); ?>" 
               class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Kompetitor Pertama
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $competitors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $competitor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition competitor-card" 
             data-competitor-id="<?php echo e($competitor->id); ?>"
             data-competitor-name="<?php echo e($competitor->username); ?>">
            
            <!-- Compare Checkbox (Hidden by default) -->
            <div class="compare-checkbox hidden mb-3">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" 
                           class="competitor-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                           value="<?php echo e($competitor->id); ?>"
                           onchange="updateCompareSelection()">
                    <span class="text-sm text-gray-700">Pilih untuk perbandingan</span>
                </label>
            </div>
            
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg">
                        <?php echo e(strtoupper(substr($competitor->username, 0, 1))); ?>

                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900"><?php echo e($competitor->username); ?></h3>
                        <div class="flex items-center space-x-2">
                            <p class="text-xs text-gray-500 capitalize"><?php echo e($competitor->platform); ?></p>
                            <?php if($competitor->category): ?>
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded"><?php echo e($competitor->category); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <span class="px-2 py-1 text-xs rounded-full <?php echo e($competitor->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'); ?>">
                    <?php echo e($competitor->is_active ? 'Active' : 'Inactive'); ?>

                </span>
            </div>

            <!-- Stats -->
            <?php if($competitor->latestSummary): ?>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 font-medium">Total Posts</p>
                    <p class="text-lg font-bold text-blue-900"><?php echo e($competitor->latestSummary->total_posts); ?></p>
                </div>
                <div class="bg-purple-50 rounded-lg p-3">
                    <p class="text-xs text-purple-600 font-medium">Avg Engagement</p>
                    <p class="text-lg font-bold text-purple-900"><?php echo e(number_format($competitor->latestSummary->avg_engagement_rate, 1)); ?>%</p>
                </div>
            </div>

            <!-- Posting Pattern -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-600 mb-1">Posting Pattern</p>
                <p class="text-sm font-medium text-gray-900"><?php echo e($competitor->latestSummary->posting_frequency ?? 0); ?> posts/week</p>
                <p class="text-xs text-gray-500">Best time: <?php echo e($competitor->latestSummary->best_posting_time ?? 'N/A'); ?></p>
            </div>
            <?php else: ?>
            <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-xs text-yellow-800">Belum ada data analisis</p>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
                <a href="<?php echo e(route('competitor-analysis.show', $competitor)); ?>" 
                   class="flex-1 px-4 py-2 bg-purple-600 text-white text-sm text-center rounded-lg hover:bg-purple-700 transition">
                    Lihat Detail
                </a>
                <form action="<?php echo e(route('competitor-analysis.refresh', $competitor)); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" 
                            class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                            title="Refresh Analysis">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <!-- Recent Alerts -->
    <?php if($recentAlerts->isNotEmpty()): ?>
    <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Alerts</h2>
            <a href="<?php echo e(route('competitor-analysis.alerts')); ?>" class="text-sm text-purple-600 hover:text-purple-700">
                Lihat Semua →
            </a>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-200">
            <?php $__currentLoopData = $recentAlerts->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="p-4 hover:bg-gray-50 transition <?php echo e($alert->is_read ? 'opacity-60' : ''); ?>">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($alert->alert_type === 'new_post' ? 'bg-blue-100 text-blue-700' : ($alert->alert_type === 'high_engagement' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700')); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $alert->alert_type))); ?>

                            </span>
                            <span class="text-xs text-gray-500"><?php echo e($alert->triggered_at->diffForHumans()); ?></span>
                        </div>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($alert->competitor->username); ?></p>
                        <p class="text-sm text-gray-600"><?php echo e($alert->alert_message); ?></p>
                    </div>
                    <?php if(!$alert->is_read): ?>
                    <form action="<?php echo e(route('competitor-analysis.alert.read', $alert)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-xs text-purple-600 hover:text-purple-700">
                            Mark Read
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
let compareMode = false;
let selectedCompetitors = [];

function toggleCompareMode() {
    compareMode = !compareMode;
    const toggleBtn = document.getElementById('toggleCompareMode');
    const toggleText = document.getElementById('toggleText');
    const compareBtn = document.getElementById('compareBtn');
    const instructions = document.getElementById('compareInstructions');
    const checkboxes = document.querySelectorAll('.compare-checkbox');
    
    if (compareMode) {
        // Enable compare mode
        toggleBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        toggleBtn.classList.add('bg-red-600', 'hover:bg-red-700');
        toggleText.textContent = 'Exit Compare';
        instructions.classList.remove('hidden');
        
        // Show checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.classList.remove('hidden');
        });
        
        // Add selection styling to cards
        document.querySelectorAll('.competitor-card').forEach(card => {
            card.classList.add('cursor-pointer', 'hover:border-blue-300');
        });
        
    } else {
        // Disable compare mode
        toggleBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        toggleBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        toggleText.textContent = 'Compare Mode';
        instructions.classList.add('hidden');
        compareBtn.classList.add('hidden');
        
        // Hide checkboxes and reset selections
        checkboxes.forEach(checkbox => {
            checkbox.classList.add('hidden');
        });
        
        document.querySelectorAll('.competitor-checkbox').forEach(cb => {
            cb.checked = false;
        });
        
        selectedCompetitors = [];
        
        // Remove selection styling
        document.querySelectorAll('.competitor-card').forEach(card => {
            card.classList.remove('cursor-pointer', 'hover:border-blue-300', 'border-blue-500', 'bg-blue-50');
        });
    }
}

function updateCompareSelection() {
    selectedCompetitors = [];
    const checkboxes = document.querySelectorAll('.competitor-checkbox:checked');
    
    checkboxes.forEach(cb => {
        selectedCompetitors.push({
            id: cb.value,
            name: cb.closest('.competitor-card').dataset.competitorName
        });
    });
    
    const compareBtn = document.getElementById('compareBtn');
    const compareText = document.getElementById('compareText');
    
    if (selectedCompetitors.length >= 2 && selectedCompetitors.length <= 5) {
        compareBtn.classList.remove('hidden');
        compareText.textContent = `Compare ${selectedCompetitors.length} Competitors`;
    } else {
        compareBtn.classList.add('hidden');
    }
    
    // Update card styling
    document.querySelectorAll('.competitor-card').forEach(card => {
        const checkbox = card.querySelector('.competitor-checkbox');
        if (checkbox && checkbox.checked) {
            card.classList.add('border-blue-500', 'bg-blue-50');
        } else {
            card.classList.remove('border-blue-500', 'bg-blue-50');
        }
    });
}

function startComparison() {
    if (selectedCompetitors.length < 2) {
        alert('Pilih minimal 2 kompetitor untuk dibandingkan');
        return;
    }
    
    if (selectedCompetitors.length > 5) {
        alert('Maksimal 5 kompetitor yang bisa dibandingkan');
        return;
    }
    
    // Create comparison URL with competitor IDs
    const competitorIds = selectedCompetitors.map(c => c.id).join(',');
    const comparisonUrl = `/competitor-analysis/compare?competitors=${competitorIds}`;
    
    // Redirect to comparison page
    window.location.href = comparisonUrl;
}

// Allow clicking on card to toggle checkbox in compare mode
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.competitor-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (compareMode && !e.target.closest('a') && !e.target.closest('button') && !e.target.closest('form')) {
                const checkbox = this.querySelector('.competitor-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    updateCompareSelection();
                }
            }
        });
    });
});

// 💰 PRICING CALCULATOR FUNCTIONS
function togglePricingCalculator() {
    const calculator = document.getElementById('pricingCalculator');
    const toggleText = document.getElementById('calculatorToggleText');
    
    if (calculator.classList.contains('hidden')) {
        calculator.classList.remove('hidden');
        toggleText.textContent = 'Tutup Calculator';
    } else {
        calculator.classList.add('hidden');
        toggleText.textContent = 'Buka Calculator';
    }
}

function calculatePricing() {
    const modalCost = parseFloat(document.getElementById('modalCost').value) || 0;
    const targetProfit = parseFloat(document.getElementById('targetProfit').value) || 30;
    const competitorPrice = parseFloat(document.getElementById('competitorPrice').value) || 0;
    
    if (modalCost <= 0) {
        alert('Mohon masukkan modal produk yang valid');
        return;
    }
    
    // Show loading state
    const calculateBtn = document.querySelector('button[onclick="calculatePricing()"]');
    const buttonText = document.getElementById('calculateButtonText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    calculateBtn.disabled = true;
    buttonText.textContent = 'Menghitung...';
    loadingSpinner.classList.remove('hidden');
    
    // Call API for advanced pricing analysis
    fetch('/api/competitor-analysis/calculate-pricing', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            modal_cost: modalCost,
            target_profit: targetProfit,
            competitor_price: competitorPrice > 0 ? competitorPrice : null,
            product_category: 'Umum', // Could be made dynamic
            target_market: 'middle' // Could be made dynamic
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayAdvancedPricingResults(data);
        } else {
            console.warn('API returned error, falling back to basic calculation:', data.error);
            // Fallback to basic calculation
            calculateBasicPricing(modalCost, targetProfit, competitorPrice);
        }
    })
    .catch(error => {
        console.error('API Error, falling back to basic calculation:', error);
        // Fallback to basic calculation
        calculateBasicPricing(modalCost, targetProfit, competitorPrice);
    })
    .finally(() => {
        // Reset button state
        calculateBtn.disabled = false;
        buttonText.textContent = 'Hitung Harga';
        loadingSpinner.classList.add('hidden');
    });
}

function displayAdvancedPricingResults(data) {
    const { pricing_analysis, ai_insights, promotional_content, break_even_price, profit_margin } = data;
    
    // Update basic results
    document.getElementById('recommendedPrice').textContent = formatRupiah(pricing_analysis.recommended_price);
    document.getElementById('recommendedReason').textContent = pricing_analysis.reason;
    document.getElementById('breakEvenPrice').textContent = formatRupiah(break_even_price);
    document.getElementById('profitMargin').textContent = formatRupiah(profit_margin.amount) + ` (${profit_margin.percentage.toFixed(1)}%)`;
    
    // Generate enhanced pricing strategy
    generateAdvancedPricingStrategy(pricing_analysis.strategy, ai_insights);
    
    // Generate AI-powered caption
    if (promotional_content.captions && promotional_content.captions.length > 0) {
        const randomCaption = promotional_content.captions[Math.floor(Math.random() * promotional_content.captions.length)];
        document.getElementById('generatedCaption').textContent = randomCaption;
    }
    
    // Generate enhanced pricing table
    generateAdvancedPricingTable(promotional_content.pricing_table);
    
    // Show results with animation
    const resultsSection = document.getElementById('pricingResults');
    resultsSection.classList.remove('hidden');
    resultsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Show success notification
    showNotification('✅ Analisis harga berhasil! AI telah menganalisis strategi pricing terbaik untuk bisnis Anda.', 'success');
}

function calculateBasicPricing(modalCost, targetProfit, competitorPrice) {
    // Original basic calculation logic
    const basePriceWithProfit = modalCost * (1 + targetProfit / 100);
    const breakEvenPrice = modalCost;
    
    let recommendedPrice, recommendedReason, strategy;
    
    if (competitorPrice > 0) {
        if (basePriceWithProfit <= competitorPrice * 0.8) {
            recommendedPrice = Math.round(competitorPrice * 0.85);
            recommendedReason = 'Harga kompetitif (15% lebih murah)';
            strategy = 'penetration';
        } else if (basePriceWithProfit <= competitorPrice * 0.95) {
            recommendedPrice = Math.round(competitorPrice * 0.92);
            recommendedReason = 'Harga sedikit lebih murah (8% lebih murah)';
            strategy = 'competitive';
        } else if (basePriceWithProfit <= competitorPrice * 1.1) {
            recommendedPrice = Math.round(basePriceWithProfit);
            recommendedReason = 'Harga sesuai target profit';
            strategy = 'value';
        } else {
            recommendedPrice = Math.round(basePriceWithProfit);
            recommendedReason = 'Premium pricing (fokus kualitas)';
            strategy = 'premium';
        }
    } else {
        recommendedPrice = Math.round(basePriceWithProfit);
        recommendedReason = 'Berdasarkan target profit';
        strategy = 'standard';
    }
    
    const profitAmount = recommendedPrice - modalCost;
    const profitPercentage = ((profitAmount / modalCost) * 100).toFixed(1);
    
    // Update UI
    document.getElementById('recommendedPrice').textContent = formatRupiah(recommendedPrice);
    document.getElementById('recommendedReason').textContent = recommendedReason;
    document.getElementById('breakEvenPrice').textContent = formatRupiah(breakEvenPrice);
    document.getElementById('profitMargin').textContent = formatRupiah(profitAmount) + ` (${profitPercentage}%)`;
    
    // Generate basic pricing strategy
    generatePricingStrategy(strategy, recommendedPrice, competitorPrice, modalCost);
    
    // Generate basic caption
    generatePricingCaption(recommendedPrice, modalCost, strategy);
    
    // Generate basic pricing table
    generatePricingTable(recommendedPrice, modalCost);
    
    // Show results
    document.getElementById('pricingResults').classList.remove('hidden');
}

function generateAdvancedPricingStrategy(strategy, aiInsights) {
    const strategyContainer = document.getElementById('pricingStrategy');
    let strategies = [];
    
    if (aiInsights && aiInsights.pricing_strategy) {
        strategies = aiInsights.pricing_strategy.map(s => `🎯 ${s}`);
    } else {
        // Fallback to basic strategy
        switch (strategy) {
            case 'penetration':
                strategies = [
                    '🎯 Strategi Penetrasi Pasar - Harga rendah untuk menarik customer',
                    '📈 Fokus volume penjualan tinggi',
                    '💡 Highlight value for money di marketing',
                    '⚡ Promo "Harga Terbaik Se-Kota" atau "Dijamin Termurah"'
                ];
                break;
            case 'competitive':
                strategies = [
                    '⚖️ Strategi Kompetitif - Harga seimbang dengan kompetitor',
                    '🎯 Fokus diferensiasi produk/service',
                    '💡 Highlight keunggulan unik (kualitas, service, garansi)',
                    '🎁 Tambahkan bonus/bundling untuk added value'
                ];
                break;
            case 'premium':
                strategies = [
                    '👑 Strategi Premium - Harga tinggi, kualitas superior',
                    '✨ Fokus branding eksklusif dan prestige',
                    '💎 Highlight kualitas premium dan craftsmanship',
                    '🏆 Target customer yang mengutamakan kualitas'
                ];
                break;
            default:
                strategies = [
                    '📊 Strategi Standard - Harga berdasarkan cost + profit',
                    '🎯 Fokus value proposition yang jelas',
                    '💡 Highlight benefit dan keunggulan produk',
                    '📈 Monitor kompetitor untuk adjustment'
                ];
        }
    }
    
    strategyContainer.innerHTML = strategies.map(s => `<div class="flex items-start space-x-2"><span class="text-green-600">•</span><span class="text-sm text-gray-700">${s}</span></div>`).join('');
}

function generateAdvancedPricingTable(pricingTableData) {
    if (!pricingTableData) {
        generatePricingTable(0, 0); // Fallback
        return;
    }
    
    const table = `📋 DAFTAR HARGA 📋

🛍️ Beli 1 pcs: ${formatRupiah(pricingTableData.qty_1)}
🛍️ Beli 3 pcs: ${formatRupiah(pricingTableData.qty_3)}/pcs (Hemat 5%!)
🛍️ Beli 5 pcs: ${formatRupiah(pricingTableData.qty_5)}/pcs (Hemat 10%!)
🛍️ Beli 10 pcs: ${formatRupiah(pricingTableData.qty_10)}/pcs (Hemat 15%!)

💰 Semakin banyak, semakin hemat!
📞 Chat untuk order & info lebih lanjut

#DaftarHarga #Grosir #SemakinBanyakSemakinHemat`;
    
    document.getElementById('pricingTable').textContent = table;
}

function generatePricingStrategy(strategy, price, competitorPrice, modalCost) {
    const strategyContainer = document.getElementById('pricingStrategy');
    let strategies = [];
    
    switch (strategy) {
        case 'penetration':
            strategies = [
                '🎯 Strategi Penetrasi Pasar - Harga rendah untuk menarik customer',
                '📈 Fokus volume penjualan tinggi',
                '💡 Highlight value for money di marketing',
                '⚡ Promo "Harga Terbaik Se-Kota" atau "Dijamin Termurah"'
            ];
            break;
        case 'competitive':
            strategies = [
                '⚖️ Strategi Kompetitif - Harga seimbang dengan kompetitor',
                '🎯 Fokus diferensiasi produk/service',
                '💡 Highlight keunggulan unik (kualitas, service, garansi)',
                '🎁 Tambahkan bonus/bundling untuk added value'
            ];
            break;
        case 'premium':
            strategies = [
                '👑 Strategi Premium - Harga tinggi, kualitas superior',
                '✨ Fokus branding eksklusif dan prestige',
                '💎 Highlight kualitas premium dan craftsmanship',
                '🏆 Target customer yang mengutamakan kualitas'
            ];
            break;
        default:
            strategies = [
                '📊 Strategi Standard - Harga berdasarkan cost + profit',
                '🎯 Fokus value proposition yang jelas',
                '💡 Highlight benefit dan keunggulan produk',
                '📈 Monitor kompetitor untuk adjustment'
            ];
    }
    
    strategyContainer.innerHTML = strategies.map(s => `<div class="flex items-start space-x-2"><span class="text-green-600">•</span><span class="text-sm text-gray-700">${s}</span></div>`).join('');
}

function generatePricingCaption(price, modalCost, strategy) {
    const templates = {
        penetration: [
            `🔥 PROMO SPESIAL! 🔥\n\nHarga cuma ${formatRupiah(price)}! Dijamin termurah se-kota! 💰\n\nKualitas premium, harga bersahabat. Jangan sampai kehabisan!\n\n#PromoSpesial #HargaTerbaik #Murah #Berkualitas`,
            `💥 FLASH SALE ALERT! 💥\n\n${formatRupiah(price)} aja! Harga segini mana ada lagi? 😱\n\nBuruan order sebelum harga naik!\n\n#FlashSale #HargaMurah #JanganSampaiTelat`
        ],
        competitive: [
            `✨ Kualitas Premium, Harga Bersaing! ✨\n\nCuma ${formatRupiah(price)} untuk kualitas terbaik! 🏆\n\nBandingkan dengan yang lain, pasti pilih kita!\n\n#KualitasTerbaik #HargaBersaing #Recommended`,
            `🎯 Best Value for Money! 🎯\n\n${formatRupiah(price)} = Kualitas + Service terbaik! 💯\n\nSekali beli, pasti balik lagi!\n\n#BestValue #Berkualitas #TrustedSeller`
        ],
        premium: [
            `👑 Premium Quality, Premium Experience 👑\n\n${formatRupiah(price)} untuk yang terbaik dari yang terbaik! ✨\n\nInvestasi terbaik untuk kualitas yang tak tertandingi.\n\n#Premium #Exclusive #TopQuality #WorthIt`,
            `💎 Luxury at Its Finest 💎\n\n${formatRupiah(price)} - Harga untuk yang mengerti kualitas sejati! 🏆\n\nTidak semua orang bisa memiliki yang terbaik.\n\n#Luxury #Exclusive #PremiumQuality`
        ],
        standard: [
            `🛍️ Produk Berkualitas, Harga Terjangkau! 🛍️\n\nCuma ${formatRupiah(price)}! Kualitas terjamin, harga bersahabat! 😊\n\nOrder sekarang, stock terbatas!\n\n#Berkualitas #Terjangkau #StockTerbatas`,
            `📦 Ready Stock! 📦\n\n${formatRupiah(price)} untuk produk pilihan! ✅\n\nKualitas oke, harga pas di kantong!\n\n#ReadyStock #KualitasOke #HargaPas`
        ]
    };
    
    const captionOptions = templates[strategy] || templates.standard;
    const randomCaption = captionOptions[Math.floor(Math.random() * captionOptions.length)];
    
    document.getElementById('generatedCaption').textContent = randomCaption;
}

function generatePricingTable(price, modalCost) {
    const profit = price - modalCost;
    const profitPercentage = ((profit / modalCost) * 100).toFixed(1);
    
    // Generate quantity-based pricing
    const qty1 = price;
    const qty3 = Math.round(price * 0.95); // 5% discount
    const qty5 = Math.round(price * 0.90); // 10% discount
    const qty10 = Math.round(price * 0.85); // 15% discount
    
    const table = `📋 DAFTAR HARGA 📋

🛍️ Beli 1 pcs: ${formatRupiah(qty1)}
🛍️ Beli 3 pcs: ${formatRupiah(qty3)}/pcs (Hemat 5%!)
🛍️ Beli 5 pcs: ${formatRupiah(qty5)}/pcs (Hemat 10%!)
🛍️ Beli 10 pcs: ${formatRupiah(qty10)}/pcs (Hemat 15%!)

💰 Semakin banyak, semakin hemat!
📞 Chat untuk order & info lebih lanjut

#DaftarHarga #Grosir #SemakinBanyakSemakinHemat`;
    
    document.getElementById('pricingTable').textContent = table;
}

function regenerateCaption() {
    const modalCost = parseFloat(document.getElementById('modalCost').value) || 0;
    const recommendedPrice = parseFloat(document.getElementById('recommendedPrice').textContent.replace(/[^\d]/g, '')) || 0;
    const competitorPrice = parseFloat(document.getElementById('competitorPrice').value) || 0;
    
    let strategy = 'standard';
    const basePriceWithProfit = modalCost * 1.3;
    
    if (competitorPrice > 0) {
        if (recommendedPrice <= competitorPrice * 0.8) strategy = 'penetration';
        else if (recommendedPrice <= competitorPrice * 0.95) strategy = 'competitive';
        else if (recommendedPrice > competitorPrice * 1.1) strategy = 'premium';
        else strategy = 'competitive';
    }
    
    generatePricingCaption(recommendedPrice, modalCost, strategy);
}

function copyCaption() {
    const caption = document.getElementById('generatedCaption').textContent;
    navigator.clipboard.writeText(caption).then(() => {
        showNotification('✅ Caption berhasil di-copy ke clipboard!', 'success');
    }).catch(() => {
        showNotification('❌ Gagal copy caption. Silakan copy manual.', 'error');
    });
}

function copyPricingTable() {
    const table = document.getElementById('pricingTable').textContent;
    navigator.clipboard.writeText(table).then(() => {
        showNotification('✅ Tabel harga berhasil di-copy ke clipboard!', 'success');
    }).catch(() => {
        showNotification('❌ Gagal copy tabel harga. Silakan copy manual.', 'error');
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 
        'bg-blue-600'
    }`;
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

function formatRupiah(number) {
    return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\client\competitor-analysis\index.blade.php ENDPATH**/ ?>