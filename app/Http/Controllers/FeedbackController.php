<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display feedback list
     */
    public function index()
    {
        $feedback = Auth::user()->feedback()
            ->latest()
            ->paginate(10);

        return view('feedback.index', compact('feedback'));
    }

    /**
     * Show create feedback form
     */
    public function create()
    {
        return view('feedback.create');
    }

    /**
     * Store new feedback
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:bug,feature,improvement,question',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'page_url' => 'nullable|string',
            'browser' => 'nullable|string',
            'screenshot' => 'nullable|string', // Base64 image
        ]);

        $validated['user_id'] = Auth::id();
        
        // Auto-set priority based on type
        $validated['priority'] = match($validated['type']) {
            'bug' => 'high',
            'feature' => 'medium',
            'improvement' => 'low',
            'question' => 'low',
            default => 'medium',
        };

        $feedback = Feedback::create($validated);

        // Create notification for all admins
        $this->notifyAdmins($feedback);

        return redirect()->route('feedback.index')
            ->with('success', 'Terima kasih! Feedback Anda telah dikirim dan akan segera kami review.');
    }

    /**
     * Notify all admins about new feedback
     */
    private function notifyAdmins(Feedback $feedback)
    {
        $admins = \App\Models\User::where('role', 'admin')->get();
        
        $typeLabel = match($feedback->type) {
            'bug' => 'Bug Report',
            'feature' => 'Feature Request',
            'improvement' => 'Improvement',
            'question' => 'Question',
            default => $feedback->type,
        };
        
        $priorityEmoji = match($feedback->priority) {
            'critical' => '🔴',
            'high' => '🟠',
            'medium' => '🟡',
            'low' => '⚪',
            default => '⚪',
        };

        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => \App\Models\Notification::TYPE_FEEDBACK_NEW,
                'title' => $priorityEmoji . ' Feedback Baru: ' . $typeLabel,
                'message' => 'User ' . $feedback->user->name . ' mengirim feedback: ' . $feedback->title,
                'data' => [
                    'feedback_id' => $feedback->id,
                    'feedback_type' => $feedback->type,
                    'priority' => $feedback->priority,
                    'user_name' => $feedback->user->name,
                ],
                'action_url' => route('admin.feedback.show', $feedback->id),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Show feedback detail
     */
    public function show(Feedback $feedback)
    {
        // Ensure user can only see their own feedback
        if ($feedback->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('feedback.show', compact('feedback'));
    }
}
