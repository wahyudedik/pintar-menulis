

<?php $__env->startSection('head'); ?>
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo e($article->seo_description); ?>">
    <meta name="keywords" content="<?php echo e($article->seo_keywords); ?>">
    <meta name="canonical" href="<?php echo e($article->canonical_url); ?>">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?php echo e($article->title); ?>">
    <meta property="og:description" content="<?php echo e($article->seo_description); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo e($article->canonical_url); ?>">
    <meta property="og:image" content="<?php echo e(asset('images/article-default.jpg')); ?>">
    <meta property="og:site_name" content="<?php echo e(config('app.name')); ?>">
    <meta property="article:published_time" content="<?php echo e($article->created_at->toIso8601String()); ?>">
    <meta property="article:modified_time" content="<?php echo e($article->updated_at->toIso8601String()); ?>">
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($article->title); ?>">
    <meta name="twitter:description" content="<?php echo e($article->seo_description); ?>">
    <meta name="twitter:image" content="<?php echo e(asset('images/article-default.jpg')); ?>">
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
        <?php echo json_encode(\App\Helpers\SeoHelper::generateArticleSchema($article)); ?>

    </script>
    
    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        <?php echo json_encode(\App\Helpers\SeoHelper::generateBreadcrumbSchema($article)); ?>

    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back Link -->
        <a href="<?php echo e(route('articles.index')); ?>" class="text-blue-600 hover:text-blue-800 font-semibold mb-6 inline-block">
            ← Back to Articles
        </a>

        <!-- Ad Placement: Top -->
        <?php if($adTopCode = \App\Models\AdPlacement::getAdCode('article_detail_top')): ?>
            <div class="mb-8 p-4 bg-gray-100 rounded-lg border border-gray-300">
                <div class="text-xs text-gray-500 mb-2">Advertisement</div>
                <?php echo $adTopCode; ?>

            </div>
        <?php endif; ?>

        <!-- Article Header -->
        <article class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-8">
                <!-- Category & Industry Badges -->
                <div class="mb-6 flex gap-2">
                    <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full
                        <?php if($article->category === 'industry'): ?> bg-blue-100 text-blue-800
                        <?php elseif($article->category === 'tips'): ?> bg-green-100 text-green-800
                        <?php elseif($article->category === 'quote'): ?> bg-purple-100 text-purple-800
                        <?php else: ?> bg-gray-100 text-gray-800
                        <?php endif; ?>">
                        <?php echo e(ucfirst($article->category)); ?>

                    </span>
                    <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                        <?php echo e(ucfirst($article->industry)); ?>

                    </span>
                </div>

                <!-- Title -->
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    <?php echo e($article->title); ?>

                </h1>

                <!-- Meta Information -->
                <div class="flex items-center gap-4 text-gray-600 text-sm mb-8 pb-8 border-b border-gray-200">
                    <span><?php echo e($article->created_at->format('F d, Y')); ?></span>
                    <span>•</span>
                    <span><?php echo e(str_word_count($article->content)); ?> words</span>
                </div>

                <!-- Content -->
                <div class="prose prose-lg max-w-none mb-8">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                        <?php echo e($article->content); ?>

                    </p>
                </div>

                <!-- Ad Placement: Middle -->
                <?php if($adMiddleCode = \App\Models\AdPlacement::getAdCode('article_detail_middle')): ?>
                    <div class="my-8 p-4 bg-gray-100 rounded-lg border border-gray-300">
                        <div class="text-xs text-gray-500 mb-2">Advertisement</div>
                        <?php echo $adMiddleCode; ?>

                    </div>
                <?php endif; ?>

                <!-- Copy Button -->
                <div class="mb-8">
                    <button onclick="copyToClipboard()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                        📋 Copy Content
                    </button>
                </div>
            </div>
        </article>

        <!-- Ad Placement: Bottom -->
        <?php if($adBottomCode = \App\Models\AdPlacement::getAdCode('article_detail_bottom')): ?>
            <div class="mt-8 p-4 bg-gray-100 rounded-lg border border-gray-300">
                <div class="text-xs text-gray-500 mb-2">Advertisement</div>
                <?php echo $adBottomCode; ?>

            </div>
        <?php endif; ?>

        <!-- Related Articles -->
        <?php if($relatedArticles->count() > 0): ?>
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php $__currentLoopData = $relatedArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('articles.show', $related->slug)); ?>" class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 block">
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                <?php if($related->category === 'industry'): ?> bg-blue-100 text-blue-800
                                <?php elseif($related->category === 'tips'): ?> bg-green-100 text-green-800
                                <?php elseif($related->category === 'quote'): ?> bg-purple-100 text-purple-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?> mb-3">
                                <?php echo e(ucfirst($related->category)); ?>

                            </span>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                                <?php echo e($related->title); ?>

                            </h3>
                            <p class="text-gray-600 text-sm line-clamp-2">
                                <?php echo e(Str::limit($related->content, 100)); ?>

                            </p>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function copyToClipboard() {
    const content = <?php echo json_encode($article->content, 15, 512) ?>;
    
    // Check if clipboard API is available
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(content).then(() => {
            alert('Content copied to clipboard!');
        }).catch(() => {
            fallbackCopyToClipboard(content);
        });
    } else {
        fallbackCopyToClipboard(content);
    }
}

function fallbackCopyToClipboard(text) {
    // Fallback for older browsers or HTTP connections
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        alert('Content copied to clipboard!');
    } catch (err) {
        alert('Failed to copy content');
    }
    
    document.body.removeChild(textarea);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\articles\show.blade.php ENDPATH**/ ?>