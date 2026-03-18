@extends('layouts.admin')

@section('title', 'Monitor Operator')

@section('content')
<div class="p-6">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">👷 Monitor Operator</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola dan pantau semua operator di platform</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Operator</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Terverifikasi</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['verified'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Aktif / Online</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Komisi Order</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $commissionRate }}%</p>
            <p class="text-xs text-gray-400">
                <a href="{{ route('admin.marketplace.index') }}?tab=commission" class="text-blue-500 hover:underline">Ubah di Marketplace</a>
            </p>
        </div>
    </div>

    <!-- Operators Table -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Daftar Operator</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Operator</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Total Order</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Harga Dasar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Bergabung</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($operators as $operator)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm flex-shrink-0">
                                    {{ substr($operator->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $operator->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $operator->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                @if($operator->operatorProfile?->is_verified)
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full w-fit">✓ Terverifikasi</span>
                                @else
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full w-fit">Belum Verifikasi</span>
                                @endif
                                @if($operator->operatorProfile?->is_available)
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full w-fit">● Aktif</span>
                                @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-full w-fit">○ Tidak Aktif</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $operator->operator_orders_count ?? 0 }} order</td>
                        <td class="px-4 py-3 text-gray-700">
                            Rp {{ number_format($operator->operatorProfile?->base_price ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $operator->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                @if(!$operator->operatorProfile?->is_verified)
                                <form action="{{ route('admin.users.verify', $operator) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700">Verifikasi</button>
                                </form>
                                @else
                                <form action="{{ route('admin.users.unverify', $operator) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 border border-red-300 text-red-600 text-xs rounded-lg hover:bg-red-50">Cabut</button>
                                </form>
                                @endif
                                <a href="{{ route('admin.users.edit', $operator) }}"
                                   class="px-3 py-1 border border-gray-300 text-gray-600 text-xs rounded-lg hover:bg-gray-50">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada operator terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $operators->links() }}
        </div>
    </div>

</div>
@endsection
