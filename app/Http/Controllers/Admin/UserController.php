<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OperatorProfile;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    // User Management Dashboard
    public function index()
    {
        $users = User::withCount(['orders', 'operatorOrders'])
            ->with('operatorProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_users' => User::count(),
            'clients' => User::where('role', 'client')->count(),
            'operators' => User::where('role', 'operator')->count(),
            'gurus' => User::where('role', 'guru')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    // Create User
    public function create()
    {
        return view('admin.user-create');
    }

    // Store User
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:client,operator,guru,admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // If operator, create profile
        if ($validated['role'] === 'operator') {
            OperatorProfile::create([
                'user_id' => $user->id,
                'bio' => '',
                'specializations' => [],
                'base_price' => 50000,
                'is_available' => false,
            ]);
        }

        return redirect()->route('admin.users')
            ->with('success', 'User berhasil ditambahkan');
    }

    // Edit User
    public function edit(User $user)
    {
        return view('admin.user-edit', compact('user'));
    }

    // Update User
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:client,operator,guru,admin',
            'password' => 'nullable|string|min:8',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', 'User berhasil diupdate');
    }

    // Delete User
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }

    // Verify Operator
    public function verifyOperator(User $user)
    {
        if ($user->role !== 'operator') {
            return back()->with('error', 'User bukan operator');
        }

        $profile = $user->operatorProfile;
        if ($profile) {
            $profile->update([
                'is_verified' => true,
                'verified_at' => now(),
            ]);
        }

        // Notify operator
        $this->notificationService->notifyOperatorVerified($user);

        return back()->with('success', 'Operator berhasil diverifikasi');
    }

    // Unverify Operator
    public function unverifyOperator(User $user)
    {
        if ($user->role !== 'operator') {
            return back()->with('error', 'User bukan operator');
        }

        $profile = $user->operatorProfile;
        if ($profile) {
            $profile->update([
                'is_verified' => false,
                'verified_at' => null,
            ]);
        }

        // Notify operator
        $this->notificationService->notifyOperatorUnverified($user);

        return back()->with('success', 'Verifikasi operator dibatalkan');
    }
}
