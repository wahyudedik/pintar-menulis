

<?php $__env->startSection('title', 'Content Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Manajemen Konten</h1>
            <p class="text-sm text-gray-500">Proyek: <?php echo e($project->business_name); ?></p>
        </div>
        <div class="flex space-x-3">
            <a href="<?php echo e(route('projects.content.create', $project)); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Konten
            </a>
            <a href="<?php echo e(route('projects.show', $project)); ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Proyek
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                        <option value="review" <?php echo e(request('status') == 'review' ? 'selected' : ''); ?>>Dalam Review</option>
                        <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Disetujui</option>
                        <option value="published" <?php echo e(request('status') == 'published' ? 'selected' : ''); ?>>Dipublikasi</option>
                        <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Ditolak</option>
                    </select>
                </div>
                <div>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jenis</option>
                        <option value="caption" <?php echo e(request('type') == 'caption' ? 'selected' : ''); ?>>Caption</option>
                        <option value="article" <?php echo e(request('type') == 'article' ? 'selected' : ''); ?>>Artikel</option>
                        <option value="ad_copy" <?php echo e(request('type') == 'ad_copy' ? 'selected' : ''); ?>>Iklan</option>
                        <option value="email" <?php echo e(request('type') == 'email' ? 'selected' : ''); ?>>Email</option>
                        <option value="product_desc" <?php echo e(request('type') == 'product_desc' ? 'selected' : ''); ?>>Deskripsi Produk</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari konten..." value="<?php echo e(request('search')); ?>">
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Content List -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Daftar Konten (<?php echo e($contents->total()); ?>)
            </h3>
        </div>

        <?php if($contents->count() > 0): ?>
            <?php
                $isOwner = $project->isOwner(auth()->user());
                $statusColors = [
                    'draft'     => 'bg-gray-100 text-gray-800',
                    'review'    => 'bg-yellow-100 text-yellow-800',
                    'approved'  => 'bg-green-100 text-green-800',
                    'published' => 'bg-blue-100 text-blue-800',
                    'rejected'  => 'bg-red-100 text-red-800',
                ];
            ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $contents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $canEdit   = $content->canEdit(auth()->user());
                            $canDelete = $canEdit || $isOwner;
                            $canApprove = $isOwner && $content->status === 'review';
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($content->title); ?></div>
                                <div class="text-sm text-gray-500"><?php echo e(Str::limit($content->content, 80)); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $content->content_type))); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($content->platform): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?php echo e(ucfirst($content->platform)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statusColors[$content->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo e(ucfirst($content->status)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-medium text-blue-600"><?php echo e(substr($content->creator->name, 0, 1)); ?></span>
                                    </div>
                                    <div class="ml-3 text-sm font-medium text-gray-900"><?php echo e($content->creator->name); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                <?php echo e($content->created_at->format('d M Y')); ?>

                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <!-- Lihat -->
                                    <a href="<?php echo e(route('projects.content.show', [$project, $content])); ?>"
                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                        Lihat
                                    </a>

                                    <!-- Edit -->
                                    <?php if($canEdit): ?>
                                    <a href="<?php echo e(route('projects.content.edit', [$project, $content])); ?>"
                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                        Edit
                                    </a>
                                    <?php endif; ?>

                                    <!-- Submit Review -->
                                    <?php if($content->status === 'draft' && $canEdit): ?>
                                    <form action="<?php echo e(route('projects.content.submit-review', [$project, $content])); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                                            Kirim Review
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Approve / Reject -->
                                    <?php if($canApprove): ?>
                                    <button onclick="approveContent(<?php echo e($content->id); ?>)"
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded bg-green-100 text-green-700 hover:bg-green-200 transition">
                                        Setujui
                                    </button>
                                    <button onclick="rejectContent(<?php echo e($content->id); ?>)"
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded bg-red-100 text-red-700 hover:bg-red-200 transition">
                                        Tolak
                                    </button>
                                    <?php endif; ?>

                                    <!-- Riwayat Versi -->
                                    <a href="<?php echo e(route('projects.content.versions', [$project, $content])); ?>"
                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded bg-purple-100 text-purple-700 hover:bg-purple-200 transition">
                                        Versi
                                    </a>

                                    <!-- Hapus -->
                                    <?php if($canDelete): ?>
                                    <button onclick="deleteContent(<?php echo e($content->id); ?>)"
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded bg-red-50 text-red-600 hover:bg-red-100 transition">
                                        Hapus
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($contents->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Konten</h3>
                <p class="text-gray-500 mb-4">Mulai buat konten untuk proyek ini.</p>
                <a href="<?php echo e(route('projects.content.create', $project)); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Konten Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function approveContent(contentId) {
    if (!confirm('Setujui konten ini?')) return;
    fetch(`<?php echo e(url('projects/' . $project->id . '/content')); ?>/${contentId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(r => r.ok ? location.reload() : r.json().then(d => alert(d.message ?? 'Gagal menyetujui konten.')));
}

function rejectContent(contentId) {
    const reason = prompt('Alasan penolakan:');
    if (!reason) return;
    fetch(`<?php echo e(url('projects/' . $project->id . '/content')); ?>/${contentId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reason })
    }).then(r => r.ok ? location.reload() : r.json().then(d => alert(d.message ?? 'Gagal menolak konten.')));
}

function deleteContent(contentId) {
    if (!confirm('Hapus konten ini? Tindakan tidak bisa dibatalkan.')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `<?php echo e(url('projects/' . $project->id . '/content')); ?>/${contentId}`;
    form.innerHTML = `<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"><input type="hidden" name="_method" value="DELETE">`;
    document.body.appendChild(form);
    form.submit();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views/projects/content/index.blade.php ENDPATH**/ ?>