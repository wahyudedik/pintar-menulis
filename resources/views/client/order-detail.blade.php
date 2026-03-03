@extends('layouts.client')

@section('title', 'Order Detail')

@section('content')
<div class="p-6" x-data="{ showRevisionModal: false, showRatingModal: false }">
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-700 text-sm">
            ← Kembali ke Orders
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left: Order Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Order ID</p>
                        <p class="font-medium text-sm">#{{ $order->id }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <span class="inline-block px-2 py-1 rounded-full text-xs mt-1
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-700
                            @elseif($order->status === 'accepted' || $order->status === 'in_progress') bg-blue-100 text-blue-700
                            @elseif($order->status === 'completed') bg-green-100 text-green-700
                            @elseif($order->status === 'revision') bg-orange-100 text-orange-700
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Kategori</p>
                        <p class="font-medium text-sm">{{ $order->category }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Budget</p>
                        <p class="text-xl font-semibold text-green-600">Rp {{ number_format($order->budget, 0, ',', '.') }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Deadline</p>
                        <p class="font-medium text-sm">{{ $order->deadline->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $order->deadline->diffForHumans() }}</p>
                    </div>

                    @if($order->completed_at)
                    <div>
                        <p class="text-xs text-gray-500">Selesai</p>
                        <p class="font-medium text-sm">{{ $order->completed_at->format('d M Y H:i') }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-xs text-gray-500">Dibuat</p>
                        <p class="font-medium text-sm">{{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Operator Info -->
            @if($order->operator)
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Operator</h3>
                
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ substr($order->operator->name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <p class="font-semibold text-sm">{{ $order->operator->name }}</p>
                        @if($order->operator->operatorProfile)
                        <div class="flex items-center mt-1">
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span class="text-xs font-medium ml-1">{{ number_format($order->operator->operatorProfile->average_rating, 1) }}</span>
                            <span class="text-xs text-gray-500 ml-1">({{ $order->operator->operatorProfile->total_reviews }})</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($order->operator->operatorProfile && $order->operator->operatorProfile->bio)
                <p class="text-xs text-gray-600">{{ $order->operator->operatorProfile->bio }}</p>
                @endif
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800 text-sm">Menunggu operator mengambil order ini...</p>
            </div>
            @endif
        </div>

        <!-- Right: Order Details -->
        <div class="lg:col-span-2">
            <!-- Brief -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Brief dari Anda</h3>
                <div class="bg-gray-50 rounded p-3">
                    <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $order->brief }}</p>
                </div>
            </div>

            <!-- Result -->
            @if($order->result || $order->revisions->count() > 0)
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline Pekerjaan</h3>
                
                <!-- Timeline -->
                <div class="space-y-4">
                    @foreach($order->revisions as $revision)
                    <div class="relative pl-8 pb-4 {{ !$loop->last ? 'border-l-2 border-gray-200' : '' }}">
                        <!-- Timeline dot -->
                        <div class="absolute left-0 top-0 w-4 h-4 rounded-full {{ $loop->last ? 'bg-green-500' : 'bg-blue-500' }} -ml-2"></div>
                        
                        <!-- Revision content -->
                        <div class="bg-gray-50 rounded-lg p-4 border {{ $loop->last ? 'border-green-200' : 'border-gray-200' }}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $loop->first ? 'Hasil Pertama' : 'Revisi ke-' . ($revision->revision_number - 1) }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $revision->submitted_at->format('d M Y, H:i') }}</p>
                                </div>
                                @if($loop->last)
                                <button onclick="copyRevisionResult({{ $revision->id }})" class="text-blue-600 hover:text-blue-700 text-sm">
                                    Copy
                                </button>
                                @endif
                            </div>
                            
                            <div id="revision-result-{{ $revision->id }}" class="bg-white rounded p-3 mb-2">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap font-sans">{{ $revision->result }}</pre>
                            </div>
                            
                            @if($revision->operator_notes)
                            <div class="bg-blue-50 border border-blue-200 rounded p-2 mb-2">
                                <p class="text-xs font-medium text-blue-900 mb-1">Catatan Operator:</p>
                                <p class="text-sm text-gray-800">{{ $revision->operator_notes }}</p>
                            </div>
                            @endif
                            
                            @if($revision->revision_request)
                            <div class="bg-orange-50 border border-orange-200 rounded p-2 mt-2">
                                <p class="text-xs font-medium text-orange-900 mb-1">Request Revisi:</p>
                                <p class="text-sm text-gray-800">{{ $revision->revision_request }}</p>
                                @if($revision->revision_requested_at)
                                <p class="text-xs text-gray-500 mt-1">{{ $revision->revision_requested_at->format('d M Y, H:i') }}</p>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Revision Notes -->
            @if($order->revision_notes)
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                <h3 class="text-base font-semibold text-orange-900 mb-2">Request Revisi Anda</h3>
                <p class="text-sm text-gray-800">{{ $order->revision_notes }}</p>
            </div>
            @endif

            <!-- Rating & Review -->
            @if($order->rating)
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Rating & Review Anda</h3>
                <div class="flex items-center mb-2">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-5 h-5 {{ $i <= $order->rating ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    @endfor
                    <span class="ml-2 text-base font-medium">{{ $order->rating }}/5</span>
                </div>
                @if($order->review)
                <p class="text-sm text-gray-700 mt-2">{{ $order->review }}</p>
                @endif
            </div>
            @endif

            <!-- Actions -->
            @if($order->status === 'completed')
                <!-- Payment Button -->
                @php
                    $payment = \App\Models\Payment::where('order_id', $order->id)
                        ->where('status', 'success')
                        ->first();
                    $pendingPayment = \App\Models\Payment::where('order_id', $order->id)
                        ->where('status', 'processing')
                        ->first();
                @endphp

                @if(!$payment && !$pendingPayment)
                <div class="bg-gradient-to-r from-green-500 to-blue-500 rounded-lg p-4 mb-4 text-white">
                    <h3 class="text-lg font-semibold mb-2">Pembayaran</h3>
                    <p class="text-sm mb-3">Order telah selesai! Silakan lakukan pembayaran untuk menyelesaikan transaksi.</p>
                    <a href="{{ route('payment.show', $order) }}" 
                       class="inline-block bg-white text-green-600 px-6 py-2 rounded-lg hover:bg-green-50 font-semibold text-sm">
                        Bayar Sekarang - Rp {{ number_format($order->budget, 0, ',', '.') }}
                    </a>
                </div>
                @elseif($pendingPayment)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <h3 class="text-base font-semibold text-yellow-900 mb-2">Pembayaran Sedang Diverifikasi</h3>
                    <p class="text-sm text-yellow-800">Bukti pembayaran Anda sedang diverifikasi oleh admin. Mohon tunggu.</p>
                </div>
                @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <h3 class="text-base font-semibold text-green-900 mb-2">Pembayaran Berhasil</h3>
                    <p class="text-sm text-green-800">Pembayaran telah diverifikasi. Terima kasih!</p>
                </div>
                @endif

                @if(!$order->rating)
                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Aksi</h3>
                    <div class="flex gap-3">
                        <button @click="showRatingModal = true" 
                                class="flex-1 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition text-sm">
                            Beri Rating
                        </button>
                        <button @click="showRevisionModal = true" 
                                class="flex-1 bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition text-sm">
                            Request Revisi
                        </button>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Revision Modal -->
    <div x-show="showRevisionModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showRevisionModal = false">
        <div class="bg-white rounded-lg max-w-2xl w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-semibold">Request Revisi</h3>
                    <button @click="showRevisionModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('orders.revision', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apa yang perlu direvisi?</label>
                        <textarea name="revision_notes" required rows="6"
                                  placeholder="Jelaskan detail revisi yang Anda inginkan..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimal 20 karakter</p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showRevisionModal = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm">
                            Kirim Request Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div x-show="showRatingModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showRatingModal = false">
        <div class="bg-white rounded-lg max-w-2xl w-full mx-4" x-data="{ rating: 0 }">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-semibold">Beri Rating & Review</h3>
                    <button @click="showRatingModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('orders.rate', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                        <div class="flex gap-2">
                            <template x-for="i in 5" :key="i">
                                <button type="button" @click="rating = i" class="transition">
                                    <svg class="w-8 h-8" :class="i <= rating ? 'text-yellow-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="rating" :value="rating" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Review (opsional)</label>
                        <textarea name="review" rows="4"
                                  placeholder="Bagikan pengalaman Anda dengan operator ini..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showRatingModal = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                            Batal
                        </button>
                        <button type="submit" :disabled="rating === 0"
                                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 disabled:bg-gray-400 text-sm">
                            Kirim Rating
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function copyResult() {
        const content = document.getElementById('result-content').innerText;
        
        // Try modern clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(content).then(() => {
                alert('Hasil berhasil dicopy!');
            }).catch(err => {
                // Fallback if clipboard API fails
                fallbackCopy(content);
            });
        } else {
            // Fallback for older browsers
            fallbackCopy(content);
        }
    }
    
    function copyRevisionResult(revisionId) {
        const content = document.getElementById('revision-result-' + revisionId).innerText;
        
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(content).then(() => {
                alert('Hasil berhasil dicopy!');
            }).catch(err => {
                fallbackCopy(content);
            });
        } else {
            fallbackCopy(content);
        }
    }
    
    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            document.execCommand('copy');
            alert('Hasil berhasil dicopy!');
        } catch (err) {
            alert('Gagal copy. Silakan copy manual.');
        }
        
        document.body.removeChild(textarea);
    }
</script>
@endsection
