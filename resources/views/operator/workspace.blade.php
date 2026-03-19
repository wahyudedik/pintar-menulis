@extends('layouts.operator')

@section('title', 'Workspace')

@section('content')
<div class="p-6" x-data="workspace()">
    <div class="mb-6">
        <a href="{{ route('operator.queue') }}" class="text-green-600 hover:text-green-700 text-sm">
            ← Kembali ke Queue
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left: Order Details -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 p-4 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Client</p>
                        <p class="font-medium text-sm">{{ $order->user->name }}</p>
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
                        <p class="font-medium text-red-600 text-sm">{{ $order->deadline->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $order->deadline->diffForHumans() }}</p>
                    </div>

                    <div class="pt-3 border-t">
                        <p class="text-xs text-gray-500 mb-2">Brief dari Client</p>
                        <div class="bg-gray-50 rounded p-3">
                            <p class="text-sm text-gray-800">{{ $order->brief ?? '(tidak ada teks brief)' }}</p>
                        </div>
                        @if($order->brief_file)
                        <a href="{{ Storage::url($order->brief_file) }}" target="_blank"
                           class="mt-2 flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $order->brief_file_original_name ?? 'Download File Brief' }}
                        </a>
                        @endif
                    </div>

                    @if($order->revision_notes)
                    <div class="pt-3 border-t">
                        <p class="text-xs text-gray-500 mb-2">Request Revisi</p>
                        <div class="bg-orange-50 border border-orange-200 rounded p-3">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 text-orange-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-orange-900 mb-1">Client meminta revisi:</p>
                                    <p class="text-sm text-gray-800">{{ $order->revision_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Workspace -->
        <div class="lg:col-span-2">
            <!-- AI Assistant -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">AI Assistant</h3>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tone</label>
                    <select x-model="aiTone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                        <option value="casual">Casual</option>
                        <option value="formal">Formal</option>
                        <option value="persuasive">Persuasive</option>
                        <option value="funny">Funny</option>
                        <option value="emotional">Emotional</option>
                        <option value="educational">Educational</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keywords (opsional)</label>
                    <input type="text" x-model="aiKeywords" 
                           placeholder="Pisahkan dengan koma"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                </div>

                <button @click="generateAI" :disabled="generating"
                        class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 disabled:bg-gray-400 transition text-sm">
                    <span x-show="!generating">Generate dengan AI</span>
                    <span x-show="generating">Generating...</span>
                </button>

                <div x-show="aiResult" class="mt-4 bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <p class="text-sm font-semibold text-purple-900">AI Generated:</p>
                        <button @click="useAIResult" type="button"
                                class="text-sm bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700 transition">
                            Gunakan Hasil Ini
                        </button>
                    </div>
                    <div class="bg-white rounded-lg p-4 max-h-96 overflow-y-auto border border-purple-200">
                        <pre class="text-sm text-gray-800 whitespace-pre-wrap font-sans leading-relaxed" x-text="aiResult"></pre>
                    </div>
                    <div class="mt-2 flex gap-2">
                        <button @click="copyAIResult" type="button"
                                class="text-xs text-purple-600 hover:text-purple-700 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Copy
                        </button>
                    </div>
                </div>
            </div>

            <!-- Work Form -->
            <form method="POST" action="{{ route('operator.submit', $order) }}" class="bg-white rounded-lg border border-gray-200 p-4">
                @csrf
                
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hasil Pekerjaan</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Copywriting Result</label>
                    <textarea name="result" x-model="result" required rows="12"
                              placeholder="Tulis hasil copywriting di sini..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm focus:ring-2 focus:ring-green-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimal 50 karakter</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan untuk Client (opsional)</label>
                    <textarea name="notes" rows="3"
                              placeholder="Tambahkan catatan atau penjelasan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                        Submit Pekerjaan
                    </button>
                    <a href="{{ route('operator.queue') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function workspace() {
        return {
            aiTone: 'casual',
            aiKeywords: '',
            aiResult: '',
            generating: false,
            result: '',

            async generateAI() {
                this.generating = true;
                this.aiResult = '';

                try {
                    const response = await fetch('{{ route("api.ai.generate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            category: '{{ $order->category }}',
                            subcategory: '{{ $order->category }}',
                            platform: 'general',
                            brief: '{{ addslashes($order->brief) }}',
                            tone: this.aiTone,
                            keywords: this.aiKeywords,
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.aiResult = data.result;
                    } else {
                        alert('Error: ' + (data.message || 'Gagal generate'));
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                } finally {
                    this.generating = false;
                }
            },

            useAIResult() {
                this.result = this.aiResult;
            },

            copyAIResult() {
                const text = this.aiResult;
                
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(() => {
                        alert('Hasil AI berhasil dicopy!');
                    }).catch(() => {
                        this.fallbackCopy(text);
                    });
                } else {
                    this.fallbackCopy(text);
                }
            },

            fallbackCopy(text) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                
                try {
                    document.execCommand('copy');
                    alert('Hasil AI berhasil dicopy!');
                } catch (err) {
                    alert('Gagal copy. Silakan copy manual.');
                }
                
                document.body.removeChild(textarea);
            }
        }
    }
</script>
@endsection
