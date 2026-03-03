<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('orders')->get();
        return view('admin.packages', compact('packages'));
    }

    public function edit(Package $package)
    {
        return view('admin.package-edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer|min:0',
            'caption_quota' => 'required|integer|min:0',
            'product_description_quota' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $package->update($validated);

        return redirect()->route('admin.packages')
            ->with('success', 'Package berhasil diupdate');
    }
}
