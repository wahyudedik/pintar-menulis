<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = WithdrawalRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'pending' => $withdrawals->where('status', 'pending')->count(),
            'approved' => $withdrawals->where('status', 'approved')->count(),
            'completed' => $withdrawals->where('status', 'completed')->count(),
            'total_amount' => $withdrawals->where('status', 'completed')->sum('amount'),
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

        return back()->with('success', 'Withdrawal request approved');
    }

    public function complete(Request $request, WithdrawalRequest $withdrawal)
    {
        $withdrawal->update([
            'status' => 'completed',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return back()->with('success', 'Withdrawal completed');
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

        return back()->with('success', 'Withdrawal request rejected');
    }
}
