<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\MLTrainingData;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function earnings()
    {
        $user = auth()->user();

        $pendingWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        $availableBalance = $user->guru_total_earnings - $pendingWithdrawals;

        $myEntries = MLTrainingData::where('guru_id', $user->id)
            ->latest()
            ->paginate(20);

        $stats = [
            'total_entries'     => MLTrainingData::where('guru_id', $user->id)->count(),
            'total_earnings'    => $user->guru_total_earnings,
            'pending_withdrawal'=> $pendingWithdrawals,
            'available_balance' => $availableBalance,
            'earnings_per_entry'=> \App\Http\Controllers\Guru\MLTrainingController::EARNINGS_PER_ENTRY,
        ];

        return view('guru.earnings', compact('stats', 'myEntries'));
    }

    public function create()
    {
        $user = auth()->user();

        $pendingWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        $availableBalance = $user->guru_total_earnings - $pendingWithdrawals;

        return view('guru.withdrawal-create', compact('availableBalance', 'user'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'amount'         => 'required|integer|min:10000',
            'bank_name'      => 'required|string',
            'account_number' => 'required|string',
            'account_name'   => 'required|string',
            'notes'          => 'nullable|string',
        ]);

        $pendingWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        $availableBalance = $user->guru_total_earnings - $pendingWithdrawals;

        if ($validated['amount'] > $availableBalance) {
            return back()->with('error', 'Saldo tidak mencukupi');
        }

        $withdrawal = WithdrawalRequest::create([
            'user_id'        => $user->id,
            'type'           => 'guru',
            'amount'         => $validated['amount'],
            'bank_name'      => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name'   => $validated['account_name'],
            'notes'          => $validated['notes'] ?? null,
            'status'         => 'pending',
        ]);

        $this->notificationService->notifyWithdrawalSubmitted($user, $withdrawal);

        return redirect()->route('guru.earnings')
            ->with('success', 'Request withdrawal berhasil! Menunggu approval admin.');
    }

    public function history()
    {
        $withdrawals = WithdrawalRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.withdrawal-history', compact('withdrawals'));
    }
}
