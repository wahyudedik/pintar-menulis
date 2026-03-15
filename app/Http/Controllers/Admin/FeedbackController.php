<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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

        $oldStatus = $feedback->status;
        $hasNewResponse = $request->filled('admin_response') && $feedback->admin_response !== $request->admin_response;

        if ($request->filled('admin_response')) {
            $validated['responded_at'] = now();
        }

        $feedback->update($validated);

        // Send notification to user if admin responded or status changed to resolved
        if ($hasNewResponse || ($oldStatus !== 'resolved' && $validated['status'] === 'resolved')) {
            $this->notifyUser($feedback, $hasNewResponse);
        }

        return redirect()->back()
            ->with('success', 'Feedback berhasil diupdate');
    }

    /**
     * Notify user about admin response (in-app + email)
     */
    private function notifyUser(Feedback $feedback, bool $hasResponse)
    {
        $title   = $hasResponse ? '💬 Admin Merespons Feedback Anda' : '✅ Feedback Anda Telah Diselesaikan';
        $message = $hasResponse
            ? 'Admin telah merespons feedback Anda: ' . $feedback->title
            : 'Feedback Anda "' . $feedback->title . '" telah diselesaikan';

        $this->notificationService->create(
            $feedback->user,
            \App\Models\Notification::TYPE_FEEDBACK_RESPONSE,
            $title,
            $message,
            route('feedback.show', $feedback->id),
            [
                'feedback_id'   => $feedback->id,
                'feedback_type' => $feedback->type,
                'status'        => $feedback->status,
            ]
        );

        $emailMessage = $message . ($feedback->admin_response ? "\n\nRespons admin: " . $feedback->admin_response : '');
        $this->notificationService->sendEmail(
            $feedback->user,
            $title . ' - Noteds',
            $emailMessage,
            route('feedback.show', $feedback->id),
            'Lihat Feedback'
        );
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
