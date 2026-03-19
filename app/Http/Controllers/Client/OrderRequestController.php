<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OperatorProfile;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class OrderRequestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function index()
    {
        // Get all operators with their profiles
        $operators = User::where('role', 'operator')
            ->whereHas('operatorProfile', function($query) {
                $query->where('is_available', true);
            })
            ->with('operatorProfile')
            ->get()
            ->map(function($user) {
                $profile = $user->operatorProfile;
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'bio' => $profile->bio,
                    'rating' => $profile->average_rating,
                    'reviews' => $profile->total_reviews,
                    'base_price' => $profile->base_price,
                    'completed_orders' => $profile->completed_orders,
                    'specializations' => $profile->specializations,
                    'available' => $profile->is_available,
                ];
            });

        return view('client.browse-operators', compact('operators'));
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'operator_id' => 'required|exists:users,id',
            'category'    => 'required|string',
            'brief'       => 'nullable|string|min:20',
            'brief_file'  => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg,webp|max:10240',
            'budget'      => 'required|integer|min:50000',
            'deadline'    => 'required|date|after_or_equal:today',
        ], [
            'brief.min'               => 'Brief minimal 20 karakter.',
            'brief_file.mimes'        => 'File harus berformat PDF, DOC, DOCX, TXT, atau gambar.',
            'brief_file.max'          => 'Ukuran file maksimal 10 MB.',
            'budget.min'              => 'Budget minimal Rp 50.000.',
            'deadline.after_or_equal' => 'Deadline tidak boleh sebelum hari ini.',
            'operator_id.required'    => 'Operator harus dipilih.',
            'category.required'       => 'Kategori harus dipilih.',
        ]);

        // At least brief text OR brief file must be provided
        if (!$request->filled('brief') && !$request->hasFile('brief_file')) {
            return response()->json([
                'success' => false,
                'message' => 'Isi brief teks atau upload file brief.',
            ], 422);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(' ', $validator->errors()->all()),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle file upload
        $briefFilePath = null;
        $briefFileOriginalName = null;
        if ($request->hasFile('brief_file')) {
            $file = $request->file('brief_file');
            $briefFileOriginalName = $file->getClientOriginalName();
            $briefFilePath = $file->store('orders/briefs', 'public');
        }

        $order = Order::create([
            'user_id'                  => auth()->id(),
            'operator_id'              => $validated['operator_id'],
            'category'                 => $validated['category'],
            'brief'                    => $validated['brief'] ?? null,
            'brief_file'               => $briefFilePath,
            'brief_file_original_name' => $briefFileOriginalName,
            'budget'                   => $validated['budget'],
            'deadline'                 => $validated['deadline'],
            'status'                   => 'pending',
            'payment_status'           => 'pending_payment',
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Order berhasil dibuat! Silakan lakukan pembayaran.',
            'order_id'     => $order->id,
            'redirect_url' => route('payment.show', $order),
        ]);
    }
}
