<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectCollaborationController extends Controller
{
    use AuthorizesRequests;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Show project team management
     */
    public function index(Project $project)
    {
        if (!$project->canUserAccess(auth()->user())) {
            abort(403, 'Anda tidak memiliki akses ke project ini.');
        }
        
        $members = $project->members()->with(['user', 'invitedBy'])->get();
        $pendingInvitations = $project->pendingMembers()->with(['user', 'invitedBy'])->get();
        
        return view('projects.collaboration.index', compact('project', 'members', 'pendingInvitations'));
    }

    /**
     * Invite team member
     */
    public function inviteMember(Request $request, Project $project)
    {
        if (!$project->canUserEdit(auth()->user())) {
            abort(403, 'Anda tidak memiliki permission untuk mengundang anggota.');
        }
        
        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,editor,viewer',
            'message' => 'nullable|string|max:500'
        ]);

        // Check if user exists
        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User dengan email tersebut tidak ditemukan. Pastikan mereka sudah mendaftar di platform ini.'
            ], 404);
        }

        // Check if already a member
        if ($project->hasMember($user) || $project->isOwner($user)) {
            return response()->json([
                'success' => false,
                'message' => 'User sudah menjadi anggota project ini.'
            ], 422);
        }

        // Create invitation
        $invitation = $project->inviteMember($validated['email'], $validated['role'], auth()->user());
        
        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim undangan.'
            ], 500);
        }

        // Send invitation email
        try {
            \Mail::to($user)->send(new \App\Mail\ProjectInvitation(
                $project, 
                $invitation, 
                auth()->user(), 
                $validated['message'] ?? null
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send invitation email: ' . $e->getMessage());
            // Don't fail the invitation if email fails
        }

        return response()->json([
            'success' => true,
            'message' => 'Undangan berhasil dikirim ke ' . $validated['email'],
            'invitation' => $invitation->load(['user', 'invitedBy'])
        ]);
    }

    /**
     * Accept invitation
     */
    public function acceptInvitation(ProjectMember $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$invitation->isPending()) {
            return redirect()->route('projects.index')
                ->with('error', 'Undangan sudah tidak valid.');
        }

        $invitation->update([
            'status' => 'accepted',
            'joined_at' => now()
        ]);

        // Notify project owner
        $this->notificationService->notifyInvitationAccepted($invitation->load('project.user', 'user'));

        return redirect()->route('projects.collaboration.workspace', $invitation->project)
            ->with('success', 'Selamat datang di tim ' . $invitation->project->business_name . '!');
    }

    /**
     * Decline invitation
     */
    public function declineInvitation(ProjectMember $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$invitation->isPending()) {
            return redirect()->route('projects.index')
                ->with('error', 'Undangan sudah tidak valid.');
        }

        $invitation->update(['status' => 'declined']);

        // Notify project owner
        $this->notificationService->notifyInvitationDeclined($invitation->load('project.user', 'user'));

        return redirect()->route('projects.index')
            ->with('success', 'Undangan ditolak.');
    }

    /**
     * Update member role
     */
    public function updateMemberRole(Request $request, Project $project, ProjectMember $member)
    {
        if (!$project->canUserApprove(auth()->user())) {
            abort(403, 'Anda tidak memiliki permission untuk mengubah role.');
        }
        
        $validated = $request->validate([
            'role' => 'required|in:admin,editor,viewer'
        ]);

        // Only project owner or admin can change roles
        if (!$project->isOwner(auth()->user()) && !$project->canUserApprove(auth()->user())) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk mengubah role.'
            ], 403);
        }

        $member->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diupdate.',
            'member' => $member->fresh()
        ]);
    }

    /**
     * Remove team member
     */
    public function removeMember(Project $project, ProjectMember $member)
    {
        if (!$project->canUserApprove(auth()->user())) {
            abort(403, 'Anda tidak memiliki permission untuk menghapus anggota.');
        }
        
        // Only project owner or admin can remove members
        if (!$project->isOwner(auth()->user()) && !$project->canUserApprove(auth()->user())) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki permission untuk menghapus anggota.'
            ], 403);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anggota berhasil dihapus dari tim.'
        ]);
    }

    /**
     * Leave project
     */
    public function leaveProject(Project $project)
    {
        $member = $project->members()->where('user_id', auth()->id())->first();
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan anggota project ini.'
            ], 404);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anda berhasil keluar dari project.',
            'redirect' => route('projects.index')
        ]);
    }

    /**
     * Show team workspace
     */
    public function workspace(Project $project)
    {
        if (!$project->canUserAccess(auth()->user())) {
            abort(403, 'Anda tidak memiliki akses ke project ini.');
        }

        $userRole = $project->getMemberRole(auth()->user());
        $canEdit = $project->canUserEdit(auth()->user());
        $canApprove = $project->canUserApprove(auth()->user());
        $canManageTeam = $project->canUserApprove(auth()->user()) || $project->isOwner(auth()->user());

        // Get my content statistics
        $myContent = [
            'total' => $project->content()->where('created_by', auth()->id())->count(),
            'drafts' => $project->content()->where('created_by', auth()->id())->where('status', 'draft')->count(),
            'review' => $project->content()->where('created_by', auth()->id())->where('status', 'review')->count(),
            'approved' => $project->content()->where('created_by', auth()->id())->whereIn('status', ['approved', 'published'])->count(),
        ];

        // Get pending review count
        $pendingReview = $project->content()->where('status', 'review')->count();

        // Get pending review content (for approvers)
        $pendingReviewContent = $canApprove 
            ? $project->content()->where('status', 'review')->with(['creator'])->latest()->take(5)->get()
            : collect();

        // Get my recent content
        $myRecentContent = $project->content()
            ->where('created_by', auth()->id())
            ->with(['creator'])
            ->latest()
            ->take(10)
            ->get();

        // Get team activity (recent content from all members)
        $teamActivity = $project->content()
            ->with(['creator'])
            ->where('created_by', '!=', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        // Get team members
        $teamMembers = $project->members()->with('user')->get();

        return view('projects.collaboration.workspace', compact(
            'project', 
            'userRole', 
            'canEdit', 
            'canApprove', 
            'canManageTeam',
            'myContent',
            'pendingReview',
            'pendingReviewContent',
            'myRecentContent', 
            'teamActivity',
            'teamMembers'
        ));
    }

    /**
     * Get team activity feed
     */
    public function activityFeed(Project $project)
    {
        if (!$project->canUserAccess(auth()->user())) {
            abort(403);
        }

        // Get recent versions (activity)
        $activities = \App\Models\ContentVersion::whereHas('content', function($query) use ($project) {
            $query->where('project_id', $project->id);
        })
        ->with(['content', 'creator'])
        ->latest()
        ->take(20)
        ->get();

        return response()->json([
            'success' => true,
            'activities' => $activities->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->change_type,
                    'type_label' => $activity->getChangeTypeLabel(),
                    'type_color' => $activity->getChangeTypeColor(),
                    'content_title' => $activity->content->title,
                    'content_id' => $activity->content->id,
                    'user_name' => $activity->creator->name,
                    'user_avatar' => $activity->creator->avatar ?? null,
                    'created_at' => $activity->created_at->diffForHumans(),
                    'notes' => $activity->change_notes
                ];
            })
        ]);
    }
}