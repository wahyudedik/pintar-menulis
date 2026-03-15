<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Models\WithdrawalRequest;

class ReferralController extends Controller
{
    public function index()
    {
        $referrals = Referral::with(['referrer', 'referredUser', 'subscription.package'])
            ->orderByDesc('created_at')
            ->paginate(30);

        $stats = [
            'total'     => Referral::count(),
            'pending'   => Referral::where('status', 'pending')->count(),
            'converted' => Referral::where('status', 'converted')->count(),
            'total_commission' => Referral::where('status', 'converted')->sum('commission_amount'),
            'top_referrers' => User::where('referral_earnings', '>', 0)
                ->orderByDesc('referral_earnings')
                ->limit(5)
                ->get(['id', 'name', 'email', 'referral_earnings']),
        ];

        return view('admin.referrals.index', compact('referrals', 'stats'));
    }
}
