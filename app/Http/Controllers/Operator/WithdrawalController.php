<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function create()
    {
        $profile = auth()->user()->operatorProfile;
        $pendingWithdrawals = WithdrawalRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = ($profile->total_earnings ?? 0) - $pendingWithdrawals;

        return view('operator.withdrawal-create', compact('profile', 'availableBalance', 'pendingWithdrawals'));
    }

    public function store(Request $request)
    {
        $profile = auth()->user()->operatorProfile;

        $validated = $request->validate([
            'amount' => 'required|integer|min:50000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Check available balance
        $pendingWithdrawals = WithdrawalRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = ($profile->total_earnings ?? 0) - $pendingWithdrawals;

        if ($validated['amount'] > $availableBalance) {
            return back()->with('error', 'Saldo tidak mencukupi');
        }

        $withdrawal = WithdrawalRequest::create([
            'user_id' => auth()->id(),
            'type'    => 'operator',
            'amount' => $validated['amount'],
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Notify operator with confirmation
        $this->notificationService->notifyWithdrawalSubmitted(auth()->user(), $withdrawal);

        return redirect()->route('operator.earnings')
            ->with('success', 'Request withdrawal berhasil disubmit! Menunggu approval admin.');
    }

    public function history()
    {
        $withdrawals = WithdrawalRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('operator.withdrawal-history', compact('withdrawals'));
    }
}
