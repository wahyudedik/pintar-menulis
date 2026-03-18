

<?php $__env->startSection('title', 'Team Workspace - ' . $project->business_name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6" x-data="teamWorkspace()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <a href="<?php echo e(route('projects.show', $project)); ?>" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-gray-900">Team Workspace</h1>
                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full"><?php echo e(ucfirst($userRole)); ?></span>
            </div>
            <p class="text-sm text-gray-500"><?php echo e($project->business_name); ?> • Collaborate with your team</p>
        </div>
        <div class="flex items-center space-x-3">
            <?php if($canEdit): ?>
            <a href="<?php echo e(route('projects.content.create', $project)); ?>" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Content
            </a>
            <?php endif; ?>
            <?php if($canManageTeam): ?>
            <a href="<?php echo e(route('projects.collaboration.index', $project)); ?>" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Manage Team
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Workflow Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">My Drafts</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo e($myContent['drafts']); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">In Review</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo e($myContent['review']); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Approved</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo e($myContent['approved']); ?></p>
                </div>
            </div>
        </div>

        <?php if($canApprove): ?>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Need Review</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo e($pendingReview); ?></p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Content</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo e($myContent['total']); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Content Workflow -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Pending Reviews (for approvers) -->
            <?php if($canApprove && $pendingReviewContent->isNotEmpty()): ?>
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pending Reviews
                        </h3>
                        <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full"><?php echo e($pendingReviewContent->count()); ?></span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php $__currentLoopData = $pendingReviewContent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start space-x-3 p-4 border border-orange-200 rounded-lg bg-orange-50">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-orange-600"><?php echo e(substr($content->creator->name, 0, 1)); ?></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <a href="<?php echo e(route('projects.content.show', [$project, $content])); ?>" 
                                       class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                        <?php echo e($content->title); ?>

                                    </a>
                                    <div class="flex items-center space-x-2">
                                        <button onclick="approveContent(<?php echo e($content->id); ?>)" 
                                                class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                                            Approve
                                        </button>
                                        <button onclick="rejectContent(<?php echo e($content->id); ?>)" 
                                                class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                            Reject
                                        </button>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    By <?php echo e($content->creator->name); ?> • <?php echo e($content->created_at->diffForHumans()); ?>

                                    <?php if($content->platform): ?>
                                    • <?php echo e(ucfirst($content->platform)); ?>

                                    <?php endif; ?>
                                </p>
                                <p class="text-xs text-gray-600 mt-2"><?php echo e(Str::limit($content->content, 100)); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- My Recent Content -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">My Recent Content</h3>
                        <a href="<?php echo e(route('projects.content.index', $project)); ?>?created_by=<?php echo e(auth()->id()); ?>" 
                           class="text-sm text-blue-600 hover:text-blue-700">View All</a>
                    </div>
                </div>
                <div class="p-6">
                    <?php if($myRecentContent->isEmpty()): ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 mb-4">You haven't created any content yet</p>
                        <?php if($canEdit): ?>
                        <a href="<?php echo e(route('projects.content.create', $project)); ?>" 
                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                            Create your first content
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $myRecentContent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($content->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : ($content->status === 'review' ? 'bg-orange-100 text-orange-800' : ($content->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))); ?>">
                                    <?php echo e(ucfirst($content->status)); ?>

                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <a href="<?php echo e(route('projects.content.show', [$project, $content])); ?>" 
                                       class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                        <?php echo e($content->title); ?>

                                    </a>
                                    <span class="text-xs text-gray-500"><?php echo e($content->created_at->diffForHumans()); ?></span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?php echo e(ucfirst($content->content_type)); ?>

                                    <?php if($content->platform): ?>
                                    • <?php echo e(ucfirst($content->platform)); ?>

                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Team Activity -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Team Activity</h3>
                </div>
                <div class="p-6">
                    <?php if($teamActivity->isEmpty()): ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.196-2.121M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">No team activity yet</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $teamActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600"><?php echo e(substr($activity->creator->name, 0, 1)); ?></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">
                                    <span class="font-medium"><?php echo e($activity->creator->name); ?></span>
                                    <?php if($activity->status === 'draft'): ?>
                                        created a new draft
                                    <?php elseif($activity->status === 'review'): ?>
                                        submitted content for review
                                    <?php elseif($activity->status === 'approved'): ?>
                                        approved content
                                    <?php elseif($activity->status === 'published'): ?>
                                        published content
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('projects.content.show', [$project, $activity])); ?>" 
                                       class="text-blue-600 hover:text-blue-700">"<?php echo e($activity->title); ?>"</a>
                                </p>
                                <p class="text-xs text-gray-500"><?php echo e($activity->updated_at->diffForHumans()); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Team Members -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Team Members</h3>
                        <span class="text-sm text-gray-500"><?php echo e($teamMembers->count() + 1); ?></span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <!-- Project Owner -->
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600"><?php echo e(substr($project->user->name, 0, 1)); ?></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900"><?php echo e($project->user->name); ?></p>
                                <p class="text-xs text-gray-500">Owner</p>
                            </div>
                        </div>

                        <!-- Team Members -->
                        <?php $__currentLoopData = $teamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600"><?php echo e(substr($member->user->name, 0, 1)); ?></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900"><?php echo e($member->user->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e(ucfirst($member->role)); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <?php if($canEdit): ?>
                    <a href="<?php echo e(route('projects.content.create', $project)); ?>" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create New Content
                    </a>
                    <?php endif; ?>

                    <a href="<?php echo e(route('projects.content.index', $project)); ?>" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View All Content
                    </a>

                    <?php if($canManageTeam): ?>
                    <a href="<?php echo e(route('projects.collaboration.index', $project)); ?>" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.196-2.121M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Manage Team
                    </a>
                    <?php endif; ?>

                    <a href="<?php echo e(route('projects.show', $project)); ?>" 
                       class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        Project Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function teamWorkspace() {
    return {
        // Add any JavaScript functionality here
    }
}

function approveContent(contentId) {
    if (confirm('Are you sure you want to approve this content?')) {
        fetch(`/projects/<?php echo e($project->id); ?>/content/${contentId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}

function rejectContent(contentId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason) {
        fetch(`/projects/<?php echo e($project->id); ?>/content/${contentId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\projects\collaboration\workspace.blade.php ENDPATH**/ ?>