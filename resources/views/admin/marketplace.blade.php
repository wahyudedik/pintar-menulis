@extends('layouts.admin')

@section('title', 'Marketplace Monitor')

@section('content')
<div class="p-6" x-data="{ tab: '{{ $tab }}', rejectModal: false, rejectId: null }">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📚 Marketplace Monitor</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola template, transaksi, dan komisi marketplace</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Pending Approval</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending_approval'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Templates</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_templates'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Pembelian</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['total_purchases'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Revenue</p>
            <p class="text-lg font-bold text-green-600 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Platform Earnings</p>
            <p class="text-lg font-bold text-purple-600 mt-1">Rp {{ number_format($stats['platform_earnings'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 mb-4 bg-gray-100 rounded-xl p-1 w-fit">
        <button @click="tab = 'pending'" :class="tab === 'pending' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
            ⏳ Pending Approval
            @if($stats['pending_approval'] > 0)
            <span class="ml-1 px-1.5 py-0.5 bg-yellow-500 text-white text-xs rounded-full">{{ $stats['pending_approval'] }}</span>
            @endif
        </button>
        <button @click="tab = 'templates'" :class="tab === 'templates' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
            📋 Semua Template
        </button>
        <button @click="tab = 'purchases'" :class="tab === 'purchases' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
            💳 Transaksi
        </button>
        <button @click="tab = 'commission'" :class="tab === 'commission' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
            ⚙️ Komisi
        </button>
    </div>

    <!-- Tab: Pending Approval -->
    <div x-show="tab === 'pending'">
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Template Menunggu Persetujuan</h3>
            </div>
            @if($pendingTemplates->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($pendingTemplates as $template)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-900 text-sm">{{ $template->title }}</span>
                                @if($template->is_premium)
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">Premium — Rp {{ number_format($template->price, 0, ',', '.') }}</span>
                                @else
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Gratis</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mb-2">Oleh: {{ $template->user->name }} · {{ $template->created_at->diffForHumans() }}</p>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $template->description }}</p>
                            <div class="mt-2 p-3 bg-gray-50 rounded text-xs text-gray-700 font-mono line-clamp-3">{{ $template->template_content }}</div>
                        </div>
                        <div class="flex flex-col gap-2 flex-shrink-0">
                            <form action="{{ route('admin.marketplace.templates.approve', $template) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition">
                                    ✓ Approve
                                </button>
                            </form>
                            <button @click="rejectModal = true; rejectId = {{ $template->id }}"
                                    class="px-4 py-2 border border-red-300 text-red-600 text-xs font-medium rounded-lg hover:bg-red-50 transition">
                                ✗ Tolak
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-12 text-center text-gray-400">
                <p class="text-4xl mb-3">✅</p>
                <p class="text-sm">Tidak ada template yang menunggu persetujuan.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Tab: All Templates -->
    <div x-show="tab === 'templates'">
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Semua Template Approved</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Template</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Pemilik</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Penjualan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Rating</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($approvedTemplates as $template)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ Str::limit($template->title, 40) }}</p>
                                <p class="text-xs text-gray-500">{{ $template->category }} · {{ $template->platform }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $template->user->name }}</td>
                            <td class="px-4 py-3">
                                @if($template->is_premium)
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">Premium</span>
                                @else
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Gratis</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-900">
                                {{ $template->is_premium ? 'Rp ' . number_format($template->price, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $template->total_sales ?? 0 }}x</td>
                            <td class="px-4 py-3">
                                <span class="text-yellow-500">★</span> {{ number_format($template->rating_average, 1) }}
                                <span class="text-gray-400 text-xs">({{ $template->total_ratings }})</span>
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('admin.marketplace.templates.delete', $template) }}" method="POST"
                                      onsubmit="return confirm('Hapus template ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada template.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $approvedTemplates->links() }}
            </div>
        </div>
    </div>

    <!-- Tab: Purchases -->
    <div x-show="tab === 'purchases'">
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Transaksi Pembelian Template</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Template</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Pembeli</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Penjual</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($purchases as $purchase)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ Str::limit($purchase->template->title ?? '-', 35) }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $purchase->buyer->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $purchase->seller->name ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">Rp {{ number_format($purchase->price_paid, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending'    => 'bg-gray-100 text-gray-600',
                                        'processing' => 'bg-yellow-100 text-yellow-700',
                                        'completed'  => 'bg-green-100 text-green-700',
                                        'failed'     => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $statusColors[$purchase->payment_status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($purchase->payment_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $purchase->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($purchase->payment_status === 'processing')
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.marketplace.purchases.verify', $purchase) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700">Verifikasi</button>
                                    </form>
                                    <form action="{{ route('admin.marketplace.purchases.reject', $purchase) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 border border-red-300 text-red-600 text-xs rounded-lg hover:bg-red-50">Tolak</button>
                                    </form>
                                </div>
                                @else
                                <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>

    <!-- Tab: Commission Settings -->
    <div x-show="tab === 'commission'">
        <div class="bg-white rounded-lg border border-gray-200 p-6 max-w-lg">
            <h3 class="font-semibold text-gray-900 mb-1">⚙️ Pengaturan Komisi Platform</h3>
            <p class="text-sm text-gray-500 mb-6">Komisi dipotong dari setiap transaksi. Sisa diteruskan ke penjual/operator.</p>

            <form action="{{ route('admin.marketplace.commission') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Komisi Template Marketplace (%)</label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="template_commission_rate"
                               value="{{ cache('commission.template_rate', 20) }}"
                               min="0" max="100" step="0.5"
                               class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <span class="text-sm text-gray-500">% dari harga template</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        Contoh: Template Rp 50.000 → Platform dapat Rp {{ number_format(50000 * cache('commission.template_rate', 20) / 100, 0, ',', '.') }},
                        Penjual dapat Rp {{ number_format(50000 * (1 - cache('commission.template_rate', 20) / 100), 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Komisi Order Operator (%)</label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="order_commission_rate"
                               value="{{ cache('commission.order_rate', 10) }}"
                               min="0" max="100" step="0.5"
                               class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <span class="text-sm text-gray-500">% dari budget order</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        Contoh: Order Rp 500.000 → Platform dapat Rp {{ number_format(500000 * cache('commission.order_rate', 10) / 100, 0, ',', '.') }},
                        Operator dapat Rp {{ number_format(500000 * (1 - cache('commission.order_rate', 10) / 100), 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-700">
                    Perubahan komisi berlaku untuk transaksi baru. Transaksi yang sudah selesai tidak terpengaruh.
                </div>

                <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                    Simpan Pengaturan Komisi
                </button>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="rejectModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="rejectModal = false">
        <div class="bg-white rounded-xl p-6 max-w-md w-full">
            <h3 class="font-bold text-gray-900 mb-4">Tolak Template</h3>
            <form :action="`/admin/marketplace/templates/${rejectId}/reject`" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea name="rejection_reason" required rows="3"
                              placeholder="Jelaskan alasan penolakan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">Tolak Template</button>
                    <button type="button" @click="rejectModal = false" class="flex-1 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
