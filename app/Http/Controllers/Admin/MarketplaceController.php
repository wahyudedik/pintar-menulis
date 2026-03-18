<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserTemplate;
use App\Models\TemplatePurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        // Templates pending approval
        $pendingTemplates = UserTemplate::with('user')
            ->where('status', 'pending')
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // All approved templates
        $approvedTemplates = UserTemplate::with('user')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(30, ['*'], 'approved_page');

        // All purchases
        $purchases = TemplatePurchase::with(['buyer', 'seller', 'template'])
            ->orderBy('created_at', 'desc')
            ->paginate(30, ['*'], 'purchase_page');

        // Stats
        $stats = [
            'pending_approval'  => $pendingTemplates->count(),
            'total_templates'   => UserTemplate::where('status', 'approved')->count(),
            'total_purchases'   => TemplatePurchase::where('payment_status', 'completed')->count(),
            'total_revenue'     => TemplatePurchase::where('payment_status', 'completed')->sum('price_paid'),
            'platform_earnings' => TemplatePurchase::where('payment_status', 'completed')
                ->sum(DB::raw('price_paid * ' . (config('marketplace.commission_rate', 20) / 100))),
        ];

        $commissionRate = config('marketplace.commission_rate', 20);

        return view('admin.marketplace', compact(
            'tab', 'pendingTemplates', 'approvedTemplates', 'purchases', 'stats', 'commissionRate'
        ));
    }

    public function approveTemplate(UserTemplate $template)
    {
        $template->update([
            'status'      => 'approved',
            'is_approved' => true,
        ]);

        return back()->with('success', "Template \"{$template->title}\" berhasil diapprove.");
    }

    public function rejectTemplate(Request $request, UserTemplate $template)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $template->update([
            'status'           => 'rejected',
            'is_approved'      => false,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', "Template \"{$template->title}\" ditolak.");
    }

    public function deleteTemplate(UserTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Template berhasil dihapus.');
    }

    public function updateCommission(Request $request)
    {
        $validated = $request->validate([
            'template_commission_rate' => 'required|numeric|min:0|max:100',
            'order_commission_rate'    => 'required|numeric|min:0|max:100',
        ]);

        cache()->forever('commission.template_rate', $validated['template_commission_rate']);
        cache()->forever('commission.order_rate', $validated['order_commission_rate']);

        return back()->with('success', 'Komisi berhasil diperbarui.');
    }

    public function verifyPurchase(TemplatePurchase $purchase)
    {
        if ($purchase->payment_status !== 'processing') {
            return back()->with('error', 'Pembelian sudah diproses sebelumnya.');
        }

        $commissionRate = cache('commission.template_rate', config('marketplace.commission_rate', 20));
        $sellerEarnings = $purchase->price_paid * (1 - $commissionRate / 100);

        $purchase->update([
            'payment_status' => 'completed',
            'purchased_at'   => now(),
        ]);

        // Credit seller earnings
        $purchase->seller->increment('referral_earnings', $sellerEarnings);

        return back()->with('success', "Pembelian diverifikasi. Penjual mendapat Rp " . number_format($sellerEarnings, 0, ',', '.'));
    }

    public function rejectPurchase(Request $request, TemplatePurchase $purchase)
    {
        $purchase->update(['payment_status' => 'failed']);
        return back()->with('success', 'Pembelian ditolak.');
    }
}
