@extends('layouts.admin')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola semua user di platform</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
            + Add User
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Total Users</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Clients</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['clients'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Operators</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['operators'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Gurus</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['gurus'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600 mb-1">Admins</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['admins'] }}</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Orders</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Earnings</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Joined</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded
                                @if($user->role === 'admin') bg-red-100 text-red-700
                                @elseif($user->role === 'operator') bg-green-100 text-green-700
                                @elseif($user->role === 'guru') bg-purple-100 text-purple-700
                                @else bg-blue-100 text-blue-700
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $user->orders_count + $user->operator_orders_count }}
                        </td>
                        <td class="px-4 py-3">
                            @if($user->role === 'operator' && $user->operatorProfile)
                                <span class="text-sm font-medium text-green-600">
                                    Rp {{ number_format($user->operatorProfile->total_earnings ?? 0, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($user->role === 'operator' && $user->operatorProfile)
                                @if($user->operatorProfile->is_verified)
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-700">Verified</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-700">Unverified</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-700">Edit</a>
                            
                            @if($user->role === 'operator' && $user->operatorProfile)
                                @if($user->operatorProfile->is_verified)
                                    <form action="{{ route('admin.users.unverify', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-700">Unverify</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-700">Verify</button>
                                    </form>
                                @endif
                            @endif
                            
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
