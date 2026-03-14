@extends('layouts.app')

@section('title', $content->title . ' - ' . $project->business_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Content Display -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1">{{ $content->title }}</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $content->content_type)) }}</span>
                                @if($content->platform)
                                    <span class="badge bg-secondary">{{ ucfirst($content->platform) }}</span>
                                @endif
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'review' => 'warning',
                                        'approved' => 'success',
                                        'published' => 'primary',
                                        'rejected' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$content->status] ?? 'secondary' }}">
                                    {{ ucfirst($content->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                @if($content->canEdit(auth()->user()))
                                <li>
                                    <a class="dropdown-item" href="{{ route('projects.content.edit', [$project, $content]) }}">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                @endif
                                @if($content->status == 'draft' && $content->canEdit(auth()->user()))
                                <li>
                                    <form action="{{ route('projects.content.submit-review', [$project, $content]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-paper-plane me-2"></i>Submit for Review
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @if($content->status == 'review' && auth()->user()->can('approve-content', $project))
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
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('projects.content.versions', [$project, $content]) }}">
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
                            {!! nl2br(e($content->content)) !!}
                        </div>
                        <div class="mt-2 text-muted small">
                            <i class="fas fa-font me-1"></i>{{ strlen($content->content) }} characters
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($content->tags)
                    <div class="mb-4">
                        <h6>Tags:</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(explode(',', $content->tags) as $tag)
                                <span class="badge bg-light text-dark">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($content->notes)
                    <div class="mb-4">
                        <h6>Notes:</h6>
                        <div class="p-3 bg-info bg-opacity-10 rounded">
                            {!! nl2br(e($content->notes)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Metadata -->
                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-user me-1"></i>Created by: {{ $content->creator->name }}
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-clock me-1"></i>{{ $content->created_at->format('M d, Y \a\t H:i') }}
                        </div>
                        @if($content->updated_at != $content->created_at)
                        <div class="col-md-6 mt-1">
                            <i class="fas fa-edit me-1"></i>Last updated: {{ $content->updated_at->format('M d, Y \a\t H:i') }}
                        </div>
                        @endif
                        @if($content->approved_at)
                        <div class="col-md-6 mt-1">
                            <i class="fas fa-check me-1"></i>Approved: {{ $content->approved_at->format('M d, Y \a\t H:i') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comments me-2"></i>Comments ({{ $content->comments->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Add Comment Form -->
                    <form action="{{ route('projects.content.comments.store', [$project, $content]) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      name="comment" rows="3" 
                                      placeholder="Add a comment..." required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Add Comment
                        </button>
                    </form>

                    <!-- Comments List -->
                    @if($content->comments->count() > 0)
                        <div class="comments-list">
                            @foreach($content->comments->sortByDesc('created_at') as $comment)
                            <div class="comment-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-primary rounded-circle">
                                                {{ substr($comment->user->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <strong>{{ $comment->user->name }}</strong>
                                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                @if($comment->is_resolved)
                                                    <span class="badge bg-success">Resolved</span>
                                                @endif
                                            </div>
                                            <div class="comment-content">
                                                {!! nl2br(e($comment->comment)) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if(!$comment->is_resolved && (auth()->id() == $comment->user_id || auth()->user()->can('approve-content', $project)))
                                            <li>
                                                <form action="{{ route('projects.content.comments.resolve', [$project, $content, $comment]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-check me-2"></i>Mark as Resolved
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <p>No comments yet. Be the first to add feedback!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Navigation -->
            <div class="card mb-4">
                <div class="card-body">
                    <a href="{{ route('projects.content.index', $project) }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Content List
                    </a>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary w-100">
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
                        <div class="step {{ $content->status == 'draft' ? 'active' : ($content->created_at ? 'completed' : '') }}">
                            <div class="step-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="step-content">
                                <h6>Draft</h6>
                                <small class="text-muted">Content created</small>
                            </div>
                        </div>
                        <div class="step {{ $content->status == 'review' ? 'active' : ($content->submitted_at ? 'completed' : '') }}">
                            <div class="step-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="step-content">
                                <h6>Review</h6>
                                <small class="text-muted">Under review</small>
                            </div>
                        </div>
                        <div class="step {{ in_array($content->status, ['approved', 'published']) ? 'active' : '' }}">
                            <div class="step-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="step-content">
                                <h6>Approved</h6>
                                <small class="text-muted">Ready to publish</small>
                            </div>
                        </div>
                        <div class="step {{ $content->status == 'published' ? 'active' : '' }}">
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
                    @if($content->versions->count() > 0)
                        <div class="version-list">
                            @foreach($content->versions->take(5) as $version)
                            <div class="version-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <small class="fw-bold">v{{ $version->version_number }}</small>
                                    <br>
                                    <small class="text-muted">{{ $version->created_at->format('M d, H:i') }}</small>
                                </div>
                                <div>
                                    <a href="{{ route('projects.content.versions.restore', [$project, $content, $version]) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       onclick="return confirm('Restore this version? Current content will be saved as a new version.')">
                                        Restore
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($content->versions->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('projects.content.versions', [$project, $content]) }}" class="btn btn-sm btn-outline-secondary">
                                View All Versions
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <p>No version history yet</p>
                        </div>
                    @endif
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
            <form action="{{ route('projects.content.approve', [$project, $content]) }}" method="POST">
                @csrf
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
            <form action="{{ route('projects.content.reject', [$project, $content]) }}" method="POST">
                @csrf
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

@push('styles')
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
@endpush

@push('scripts')
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
@endpush
@endsection