<?php

namespace App\Http\Controllers;

use App\Models\CopywritingRequest;
use App\Models\Order;
use App\Services\AIService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CopywritingRequestController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $requests = auth()->user()->copywritingRequests()
            ->with('order.package')
            ->latest()
            ->paginate(20);
        
        return view('copywriting.index', compact('requests'));
    }

    public function create(Order $order)
    {
        $this->authorize('view', $order);
        
        return view('copywriting.create', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'type' => 'required|in:caption,product_description,email,cta,headline',
            'platform' => 'nullable|string',
            'brief' => 'required|string',
            'product_images' => 'nullable|array',
            'product_images.*' => 'image|max:2048',
            'tone' => 'required|string',
            'keywords' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $this->authorize('view', $order);

        // Check quota
        if ($validated['type'] === 'caption' && $order->remaining_caption_quota <= 0) {
            return back()->with('error', 'Kuota caption sudah habis');
        }

        if ($validated['type'] === 'product_description' && $order->remaining_product_description_quota <= 0) {
            return back()->with('error', 'Kuota deskripsi produk sudah habis');
        }

        // Handle image upload
        $imagePaths = [];
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
        }

        // Generate AI content
        try {
            $aiContent = $this->aiService->generateCopywriting([
                'type' => $validated['type'],
                'brief' => $validated['brief'],
                'tone' => $validated['tone'],
                'platform' => $validated['platform'] ?? 'instagram',
                'keywords' => $validated['keywords'] ?? '',
            ]);
        } catch (\Exception $e) {
            $aiContent = null;
        }

        $copywritingRequest = CopywritingRequest::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'platform' => $validated['platform'],
            'brief' => $validated['brief'],
            'product_images' => $imagePaths,
            'tone' => $validated['tone'],
            'keywords' => $validated['keywords'],
            'ai_generated_content' => $aiContent,
            'status' => 'pending',
            'deadline' => $validated['deadline'] ? Carbon::parse($validated['deadline']) : null,
        ]);

        return redirect()->route('copywriting.show', $copywritingRequest)
            ->with('success', 'Request copywriting berhasil dibuat');
    }

    public function show(CopywritingRequest $copywriting)
    {
        $this->authorize('view', $copywriting);
        
        $copywriting->load(['order.package', 'assignedTo']);
        
        return view('copywriting.show', compact('copywriting'));
    }

    public function update(Request $request, CopywritingRequest $copywriting)
    {
        $this->authorize('update', $copywriting);

        $validated = $request->validate([
            'final_content' => 'required|string',
            'status' => 'required|in:pending,in_progress,review,revision,completed,cancelled',
        ]);

        $copywriting->update($validated);

        if ($validated['status'] === 'completed') {
            $copywriting->update(['completed_at' => Carbon::now()]);
            
            // Update quota
            $order = $copywriting->order;
            if ($copywriting->type === 'caption') {
                $order->increment('used_caption_quota');
            } elseif ($copywriting->type === 'product_description') {
                $order->increment('used_product_description_quota');
            }
        }

        return back()->with('success', 'Copywriting berhasil diupdate');
    }

    public function requestRevision(Request $request, CopywritingRequest $copywriting)
    {
        $this->authorize('view', $copywriting);

        $validated = $request->validate([
            'revision_notes' => 'required|string',
        ]);

        if ($copywriting->revision_count >= $copywriting->order->package->revision_limit) {
            return back()->with('error', 'Batas revisi sudah tercapai');
        }

        $copywriting->update([
            'status' => 'revision',
            'revision_notes' => $validated['revision_notes'],
            'revision_count' => $copywriting->revision_count + 1,
        ]);

        return back()->with('success', 'Request revisi berhasil dikirim');
    }

    public function rate(Request $request, CopywritingRequest $copywriting)
    {
        $this->authorize('view', $copywriting);

        $validated = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        $copywriting->update($validated);

        return back()->with('success', 'Terima kasih atas rating Anda');
    }

    // Operator/Admin methods
    public function queue()
    {
        $requests = CopywritingRequest::whereIn('status', ['pending', 'in_progress', 'revision'])
            ->with(['user', 'order.package'])
            ->orderBy('deadline')
            ->paginate(20);
        
        return view('operator.queue', compact('requests'));
    }

    public function assign(Request $request, CopywritingRequest $copywriting)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $copywriting->update([
            'assigned_to' => $validated['assigned_to'],
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'Request berhasil di-assign');
    }
}
