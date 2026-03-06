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

        Feedback::create($validated);

        return redirect()->route('feedback.index')
            ->with('success', 'Terima kasih! Feedback Anda telah dikirim dan akan segera kami review.');
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
