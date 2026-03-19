@extends('layouts.admin')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Withdrawal Management</h1>
        <p class="text-sm text-gray-600 mt-1">Kelola permintaan penarikan dana operator, guru & referral client</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filter by Type -->
    <div class="flex gap-2 mb-6 flex-wrap">
        <a href="{{ route('admin.withdrawals') }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ !request('type') ? 'bg-gray-900 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            Semua
        </a>
        <a href="{{ route('admin.withdrawals', ['type' => 'operator']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('type') === 'operator' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            🔧 Operator
        </a>
        <a href="{{ route('admin.withdrawals', ['type' => 'guru']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('type') === 'guru' ? 'bg-purple-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            📚 Guru
        </a>
        <a href="{{ route('admin.withdrawals', ['type' => 'referral']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('type') === 'referral' ? 'bg-green-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            🎁 Referral
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Approved</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Paid</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawals List -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">All Withdrawal Requests</h2>
        </div>
        
        @if($withdrawals->count() > 0)
        <div class="p-4 space-y-4">
            @foreach($withdrawals as $withdrawal)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-base font-semibold text-gray-900">{{ $withdrawal->user->name }}</h3>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $withdrawal->user->isGuru() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($withdrawal->user->role) }}
                            </span>
                            @php
                                $typeBadge = match($withdrawal->type ?? 'operator') {
                                    'referral' => ['label' => '🎁 Referral', 'class' => 'bg-green-100 text-green-700'],
                                    'guru'     => ['label' => '📚 Guru', 'class' => 'bg-purple-100 text-purple-700'],
                                    default    => ['label' => '🔧 Operator', 'class' => 'bg-blue-100 text-blue-700'],
                                };
                            @endphp
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $typeBadge['class'] }}">
                                {{ $typeBadge['label'] }}
                            </span>
                            <span class="px-2 py-1 text-xs font-medium rounded
                                @if($withdrawal->status === 'pending') bg-yellow-100 text-yellow-700
                                @elseif($withdrawal->status === 'approved') bg-blue-100 text-blue-700
                                @elseif($withdrawal->status === 'completed') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700
                                @endif">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Amount</p>
                                <p class="font-bold text-red-600">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Bank Account</p>
                                <p class="font-medium text-gray-900">{{ $withdrawal->bank_name }} - {{ $withdrawal->account_number }}</p>
                                <p class="text-xs text-gray-500">{{ $withdrawal->account_name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Requested</p>
                                <p class="font-medium text-gray-900">{{ $withdrawal->created_at->format('d M Y H:i') }}</p>
                            </div>
                            @if($withdrawal->processed_at)
                            <div>
                                <p class="text-gray-600">Processed</p>
                                <p class="font-medium text-gray-900">{{ $withdrawal->processed_at->format('d M Y H:i') }}</p>
                            </div>
                            @endif
                        </div>
                        @if($withdrawal->admin_notes)
                        <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Admin Notes:</p>
                            <p class="text-sm text-gray-800">{{ $withdrawal->admin_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($withdrawal->status === 'pending')
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Approve withdrawal request ini?')"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                            Approve
                        </button>
                    </form>
                    <button onclick="showRejectModal({{ $withdrawal->id }})"
                            class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        Reject
                    </button>
                </div>
                @elseif($withdrawal->status === 'approved')
                <div class="pt-4 border-t border-gray-200">
                    <form action="{{ route('admin.withdrawals.complete', $withdrawal) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Mark as completed? Pastikan dana sudah ditransfer.')"
                                class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                            Mark as Completed
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $withdrawals->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500">Belum ada withdrawal request</p>
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Withdrawal Request</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
                <textarea name="admin_notes" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                          placeholder="Jelaskan alasan rejection..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Reject
                </button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(withdrawalId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/admin/withdrawals/${withdrawalId}/reject`;
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
}

// Close modal on outside click
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection
