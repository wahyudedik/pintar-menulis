<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function index()
    {
        $query = WithdrawalRequest::with('user')->orderBy('created_at', 'desc');

        if (request('type') && in_array(request('type'), ['referral', 'operator', 'guru'])) {
            $query->where('type', request('type'));
        }

        $withdrawals = $query->paginate(50);

        $stats = [
            'pending'      => WithdrawalRequest::where('status', 'pending')->count(),
            'approved'     => WithdrawalRequest::where('status', 'approved')->count(),
            'completed'    => WithdrawalRequest::where('status', 'completed')->count(),
            'total_amount' => WithdrawalRequest::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.withdrawals', compact('withdrawals', 'stats'));
    }

    public function approve(Request $request, WithdrawalRequest $withdrawal)
    {
        $withdrawal->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        // Notify operator
        $this->notificationService->notifyWithdrawalApproved($withdrawal);

        return back()->with('success', 'Withdrawal request approved');
    }

    public function complete(Request $request, WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status === 'completed') {
            return back()->with('error', 'Withdrawal sudah diproses sebelumnya');
        }

        // Update withdrawal status
        $withdrawal->update([
            'status' => 'completed',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        // Deduct from user's earnings based on role
        $operator = $withdrawal->user;

        if ($operator->isGuru()) {
            // Guru: deduct from guru_total_earnings on users table
            $operator->decrement('guru_total_earnings', $withdrawal->amount);
        } elseif ($operator->isClient()) {
            // Client: deduct from referral_earnings
            $operator->decrement('referral_earnings', $withdrawal->amount);
        } else {
            // Operator: deduct from operator_profiles.total_earnings
            $profile = $operator->operatorProfile;
            if ($profile) {
                $profile->decrement('total_earnings', $withdrawal->amount);
            }
        }

        // Notify operator
        $this->notificationService->notifyWithdrawalCompleted($withdrawal);

        return back()->with('success', 'Withdrawal completed dan balance operator telah dikurangi');
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $withdrawal->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        // Notify operator
        $this->notificationService->notifyWithdrawalRejected($withdrawal);

        return back()->with('success', 'Withdrawal request rejected');
    }
}
