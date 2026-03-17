<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('orders')->orderBy('sort_order')->get();
        $totalSubs = UserSubscription::whereIn('status', ['active', 'trial'])->count();
        $pendingSubs = UserSubscription::where('status', 'pending_payment')->count();
        return view('admin.packages', compact('packages', 'totalSubs', 'pendingSubs'));
    }

    public function create()
    {
        $featureGroups = \App\Enums\PackageFeatures::groups();
        $featureLabels = \App\Enums\PackageFeatures::labels();
        return view('admin.package-create', compact('featureGroups', 'featureLabels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                       => 'required|string|max:255',
            'description'                => 'required|string',
            'price'                      => 'required|integer|min:0',
            'yearly_price'               => 'nullable|integer|min:0',
            'ai_quota_monthly'           => 'required|integer|min:0',
            'caption_quota'              => 'nullable|integer|min:0',
            'product_description_quota'  => 'nullable|integer|min:0',
            'trial_days'                 => 'nullable|integer|min:0',
            'badge_text'                 => 'nullable|string|max:50',
            'badge_color'                => 'nullable|in:green,blue,red,purple,yellow,gray',
            'sort_order'                 => 'nullable|integer|min:0',
        ]);

        if ($request->filled('features_text')) {
            $validated['features'] = array_values(array_filter(
                array_map('trim', explode("\n", $request->features_text))
            ));
        }

        $validated['allowed_features'] = $request->input('allowed_features', []);
        $validated['is_active']        = $request->boolean('is_active');
        $validated['is_featured']      = $request->boolean('is_featured');
        $validated['has_trial']        = $request->boolean('has_trial');

        Package::create($validated);

        return redirect()->route('admin.packages')
            ->with('success', 'Paket baru berhasil dibuat.');
    }

    public function edit(Package $package)
    {
        $featureGroups = \App\Enums\PackageFeatures::groups();
        $featureLabels = \App\Enums\PackageFeatures::labels();
        return view('admin.package-edit', compact('package', 'featureGroups', 'featureLabels'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name'                       => 'required|string|max:255',
            'description'                => 'required|string',
            'price'                      => 'required|integer|min:0',
            'yearly_price'               => 'nullable|integer|min:0',
            'ai_quota_monthly'           => 'required|integer|min:0',
            'caption_quota'              => 'nullable|integer|min:0',
            'product_description_quota'  => 'nullable|integer|min:0',
            'trial_days'                 => 'nullable|integer|min:0',
            'badge_text'                 => 'nullable|string|max:50',
            'badge_color'                => 'nullable|in:green,blue,red,purple,yellow,gray',
            'sort_order'                 => 'nullable|integer|min:0',
        ]);

        if ($request->filled('features_text')) {
            $validated['features'] = array_values(array_filter(
                array_map('trim', explode("\n", $request->features_text))
            ));
        }

        $validated['allowed_features'] = $request->input('allowed_features', []);
        $validated['is_active']        = $request->boolean('is_active');
        $validated['is_featured']      = $request->boolean('is_featured');
        $validated['has_trial']        = $request->boolean('has_trial');

        $package->update($validated);

        return redirect()->route('admin.packages')
            ->with('success', 'Package berhasil diupdate.');
    }

    public function destroy(Package $package)
    {
        // Cegah hapus paket yang masih punya subscriber aktif
        $activeCount = $package->subscriptions()->whereIn('status', ['active', 'trial'])->count();
        if ($activeCount > 0) {
            return back()->with('error', "Tidak bisa hapus paket \"{$package->name}\" — masih ada {$activeCount} subscriber aktif.");
        }

        $package->delete();
        return redirect()->route('admin.packages')->with('success', "Paket \"{$package->name}\" berhasil dihapus.");
    }

    public function toggle(Package $package)
    {
        $package->update(['is_active' => !$package->is_active]);
        $status = $package->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Paket \"{$package->name}\" berhasil {$status}.");
    }

    public function subscriptions(Request $request)
    {
        $query = UserSubscription::with(['user', 'package'])->latest();

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->paginate(20)->withQueryString();

        return view('admin.subscriptions', compact('subscriptions'));
    }
}
