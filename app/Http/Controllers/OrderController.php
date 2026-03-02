<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('package')
            ->latest()
            ->get();
        
        return view('orders.index', compact('orders'));
    }

    public function create(Package $package)
    {
        return view('orders.create', compact('package'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $package = Package::findOrFail($validated['package_id']);
        
        $order = Order::create([
            'user_id' => auth()->id(),
            'package_id' => $package->id,
            'project_id' => $validated['project_id'] ?? null,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
            'status' => 'active',
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Berhasil subscribe paket ' . $package->name);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        $order->load(['package', 'copywritingRequests']);
        
        return view('orders.show', compact('order'));
    }
}
