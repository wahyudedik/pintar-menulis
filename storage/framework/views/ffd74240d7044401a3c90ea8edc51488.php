

<?php $__env->startSection('title', $content->title . ' - ' . $project->business_name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Content Display -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1"><?php echo e($content->title); ?></h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-info"><?php echo e(ucfirst(str_replace('_', ' ', $content->content_type))); ?></span>
                                <?php if($content->platform): ?>
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($content->platform)); ?></span>
                                <?php endif; ?>
                                <?php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'review' => 'warning',
                                        'approved' => 'success',
                                        'published' => 'primary',
                                        'rejected' => 'danger'
                                    ];
                                ?>
                                <span class="badge bg-<?php echo e($statusColors[$content->status] ?? 'secondary'); ?>">
                                    <?php echo e(ucfirst($content->status)); ?>

                                </span>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <?php if($content->canEdit(auth()->user())): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('projects.content.edit', [$project, $content])); ?>">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if($content->status == 'draft' && $content->canEdit(auth()->user())): ?>
                                <li>
                                    <form action="<?php echo e(route('projects.content.submit-review', [$project, $content])); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-paper-plane me-2"></i>Submit for Review
                                        </button>
                                    </form>
                                </li>
                                <?php endif; ?>
                                <?php if($content->status == 'review' && auth()->user()->can('approve-content', $project)): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-success" onclick="approveContent()">
                                        <i class="fas fa-check me-2"></i>Approve
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="rejectContent()">
                                        <i class="fas fa-times me-2"></i>Reject
                                    </button>
                                </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('projects.content.versions', [$project, $content])); ?>">
                                        <i class="fas fa-history me-2"></i>Version History
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item" onclick="copyContent()">
                                        <i class="fas fa-copy me-2"></i>Copy Content
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Content -->
                    <div class="mb-4">
                        <div class="content-display p-3 bg-light rounded" id="contentDisplay">
                            <?php echo nl2br(e($content->content)); ?>

                        </div>
                        <div class="mt-2 text-muted small">
                            <i class="fas fa-font me-1"></i><?php echo e(strlen($content->content)); ?> characters
                        </div>
                    </div>

                    <!-- Tags -->
                    <?php if($content->tags): ?>
                    <div class="mb-4">
                        <h6>Tags:</h6>
                        <div class="d-flex flex-wrap gap-1">
                            <?php $__currentLoopData = explode(',', $content->tags); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-light text-dark"><?php echo e(trim($tag)); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Notes -->
                    <?php if($content->notes): ?>
                    <div class="mb-4">
                        <h6>Notes:</h6>
                        <div class="p-3 bg-info bg-opacity-10 rounded">
                            <?php echo nl2br(e($content->notes)); ?>

                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Metadata -->
                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-user me-1"></i>Created by: <?php echo e($content->creator->name); ?>

                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-clock me-1"></i><?php echo e($content->created_at->format('M d, Y \a\t H:i')); ?>

                        </div>
                        <?php if($content->updated_at != $content->created_at): ?>
                        <div class="col-md-6 mt-1">
                            <i class="fas fa-edit me-1"></i>Last updated: <?php echo e($content->updated_at->format('M d, Y \a\t H:i')); ?>

                        </div>
                        <?php endif; ?>
                        <?php if($content->approved_at): ?>
                        <div class="col-md-6 mt-1">
                            <i class="fas fa-check me-1"></i>Approved: <?php echo e($content->approved_at->format('M d, Y \a\t H:i')); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comments me-2"></i>Comments (<?php echo e($content->comments->count()); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Add Comment Form -->
                    <form action="<?php echo e(route('projects.content.comments.store', [$project, $content])); ?>" method="POST" class="mb-4">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <textarea class="form-control <?php $__errorArgs = ['comment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      name="comment" rows="3" 
                                      placeholder="Add a comment..." required><?php echo e(old('comment')); ?></textarea>
                            <?php $__errorArgs = ['comment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Add Comment
                        </button>
                    </form>

                    <!-- Comments List -->
                    <?php if($content->comments->count() > 0): ?>
                        <div class="comments-list">
                            <?php $__currentLoopData = $content->comments->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="comment-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-primary rounded-circle">
                                                <?php echo e(substr($comment->user->name, 0, 1)); ?>

                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <strong><?php echo e($comment->user->name); ?></strong>
                                                <small class="text-muted"><?php echo e($comment->created_at->diffForHumans()); ?></small>
                                                <?php if($comment->is_resolved): ?>
                                                    <span class="badge bg-success">Resolved</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="comment-content">
                                                <?php echo nl2br(e($comment->comment)); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php if(!$comment->is_resolved && (auth()->id() == $comment->user_id || auth()->user()->can('approve-content', $project))): ?>
                                            <li>
                                                <form action="<?php echo e(route('projects.content.comments.resolve', [$project, $content, $comment])); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-check me-2"></i>Mark as Resolved
                                                    </button>
                                                </form>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <p>No comments yet. Be the first to add feedback!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Navigation -->
            <div class="card mb-4">
                <div class="card-body">
                    <a href="<?php echo e(route('projects.content.index', $project)); ?>" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Content List
                    </a>
                    <a href="<?php echo e(route('projects.show', $project)); ?>" class="btn btn-outline-primary w-100">
                        <i class="fas fa-project-diagram me-2"></i>Back to Project
                    </a>
                </div>
            </div>

            <!-- Workflow Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Workflow Status</h6>
                </div>
                <div class="card-body">
                    <div class="workflow-steps">
                        <div class="step <?php echo e($content->status == 'draft' ? 'active' : ($content->created_at ? 'completed' : '')); ?>">
                            <div class="step-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="step-content">
                                <h6>Draft</h6>
                                <small class="text-muted">Content created</small>
                            </div>
                        </div>
                        <div class="step <?php echo e($content->status == 'review' ? 'active' : ($content->submitted_at ? 'completed' : '')); ?>">
                            <div class="step-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="step-content">
                                <h6>Review</h6>
                                <small class="text-muted">Under review</small>
                            </div>
                        </div>
                        <div class="step <?php echo e(in_array($content->status, ['approved', 'published']) ? 'active' : ''); ?>">
                            <div class="step-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="step-content">
                                <h6>Approved</h6>
                                <small class="text-muted">Ready to publish</small>
                            </div>
                        </div>
                        <div class="step <?php echo e($content->status == 'published' ? 'active' : ''); ?>">
                            <div class="step-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="step-content">
                                <h6>Published</h6>
                                <small class="text-muted">Live content</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Version History -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Recent Versions</h6>
                </div>
                <div class="card-body">
                    <?php if($content->versions->count() > 0): ?>
                        <div class="version-list">
                            <?php $__currentLoopData = $content->versions->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="version-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <small class="fw-bold">v<?php echo e($version->version_number); ?></small>
                                    <br>
                                    <small class="text-muted"><?php echo e($version->created_at->format('M d, H:i')); ?></small>
                                </div>
                                <div>
                                    <form action="<?php echo e(route('projects.content.restore-version', [$project, $content, $version])); ?>" method="POST"
                                          onsubmit="return confirm('Restore this version? Current content will be saved as a new version.')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            Restore
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php if($content->versions->count() > 5): ?>
                        <div class="text-center mt-3">
                            <a href="<?php echo e(route('projects.content.versions', [$project, $content])); ?>" class="btn btn-sm btn-outline-secondary">
                                View All Versions
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <p>No version history yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('projects.content.approve', [$project, $content])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" name="notes" id="approval_notes" rows="3" 
                                  placeholder="Add any notes about the approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Content</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('projects.content.reject', [$project, $content])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" id="rejection_reason" rows="3" 
                                  placeholder="Please explain why this content is being rejected..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Content</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.workflow-steps .step {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.workflow-steps .step.active,
.workflow-steps .step.completed {
    opacity: 1;
}

.workflow-steps .step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.workflow-steps .step.active .step-icon {
    background: #0d6efd;
    color: white;
}

.workflow-steps .step.completed .step-icon {
    background: #198754;
    color: white;
}

.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function approveContent() {
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

function rejectContent() {
    new bootstrap.Modal(document.getElementById('rejectionModal')).show();
}

function copyContent() {
    const content = document.getElementById('contentDisplay').innerText;
    navigator.clipboard.writeText(content).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    Content copied to clipboard!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\projects\content\show.blade.php ENDPATH**/ ?>