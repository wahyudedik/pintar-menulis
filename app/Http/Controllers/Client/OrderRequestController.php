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
        $validated = $request->validate([
            'operator_id' => 'required|exists:users,id',
            'category' => 'required|string',
            'brief' => 'required|string|min:20',
            'budget' => 'required|integer|min:50000',
            'deadline' => 'required|date|after:today',
        ]);

        // Create order with pending_payment status
        $order = Order::create([
            'user_id' => auth()->id(),
            'operator_id' => $validated['operator_id'], // Store requested operator
            'category' => $validated['category'],
            'brief' => $validated['brief'],
            'budget' => $validated['budget'],
            'deadline' => $validated['deadline'],
            'status' => 'pending_payment', // Order not visible to operator yet!
            'payment_status' => 'pending_payment',
        ]);

        // ESCROW: Redirect to payment page instead of notifying operator
        // Operator will be notified AFTER payment is verified by admin
        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat! Silakan lakukan pembayaran.',
            'order_id' => $order->id,
            'redirect_url' => route('payment.show', $order),
        ]);
    }
}
