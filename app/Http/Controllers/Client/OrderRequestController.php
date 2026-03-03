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

        $order = Order::create([
            'user_id' => auth()->id(),
            'operator_id' => null, // Set to null initially, operator will accept later
            'category' => $validated['category'],
            'brief' => $validated['brief'],
            'budget' => $validated['budget'],
            'deadline' => $validated['deadline'],
            'status' => 'pending',
        ]);

        // Send notification to the requested operator
        $this->notificationService->notifyNewOrder($order, $validated['operator_id']);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dikirim!',
            'order_id' => $order->id,
        ]);
    }
}
