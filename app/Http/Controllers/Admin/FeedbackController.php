<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display all feedback
     */
    public function index(Request $request)
    {
        $query = Feedback::with('user')->latest();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $feedback = $query->paginate(20);

        $stats = [
            'total' => Feedback::count(),
            'open' => Feedback::where('status', 'open')->count(),
            'in_progress' => Feedback::where('status', 'in_progress')->count(),
            'resolved' => Feedback::where('status', 'resolved')->count(),
            'bugs' => Feedback::where('type', 'bug')->count(),
            'features' => Feedback::where('type', 'feature')->count(),
        ];

        return view('admin.feedback.index', compact('feedback', 'stats'));
    }

    /**
     * Show feedback detail
     */
    public function show(Feedback $feedback)
    {
        $feedback->load('user');
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Update feedback status and response
     */
    public function update(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,critical',
            'admin_response' => 'nullable|string',
        ]);

        if ($request->filled('admin_response')) {
            $validated['responded_at'] = now();
        }

        $feedback->update($validated);

        return redirect()->back()
            ->with('success', 'Feedback berhasil diupdate');
    }

    /**
     * Delete feedback
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return redirect()->route('admin.feedback')
            ->with('success', 'Feedback berhasil dihapus');
    }
}
