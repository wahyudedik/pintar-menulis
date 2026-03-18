<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\UserTemplate;
use App\Models\TemplateRating;
use App\Models\TemplateFavorite;
use App\Models\TemplatePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TemplateMarketplaceController extends Controller
{
    // 📚 List all templates (system + community)
    public function index(Request $request)
    {
        $query = UserTemplate::with(['user', 'ratings'])
            ->where(function($q) {
                $q->where('is_public', true)
                  ->where('is_approved', true);
            });

        // Filters
        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->platform) {
            $query->where('platform', $request->platform);
        }

        if ($request->tone) {
            $query->where('tone', $request->tone);
        }

        if ($request->type === 'free') {
            $query->where('is_premium', false);
        } elseif ($request->type === 'premium') {
            $query->where('is_premium', true);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        switch ($request->sort) {
            case 'popular':
                $query->orderBy('usage_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_average', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('usage_count', 'desc');
        }

        $templates = $query->paginate(24);

        // Add favorite status for logged-in users
        if (Auth::check()) {
            $favoriteIds = TemplateFavorite::where('user_id', Auth::id())
                ->pluck('template_id')
                ->toArray();
            
            $templates->each(function($template) use ($favoriteIds) {
                $template->is_favorited = in_array($template->id, $favoriteIds);
            });
        }

        return view('client.template-marketplace.index', compact('templates'));
    }

    // 📝 Create template form
    public function create()
    {
        return view('client.template-marketplace.create-new');
    }

    // 💾 Store new template
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'platform' => 'required|string',
            'tone' => 'required|string',
            'template_content' => 'required|string',
            'format_instructions' => 'nullable|string',
            'tags' => 'nullable|array',
            'is_public' => 'boolean',
            'is_premium' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'license_type' => 'required|in:free,personal,commercial,extended',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = $request->is_public ? 'pending' : 'draft';

        $template = UserTemplate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dibuat!',
            'template' => $template
        ]);
    }

    // 👁️ Show template detail
    public function show($id)
    {
        $template = UserTemplate::with(['user', 'ratings.user'])
            ->findOrFail($id);

        $template->is_favorited = Auth::check()
            ? $template->isFavoritedBy(Auth::id())
            : false;

        $template->user_rating = Auth::check()
            ? TemplateRating::where('template_id', $id)
                ->where('user_id', Auth::id())
                ->first()
            : null;

        return view('client.template-marketplace.show', compact('template'));
    }

    // ✏️ Edit template
    public function edit($id)
    {
        $template = UserTemplate::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('client.template-marketplace.edit', compact('template'));
    }

    // 🔄 Update template
    public function update(Request $request, $id)
    {
        $template = UserTemplate::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'platform' => 'required|string',
            'tone' => 'required|string',
            'template_content' => 'required|string',
            'format_instructions' => 'nullable|string',
            'tags' => 'nullable|array',
            'is_public' => 'boolean',
            'is_premium' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'license_type' => 'required|in:free,personal,commercial,extended',
        ]);

        // If making public, set to pending approval
        if ($request->is_public && !$template->is_public) {
            $validated['status'] = 'pending';
            $validated['is_approved'] = false;
        }

        $template->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil diupdate!',
            'template' => $template
        ]);
    }

    // 🗑️ Delete template
    public function destroy($id)
    {
        $template = UserTemplate::where('user_id', Auth::id())
            ->findOrFail($id);

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dihapus!'
        ]);
    }

    // ⭐ Rate template
    public function rate(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $template = UserTemplate::findOrFail($id);

        // Can't rate own template
        if ($template->user_id == Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa memberi rating pada template sendiri.'
            ], 403);
        }

        $rating = TemplateRating::updateOrCreate(
            [
                'template_id' => $id,
                'user_id' => Auth::id()
            ],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil disimpan!',
            'rating' => $rating,
            'template_rating' => [
                'average' => $template->fresh()->rating_average,
                'total' => $template->fresh()->total_ratings
            ]
        ]);
    }

    // ❤️ Toggle favorite
    public function toggleFavorite($id)
    {
        $template = UserTemplate::findOrFail($id);

        $favorite = TemplateFavorite::where('template_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
            $message = 'Template dihapus dari favorit';
        } else {
            TemplateFavorite::create([
                'template_id' => $id,
                'user_id' => Auth::id()
            ]);
            $isFavorited = true;
            $message = 'Template ditambahkan ke favorit';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorited' => $isFavorited,
            'favorite_count' => $template->fresh()->favorite_count
        ]);
    }

    // 📥 Use template (increment usage)
    public function use($id)
    {
        $template = UserTemplate::findOrFail($id);

        // Check access for premium templates
        if (!$template->canBeAccessedBy(Auth::id())) {
            return response()->json([
                'success' => false,
                'needs_purchase' => true,
                'message' => 'Anda perlu membeli template ini terlebih dahulu.',
                'purchase_url' => route('templates.purchase', $id),
            ], 403);
        }

        $template->incrementUsage();

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    // 💳 Purchase premium template
    public function purchase($id)
    {
        $template = UserTemplate::findOrFail($id);

        if (!$template->is_premium || $template->price <= 0) {
            return redirect()->route('templates.show', $id)
                ->with('info', 'Template ini gratis, langsung gunakan saja.');
        }

        if ($template->user_id === Auth::id()) {
            return redirect()->route('templates.show', $id)
                ->with('info', 'Ini template milik Anda sendiri.');
        }

        if ($template->isPurchasedBy(Auth::id())) {
            return redirect()->route('templates.show', $id)
                ->with('info', 'Anda sudah memiliki template ini.');
        }

        // Create pending purchase record
        $purchase = TemplatePurchase::create([
            'buyer_id'       => Auth::id(),
            'template_id'    => $template->id,
            'seller_id'      => $template->user_id,
            'price_paid'     => $template->price,
            'license_type'   => $template->license_type ?? 'personal',
            'payment_status' => 'pending',
        ]);

        return redirect()->route('templates.checkout', $purchase->id);
    }

    // 🧾 Checkout page for template purchase
    public function checkout($purchaseId)
    {
        $purchase = TemplatePurchase::with(['template', 'seller'])
            ->where('buyer_id', Auth::id())
            ->where('payment_status', 'pending')
            ->findOrFail($purchaseId);

        $commissionRate = cache('commission.template_rate', config('marketplace.commission_rate', 20));
        $sellerEarnings = $purchase->price_paid * (1 - $commissionRate / 100);

        return view('client.template-marketplace.checkout', compact('purchase', 'commissionRate', 'sellerEarnings'));
    }

    // ✅ Confirm template purchase (after manual payment proof)
    public function confirmPurchase(Request $request, $purchaseId)
    {
        $purchase = TemplatePurchase::where('buyer_id', Auth::id())
            ->where('payment_status', 'pending')
            ->findOrFail($purchaseId);

        $validated = $request->validate([
            'proof_image' => 'required|image|max:2048',
        ]);

        $path = $request->file('proof_image')->store('template-payments', 'public');

        $purchase->update([
            'payment_status' => 'processing',
            'transaction_id' => 'TMP-' . strtoupper(Str::random(10)),
            'payment_details' => ['proof_image' => $path],
        ]);

        return redirect()->route('templates.show', $purchase->template_id)
            ->with('success', 'Bukti pembayaran berhasil dikirim. Admin akan memverifikasi dalam 1x24 jam.');
    }
}
