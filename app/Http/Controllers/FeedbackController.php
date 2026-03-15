<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $feedback = Auth::user()->feedback()
            ->latest()
            ->paginate(10);

        return view('feedback.index', compact('feedback'));
    }

    public function create()
    {
        return view('feedback.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => 'required|in:bug,feature,improvement,question',
            'title'       => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'page_url'    => 'nullable|string',
            'browser'     => 'nullable|string',
            'screenshot'  => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $validated['priority'] = match($validated['type']) {
            'bug'         => 'high',
            'feature'     => 'medium',
            'improvement' => 'low',
            'question'    => 'low',
            default       => 'medium',
        };

        $feedback = Feedback::create($validated);

        $feedback->load('user');
        $this->notifyAdmins($feedback);

        return redirect()->route('feedback.index')
            ->with('success', 'Terima kasih! Feedback Anda telah dikirim dan akan segera kami review.');
    }

    /**
     * Notify all admins about new feedback (in-app + email)
     */
    private function notifyAdmins(Feedback $feedback)
    {
        $admins = \App\Models\User::where('role', 'admin')->get();

        $typeLabel = match($feedback->type) {
            'bug'         => 'Bug Report',
            'feature'     => 'Feature Request',
            'improvement' => 'Improvement',
            'question'    => 'Question',
            default       => $feedback->type,
        };

        $priorityEmoji = match($feedback->priority) {
            'critical' => '🔴',
            'high'     => '🟠',
            'medium'   => '🟡',
            'low'      => '⚪',
            default    => '⚪',
        };

        $title   = $priorityEmoji . ' Feedback Baru: ' . $typeLabel;
        $message = 'User ' . $feedback->user->name . ' mengirim feedback: ' . $feedback->title;

        foreach ($admins as $admin) {
            $this->notificationService->create(
                $admin,
                \App\Models\Notification::TYPE_FEEDBACK_NEW,
                $title,
                $message,
                route('admin.feedback.show', $feedback->id),
                [
                    'feedback_id'   => $feedback->id,
                    'feedback_type' => $feedback->type,
                    'priority'      => $feedback->priority,
                    'user_name'     => $feedback->user->name,
                ]
            );

            $this->notificationService->sendEmail(
                $admin,
                $title . ' - Noteds',
                $message,
                route('admin.feedback.show', $feedback->id),
                'Lihat Feedback'
            );
        }
    }

    public function show(Feedback $feedback)
    {
        if ($feedback->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('feedback.show', compact('feedback'));
    }
}
