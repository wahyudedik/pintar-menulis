<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $projects = $user->projects()->latest()->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function show(Project $project)
    {
        if (!$project->canUserAccess(auth()->user())) {
            abort(403, 'Anda tidak memiliki akses ke project ini.');
        }

        $userRole = $project->getMemberRole(auth()->user());
        $canEdit = $project->canUserEdit(auth()->user());
        $canApprove = $project->canUserApprove(auth()->user());

        // Get project statistics
        $stats = [
            'total_content' => $project->content()->count(),
            'drafts' => $project->drafts()->count(),
            'pending_review' => $project->pendingReview()->count(),
            'approved' => $project->approvedContent()->count(),
            'team_members' => $project->getTotalMembers()
        ];

        // Get recent content
        $recentContent = $project->content()
            ->with(['creator', 'reviewer'])
            ->latest()
            ->take(5)
            ->get();

        // Get team members
        $teamMembers = $project->acceptedMembers()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('projects.show', compact(
            'project', 
            'userRole', 
            'canEdit', 
            'canApprove', 
            'stats', 
            'recentContent', 
            'teamMembers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'business_description' => 'required|string',
            'target_audience' => 'nullable|string',
            'brand_tone' => 'nullable|string',
            'preferred_platforms' => 'nullable|array',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $project = $user->projects()->create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil ditambahkan');
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'business_description' => 'required|string',
            'target_audience' => 'nullable|string',
            'brand_tone' => 'nullable|string',
            'preferred_platforms' => 'nullable|array',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil diperbarui');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus');
    }
}
