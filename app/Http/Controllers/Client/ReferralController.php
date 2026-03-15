<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function __construct(protected ReferralService $referralService) {}

    public function index()
    {
        $user  = auth()->user();
        $stats = $this->referralService->getStats($user);

        $withdrawalHistory = WithdrawalRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('client.referral.index', compact('stats', 'withdrawalHistory'));
    }

    public function withdrawCreate()
    {
        $user  = auth()->user();
        $stats = $this->referralService->getStats($user);

        return view('client.referral.withdraw', compact('stats', 'user'));
    }

    public function withdrawStore(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'amount'         => 'required|integer|min:10000',
            'bank_name'      => 'required|string',
            'account_number' => 'required|string',
            'account_name'   => 'required|string',
            'notes'          => 'nullable|string',
        ]);

        $stats = $this->referralService->getStats($user);

        if ($validated['amount'] > $stats['available_balance']) {
            return back()->with('error', 'Saldo referral tidak mencukupi.');
        }

        WithdrawalRequest::create([
            'user_id'        => $user->id,
            'amount'         => $validated['amount'],
            'bank_name'      => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name'   => $validated['account_name'],
            'notes'          => $validated['notes'] ?? null,
            'status'         => 'pending',
        ]);

        return redirect()->route('client.referral.index')
            ->with('success', 'Request withdrawal referral berhasil! Menunggu approval admin.');
    }
}
